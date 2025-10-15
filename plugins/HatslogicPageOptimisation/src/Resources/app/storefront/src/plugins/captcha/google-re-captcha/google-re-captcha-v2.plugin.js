import DomAccess from 'src/helper/dom-access.helper';
import GoogleReCaptchaV2Plugin from 'src/plugin/captcha/google-re-captcha/google-re-captcha-v2.plugin';

export default class NFXGoogleReCaptchaV2Plugin  extends GoogleReCaptchaV2Plugin {

    static options = {
        grecaptchaInputSelector: '.grecaptcha-v2-input',
        checkboxContainer: '.grecaptcha-v2-container',
        grecaptchaIframeHasErrorClassSelector: 'has-error',
        siteKey: null,
        invisible: false,
    };

    init() {

        alert(captchaEndPoint);

        var fired = false;
        const recaptchaScript = document.getElementById('recaptcha-script');
        const captchaEndPoint = recaptchaScript.getAttribute('data-src');

        const loadCaptcha = () => {
            if (!fired) {
                fired = true;
                
                if (recaptchaScript) {
                    const require = (urls, callback) => {
                        let loadedCount = 0;

                        const loadScript = (url) => {
                            var script = document.createElement("script");
                            script.src = url;
                            script.type = "text/javascript";
                            script.addEventListener('load', function() {
                                loadedCount++;
                                if (loadedCount === urls.length) {
                                    callback();
                                }
                            });
                            document.getElementsByTagName("head")[0].appendChild(script);
                        }

                        urls.forEach(loadScript);
                    }

                    require([captchaEndPoint], () => {
                        recaptchaScript.setAttribute('src', captchaEndPoint);
                        recaptchaScript.removeAttribute('data-src');

                        super.init();

                        this.grecaptchaContainer = this.el.querySelector(this.options.checkboxContainer);
                        this.grecaptchaContainerIframe = null;
                        this.grecaptchaWidgetId = null;

                        this._renderV2Captcha();
                    });
                }
            }
        };

        window.addEventListener('scroll', loadCaptcha, true);
        document.addEventListener('input', loadCaptcha, true);
        document.addEventListener('click', loadCaptcha, true);
        document.addEventListener('keydown', loadCaptcha, true);
        document.addEventListener('touchstart', loadCaptcha, true);
        document.addEventListener('mousemove', loadCaptcha, true);

    }

    getGreCaptchaInfo() {
        return {
            version: 'GoogleReCaptchaV2',
            invisible: this.options.invisible,
        };
    }

    onFormSubmit() {
        if (this.options.invisible) {
            if (this.grecaptchaWidgetId === null) {
                return;
            }

            this.grecaptcha.execute(this.grecaptchaWidgetId).then(() => {
                this._formSubmitting = false;
            });
        } else {
            if (!this.grecaptchaInput.value) {
                this.grecaptchaContainerIframe = DomAccess.querySelector(this.el, 'iframe');
                this.grecaptchaContainerIframe.classList.add(this.options.grecaptchaIframeHasErrorClassSelector);
            }

            this._formSubmitting = false;

            this.$emitter.publish('beforeGreCaptchaFormSubmit', {
                info: this.getGreCaptchaInfo(),
                token: this.grecaptchaInput.value,
            });
        }
    }

    /**
     * @private
     */
    _renderV2Captcha() {
        this.grecaptcha.ready(this._onGreCaptchaReady.bind(this));
    }

    /**
     * @private
     */
    _onCaptchaTokenResponse(token) {
        this.$emitter.publish('onGreCaptchaTokenResponse', {
            info: this.getGreCaptchaInfo(),
            token,
        });

        this._formSubmitting = false;
        this.grecaptchaInput.value = token;

        if (!this.options.invisible) {
            this.grecaptchaContainerIframe.classList.remove(this.options.grecaptchaIframeHasErrorClassSelector);
            return;
        }

        this._submitInvisibleForm();
    }

    /**
     * @private
     */
    _onGreCaptchaReady() {
        this.grecaptchaWidgetId = this.grecaptcha.render(this.grecaptchaContainer, {
            sitekey: this.options.siteKey,
            size: this.options.invisible ? 'invisible' : 'normal',
            callback: this._onCaptchaTokenResponse.bind(this),
            'expired-callback': this._onGreCaptchaExpire.bind(this),
            'error-callback': this._onGreCaptchaError.bind(this),
        });

        this.grecaptchaContainerIframe = DomAccess.querySelector(this.el, 'iframe');
    }

    /**
     * @private
     */
    _onGreCaptchaExpire() {
        this.$emitter.publish('onGreCaptchaExpire', {
            info: this.getGreCaptchaInfo(),
        });

        this.grecaptcha.reset(this.grecaptchaWidgetId);
        this.grecaptchaInput.value = '';
    }

    /**
     * @private
     */
    _onGreCaptchaError() {
        this.$emitter.publish('onGreCaptchaError', {
            info: this.getGreCaptchaInfo(),
        });
    }
}
import GoogleReCaptchaV3Plugin from 'src/plugin/captcha/google-re-captcha/google-re-captcha-v3.plugin';

export default class NFXGoogleReCaptchaV3Plugin  extends GoogleReCaptchaV3Plugin {
    init() {

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
}
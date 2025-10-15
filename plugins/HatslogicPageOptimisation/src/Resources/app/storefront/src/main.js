const PluginManager = window.PluginManager;

if (window.googleReCaptchaV2Active) {
    PluginManager.override('GoogleReCaptchaV2', () => import('./plugins/captcha/google-re-captcha/google-re-captcha-v2.plugin'), '[data-google-re-captcha-v2]');
  }
  
  if (window.googleReCaptchaV3Active) {
    PluginManager.override('GoogleReCaptchaV3', () => import('./plugins/captcha/google-re-captcha/google-re-captcha-v3.plugin'), '[data-google-re-captcha-v3]');
  }
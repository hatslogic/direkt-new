import Plugin from 'src/plugin-system/plugin.class';

export default class CookieConfigurationOverride extends Plugin {
    init() { }

    openOffCanvas() {
        window.openCookieConsentManager();
    }
}

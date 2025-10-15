import Plugin from 'src/plugin-system/plugin.class';
import { COOKIE_CONFIGURATION_UPDATE } from 'src/plugin/cookie/cookie-configuration.plugin';

function getCookie(name) {
	const cookieMatch = document.cookie.match(name + '=(.*?)(;|$)')
	return cookieMatch && decodeURI(cookieMatch[1])
}

export default class SmartsuppPlugin extends Plugin {
	init() {
		document.$emitter.subscribe(COOKIE_CONFIGURATION_UPDATE, this.onCookieUpdated)
		if (getCookie('smartsupp-functional') === '1') {
			smartsupp.getWidget('default').render()
			smartsupp('analyticsConsent', getCookie('smartsupp-analytical') === '1')
			smartsupp('marketingConsent', getCookie('smartsupp-marketing') === '1')
		}
	}

	onCookieUpdated() {
		if (getCookie('smartsupp-functional') === '1') {
			smartsupp.getWidget('default').render()
			smartsupp('analyticsConsent', getCookie('smartsupp-analytical') === '1')
			smartsupp('marketingConsent', getCookie('smartsupp-marketing') === '1')
		}
	}
}

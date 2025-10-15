import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';
import AjaxOffCanvas from 'src/plugin/offcanvas/ajax-offcanvas.plugin';
import HttpClient from 'src/service/http-client.service';


import AddToCartEvent from './events/add-to-cart.event';
import AddToCartByNumberEvent from './events/add-to-cart-by-number.event';
import BeginCheckoutEvent from './events/begin-checkout.event';
import BeginCheckoutOnCartEvent from './events/begin-checkout-on-cart.event';
import CheckoutProgressEvent from './events/checkout-progress.event';
import LoginEvent from './events/login.event';
import PurchaseEvent from './events/purchase.event';
import RemoveFromCartEvent from './events/remove-from-cart.event';
import SearchAjaxEvent from './events/search-ajax.event';
import SignUpEvent from './events/sign-up.event';
import ViewItemEvent from './events/view-item.event';
import ViewItemListEvent from './events/view-item-list.event';
import ViewSearchResultsEvent from './events/view-search-results';

import CookieStorageHelper from 'src/helper/storage/cookie-storage.helper';
import { COOKIE_CONFIGURATION_UPDATE } from 'src/plugin/cookie/cookie-configuration.plugin';

/**
 * This plugin handels the price request window activities
 */
export default class ga4script extends Plugin {
    static options = {

    }

    init() {
        this.cookieEnabledName = window.nbGA4CookieName + "_enabled";

        this.handleCookieChangeEvent();

        if (window.useDefaultCookieConsent && !CookieStorageHelper.getItem(this.cookieEnabledName)) {
            return;
        }

        this.launchGA4();
    }

    launchGA4(){
        const gtmScript = document.createElement('script');
        gtmScript.src = window.nbGA4TrackingUrl;
        document.head.append(gtmScript);

        gtag('js', new Date());
        gtag('config', window.nbGA4TrackingId);

        const bodyClasses = [...document.body.classList]

        this.controllerName = bodyClasses.find(e => e.indexOf("is-ctl-") === 0).replace("is-ctl-", "");
        this.actionName = bodyClasses.find(e => e.indexOf("is-act-") === 0).replace("is-act-", "");
        this.events = [];

        this.registerDefaultEvents();
        this.handleEvents();
    }

    registerDefaultEvents() {
        this.registerEvent(AddToCartEvent);
        this.registerEvent(AddToCartByNumberEvent);
        this.registerEvent(BeginCheckoutEvent);
        this.registerEvent(BeginCheckoutOnCartEvent);
        this.registerEvent(CheckoutProgressEvent);
        this.registerEvent(LoginEvent);
        this.registerEvent(PurchaseEvent);
        this.registerEvent(RemoveFromCartEvent);
        this.registerEvent(SearchAjaxEvent);
        this.registerEvent(SignUpEvent);
        this.registerEvent(ViewItemEvent);
        this.registerEvent(ViewItemListEvent);
        this.registerEvent(ViewSearchResultsEvent);
    }

    /**
     * @param { AnalyticsEvent } event
     */
    registerEvent(event) {
        this.events.push(new event());
    }

    handleEvents() {
        this.events.forEach(event => {
            if (!event.supports(this.controllerName, this.actionName)) {
                return;
            }

            event.execute();
        });
    }

    handleCookieChangeEvent() {
        document.$emitter.subscribe(COOKIE_CONFIGURATION_UPDATE, this.handleCookies.bind(this));
    }

    handleCookies(cookieUpdateEvent) {
        const updatedCookies = cookieUpdateEvent.detail;

        if (!Object.prototype.hasOwnProperty.call(updatedCookies, this.cookieEnabledName)) {
            return;
        }

        if (updatedCookies[this.cookieEnabledName]) {
            this.startGoogleAnalytics();
            return;
        }

        this.removeCookies();
        this.disableEvents();
    }

    removeCookies() {
        const allCookies = document.cookie.split(';');
        const gaCookieRegex = /^(_swag_ga|_gat_gtag)/;

        allCookies.forEach(cookie => {
            const cookieName = cookie.split('=')[0].trim();
            if (!cookieName.match(gaCookieRegex)) {
                return;
            }

            CookieStorageHelper.removeItem(cookieName);
        });
    }

    disableEvents() {
        this.events.forEach(event => {
            event.disable();
        });
    }

}
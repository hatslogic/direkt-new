import NimbitsAnalyticsEvent from '../nimbits-analytics-event';
import DomAccessHelper from 'src/helper/dom-access.helper';
import NimbitsLineItemHelper from '../nimbits-line-item.helper';

export default class BeginCheckoutOnCartEvent extends NimbitsAnalyticsEvent
{
    supports(controllerName, actionName) {
        return controllerName === 'checkout' && actionName === 'cartpage';
    }

    execute() {
        const beginCheckoutBtn = DomAccessHelper.querySelector(document, '.begin-checkout-btn', false);

        if (!beginCheckoutBtn) {
            return;
        }

        beginCheckoutBtn.addEventListener('click', this._onBeginCheckout.bind(this));
    }

    _onBeginCheckout() {
        if (!this.active) {
            return;
        }

        gtag('event', 'begin_checkout', {
            'items': NimbitsLineItemHelper.getLineItems(),
        });
    }
}

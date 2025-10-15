import EventAwareNimbitsAnalyticsEvent from '../event-aware-nimbits-analytics-event';
import DomAccessHelper from 'src/helper/dom-access.helper';
import NimbitsLineItemHelper from '../nimbits-line-item.helper';

export default class BeginCheckoutEvent extends EventAwareNimbitsAnalyticsEvent
{
    supports() {
        return true;
    }

    getEvents() {
        return {
            'offCanvasOpened': this._offCanvasOpened.bind(this),
        };
    }

    getPluginName() {
        return 'OffCanvasCart'
    }

    _offCanvasOpened() {
        DomAccessHelper.querySelector(document, '.begin-checkout-btn').addEventListener('click', this._onBeginCheckout.bind(this));
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

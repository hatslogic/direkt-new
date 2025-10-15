import NimbitsAnalyticsEvent from '../nimbits-analytics-event';
import DomAccessHelper from 'src/helper/dom-access.helper';
import NimbitsLineItemHelper from '../nimbits-line-item.helper';

export default class PurchaseEvent extends NimbitsAnalyticsEvent
{
    supports(controllerName, actionName) {
        return controllerName === 'checkout' && actionName === 'finishpage' && window.trackOrders;
    }

    execute() {
        if (!this.active) {
            return;
        }

        const orderNumberElement = DomAccessHelper.querySelector(document, '.finish-ordernumber');

        if (!orderNumberElement) {
            return;
        }

        const orderNumber = DomAccessHelper.getDataAttribute(orderNumberElement, 'order-number');
        if (!orderNumber) {
            console.warn('Cannot determine order number - Skip order tracking');

            return;
        }

        gtag('event', 'purchase', { ...{
                'transaction_id': orderNumber,
                'items':  NimbitsLineItemHelper.getLineItems(),
            }, ...NimbitsLineItemHelper.getAdditionalProperties() });
    }
}

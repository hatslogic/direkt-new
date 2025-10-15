import NimbitsAnalyticsEvent from '../nimbits-analytics-event';
import NimbitsLineItemHelper from '../nimbits-line-item.helper';

export default class CheckoutProgressEvent extends NimbitsAnalyticsEvent
{
    supports(controllerName, actionName) {
        return controllerName === 'checkout' && actionName === 'confirmpage';
    }

    execute() {
        if (!this.active) {
            return;
        }

        gtag('event', 'checkout_progress', {
            'items': NimbitsLineItemHelper.getLineItems(),
        });
    }
}

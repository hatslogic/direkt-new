import AcrisAnalyticsEvent from '../acris-analytics-event';
import DomAccessHelper from 'src/helper/dom-access.helper';
import AcrisEventHelper from "../helper/acris-event-helper";

export default class AcrisRemoveFromCartEvent extends AcrisAnalyticsEvent
{
    execute() {
        document.addEventListener('click', this._onRemoveFromCart.bind(this));
    }

    async _onRemoveFromCart(event) {

        let closest = event.target.closest('.line-item-remove-button');
        if (!closest) {
            closest = event.target.closest('.cart-item-remove-button');
            if (!closest) {
                return null;
            }
        }

        const productId = this._findProductId(closest);
        if (!productId) {
            return;
        }

        const tempProduct = this._findProduct(productId)
        if (!tempProduct) {
            const productId = this._findParentProductId(closest);
            if (!productId) {
                return;
            }
            await this._pushToDataLayer(productId);
        }else{
            await this._pushToDataLayer(productId);
        }

    }

    _findProductId(element) {
        return DomAccessHelper.getDataAttribute(element, 'product-id');
    }

    _findParentProductId(element) {
        return DomAccessHelper.getDataAttribute(element, 'configurator-product-parent-id');
    }

    async _pushToDataLayer(productId) {
        const product = this._findProduct(productId)
        if (!product) {
            return;
        }

        const unitRounded = Number(parseFloat(product.price).toFixed(2));
        const productTotalPrice = Number((unitRounded * product.quantity).toFixed(2));

        window.dataLayer.push({
            'event': 'remove_from_cart',
            'ecommerce': {
                'currencyCode': product.currency,
                'value': productTotalPrice,
                'remove': {
                    'products': [{
                        'name': product.name,
                        'id': product.number,
                        'price': unitRounded,
                        'quantity': product.quantity
                    },]
                },
            }
        });
    }

    _findProduct(productId) {
        const dataEvents = [
            'checkout-cart-page-loaded',
            'checkout-confirm-page-loaded',
            'checkout-info-widget-loaded',
            'checkout-offcanvas-widget-loaded',
        ];

        for (const dataEvent of dataEvents) {
            const data = AcrisEventHelper.findData(dataEvent)

            if (data && data.cart && data.cart.products) {
                for (const product of data.cart.products) {
                    if (product.id === productId) {
                        return product;
                    }
                }
            }
        }

        return null;
    }
}

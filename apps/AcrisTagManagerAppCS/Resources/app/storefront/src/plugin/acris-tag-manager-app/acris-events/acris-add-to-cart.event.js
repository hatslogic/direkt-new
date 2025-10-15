import AcrisAnalyticsEvent from '../acris-analytics-event';
import AcrisEventHelper from "../helper/acris-event-helper";

export default class AcrisAddToCartEvent extends AcrisAnalyticsEvent
{
    execute() {
        document.addEventListener('submit', this._beforeFormSubmit.bind(this));
    }

    async _beforeFormSubmit(event) {
        // Only handle add-to-cart forms, ignore removals and others
        if (!this._isAddToCartForm(event.target)) {
            return;
        }

        const productId = this._findProductId(event.target);
        await this._pushToDataLayer(productId, event.target);
    }

    _isAddToCartForm(form) {
        if (!form || !form.action) {
            return false;
        }
        try {
            return form.action.includes('/checkout/line-item/add');
        } catch (e) {
            return false;
        }
    }

    _findProductId(formData) {
        let productId = null;

        for (const data of formData) {
            if (data.name && data.name.endsWith('[id]')) {
                productId = data.value;
            }
        }

        if (!productId) {
            return;
        }

        return productId;
    }

    async _pushToDataLayer(productId, formData) {
        const product = this._findProduct(productId);
        if (!product) {
            return;
        }

        let productQuantity;
        let productConfiguratorPrice;
        for (const data of formData) {
            if (data.name && data.name.endsWith('[quantity]')) {
                // Parse quantity robustly; handle empty strings and invalid values
                const raw = (typeof data.value === 'string') ? data.value.trim() : data.value;
                const parsed = parseInt(raw, 10);
                productQuantity = Number.isFinite(parsed) ? parsed : null;
            }
            if (data.name && data.name.endsWith('[configuratorPrice]')) {
                productConfiguratorPrice = data.value;
            }
        }

        // Determine unit price considering tier prices if available
        const fallbackQty = (product && Number.isFinite(product.quantity) && product.quantity > 0) ? product.quantity : 1;
        const qty = (Number.isFinite(productQuantity) && productQuantity > 0) ? productQuantity : fallbackQty;
        let unitPrice = null;

        // Select correct tier using quantityEnd if available
        if (product.priceTiers && Array.isArray(product.priceTiers) && product.priceTiers.length > 0 && qty) {
            // Use only quantityEnd to determine the matching tier
            const tiers = [...product.priceTiers].sort((a, b) => {
                const ea = (a.quantityEnd === null || typeof a.quantityEnd === 'undefined') ? Number.POSITIVE_INFINITY : (typeof a.quantityEnd === 'number' ? a.quantityEnd : parseInt(a.quantityEnd));
                const eb = (b.quantityEnd === null || typeof b.quantityEnd === 'undefined') ? Number.POSITIVE_INFINITY : (typeof b.quantityEnd === 'number' ? b.quantityEnd : parseInt(b.quantityEnd));
                return ea - eb;
            });
            let matched = null;
            for (const tier of tiers) {
                const end = (tier.quantityEnd === null || typeof tier.quantityEnd === 'undefined') ? Number.POSITIVE_INFINITY : (typeof tier.quantityEnd === 'number' ? tier.quantityEnd : parseInt(tier.quantityEnd));
                if (qty <= end) {
                    matched = tier;
                    break;
                }
            }
            if (!matched) {
                matched = tiers[tiers.length - 1];
            }
            unitPrice = parseFloat(matched.price);
        } else if (productConfiguratorPrice) {
            // If no tier is present, but configurator price is provided, use it as the unit price
            unitPrice = parseFloat(productConfiguratorPrice);
        } else {
            unitPrice = parseFloat(product.price);
        }

        const unitRounded = Number(unitPrice.toFixed(2));
        const productTotalPrice = Number((unitRounded * qty).toFixed(2));

        if (qty) {
            window.dataLayer.push({
                'event': 'add_to_cart',
                'ecommerce': {
                    'currencyCode': product.currency,
                    'value': productTotalPrice,
                    'add': {
                        'products': [{
                            'name': product.name,
                            'id': product.number,
                            'price': unitRounded,
                            'quantity': qty
                        },]
                    },
                }
            });
        }
    }

    _findProduct(productId) {
        const dataEvents = [
            'product-page-loaded',
            'navigation-page-loaded',
            'search-page-loaded',
            'product-quick-view-widget-loaded'
        ];

        for (const dataEvent of dataEvents) {
            const data = AcrisEventHelper.findData(dataEvent)
            if (data && data.product) {
                if (data.product.id === productId) {
                    return data.product;
                }
            }

            if (data && data.productListing && data.productListing.products) {
                for (const product of data.productListing.products) {
                    if (product.id === productId) {
                        return product;
                    }
                }
            }
        }

        return null;
    }
}

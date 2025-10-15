import AcrisAnalyticsEvent from '../acris-analytics-event';
import AcrisEventHelper from "../helper/acris-event-helper";

export default class AcrisAddToWishlistEvent extends AcrisAnalyticsEvent
{
    execute() {
        let elementsDetail = document.getElementsByClassName('product-wishlist-action');
        for(let buttonDetail of elementsDetail) {
            buttonDetail.addEventListener("click", this._onClick.bind(buttonDetail));
        }
        let elementsListing = document.getElementsByClassName('product-wishlist-btn');
        for(let buttonListing of elementsListing) {
            buttonListing.addEventListener("click", this._onClick.bind(buttonListing));
        }
    }

    async _onClick(event) {
        let productId;
        const productNumberElement = document.querySelector('.product-detail-ordernumber');
        if(productNumberElement) {
            if(event.currentTarget.classList.contains("product-wishlist-added")) {
                productId = productNumberElement.innerText;
            }
        }else{
            if(event.currentTarget.classList.contains("product-wishlist-added")) {
                productId = event.currentTarget.firstChild.nextSibling.innerText;
            }
        }

        if(productId) {
            let product;

            const dataEvents = [
                'product-page-loaded',
                'navigation-page-loaded',
                'search-page-loaded',
                'product-quick-view-widget-loaded'
            ];

            for (const dataEvent of dataEvents) {
                const data = AcrisEventHelper.findData(dataEvent)
                if (data && data.product) {
                    product = data.product
                    window.dataLayer.push({
                        'event': 'add_to_wishlist',
                        'ecommerce': {
                            'currency': product.currency,
                            'value': product.price,
                            'items': {
                                'item_name': product.name,
                                'item_id': product.number,
                                'price': product.price,
                                'quantity': 1
                            },
                        }
                    });
                }

                if (data && data.productListing && data.productListing.products) {
                    for (const listingProduct of data.productListing.products) {
                        if (listingProduct.number === productId) {
                            product = listingProduct;
                            window.dataLayer.push({
                                'event': 'add_to_wishlist',
                                'ecommerce': {
                                    'currency': product.currency,
                                    'value': product.price,
                                    'items': {
                                        'item_name': product.name,
                                        'item_id': product.number,
                                        'price': product.price,
                                        'quantity': 1
                                    },
                                }
                            });
                        }
                    }
                }
            }
        }
    }
}

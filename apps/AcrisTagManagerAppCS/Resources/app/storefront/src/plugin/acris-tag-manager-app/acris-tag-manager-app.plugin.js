import Plugin from 'src/plugin-system/plugin.class';
import AcrisAddToCartEvent from './acris-events/acris-add-to-cart.event';
import AcrisRemoveFromCartEvent from './acris-events/acris-remove-from-cart.event';
import AcrisAddToWishlistEvent from "./acris-events/acris-add-to-wishlist.event";

export default class AcrisTagManagerApp extends Plugin
{
    init() {
        this.acrisEvents = [];
        this._registerEvents();
        this._executeEvents();
    }

    _registerEvents() {
        const addToCartEvent = new AcrisAddToCartEvent({context: this.options.context});
        addToCartEvent.init();
        this.acrisEvents.push(addToCartEvent);

        const removeFromCartEvent = new AcrisRemoveFromCartEvent({context: this.options.context});
        removeFromCartEvent.init();
        this.acrisEvents.push(removeFromCartEvent);

        const addToWishlistEvent = new AcrisAddToWishlistEvent({context: this.options.context});
        addToWishlistEvent.init();
        this.acrisEvents.push(addToWishlistEvent);
    }

    _executeEvents() {
        this.acrisEvents.forEach(acrisEvent => {
            if (typeof this.options.context === 'undefined' || this.options.context === null) {
                return;
            }
            acrisEvent.execute();
        });
    }
}
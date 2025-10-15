import AcrisTagManagerApp from './plugin/acris-tag-manager-app/acris-tag-manager-app.plugin';
import AcrisRemoveFromCartEvent from "./plugin/acris-tag-manager-app/acris-events/acris-remove-from-cart.event";
import AcrisAddToCartEvent from "./plugin/acris-tag-manager-app/acris-events/acris-add-to-cart.event";
import AcrisAddToWishlistEvent from "./plugin/acris-tag-manager-app/acris-events/acris-add-to-wishlist.event";
import "./plugin/reacting-cookie/reacting-cookie";

const { PluginManager } = window;

PluginManager.register('AcrisTagManagerApp', AcrisTagManagerApp, '[data-acris-tag-manager-app]');

try{
    window.PluginManager.getPluginInstances('AcrisAddToCartEvent');
}catch (error){
    window.PluginManager.register('AcrisAddToCartEvent', AcrisAddToCartEvent, '[data-acris-tag-manager-app]');
}

try{
    window.PluginManager.getPluginInstances('AcrisRemoveFromCartEvent');
}catch (error){
    window.PluginManager.register('AcrisRemoveFromCartEvent', AcrisRemoveFromCartEvent, '[data-acris-tag-manager-app]');
}

try{
    window.PluginManager.getPluginInstances('AcrisAddToWishlistEvent');
}catch (error){
    window.PluginManager.register('AcrisAddToWishlistEvent', AcrisAddToWishlistEvent, '[data-acris-tag-manager-app]');
}

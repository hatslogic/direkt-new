import ga4 from './nimbits-ga4/ga4script';

// Register them via the existing PluginManager
const PluginManager = window.PluginManager;
PluginManager.register('ga4', ga4);//, 'body');

// Necessary for the webpack hot module reloading server
if (module.hot) {
    module.hot.accept();
}

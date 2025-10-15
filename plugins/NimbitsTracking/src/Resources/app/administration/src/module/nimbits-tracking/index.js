import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

Shopware.Module.register('nimbits-tracking', {
    type: 'plugin',
    name: 'nimbits-tracking.module.name',
    title: 'nimbits-tracking.module.title',
    description: 'This module allows you to see how many users came through partners and how much money they spend.',
    color: '#6F58FF',
    icon: 'default-chart-line',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        dashboard: {
            component: 'nimbits-tracking-dashboard',
            path: 'dashboard'
        }
    },

    navigation: [{
        label: 'nimbits-tracking.navigation.label',
        color: '#02d3ff',
        path: 'nimbits.tracking.dashboard',
        icon: 'default-chart-line',
        parent: 'sw-marketing',
        position: 100
    }]
});
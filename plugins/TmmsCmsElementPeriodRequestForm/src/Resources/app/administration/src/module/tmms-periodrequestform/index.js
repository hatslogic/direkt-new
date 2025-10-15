import './acl';

const { Module } = Shopware;

Shopware.Component.register('tmms-periodrequestform-list', () => import('./page/tmms-periodrequestform-list'));
Shopware.Component.register('tmms-periodrequestform-detail', () => import('./page/tmms-periodrequestform-detail'));
Shopware.Component.extend('tmms-periodrequestform-create', 'tmms-periodrequestform-detail', () => import('./page/tmms-periodrequestform-create'));

Module.register('tmms-periodrequestform', {
    type: 'plugin',
    name: 'PeriodRequestForm',
    title: 'periodrequestform.list.textPeriodrequestformOverview',
    description: 'periodrequestform.list.textPeriodrequestformDescription',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#ff3d58',
    icon: 'regular-users',
    favicon: 'icon-module-customers.png',
    entity: 'periodrequestform',

    routes: {
        list: {
            components: {
                default: 'tmms-periodrequestform-list',
            },
            path: 'list',
            meta: {
                privilege: 'periodrequestform.viewer',
            }
        },
        create: {
            component: 'tmms-periodrequestform-create',
            path: 'create',
            meta: {
                parentPath: 'tmms.periodrequestform.list',
                privilege: 'periodrequestform.viewer',
            }
        },
        detail: {
            component: 'tmms-periodrequestform-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'tmms.periodrequestform.list',
                privilege: 'periodrequestform.viewer',
            }
        },
    },
    navigation: [{
        id: 'tmms-periodrequestform',
        label: 'periodrequestform.list.textPeriodrequestformOverview',
        color: '#57D9A3',
        path: 'tmms.periodrequestform.list',
        icon: 'regular-users',
        parent: 'sw-customer',
        position: 20,
        privilege: 'periodrequestform.viewer',
    }]
});

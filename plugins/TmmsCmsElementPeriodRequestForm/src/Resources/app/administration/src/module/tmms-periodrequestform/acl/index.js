if (Shopware.Service('privileges')) {
    Shopware.Service('privileges').addPrivilegeMappingEntry({
        category: 'permissions',
        parent: null,
        key: 'periodrequestform',
        roles: {
            viewer: {
                privileges: [],
                dependencies: []
            },
            editor: {
                privileges: [],
                dependencies: [
                    'periodrequestform.viewer'
                ]
            },
            creator: {
                privileges: [],
                dependencies: [
                    'periodrequestform.viewer'
                ]
            },
            deleter: {
                privileges: [],
                dependencies: [
                    'periodrequestform.viewer'
                ]
            }
        }
    });
}

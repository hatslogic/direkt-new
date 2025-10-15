/**
 * @private
 * @package buyers-experience
 */
Shopware.Component.register('sw-cms-block-preview-period-request-form', () => import('./preview'));
/**
 * @private
 * @package buyers-experience
 */
Shopware.Component.register('sw-cms-block-period-request-form', () => import('./component'));

/**
 * @private
 * @package buyers-experience
 */

Shopware.Service('cmsService').registerCmsBlock({
    name: 'period-request-form',
    label: 'sw-cms.blocks.form.periodRequestFormElement.label',
    category: 'form',
    component: 'sw-cms-block-period-request-form',
    previewComponent: 'sw-cms-block-preview-period-request-form',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed'
    },
    slots: {
        content: 'period-request-form'
    }
});

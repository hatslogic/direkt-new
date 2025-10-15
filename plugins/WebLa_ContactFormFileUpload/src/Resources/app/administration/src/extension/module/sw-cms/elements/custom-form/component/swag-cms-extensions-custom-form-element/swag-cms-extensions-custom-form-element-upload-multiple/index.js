import template from './swag-cms-extensions-custom-form-element-upload-multiple.html.twig';

const { Component } = Shopware;

Component.register('swag-cms-extensions-custom-form-element-upload-multiple', {
    template,

    props: {
        field: {
            type: Object,
            required: true,
        },
    },
});

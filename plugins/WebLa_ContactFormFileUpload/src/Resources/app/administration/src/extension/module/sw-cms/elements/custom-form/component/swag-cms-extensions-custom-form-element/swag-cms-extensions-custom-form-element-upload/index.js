import template from './swag-cms-extensions-custom-form-element-upload.html.twig';

const { Component } = Shopware;

Component.register('swag-cms-extensions-custom-form-element-upload', {
    template,

    props: {
        field: {
            type: Object,
            required: true,
        },
    },
});

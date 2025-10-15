import template from './hatslogic-image-convert-into-webp-btn.html.twig';

const { Component, Context, Utils, Service, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('hatslogic-image-convert-into-webp-btn', {
    template,

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            buttonLabel: 'Convert image into Webp',
            isLoading: false,
        };
    },

    created() {
        this.syncService = Service('syncService');
        this.httpClient = this.syncService.httpClient;
    },

    methods: {
        convertImageIntoWebP() {
            this.isLoading = true;

            this.createNotificationInfo({
                title: 'Convert image into Webp',
                message: 'Converting images'
            });

            this.httpClient.get('/_action/hatslogic/convert/imagestowebp', {
                headers: this.syncService.getBasicHeaders()
            }).then(response => {
                this.isLoading = false;

                if (response.data.success === true) {
                    this.createNotificationSuccess({
                        title: this.$tc('global.default.success'),
                        message: 'Successfully converted images'
                    });
                } else {
                    this.createNotificationError({
                        title: this.$tc('global.default.error'),
                        message: response.data.message
                    });
                }
            }).catch(e => {
                this.isLoading = false;

                this.createNotificationError({
                    title: this.$tc('global.default.error'),
                    message: e.response.data.message
                });
            });
        }
    }
});

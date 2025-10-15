const { Component, Service, Mixin } = Shopware;
import template from './sw-media-quickinfo.html.twig';

Component.override('sw-media-quickinfo', {
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
            if (!this.item || !this.item.url) {
                this.createNotificationError({
                    title: this.$tc('global.default.error'),
                    message: 'No image URL found to convert.'
                });
                return;
            }

            console.log(this.item);
            console.log(this.item.url);

            this.isLoading = true;

            this.createNotificationInfo({
                title: 'Convert image into Webp',
                message: 'Converting images'
            });

            this.httpClient.post(
                '/_action/hatslogic/convert/singleimagestowebp', // API endpoint
                { url: this.item.url }, // Payload
                {
                    headers: this.syncService.getBasicHeaders()
                }
            ).then(response => {
                this.isLoading = false;

                if (response.data.success === true) {
                    this.createNotificationSuccess({
                        title: this.$tc('global.default.success'),
                        message: 'Successfully converted the image to WebP format.'
                    });
                } else {
                    this.createNotificationError({
                        title: this.$tc('global.default.error'),
                        message: response.data.message || 'Failed to convert the image.'
                    });
                }
            }).catch(error => {
                this.isLoading = false;

                this.createNotificationError({
                    title: this.$tc('global.default.error'),
                    message: error.response?.data?.message || 'An error occurred while converting the image.'
                });
                console.error('Error:', error);
            });
        }
    }
});

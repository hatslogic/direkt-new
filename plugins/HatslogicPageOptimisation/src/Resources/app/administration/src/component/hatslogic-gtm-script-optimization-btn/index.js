import template from './hatslogic-gtm-script-optimization-btn.html.twig';

const { Component, Context, Utils, Service, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('hatslogic-gtm-script-optimization-btn', {
    template,

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            buttonLabel: 'Optimize Gtm Script',
            isLoading: false,
        };
    },

    created() {
        this.syncService = Service('syncService');
        this.httpClient = this.syncService.httpClient;
    },

    methods: {
        optimizeGtmScript() {
            this.isLoading = true;

            this.createNotificationInfo({
                title: 'Optimize Gtm Script',
                message: 'Optimizing Gtm Script'
            });

            this.httpClient.get('/_action/hatslogic/convert/gtm-script', {
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

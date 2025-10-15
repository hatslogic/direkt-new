import template from './hatslogic-critical-assets-generation-btn.html.twig';
import './hatslogic-critical-assets-generation-btn.css';

const { Component, Context, Utils, Service, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('hatslogic-critical-assets-generation-btn', {
    template,

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            buttonLabel: this.$tc('hatslogic-critical-assets.criticalCssGeneration.buttonLabel'),
            isLoading: false,
        };
    },

    created() {
        this.syncService = Service('syncService');
        this.httpClient = this.syncService.httpClient;
    },

    methods: {
        generateCriticalCSS() {
            this.isLoading = true;

            this.createNotificationInfo({
                title: this.$tc('hatslogic-critical-assets.criticalCssGeneration.startedTitle'),
                message: this.$tc('hatslogic-critical-assets.criticalCssGeneration.startedMessage')
            });

            this.httpClient.get('/_action/hatslogic/generate/critical-css', {
                headers: this.syncService.getBasicHeaders()
            }).then(response => {
                this.isLoading = false;

                if (response.data.success === true) {
                    this.createNotificationSuccess({
                        title: this.$tc('global.default.success'),
                        message: this.$tc('hatslogic-critical-assets.criticalCssGeneration.successMessage')
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

const { Component, Mixin } = Shopware;
import template from './api-test-button.html.twig';

Component.register('api-test-button', {
    template,

    props:['label'],
    inject:['shopvotePlugin'],

    mixins:[Mixin.getByName('notification')],

    data(){
        return{
            isLoading: false,
            isSaveSuccessful: false,
        };
    },

    computed:{
        pluginConfig() {
            return function(self) {
                var current = self;
                while (typeof current.$parent !== "undefined") {
                    current = current.$parent;
                    if (typeof current.actualConfigData !=="undefined") {
                        return current.actualConfigData.null;
                    }
                }
                return null;
            }(this);
        }
    },

    methods: {
        saveFinish() {
            this.isSaveSuccessful = false;
        },

        check() {
            this.isLoading = true;
            this.shopvotePlugin.check(this.pluginConfig).then((res) => {
                if (res.success) {
                    this.isSaveSuccessful = true;
                    this.createNotificationSuccess({
                        title: this.$tc('api-test-button.title'),
                        message: this.$tc('api-test-button.success')
                    });
                } else {
                    this.createNotificationError({
                        title: this.$tc('api-test-button.title'),
                        message: this.$tc('api-test-button.error')
                    });
                }

                this.isLoading = false;
            });
        }
    }
})

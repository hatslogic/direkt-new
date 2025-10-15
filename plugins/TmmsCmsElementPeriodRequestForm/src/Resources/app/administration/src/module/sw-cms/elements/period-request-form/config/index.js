import template from './sw-cms-el-config-period-request-form.html.twig';
import './sw-cms-el-config-period-request-form.scss';

const { Mixin } = Shopware;

/**
 * @private
 * @package buyers-experience
 */
export default {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('period-request-form');
        },

        onBlur(content) {
            this.emitChanges(content);
        },

        onInput(content) {
            this.emitChanges(content);
        },

        emitChanges(content) {
            if (content !== this.element.config.periodRequestFormText.value) {
                this.element.config.periodRequestFormText.value = content;
                this.$emit('element-update', this.element);
            }
        },
    },
};

import template from './tmms-periodrequestform-detail.html.twig';
import './tmms-periodrequestform-detail.scss';

const { Mixin } = Shopware;

export default {
    template,

    inject: ['repositoryFactory', 'acl'],

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('notification'),
    ],

    shortcuts: {
        'SYSTEMKEY+S': {
            active() {
                return this.acl.can('periodrequestform.editor');
            },
            method: 'onSave',
        },
        ESCAPE: 'onCancel',
    },

    props: {
        allowEdit: {
            type: Boolean,
            required: false,
            default: false,
        }
    },

    data() {
        return {
            isLoading: false,
            isSaveSuccessful: false,
            periodrequestform: null,
            repository: null,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier)
        };
    },

    computed: {
        identifier() {
            return this.placeholder(this.periodrequestform, 'date');
        },

        periodrequestformEntityRepository() {
            return this.repositoryFactory.create('periodrequestform');
        },

        tooltipSave() {
            if (!this.acl.can('periodrequestform.editor')) {
                return {
                    message: this.$tc('sw-privileges.tooltip.warning'),
                    disabled: true,
                    showOnDisabledElements: true,
                };
            }

            const systemKey = this.$device.getSystemKey();

            return {
                message: `${systemKey} + S`,
                appearance: 'light',
            };
        },

        tooltipCancel() {
            return {
                message: 'ESC',
                appearance: 'light',
            };
        },

        dateFilter() {
            return Shopware.Filter.getByName('date');
        },
    },

    created() {
        if (this.periodrequestformEntityRepository != null) {
            this.createdComponent();
        }
    },

    methods: {
        createdComponent() {
            if (this.periodrequestformEntityRepository != null) {
                this.getPeriodrequestform();
            }
        },

        getPeriodrequestform() {
            this.isLoading = true;

            if (this.periodrequestformEntityRepository != null) {
                this.periodrequestformEntityRepository
                    .get(this.$route.params.id, Shopware.Context.api)
                    .then((periodrequestform) => {
                        this.isLoading = false;
                        this.periodrequestform = periodrequestform;
                    });
            }
        },

        onSave() {
            this.isLoading = true;
            const messageSaveError = this.$tc(
                'periodrequestform.detail.errorTitle',
            );

            this.periodrequestformEntityRepository
            .save(this.periodrequestform, Shopware.Context.api)
            .then(() => {
                this.isLoading = false;
                this.isSaveSuccessful = true;
                this.getPeriodrequestform();
            }).catch(() => {
                this.isLoading = false;
                this.createNotificationError({
                    message: messageSaveError,
                });
            });
        },

        onSaveFinish() {
            this.isSaveSuccessful = false;
        },

        onCancel() {
            this.$router.push({ name: 'tmms.periodrequestform.list' });
        },
    }
};

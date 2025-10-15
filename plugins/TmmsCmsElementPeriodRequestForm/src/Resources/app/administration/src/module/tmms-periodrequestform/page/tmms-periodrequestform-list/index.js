import template from './tmms-periodrequestform-list.html.twig';

const { Mixin } = Shopware;
const { Criteria } = Shopware.Data;

export default {
    template,

    inject: ['repositoryFactory',  'acl'],

    mixins: [
        Mixin.getByName('listing'),
    ],

    data() {
        return {
            isLoading: false,
            periodrequestform: null,
            sortBy: 'createdAt',
            sortDirection: 'DESC',
            repository: null
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    computed: {
        periodrequestformColumns() {
            return this.getPeriodRequestFormColumns();
        },

        periodrequestformRepository() {
            return this.repositoryFactory.create('periodrequestform');
        },

        dateFilter() {
            return Shopware.Filter.getByName('date');
        },
    },

    methods: {
        getPeriodRequestFormColumns() {
            return [
                {
                    property: 'createdAt',
                    dataIndex: 'createdAt',
                    allowResize: true,
                    label: this.$t('periodrequestform.list.columnCreatedAt'),
                }, {
                    property: 'salutation',
                    dataIndex: 'salutation',
                    allowResize: true,
                    label: this.$t('periodrequestform.list.columnSalutation'),
                    routerLink: 'tmms.periodrequestform.detail',
                }, {
                    property: 'firstname',
                    dataIndex: 'firstname',
                    allowResize: true,
                    label: this.$t('periodrequestform.list.columnFirstname'),
                    routerLink: 'tmms.periodrequestform.detail',
                }, {
                    property: 'lastname',
                    dataIndex: 'lastname',
                    allowResize: true,
                    label: this.$t('periodrequestform.list.columnLastname'),
                    routerLink: 'tmms.periodrequestform.detail',
                }, {
                    property: 'email',
                    dataIndex: 'email',
                    allowResize: true,
                    label: this.$t('periodrequestform.list.columnEmail'),
                }, {
                    property: 'phone',
                    dataIndex: 'phone',
                    allowResize: true,
                    label: this.$t('periodrequestform.list.columnPhone'),
                }, {
                    property: 'date',
                    dataIndex: 'date',
                    allowResize: true,
                    label: this.$t('periodrequestform.list.columnDate'),
                    inlineEdit: 'string',
                }, {
                    property: 'originname',
                    dataIndex: 'originname',
                    allowResize: true,
                    label: this.$t('periodrequestform.list.columnOriginname'),
                }, {
                    property: 'comment',
                    dataIndex: 'comment',
                    allowResize: true,
                    label: this.$t('periodrequestform.list.columnComment'),
                }, {
                    property: 'confirmed',
                    dataIndex: 'confirmed',
                    allowResize: true,
                    label: this.$t('periodrequestform.list.columnConfirmed'),
                    inlineEdit: 'boolean',
                }, {
                    property: 'answered',
                    dataIndex: 'answered',
                    allowResize: true,
                    label: this.$t('periodrequestform.list.columnAnswered'),
                    inlineEdit: 'boolean',
                }
            ];
        },

        getList() {
            this.isLoading = true;

            const criteria = new Criteria(this.page, this.limit);

            criteria.setTerm(this.term);
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection));

            if (this.periodrequestformRepository !== null) {
                return this.periodrequestformRepository.search(criteria, Shopware.Context.api)
                    .then((searchResult) => {
                        this.periodrequestform = searchResult;
                        this.total = searchResult.total;
                        this.isLoading = false;

                        return this.periodrequestform;
                    });
            }
        },

        updateTotal({ total }) {
            this.total = total;
        },
    },
};

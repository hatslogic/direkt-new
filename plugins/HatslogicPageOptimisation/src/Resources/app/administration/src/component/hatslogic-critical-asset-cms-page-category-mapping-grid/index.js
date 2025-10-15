import template from './hatslogic-critical-asset-cms-page-category-mapping-grid.html.twig';
import './hatslogic-critical-asset-cms-page-category-mapping-grid.scss';

const { Component, Context, Utils } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('hatslogic-critical-asset-cms-page-category-mapping-grid', {
    template,

    inject: ['acl', 'repositoryFactory'],

    data() {
        return {
            isLoading: false,
            rows: [],
            selectedItems: {},
            editable: true
        };
    },

    props: {
        value: {
            type: String,
            required: false,
            default: ""
        }
    },

    created() {
        if (this.value) {
            const mapping = JSON.parse(this.value);
            Object.keys(mapping).forEach(key => {
                this.rows.push({
                    id: Utils.createId(),
                    cmsPageId: key,
                    categoryId: mapping[key],
                })
            })
        }

        if (this.rows.length <= 0) {
            this.onAddNewRow()
        }
    },

    watch: {
        rows: function(rows) {
            this.onInlineEditSave();
        }
    },

    computed: {
        cmsPageRepository() {
            return this.repositoryFactory.create('cms_page');
        },

        cmsPageCriteria() {
            const criteria = new Criteria;
            criteria.addAssociation('categories');

            return criteria;
        },

        categoryRepository() {
            return this.repositoryFactory.create('category');
        },

        context() {
            return Context.api;
        },

        columns() {
            return [{
                property: 'cmsPageId',
                dataIndex: 'cmsPageId',
                label: 'hatslogic-critical-assets.mappingGrid.cmsPageLabel',
                allowResize: true,
                primary: false,
                inlineEdit: true,
            }, {
                property: 'categoryId',
                dataIndex: 'categoryId',
                label: 'hatslogic-critical-assets.mappingGrid.categoryLabel',
                allowResize: true,
                primary: false,
                inlineEdit: true,
            }]
        }
    },

    methods: {
        categoryCriteria(cmsPageId) {
            const criteria = new Criteria;

            criteria.addFilter(Criteria.equals('cmsPageId', cmsPageId))

            return criteria;
        },

        onDeleteSelectedItems() {
            Object.values(this.selectedItems).forEach(itemToDelete => {
                this.rows = this.rows.filter(item => {
                    return item.id != itemToDelete.id;
                });
            });
        },

        onCmsPageSelected(item, value) {
            item.cmsPageId = value;
            this.onInlineEditSave();
        },

        onCategorySelected(item, value) {
            item.categoryId = value;
            this.onInlineEditSave();
        },

        onSelectionChanged(selection) {
            this.selectedItems = selection;
        },

        onInlineEditSave() {
            const rows = {};

            this.rows.filter(item => {
                if (!!item.cmsPageId && !!item.categoryId) {
                    rows[item.cmsPageId] = item.categoryId;
                }
            })

            this.$emit(
                'change',
                JSON.stringify(rows)
            )
        },

        onAddNewRow() {
            this.rows.push({
                id: Utils.createId(),
                cmsPageId: null,
                categoryId: null,
            })
        },
    }
});

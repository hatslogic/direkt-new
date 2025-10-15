const { Component } = Shopware;

Component.override('swag-cms-extensions-form-editor-settings-field-type-header', {
	computed: {
		types() {
			const typesSuper = this.$super('types');

			const uploadTypes = [
				{
					value: 'upload',
					label: this.$tc('swag-cms-extensions.sw-cms.components.form-editor.settings-field.types.upload'),
				},
				{
					value: 'upload-multiple',
					label: this.$tc('swag-cms-extensions.sw-cms.components.form-editor.settings-field.types.upload-multiple'),
				},
			];

			return [...typesSuper, ...uploadTypes];
		},
	},
});

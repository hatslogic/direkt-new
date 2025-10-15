export default {
    methods: {
        getPeriodrequestform() {
            this.periodrequestform = this.periodrequestformEntityRepository.create(Shopware.Context.api);
            this.periodrequestform.confirmed = false;
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
                this.$router.push({ name: 'tmms.periodrequestform.detail', params: { id: this.periodrequestform.id } });
            }).catch(() => {
                this.isLoading = false;
                this.createNotificationError({
                    message: messageSaveError,
                });
            });
        }
    }
};

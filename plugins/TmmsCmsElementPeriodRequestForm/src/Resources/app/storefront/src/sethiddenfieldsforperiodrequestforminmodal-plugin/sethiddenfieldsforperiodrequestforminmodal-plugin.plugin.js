import DomAccess from 'src/helper/dom-access.helper';

export default class SetHiddenFieldsForPeriodRequestFormInModalPlugin extends window.PluginBaseClass {
    init() {
        this.$emitter.publish('beforeSetHiddenFieldsForPeriodRequestFormInModalInit');

        this._registerEvents();

        this.$emitter.publish('afterSetHiddenFieldsForPeriodRequestFormInModalInit');
    }

    _registerEvents() {
        if (document.querySelector('.period-request-form-modal-btn')) {
            let periodRequestFormModalBtn = DomAccess.querySelector(document, '.period-request-form-modal-btn');
            const plugin = window.PluginManager.getPluginInstanceFromElement(periodRequestFormModalBtn, 'AjaxModal');

            plugin.$emitter.subscribe('ajaxModalOpen', () => {
                this.$emitter.publish('beforeSetHiddenFieldsForPeriodRequestFormInModalAjaxModalOpen');

                let formElementId = periodRequestFormModalBtn.dataset.formElementId,
                    formHiddenFieldsSelector = ((formElementId)? '.cms-element-period-request-form #period-request-form-card-body-' + formElementId : '.cms-element-period-request-form'),
                    formHiddenFields = DomAccess.querySelector(document, formHiddenFieldsSelector + ' .form-hidden-fields'),
                    inputOrigin = document.createElement('input'),
                    inputOriginId = document.createElement('input'),
                    inputOriginName = document.createElement('input');

                inputOrigin.setAttribute('type', 'hidden');
                inputOrigin.setAttribute('name', 'origin');
                inputOrigin.setAttribute('value', periodRequestFormModalBtn.dataset.origin);

                inputOriginId.setAttribute('type', 'hidden');
                inputOriginId.setAttribute('name', 'originId');
                inputOriginId.setAttribute('value', periodRequestFormModalBtn.dataset.originId);

                inputOriginName.setAttribute('type', 'hidden');
                inputOriginName.setAttribute('name', 'originName');
                inputOriginName.setAttribute('value', periodRequestFormModalBtn.dataset.originName);

                formHiddenFields.appendChild(inputOrigin);
                formHiddenFields.appendChild(inputOriginId);
                formHiddenFields.appendChild(inputOriginName);

                this.$emitter.publish('afterSetHiddenFieldsForPeriodRequestFormInModalAjaxModalOpen');
            });
        }
    }
}

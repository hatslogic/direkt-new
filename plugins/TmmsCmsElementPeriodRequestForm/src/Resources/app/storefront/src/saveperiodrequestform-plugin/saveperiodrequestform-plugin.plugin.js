function setMaxLengthAttribute(el) {
    let formElementId = el.getAttribute('data-form-element-id'),
        inputMaxLength = el.getAttribute('data-input-max-length'),
        textareaMaxLength = el.getAttribute('data-textarea-max-length'),
        formInputs = document.querySelectorAll("#period-request-form-" + formElementId + " input, #period-request-form-" + formElementId + " textarea");

    formInputs.forEach(function(input) {
        if (input.getAttribute('type') !== "hidden" && input.getAttribute('type') !== "checkbox" && input.getAttribute('type') !== "submit" && input.getAttribute('name') !== "shopware_surname_confirm" && input.getAttribute('name') !== "date" && input.getAttribute('name') !== "_grecaptcha_v3")  {
            if (input.tagName === "TEXTAREA") {
                input.setAttribute("maxlength", textareaMaxLength);
            } else {
                input.setAttribute("maxlength", inputMaxLength);
            }
        }
    });
}

export default class SavePeriodRequestFormPlugin extends window.PluginBaseClass {
    init() {
        this.$emitter.publish('beforeSavePeriodRequestFormInit');

        this._registerEvents();

        this.$emitter.publish('afterSavePeriodRequestFormInit');
    }

    _registerEvents() {
        setMaxLengthAttribute(this.el.closest('form'));
    }
}

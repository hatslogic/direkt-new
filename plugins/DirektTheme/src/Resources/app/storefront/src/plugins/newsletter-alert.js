import FormCmsHandler from 'src/plugin/forms/form-cms-handler.plugin.js';

export default class ExtendedFormCmsHandler extends FormCmsHandler {
    _createResponse(changeContent, content) {
        const customContainer = document.querySelector('.cms-element-form .col-lg-12');

        if (!customContainer) {
            console.error('Custom container (.cms-element-form) not found in the DOM. Check your template structure.');
            return;
        }

        if (changeContent) {
            if (this._confirmationText) {
                content = this._confirmationText;
            }
            customContainer.innerHTML = `<div class="confirm-message" role="alert" aria-live="assertive">${content}</div>`;
        } else {
            const confirmDiv = customContainer.querySelector('.confirm-alert');
            if (confirmDiv) {
                confirmDiv.remove();
            }
            const html = `<div class="confirm-alert" role="alert" aria-live="assertive">${content}</div>`;
            customContainer.insertAdjacentHTML('beforeend', html);
        }

        customContainer.scrollIntoView({
            behavior: 'smooth',
            block: 'end',
        });
    }
}

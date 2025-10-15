import DomAccessHelper from 'src/helper/dom-access.helper';

export default class AcrisEventHelper
{
    static findData(event) {
        const script = DomAccessHelper.querySelector(document, `script[data-acris-tag-manager-app-data="${event}"]`, false);
        if (!script) {
            return null;
        }

        const rawJson = script.innerText.trim()
        if (!rawJson || rawJson.length === 0) {
            return null;
        }

        const json = JSON.parse(rawJson);
        if (!json) {
            return null;
        }

        return json;
    }

}
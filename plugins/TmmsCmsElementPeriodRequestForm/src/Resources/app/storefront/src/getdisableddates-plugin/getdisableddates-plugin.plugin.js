import DomAccess from 'src/helper/dom-access.helper';
import HttpClient from 'src/service/http-client.service';

export default class GetDisabledDatesPlugin extends window.PluginBaseClass {
    static options = {
        dataurl: '/periodrequestform/getdisableddates',
        placeholder: '',
        origin: 'Kategorie',
        originid: 0,
        dateisrequired: 0,
        defaultdatevalue: '',
        mindatevalue: '',
        maxdatevalue: '',
        inputdisableddates: '',
        dateformat: 'd.m.Y',
        showweeksnumbers: 1,
        locale: 'de-DE',
        showperiodselection: 1,
        showcalendarpermanentlyopen: 0,
        showselectfieldformonth: 1,
        showtwomonthssidebyside: 0,
        formelementid: 0
    }

    init() {
        this.$emitter.publish('beforeGetDisabledDatesInit');

        this._registerEvents();

        this.$emitter.publish('afterGetDisabledDatesInit');
    }

    _registerEvents() {
        this._client = new HttpClient();

        this.fetch();

        this.$emitter.publish('afterGetDisabledDatesRegisterEvents');
    }

    /**
     * fetch the data
     */
    fetch() {
        this._client.get(`${this.options.dataurl}?placeholder=${this.options.placeholder}&origin=${this.options.origin}&originid=${this.options.originid}&dateisrequired=${this.options.dateisrequired}&defaultdatevalue=${this.options.defaultdatevalue}&mindatevalue=${this.options.mindatevalue}&maxdatevalue=${this.options.maxdatevalue}&inputdisableddates=${this.options.inputdisableddates}&dateformat=${this.options.dateformat}&showweeksnumbers=${this.options.showweeksnumbers}&locale=${this.options.locale}&showperiodselection=${this.options.showperiodselection}&showcalendarpermanentlyopen=${this.options.showcalendarpermanentlyopen}&showselectfieldformonth=${this.options.showselectfieldformonth}&showtwomonthssidebyside=${this.options.showtwomonthssidebyside}&formelementid=${this.options.formelementid}`, (response) => {
            let dateField = DomAccess.querySelector(document, '#period-request-form-' + this.options.formelementid + ' input[name="date"]');
            dateField.setAttribute('data-date-picker-periodrequestform', true);
            dateField.setAttribute('data-date-picker-options', response.trim().replace(/&quot;/g, '\"'));

            window.PluginManager.register('DatePicker', () => import('src/plugin/date-picker/date-picker.plugin'), '[data-date-picker-periodrequestform]');
            window.PluginManager.initializePlugin('DatePicker', '[data-date-picker-periodrequestform]');

            this.$emitter.publish('afterGetDisabledDatesFetch');
        });
    }

    /**
     * removes the element
     */
    destroy() {
        this.el.lastChild.remove();
    }
}

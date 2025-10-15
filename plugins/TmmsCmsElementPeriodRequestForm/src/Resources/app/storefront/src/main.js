import SavePeriodRequestFormPlugin from './saveperiodrequestform-plugin/saveperiodrequestform-plugin.plugin';
import GetDisabledDatesPlugin from './getdisableddates-plugin/getdisableddates-plugin.plugin';
import SetHiddenFieldsForPeriodRequestFormInModalPlugin from './sethiddenfieldsforperiodrequestforminmodal-plugin/sethiddenfieldsforperiodrequestforminmodal-plugin.plugin';

const PluginManager = window.PluginManager;

PluginManager.register('SavePeriodRequestFormPlugin', SavePeriodRequestFormPlugin, '[data-save-period-request-form="true"]');
PluginManager.register('GetDisabledDates', GetDisabledDatesPlugin, '[data-get-disabled-dates="true"]');
PluginManager.register('SetHiddenFieldsForPeriodRequestFormInModalPlugin', SetHiddenFieldsForPeriodRequestFormInModalPlugin, '.period-request-form-modal-btn');

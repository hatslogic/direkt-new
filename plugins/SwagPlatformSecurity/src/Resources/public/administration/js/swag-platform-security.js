(()=>{var a=Shopware.Classes.ApiService,n=class extends a{constructor(e,t,i="swag-security"){super(e,t,i)}getFixes(){let e=this.getBasicHeaders({});return this.httpClient.get(`_action/${this.getApiBasePath()}/available-fixes`,{headers:e}).then(t=>a.handleResponse(t))}saveValues(e,t){let i=this.getBasicHeaders({});return this.httpClient.post(`_action/${this.getApiBasePath()}/save-config`,{config:e,currentPassword:t},{headers:i}).then(u=>a.handleResponse(u))}cacheClear(){let e=this.getBasicHeaders({});return this.httpClient.delete(`_action/${this.getApiBasePath()}/clear-container-cache`,{headers:e})}},c=n;var r=class{isActive(e){return Shopware.State.get("context").app.config.swagSecurity.includes(e)}},l=r;var{Application:o}=Shopware;o.addServiceProvider("swagSecurityApi",s=>{let e=o.getContainer("init");return new c(e.httpClient,s.loginService)});o.addServiceProvider("swagSecurityState",()=>new l);var d=`{% block sw_settings_security_index %}
    <sw-page class="sw-settings-security">

        {% block sw_settings_security_search_bar %}
            <template #search-bar>
                <sw-search-bar>
                </sw-search-bar>
            </template>
        {% endblock %}

        {% block sw_settings_security_smart_bar_header %}
            <template #smart-bar-header>
                {% block sw_settings_security_smart_bar_header_title %}
                    <h2>
                        {% block sw_settings_security_smart_bar_header_title_text %}
                            {{ $tc('sw-settings.index.title') }}
                            <sw-icon name="regular-chevron-right-xs" small>
                            </sw-icon>
                            {{ $tc('sw-settings-security.general.textHeadline') }}
                        {% endblock %}
                    </h2>
                {% endblock %}
            </template>
        {% endblock %}

        {% block sw_settings_security_smart_bar_actions %}
            <template #smart-bar-actions>
                {% block sw_settings_security_actions_save %}
                    <sw-button-process
                        v-if="fixes.availableFixes && fixes.availableFixes.length"
                        class="sw-settings-security__save-action"
                        :isLoading="isLoading"
                        :processSuccess="isSaveSuccessful"
                        :disabled="isLoading"
                        variant="primary"
                        @process-finish="saveFinish"
                        @click="onSave">
                        {{ $tc('sw-settings-security.general.buttonSave') }}
                    </sw-button-process>
                {% endblock %}
            </template>
        {% endblock %}

        {% block sw_settings_security_content %}
            <template #content>
                <sw-card-view>
                    <sw-card :title="$tc('sw-settings-security.general.cardTitle')" :isLoading="isLoading" v-if="isLoading || fixes.availableFixes.length">
                        <sw-alert variant="warning">{{ $tc('sw-settings-security.general.alert') }}</sw-alert>
                        <div v-for="fix in fixes.availableFixes" v-if="fixes">
                            <sw-checkbox-field
                                :name="fix"
                                :label="$tc('sw-settings-security.fixes.' + fix + '.label')"
                                v-model:value="config[fix]"
                                :helpText="$tc('sw-settings-security.fixes.' + fix + '.tooltip')"
                            />
                        </div>
                    </sw-card>

                    <sw-empty-state v-else
                                    :title="$tc('sw-settings-security.general.noFixes')">
                    </sw-empty-state>
                </sw-card-view>

                <sw-modal v-if="confirmPasswordModal"
                          @modal-close="onCloseConfirmPasswordModal"
                          :title="$tc('sw-settings-security.modal.placeholderConfirmWithPassword')"
                          variant="small">
                    <sw-password-field
                        class="sw-settings-user-detail__confirm-password"
                        v-model:value="confirmPassword"
                        required
                        name="sw-field--confirm-password"
                        :passwordToggleAble="true"
                        :copyAble="false"
                        :label="$tc('sw-settings-security.modal.labelConfirmWithPassword')"
                        :placeholder="$tc('sw-settings-security.modal.enterPassword')">
                    </sw-password-field>

                    <template #modal-footer>
                        <sw-button @click="onCloseConfirmPasswordModal"
                                   size="small">
                            {{ $tc('sw-settings-security.modal.labelButtonCancel') }}
                        </sw-button>
                        <sw-button @click="onVerifiedSave"
                                   variant="primary"
                                   :disabled="!confirmPassword"
                                   size="small">
                            {{ $tc('sw-settings-security.modal.labelButtonConfirm') }}
                        </sw-button>
                    </template>
                </sw-modal>
            </template>
        {% endblock %}
    </sw-page>
{% endblock %}
`;var{Component:p,Mixin:h}=Shopware;p.register("sw-settings-security-view",{template:d,inject:["swagSecurityApi","systemConfigApiService"],data(){return{isLoading:!0,isSaveSuccessful:!1,confirmPasswordModal:!1,confirmPassword:"",config:{},fixes:[]}},mixins:[h.getByName("notification")],methods:{onCloseConfirmPasswordModal(){this.confirmPasswordModal=!1,this.isLoading=!1,this.confirmPassword=""},onSave(){this.confirmPasswordModal=!0},onVerifiedSave(){this.isLoading=!0,this.swagSecurityApi.saveValues(this.config,this.confirmPassword).then(()=>{this.isLoading=!0,this.confirmPasswordModal=!1,this.confirmPassword="",this.swagSecurityApi.cacheClear().then(()=>{this.isLoading=!1,this.isSaveSuccessful=!0,window.location.reload()})}).catch(()=>{this.createNotificationError({title:this.$tc("sw-settings-security.notification.passwordErrorTitle"),message:this.$tc("sw-settings-security.notification.passwordErrorMessage")})})},saveFinish(){this.isSaveSuccessful=!1}},async mounted(){this.fixes=await this.swagSecurityApi.getFixes();for(let s of this.fixes.availableFixes)this.config[s]=this.fixes.activeFixes.includes(s);this.isLoading=!1}});var g=`{% block sw_settings_content_card_slot_plugins %}
    {% parent %}

    {% block sw_settings_swag_security %}
        <sw-settings-item
            v-if="canViewSecuritySettings"
            :label="$tc('sw-settings-security.general.mainMenuItemGeneral')"
            :to="{ name: 'sw.settings.security.index' }">
            <template slot="icon">
                <sw-icon name="regular-shield"></sw-icon>
            </template>
        </sw-settings-item>
    {% endblock %}
{% endblock %}
`;Shopware.Component.override("sw-settings-index",{template:g,computed:{canViewSecuritySettings(){let s=Shopware.Service("acl");return s?s.can("admin"):!0}}});var{Module:v}=Shopware,w={type:"plugin",name:"settings-security",title:"sw-settings-security.general.mainMenuItemGeneral",description:"sw-settings-security.general.description",version:"1.0.0",targetVersion:"1.0.0",color:"#9AA8B5",icon:"regular-cog",favicon:"icon-module-settings.png",routes:{index:{component:"sw-settings-security-view",path:"index",meta:{parentPath:"sw.settings.index",privilege:"admin"}}},settingsItem:[{group:"plugins",to:"sw.settings.security.index",icon:"regular-shield",name:"sw-settings-security.general.mainMenuItemGeneral"}]};Shopware.Component.getComponentRegistry().has("sw-extension-config")||delete w.settingsItem;v.register("sw-settings-security",w);})();

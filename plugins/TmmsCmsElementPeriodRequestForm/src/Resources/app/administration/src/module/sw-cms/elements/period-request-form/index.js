/**
 * @private
 * @package buyers-experience
 */
Shopware.Component.register('sw-cms-el-preview-period-request-form', () => import('./preview'));
/**
 * @private
 * @package buyers-experience
 */
Shopware.Component.register('sw-cms-el-config-period-request-form', () => import('./config'));
/**
 * @private
 * @package buyers-experience
 */
Shopware.Component.register('sw-cms-el-period-request-form', () => import('./component'));

/**
 * @private
 * @package buyers-experience
 */
Shopware.Service('cmsService').registerCmsElement({
    name: 'period-request-form',
    label: 'sw-cms.elements.periodRequestFormElement.label',
    component: 'sw-cms-el-period-request-form',
    configComponent: 'sw-cms-el-config-period-request-form',
    previewComponent: 'sw-cms-el-preview-period-request-form',
    defaultConfig: {
        periodRequestFormTitle: {
            source: 'static',
            value: 'Anfrageformular'
        },
        periodRequestFormText: {
            source: 'static',
            value: ''
        },
        showSalutation: {
            source: 'static',
            value: true
        },
        salutationIsRequired: {
            source: 'static',
            value: true
        },
        salutation: {
            source: 'static',
            value: 'Herr,Frau,Keine Angabe'
        },
        showFirstname: {
            source: 'static',
            value: true
        },
        firstnameIsRequired: {
            source: 'static',
            value: true
        },
        showLastname: {
            source: 'static',
            value: true
        },
        lastnameIsRequired: {
            source: 'static',
            value: true
        },
        showStreet: {
            source: 'static',
            value: true
        },
        streetIsRequired: {
            source: 'static',
            value: false
        },
        showZipcode: {
            source: 'static',
            value: true
        },
        zipcodeIsRequired: {
            source: 'static',
            value: false
        },
        showCity: {
            source: 'static',
            value: true
        },
        cityIsRequired: {
            source: 'static',
            value: false
        },
        showCountry: {
            source: 'static',
            value: true
        },
        countryIsRequired: {
            source: 'static',
            value: false
        },
        country: {
            source: 'static',
            value: 'Deutschland,Österreich,Schweiz'
        },
        showEmail: {
            source: 'static',
            value: true
        },
        emailIsRequired: {
            source: 'static',
            value: true
        },
        showPhone: {
            source: 'static',
            value: true
        },
        phoneIsRequired: {
            source: 'static',
            value: false
        },
        showComment: {
            source: 'static',
            value: true
        },
        commentIsRequired: {
            source: 'static',
            value: false
        },
        showCommentAboveForm: {
            source: 'static',
            value: true
        },
        showDate: {
            source: 'static',
            value: false
        },
        dateIsRequired: {
            source: 'static',
            value: false
        },
        showDateAboveForm: {
            source: 'static',
            value: true
        },
        showPeriodSelection: {
            source: 'static',
            value: true
        },
        showSelectFieldForMonth: {
            source: 'static',
            value: false
        },
        showCalendarPermanentlyOpen: {
            source: 'static',
            value: false
        },
        alignCalendarCentrally: {
            source: 'static',
            value: false
        },
        showTwoMonthsSideBySide: {
            source: 'static',
            value: false
        },
        labelDate: {
            source: 'static',
            value: 'Zeitraum'
        },
        startdate: {
            source: 'static',
            value: '+1 day'
        },
        enddate: {
            source: 'static',
            value: ''
        },
        disableddates: {
            source: 'static',
            value: ''
        },
        determineAvailableDatesFromRequests: {
            source: 'static',
            value: true
        },
        showPrivacyNotice: {
            source: 'static',
            value: true
        },
        privacyNoticeIsRequired: {
            source: 'static',
            value: true
        },
        privacyNoticeWithoutTermsOfService: {
            source: 'static',
            value: false
        },
        useOwnSnippetForPrivacyNoticeText: {
            source: 'static',
            value: false
        },
        showRequiredFieldsInfo: {
            source: 'static',
            value: true
        },
        submitButton: {
            source: 'static',
            value: 'Absenden'
        },
        setOriginOfRequestManually: {
            source: 'static',
            value: false
        },
        originValue: {
            source: 'static',
            value: ''
        },
        originNameValue: {
            source: 'static',
            value: ''
        },
        originIdValue: {
            source: 'static',
            value: ''
        },
        showFreeInputAboveForm: {
            source: 'static',
            value: false
        },
        freeInputsSideBySide: {
            source: 'static',
            value: '1',
        },
        defaultSelectPlaceholder: {
            source: 'static',
            value: ''
        },
        showFreeInput: {
            source: 'static',
            value: false
        },
        freeInputIsRequired: {
            source: 'static',
            value: false
        },
        labelFreeInput: {
            source: 'static',
            value: 'Anzahl Erwachsene (ab 13 Jahren)'
        },
        freeInputType: {
            source: 'static',
            value: 'select',
        },
        freeInput: {
            source: 'static',
            value: '1,2,3,4'
        },
        showFreeInput2: {
            source: 'static',
            value: false
        },
        freeInput2IsRequired: {
            source: 'static',
            value: false
        },
        labelFreeInput2: {
            source: 'static',
            value: 'Anzahl Kinder (2 bis 12 Jahre)'
        },
        freeInput2Type: {
            source: 'static',
            value: 'select',
        },
        freeInput2: {
            source: 'static',
            value: '0,1,2,3,4'
        },
        showFreeInput3: {
            source: 'static',
            value: false
        },
        freeInput3IsRequired: {
            source: 'static',
            value: false
        },
        labelFreeInput3: {
            source: 'static',
            value: 'Anzahl Kleinkinder (unter 2 Jahre)'
        },
        freeInput3Type: {
            source: 'static',
            value: 'select',
        },
        freeInput3: {
            source: 'static',
            value: '0,1,2,3,4'
        },
        showFreeInput4: {
            source: 'static',
            value: false
        },
        freeInput4IsRequired: {
            source: 'static',
            value: false
        },
        labelFreeInput4: {
            source: 'static',
            value: 'Anzahl Haustiere'
        },
        freeInput4Type: {
            source: 'static',
            value: 'select',
        },
        freeInput4: {
            source: 'static',
            value: '0,1,2,3,4'
        },
        showFreeInput5: {
            source: 'static',
            value: false
        },
        freeInput5IsRequired: {
            source: 'static',
            value: false
        },
        labelFreeInput5: {
            source: 'static',
            value: 'Anzahl der Schlafzimmer'
        },
        freeInput5Type: {
            source: 'static',
            value: 'select',
        },
        freeInput5: {
            source: 'static',
            value: '1,2,3,4'
        },
        showFreeInput6: {
            source: 'static',
            value: false
        },
        freeInput6IsRequired: {
            source: 'static',
            value: false
        },
        labelFreeInput6: {
            source: 'static',
            value: 'Anzahl der Betten'
        },
        freeInput6Type: {
            source: 'static',
            value: 'select',
        },
        freeInput6: {
            source: 'static',
            value: '1,2,3,4'
        },
        showFreeInput7: {
            source: 'static',
            value: false
        },
        freeInput7IsRequired: {
            source: 'static',
            value: false
        },
        labelFreeInput7: {
            source: 'static',
            value: 'Anzahl der Badezimmer'
        },
        freeInput7Type: {
            source: 'static',
            value: 'select',
        },
        freeInput7: {
            source: 'static',
            value: '1,2,3,4'
        },
        showFreeInput8: {
            source: 'static',
            value: false
        },
        freeInput8IsRequired: {
            source: 'static',
            value: false
        },
        labelFreeInput8: {
            source: 'static',
            value: 'Unterkunftsstandard'
        },
        freeInput8Type: {
            source: 'static',
            value: 'select',
        },
        freeInput8: {
            source: 'static',
            value: '1,2,3,4,5'
        },
        showFreeInput9: {
            source: 'static',
            value: false
        },
        freeInput9IsRequired: {
            source: 'static',
            value: false
        },
        labelFreeInput9: {
            source: 'static',
            value: 'Unterkunftsart'
        },
        freeInput9Type: {
            source: 'static',
            value: 'select',
        },
        freeInput9: {
            source: 'static',
            value: 'Hotel,Ferienunterkunft,Apartment,Privates Ferienhaus,Pension,Residenz'
        },
        showFreeInput10: {
            source: 'static',
            value: false
        },
        freeInput10IsRequired: {
            source: 'static',
            value: false
        },
        labelFreeInput10: {
            source: 'static',
            value: 'Verpflegung'
        },
        freeInput10Type: {
            source: 'static',
            value: 'select',
        },
        freeInput10: {
            source: 'static',
            value: 'Frühstück inbegriffen,Mittag inbegriffen,Abendessen inbegriffen,All-Inclusive'
        },
        periodRequestFormSendMail: {
            source: 'static',
            value: true
        },
        periodRequestFormMailReceiverName: {
            source: 'static',
            value: 'Max Mustermann'
        },
        periodRequestFormMailReceiver: {
            source: 'static',
            value: 'max@mustermann.de'
        },
        periodRequestFormSendMailToRequester: {
            source: 'static',
            value: false
        },
        periodRequestFormSaveData: {
            source: 'static',
            value: true
        },
    }
});

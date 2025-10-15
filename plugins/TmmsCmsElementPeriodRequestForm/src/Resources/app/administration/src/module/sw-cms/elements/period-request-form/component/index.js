import template from './sw-cms-el-period-request-form.html.twig';
import './sw-cms-el-period-request-form.scss';

const { Mixin } = Shopware;

/**
 * @private
 * @package buyers-experience
 */
export default {
    template,

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    computed: {
        formGroupAdditionalClassThreeColumnsRow1() {
            if (this.element.config.showSalutation.value === true && this.element.config.showFirstname.value === true && this.element.config.showLastname.value === true) {
                return `three-items`;
            } else if ((this.element.config.showSalutation.value === true && this.element.config.showFirstname.value === true && this.element.config.showLastname.value === false) || (this.element.config.showSalutation.value === true && this.element.config.showFirstname.value === false && this.element.config.showLastname.value === true) || (this.element.config.showSalutation.value === false && this.element.config.showFirstname.value === true && this.element.config.showLastname.value === true)) {
                return `two-items`;
            }
        },
        formGroupAdditionalClassThreeColumnsRow2() {
            if (this.element.config.showStreet.value === true && this.element.config.showZipcode.value === true && this.element.config.showCity.value === true) {
                return `three-items`;
            } else if ((this.element.config.showStreet.value === true && this.element.config.showZipcode.value === true && this.element.config.showCity.value === false) || (this.element.config.showStreet.value === true && this.element.config.showZipcode.value === false && this.element.config.showCity.value === true) || (this.element.config.showStreet.value === false && this.element.config.showZipcode.value === true && this.element.config.showCity.value === true)) {
                return `two-items`;
            }
        },
        formGroupAdditionalClassThreeColumnsRow3() {
            if (this.element.config.freeInputsSideBySide.value == 3) {
                return `three-items`;
            } else if (this.element.config.freeInputsSideBySide.value == 2) {
                return `two-items`;
            }
        },
        formGroupAdditionalClassTwoColumns() {
            if (this.element.config.showEmail.value === true && this.element.config.showPhone.value === true) {
                return `two-items`;
            }
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('period-request-form');
        },
        requiredFieldClass: function(i) {
            if (i === 1) {
                if (this.element.config.salutationIsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 2) {
                if (this.element.config.firstnameIsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 3) {
                if (this.element.config.lastnameIsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 4) {
                if (this.element.config.streetIsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 5) {
                if (this.element.config.zipcodeIsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 6) {
                if (this.element.config.cityIsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 7) {
                if (this.element.config.countryIsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 8) {
                if (this.element.config.emailIsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 9) {
                if (this.element.config.phoneIsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 10) {
                if (this.element.config.commentIsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 11) {
                if (this.element.config.dateIsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 12) {
                if (this.element.config.freeInputIsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 13) {
                if (this.element.config.freeInput2IsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 14) {
                if (this.element.config.freeInput3IsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 15) {
                if (this.element.config.freeInput4IsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 16) {
                if (this.element.config.freeInput5IsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 17) {
                if (this.element.config.freeInput6IsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 18) {
                if (this.element.config.freeInput7IsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 19) {
                if (this.element.config.freeInput8IsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 20) {
                if (this.element.config.freeInput9IsRequired.value === true) {
                    return `is-required`;
                }
            } else if (i === 21) {
                if (this.element.config.freeInput10IsRequired.value === true) {
                    return `is-required`;
                }
            }
        }
    }
};

// @ts-check

/**
 * Handles validation for Contact Form 7 fields in the admin area
 * @param {Object} $ - jQuery instance
 * @constructor
 */
export function Wpcf7_admin_validations($){

    this.rules = {
    	rules: {
    	}
    };

    /**
     * Initializes validation by adding custom methods and applying validation to the form
     */
    this.init = function(){
        this.addMethods();

        $('#wpcf7-contact-form-editor form').validate({

            rules: this.rules,

            onfocusout: function(element) {

                this.element(element);

            }

        });

    };

    /**
     * Registers all custom validation methods
     */
    this.addMethods = function(){

        this.nospaces();

        this.englishAndNumbersOnly();

    };

    /**
     * Adds validation method to allow only English characters and numbers
     * @return {void}
     */
    this.englishAndNumbersOnly = function(){

        $.validator.addMethod("validateenglishnumbers", function(value, element) {

            return this.optional(element) || /^[a-z0-9_\-," "]+$/i.test(value);

        }, "English and numbers only");

        $.validator.addClassRules("validateenglishnumbers", {

            validateenglishnumbers: true

        });

    };

    /**
     * Adds validation method to disallow spaces in input fields
     * @return {void}
     */
    this.nospaces = function(){

        $.validator.addMethod("validatenospace", function(value, element) {

            return value.indexOf(" ") < 0 && value != "";

        }, "No spaces please");

        $.validator.addClassRules("validatenospace", {

            validatenospace: true

        });

    };

    this.init();
}
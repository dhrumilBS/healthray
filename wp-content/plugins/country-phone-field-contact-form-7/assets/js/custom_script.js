(function($) {
	$(function() {
		
         $(".wpcf7-countrytext").countrySelect({
			//defaultCountry: "jp",
			//onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
			//preferredCountries: ['ca', 'gb', 'us']
		});
		
		
		$(".wpcf7-phonetext").intlTelInput({
              // allowDropdown: false,
              // autoHideDialCode: false,
               autoPlaceholder: "off",
              // dropdownContainer: "body",
              // excludeCountries: ["us"],
              // formatOnDisplay: false,
              // geoIpLookup: function(callback) {
              //   $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
              //     var countryCode = (resp && resp.country) ? resp.country : "";
              //     callback(countryCode);
              //   });
              // },
               hiddenInput: "full_number",
              // initialCountry: "auto",
              // localizedCountries: { 'de': 'Deutschland' },
               nationalMode: false,
              // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
              // placeholderNumberType: "MOBILE",
              // preferredCountries: ['cn', 'jp'],
               //separateDialCode: true,
              //utilsScript: "nb_intl/js/utils.js"
            
        });
	
 });
})(jQuery);
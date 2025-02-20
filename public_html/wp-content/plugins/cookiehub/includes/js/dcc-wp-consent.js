'use strict';
(window => {
    window.wp_consent_type='optin';
    window.addEventListener('load', dcc_allow_functional_cookies, false);

    function dcc_allow_functional_cookies() {
        //functional cookies always allowed
        wp_set_consent('functional', 'allow'); 
    }
})(window);

var wpConsentProxy = function(category, action) {
	var categories = ['preferences', 'analytics', 'marketing'];
	if (typeof wp_set_consent === 'function' && categories.indexOf(category) != -1) {
		if (category == 'analytics') {
			wp_set_consent('statistics', action);
			wp_set_consent('statistics-anonymous', action);
		} else {
			wp_set_consent(category, action);
		}
	}
}

jQuery(function($){
    var $buttons = jQuery('.dcchub-tab-buttons > li');
    var $contents = jQuery('.dcchub-tabs-content > li');
    
    $buttons.click(function(){
        $buttons.removeClass('active');
        $contents.removeClass('active');

        jQuery(this).addClass('active');
        var _target = jQuery(this).data('tab');
        jQuery(_target).addClass('active');

        return false;
    });


    $('#domain_code_form_submit').on('click', function() {
        var code = $('input[name=dcchub_domain_code]').val();
        var nonce = $('input[name=register_domain_code_nonce]').val();
        
        blockUI();
        
        // Spinner
        var buttonId = 'domain_code_form_submit'
        var text = document.getElementById(buttonId).innerHTML;
        document.getElementById(buttonId).innerHTML = '';
        $(this).addClass('dcchub-spinner');

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'register_domain_code',
                dcchub_domain_code: code,
                register_domain_code_nonce: nonce
            },
            success: function(response) {
                // Remove Spinner
                $('#domain_code_form_submit').removeClass('dcchub-spinner');
                document.getElementById(buttonId).innerHTML = text;

                if (response["success"] != null && response["success"]) {
                    window.location.href = "?page=my-setting-admin&success=2";
                }
                else {
                    showError(response["data"]["errorMessage"] ?? "Unknown error occurred.");
                }
            },
            error: (function(data)
            {
                // Remove Spinner
                $('#domain_code_form_submit').removeClass('dcchub-spinner');
                document.getElementById(buttonId).innerHTML = text;

                showError("Unknown error occurred.");
            }),
        });
    });

    $('#register_account_form_submit').on('click', function() {
        var email = $('input[name=dcchub_email]').val();
        var domain = $('input[name=dcchub_domain]').val();
        var nonce = $('input[name=register_account_nonce]').val();
        
        blockUI();
        
        // Spinner
        var buttonId = 'register_account_form_submit'
        var text = document.getElementById(buttonId).innerHTML;
        document.getElementById(buttonId).innerHTML = '';
        $(this).addClass('dcchub-spinner');

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'register_account',
                dcchub_email: email,
                dcchub_domain: domain,
                register_account_nonce: nonce
            },
            success: function(response) {
                // Remove Spinner
                $('#register_account_form_submit').removeClass('dcchub-spinner');
                document.getElementById(buttonId).innerHTML = text;

                if (response["success"] != null && response["success"]) {
                    window.location.href = "?page=my-setting-admin&success=1";
                }
                else {
                    showError(response["data"]["errorMessage"] ?? "Unknown error occurred.");
                }
            },
            error: (function(data)
            {
                // Remove Spinner
                $('#register_account_form_submit').removeClass('dcchub-spinner');
                document.getElementById(buttonId).innerHTML = text;

                showError("Unknown error occurred.");
            }),
        });
    });

    window.onload = function() {
        var optionalParamValue = getQueryParam('success');
        if (optionalParamValue === "1") {
            setTimeout(function()
            {
                showSuccess("CookieHub will now crawl the first page of your website to identify cookies in use and the cookie notice will automatically shown up on your website once finished. You can log in to the CookieHub dashboard to adjust the content and design of the cookie notice."
                , 8000);
            }, 500);
        } 
        else if (optionalParamValue === "2") {
            setTimeout(function()
            {
                showSuccess("Domain code registered!");
            }, 500);
        } 
    };

    $('#dcchub_general_settings_form_submit').on('click', function() {
        var code = $('input[name=dcchub_domain_code]').val();
        var nonce = $('input[name=update_domain_code_nonce]').val();

        blockUI();
        // Spinner
        var buttonId = 'dcchub_general_settings_form_submit'
        var text = document.getElementById(buttonId).innerHTML;
        document.getElementById(buttonId).innerHTML = '';
        $(this).addClass('dcchub-spinner');
        
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'update_domain_code',
                dcchub_domain_code: code,
                update_domain_code_nonce: nonce
            },
            success: function(response) {
                // Remove Spinner
                $('#dcchub_general_settings_form_submit').removeClass('dcchub-spinner');
                document.getElementById(buttonId).innerHTML = text;

                if (response["success"] != null && response["success"]) {
                    var data = response["data"];
                    if (data != null && data["starter"] != null) {
                        document.getElementById('dcchub-product-type').innerText = data["starter"] ? 'Starter (FREE)' : 'Paid';
                        document.getElementById('dcchub-product-text').innerText = data["starter"] ? 'Upgrade to a paid plan for more monthly session and more features.' : 'You are currently subscribed to CookieHub with a paid plan.';
                        document.getElementById('dcchub-promotion-banner').style.display = data["starter"] ? null : 'none';
                    }
                    
                    showSuccess("Domain Code updated successfully");
                }
                else {
                    showError(response["data"]["errorMessage"] ?? "Unknown error occurred.");
                }
            },
            error: (function(data)
            {
                // Remove Spinner
                $('#dcchub_general_settings_form_submit').removeClass('dcchub-spinner');
                document.getElementById(buttonId).innerHTML = text;

                showError("Unknown error occurred.");
            }),
        });
    });

    $('#dcchub_advanced_settings_form_submit').on('click', function() {
        
        var form = document.getElementById("dcchub-advanced-settings-form");
        var formData = new FormData(form);

        // Nonce is in the form so we don't need to fetch it specificly
        var data = {
            action: 'update_advanced_settings'
        };
        formData.forEach(function(value, key) {
            data[key] = value;
        });

        blockUI();
        
        // Spinner
        var buttonId = 'dcchub_advanced_settings_form_submit'
        var text = document.getElementById(buttonId).innerHTML;
        document.getElementById(buttonId).innerHTML = '';
        $(this).addClass('dcchub-spinner');

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: data,
            success: function(response) {
                // Remove Spinner
                $('#dcchub_advanced_settings_form_submit').removeClass('dcchub-spinner');
                document.getElementById(buttonId).innerHTML = text;

                if (response["success"] != null && response["success"]) {
                    showSuccess("Settings saved");
                }
                else {
                    showError(response["data"]["errorMessage"] ?? "Unknown error occurred.");
                }
            },
            error: (function(data)
            {
                // Remove Spinner
                $('#dcchub_advanced_settings_form_submit').removeClass('dcchub-spinner');
                document.getElementById(buttonId).innerHTML = text;

                showSuccess("Settings saved");
            }),
        });
    });
    
    /* functions */

    function blockUI() {
        var inputElements = document.querySelectorAll('input, button');
        inputElements.forEach(function(element) {
            element.disabled = true;
        });
    }

    function enableUI() {
        var inputElements = document.querySelectorAll('input, button');
        inputElements.forEach(function(element) {
            element.disabled = false;
        });
    }

    function showSuccess(message, displayTime = 2500)
    {
        enableUI();

        window.scrollTo({ top: 0, behavior: 'smooth' });
        $('.dcchub-success-message').remove();
        $('.dcchub-error-message').remove();
        var m = $('<div />').addClass('dcchub-success-message');
        m.append($('<p />').addClass('dcchub-message-text').html(message));
    
        $('#message-container').append(m);
        $('#message-container').fadeIn();
            
        setTimeout(function()
        {
            $('#message-container').fadeOut();
        }, displayTime);
    }
    
    function showError(message, displayTime = 3500)
    {
        enableUI();

        window.scrollTo({ top: 0, behavior: 'smooth' });
        $('.dcchub-error-message').remove();
        $('.dcchub-success-message').remove();
        
        var m = $('<div />').addClass('dcchub-error-message');
        m.append($('<p />').addClass('dcchub-message-text').html(message));
    
        $('#message-container').append(m);
        $('#message-container').fadeIn();
            
        setTimeout(function()
        {
            $('#message-container').fadeOut();
        }, displayTime);
    }

    function getQueryParam(parameterName) {
        var queryString = window.location.search.substring(1);
        var queryParams = new URLSearchParams(queryString);
    
        // Check if the parameter exists in the query string
        if (queryParams.has(parameterName)) {
            var parameterValue = queryParams.get(parameterName);
            
            // Update the URL without the parameter
            queryParams.delete(parameterName);
            var newUrl = window.location.href.split('?')[0] + '?' + queryParams.toString();
            
            // Replace the current URL with the updated URL
            window.history.replaceState({}, document.title, newUrl);

            // Return the value of the parameter
            return parameterValue;
        } else {
            // Return a default value or null if the parameter is not present
            return null;
        }
    }
});


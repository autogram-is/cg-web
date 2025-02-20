<?php
add_action('wp_ajax_update_domain_code', 'update_domain_code');
function update_domain_code() {
    $nonce = isset($_POST['update_domain_code_nonce']) ? $_POST['update_domain_code_nonce'] : '';

    if (!wp_verify_nonce($nonce, 'update_domain_code_nonce')) {
        return wp_send_json_error(["success" => false, "errorMessage" => "Nonce verification failed."]);
    }

    $domainCode = isset( $_POST['dcchub_domain_code'] ) ? sanitize_text_field( $_POST['dcchub_domain_code'] ) : null;
    if ($domainCode == null) {
       return wp_send_json_error(["success" => false, "errorMessage" => "Domain Code is missing from the request."]);
    }

    $authResult = authenticate();
    if (isset($authResult["success"]) && $authResult["success"] && isset($authResult["token"])) {
        $result = get_domain_info($domainCode, $authResult["token"]);

        if (isset($result["success"]) && $result["success"]) {
            $options = get_option('dcchub_option_name');
            $options['dcchub_api_key'] = $domainCode;
            $options['dcchub_api_product'] = $result["starter"];
            $options['api_synced'] = time();
            update_option( 'dcchub_option_name', $options);
            
            return wp_send_json_success($result);
        }
        else {
            return wp_send_json_error($result);
        }
    }
    
    return wp_send_json_error($authResult);
}

add_action( 'admin_footer', 'sync_domain' );
function sync_domain() {
    $options = get_option('dcchub_option_name');
    $domainCode = isset( $options['dcchub_api_key'] ) ? esc_attr( $options['dcchub_api_key']) : null;
    if ($domainCode != null) {
        if ($options["api_synced"] == null || (time() - $options["api_synced"] > 86400000)) {
            // sync once per 24h
            $authResult = authenticate();
            if (isset($authResult["success"]) && $authResult["success"] && isset($authResult["token"])) {
                $result = get_domain_info($domainCode, $authResult["token"]);

                if (isset($result["success"]) && $result["success"]) {
                    $oldProduct = isset( $options['dcchub_api_product'] ) ? esc_attr( $options['dcchub_api_product']) : null;
                    $options['dcchub_api_product'] = $result["starter"];
                    $options['api_synced'] = time();
                    update_option( 'dcchub_option_name', $options);

                    if ($oldProduct != $result["starter"]) {
                        if ($result["starter"]) {
                            ?>
                            <script>
                                document.getElementById('dcchub-product-type').innerText = 'Starter (FREE)';
                                document.getElementById('dcchub-product-text').innerText = 'Upgrade to a paid plan for more monthly session and more features';
                                document.getElementById('dcchub-promotion-banner').style.display = null;
                            </script>
                            <?php
                        }
                        else {
                            ?>
                            <script>
                                document.getElementById('dcchub-product-type').innerText = 'Paid';
                                document.getElementById('dcchub-product-text').innerText = 'You are currently subscribed to CookieHub with a paid plan.';
                                document.getElementById('dcchub-promotion-banner').style.display = 'none';
                            </script>
                            <?php
                        }
                    }
                }
            }
        }
    }
}

add_action('wp_ajax_update_advanced_settings', 'update_advanced_settings');
function update_advanced_settings() {
    $nonce = isset($_POST['update_advanced_settings_nonce']) ? $_POST['update_advanced_settings_nonce'] : '';

    if (!wp_verify_nonce($nonce, 'update_advanced_settings_nonce')) {
        return wp_send_json_error(["success" => false, "errorMessage" => "Nonce verification failed."]);
    }

    $options = get_option('dcchub_option_name');

    $options['dcchub_dev'] = isset( $_POST['dcchub_development_mode'] ) && $_POST['dcchub_development_mode'] == "on" ? "1" : "0";
    $options['dcchub_language'] = isset( $_POST['dcchub_auto_detect_language'] ) && $_POST['dcchub_auto_detect_language'] == "on" ? "1" : "0";
    $options['dcchub_gcm'] = isset( $_POST['dcchub_gcm'] ) && $_POST['dcchub_gcm'] == "on" ? "1" : "0";
    $options['dcchub_blocking'] = isset( $_POST['dcchub_blocking'] ) && $_POST['dcchub_blocking'] == "on" ? "1" : "0";
    $options['dcchub_analytics_head'] = isset( $_POST['dcchub_analytics_head'] ) ? ( $_POST['dcchub_analytics_head'] ) : '';
    $options['dcchub_analytics_body'] = isset( $_POST['dcchub_analytics_body'] ) ? ( $_POST['dcchub_analytics_body'] ) : '';
    $options['dcchub_marketing_head'] = isset( $_POST['dcchub_marketing_head'] ) ? ( $_POST['dcchub_marketing_head'] ) : '';
    $options['dcchub_marketing_body'] = isset( $_POST['dcchub_marketing_body'] ) ? ( $_POST['dcchub_marketing_body'] ) : '';
    $options['dcchub_preferences_head'] = isset( $_POST['dcchub_preferences_head'] ) ? ( $_POST['dcchub_preferences_head'] ) : '';
    $options['dcchub_preferences_body'] = isset( $_POST['dcchub_preferences_body'] ) ? ( $_POST['dcchub_preferences_body'] ) : '';
    $options['dcchub_other_head'] = isset( $_POST['dcchub_other_head'] ) ? ( $_POST['dcchub_other_head'] ) : '';
    $options['dcchub_other_body'] = isset( $_POST['dcchub_other_body'] ) ? ( $_POST['dcchub_other_body'] ) : '';

    update_option( 'dcchub_option_name', $options);

    return wp_send_json_success(["success" => true]);
}

add_action('wp_ajax_register_account', 'register_account');
function register_account() {
    $nonce = isset($_POST['register_account_nonce']) ? $_POST['register_account_nonce'] : '';

    if (!wp_verify_nonce($nonce, 'register_account_nonce')) {
        return wp_send_json_error(["success" => false, "errorMessage" => "Nonce verification failed."]);
    }

    $email = isset( $_POST['dcchub_email'] ) ? sanitize_text_field( $_POST['dcchub_email'] ) : null;
    if ($email == null) {
       return wp_send_json_error(["success" => false, "errorMessage" => "Email is missing from the request."]);
    }
    $domain = isset( $_POST['dcchub_domain'] ) ? sanitize_text_field( $_POST['dcchub_domain'] ) : null;
    if ($domain == null) {
       return wp_send_json_error(["success" => false, "errorMessage" => "Domain is missing from the request."]);
    }

    $authResult = authenticate();
    if (isset($authResult["success"]) && $authResult["success"] && isset($authResult["token"])) {
        $result = create_account($email, $domain, $authResult["token"]);

        if (isset($result["success"]) && $result["success"]) {
            $options = get_option('dcchub_option_name');
            $options['dcchub_api_key'] = $result["domain_code"];
            $options['dcchub_api_product'] = $result["starter"];
            $options['api_synced'] = time();
            $options['dcchub_blocking'] = true; // Default to true on register
            $options['dcchub_gcm'] = true; // Default to true on register
            update_option( 'dcchub_option_name', $options);
            
            return wp_send_json_success($result);
        }
        else {
            return wp_send_json_error($result);
        }
    }

    return wp_send_json_error($authResult);
}

add_action('wp_ajax_register_domain_code', 'register_domain_code');
function register_domain_code() {
    $nonce = isset($_POST['register_domain_code_nonce']) ? $_POST['register_domain_code_nonce'] : '';

    if (!wp_verify_nonce($nonce, 'register_domain_code_nonce')) {
        return wp_send_json_error(["success" => false, "errorMessage" => "Nonce verification failed."]);
    }

    $domainCode = isset( $_POST['dcchub_domain_code'] ) ? sanitize_text_field( $_POST['dcchub_domain_code'] ) : null;
    if ($domainCode == null) {
       return wp_send_json_error(["success" => false, "errorMessage" => "Domain Code is missing from the request."]);
    }

    $authResult = authenticate();
    if (isset($authResult["success"]) && $authResult["success"] && isset($authResult["token"])) {
        $result = get_domain_info($domainCode, $authResult["token"]);

        if (isset($result["success"]) && $result["success"]) {
            $options = get_option('dcchub_option_name');
            $options['dcchub_api_key'] = $domainCode;
            $options['dcchub_api_product'] = $result["starter"];
            $options['api_synced'] = time();
            $options['dcchub_blocking'] = true; // Default to true on register
            $options['dcchub_gcm'] = true; // Default to true on register
            update_option( 'dcchub_option_name', $options);
            
            return wp_send_json_success($result);
        }
        else {
            return wp_send_json_error($result);
        }
    }
    
    return wp_send_json_error($authResult);
}

/* api functions */

function get_domain_info($domainCode, $token) {
    try {
        $headers = array(
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        );

        $args = array(
            'timeout' => 40, //Make sure we don't use default timeout which can be low as 5sek,
            'headers' => $headers
        );

        $domainResponse = wp_remote_get('https://dash.cookiehub.com/wp-api/domain/' . $domainCode, $args);
        
        if (wp_remote_retrieve_response_code( $domainResponse ) !== 200) {
            $error = wp_remote_retrieve_body($domainResponse);
            return ["success" => false, "errorMessage" => ($error != null && $error != "") ? json_decode($error) : "Unkown error"];
        }
        
        $body = wp_remote_retrieve_body($domainResponse);
        $data = json_decode($body, true);

        return ["success" => true, "starter" => $data["starter"]];
    }
    catch ( \Exception $e ) {
        return ["success" => false, "errorMessage" => "Unkown error"];
    }
}

function authenticate() {
    try {
        $authResponse = wp_remote_get('https://dash.cookiehub.com/wp-api/auth');

        if (wp_remote_retrieve_response_code( $authResponse ) !== 200) {
            $error = wp_remote_retrieve_body($authResponse);
            return ["success" => false, "errorMessage" => ($error != null && $error != "") ? json_decode($error) : "Unkown error"];
        } 
        
        $body = wp_remote_retrieve_body($authResponse);
        $data = json_decode($body, true);

        if (!isset($data["token"]) || $data["token"] == null) {
            return ["success" => false, "errorMessage" => "Unkown error"];
        }
        
        return ["success" => true, "token" => $data["token"]];
    } 
    catch ( \Exception $e ) {
        return ["success" => false, "errorMessage" => "Unkown error"];
    }
}

function create_account($email, $domain, $token) {
    try {
        $headers = array(
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        );

        $args = array(
            'timeout' => 40, //Make sure we don't use default timeout which can be low as 5sek,
            'headers' => $headers,
			'body' => json_encode([
				'email' => $email,
				'domain' => $domain
			]),

        );

        $response = wp_remote_post('https://dash.cookiehub.com/wp-api/domain', $args);
        
        if (wp_remote_retrieve_response_code( $response ) !== 200) {
            $error = wp_remote_retrieve_body($response);
            return ["success" => false, "errorMessage" => ($error != null && $error != "") ? json_decode($error) : "Unkown error"];
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return ["success" => true, "starter" => $data["starter"], "domain_code" => $data["domain_code"]];
    }
    catch ( \Exception $e ) {
        return ["success" => false, "errorMessage" => "Unkown error"];
    }
}

?>
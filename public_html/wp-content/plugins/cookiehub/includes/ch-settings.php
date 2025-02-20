<?php
class DCCHUBSettings
{
    public static function dcchub_register_page_html($options) {
        //Get the active tab from the $_GET param
        $default_tab = null;
        $tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
        
		?>
        <div class="dcchub-flex">
            <div class="dcchub-content">
                <div class="nav-tab-wrapper" style="margin-right: 120px; margin-top: 10px">
                    <a class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>" href='?page=my-setting-admin' >General Settings</a>
                    <a class="nav-tab <?php if($tab==='advanced_settings'):?>nav-tab-active<?php endif; ?>" href='?page=my-setting-admin&tab=advanced_settings'>Advanced Settings</a>
                </div>
                <div class="tab-content">
                    <?php switch($tab) :
                    case 'advanced_settings':
                        self::dcchub_advanced_settings($options);
                        break;
                    default:
                        self::dcchub_general_settings($options);
                        break;
                    endswitch; ?>
                </div>
            </div>
            <div class="dcchub-sidebar">
                <?php
                DCCHUBSidebar::sidebar();
                ?>
            </div>
        </div>
        <?php
    }

    static function dcchub_general_settings($options) {
        $assets_url = plugins_url('assets', __FILE__);
        $nonce = wp_create_nonce('update_domain_code_nonce');
        ?>
        <div style="margin: 58px 3px">
            <form id="dcchub-general-settings-form" method="POST">
                <input type="hidden" name="update_domain_code_nonce" value="<?php echo esc_attr($nonce); ?>" />
                <div class="dcchub-horizontal">
                    <p class="dcchub-p" style="width: 162px;" >Domain Code:</p>
                    <?php
                      printf(
                        '<input type="text" class="dcchub-input-box" id="dcchub_domain_code" name="dcchub_domain_code" maxlength="8" value="%s" />',
                        isset( $options['dcchub_api_key'] ) ? esc_attr( $options['dcchub_api_key']) : ''
                    );
                    ?>
                    <button class="dcchub-button-gray" style="margin-left:8px;" id="dcchub_general_settings_form_submit" >
                        UPDATE
                    </button>
                </div>
                <a class="dcchub-hint" target="_blank" href="https://docs.cookiehub.com/installation/wordpress#how-do-i-find-my-domain-code" style="margin-left:162px;">How do I find my domain code?</a>
            </form>
        </div>
        <div class="dcchub-wrap dcchub-box wide">
            <div class ="dcchub-horizontal">
                <h3 class="dcchub-header" style="margin: 0px auto 0px 0px;">Subscription</h3>
                <p id="dcchub-product-type" class="dcchub-p" style="margin: 0px 0px 0px auto; font-size: 18px;"><?php printf(isset( $options['dcchub_api_product'] ) ? $options['dcchub_api_product'] == "1" ?  'Starter (FREE)' : 'Paid' : '') ?></a>
            </div>
            <p class="dcchub-separator"></p>
            <p id="dcchub-product-text" class="dcchub-p">
                <?php printf(isset( $options['dcchub_api_product'] ) ? $options['dcchub_api_product'] == "1" ?  
                'Upgrade to a paid plan for more monthly session and more features.' 
                : 'You are currently subscribed to CookieHub with a paid plan.' 
                : '') ?>    
            </p>
            <p class="dcchub-p">For customization and management of your subscription plan and the CookieHub widget, you can easily access the options in the <a class="dcchub-p" target="_blank" href="https://dash.cookiehub.com">CookieHub dashboard.</a></p>
        </div>
        
        <div id="dcchub-promotion-banner" class="dcchub-promotion-banner" <?php if(!isset( $options['dcchub_api_product'] ) || $options['dcchub_api_product'] != "1") {printf('style="display: none;"');}?>>
            <div class="dcchub-promotion-banner-content">
                <h3 class="dcchub-promotion-banner-header">Become fully compliant for only €8 per month.</h3>
                <div class="dcchub-horizontal" style="margin: 22px 0px;">
                    <img style="vertical-align:bottom; margin-right: 8px;" src="<?php echo esc_url($assets_url.'/check.png'); ?>"></img> 
                    <p class="dcchub-p" style="color: #fff">30 day free trial</p>
                    <img style="vertical-align:bottom; margin-right: 8px; margin-left: 24px;" src="<?php echo esc_url($assets_url.'/no_card.png'); ?>"></img> 
                    <p class="dcchub-p" style="color: #fff;">No credit card required</p>
                </div>
                <a class="dcchub-white-button" target="_blank" href="https://www.cookiehub.com/register?product_id=29">START A 30 DAY TRIAL</a>
            </div>
            <img style="height: 100%; width: 375px" src="<?php echo esc_url($assets_url.'/cookiehub-wp-banner-bg.png'); ?>" />
        </div>
       <?php
    }

    static function dcchub_advanced_settings($options) {
        $hasOther = (isset( $options['dcchub_other_head'] ) && $options['dcchub_other_head'] != "") || (isset( $options['dcchub_other_body'] ) && $options['dcchub_other_body'] != "");
        $nonce = wp_create_nonce('update_advanced_settings_nonce');
        ?>
        <div style="margin: 58px 3px">
            <form id="dcchub-advanced-settings-form" method="POST">
                <input type="hidden" name="update_advanced_settings_nonce" value="<?php echo esc_attr($nonce); ?>" />
                <div class="dcchub-checkbox">
                    <input type="checkbox" id="dcchub_development_mode" name="dcchub_development_mode" <?php if (isset( $options['dcchub_dev'] ) && $options['dcchub_dev'] == "1") {printf('checked');}?>>
                    <label for="dcchub_development_mode" class="dcchub-p">Development Mode</label>
                </div>
                <a class="dcchub-hint" target="_blank" href="https://docs.cookiehub.com/installation/wordpress#what-is-development-mode" style="margin-left:45px;">What is Development Mode?</a>
                
                <?php 
                    if (isset( $options['dcchub_language'] ) && $options['dcchub_language'] == "1") {
                        printf('
                        <div class="dcchub-checkbox" style="margin-top:30px;">
                            <input type="checkbox" id="dcchub_auto_detect_language" name="dcchub_auto_detect_language" checked  >
                            <label for="dcchub_auto_detect_language" class="dcchub-p">Automatically detect language from Wordpress or WPML</label>
                        </div>
                        <a class="dcchub-hint" target="_blank" href="https://docs.cookiehub.com/installation/wordpress#how-does-cookiehub-detect-the-language" style="margin-left:45px;">How does CookieHub detect the language?</a>
                        ');
                    }
                ?>
                
                <div class="dcchub-checkbox" style="margin-top:30px;">
                    <input type="checkbox" id="dcchub_blocking" name="dcchub_blocking" <?php if (isset( $options['dcchub_blocking'] ) && $options['dcchub_blocking'] == "1") {printf('checked');}?>>
                    <label for="dcchub_blocking" class="dcchub-p">Automatic cookie blocking</label>
                </div>
                <a class="dcchub-hint" target="_blank" href="https://docs.cookiehub.com/installation/wordpress#what-is-automatic-cookie-blocking" style="margin-left:45px;">What Is Automatic Cookie Blocking?</a>
                
                <div class="dcchub-checkbox" style="margin-top:30px;">
                    <input type="checkbox" id="dcchub_gcm" name="dcchub_gcm" <?php if (isset( $options['dcchub_gcm'] ) && $options['dcchub_gcm'] == "1") {printf('checked');}?>>
                    <label for="dcchub_gcm" class="dcchub-p">Consent mode v2 (Only available in CookieHub 2.7.x)</label>
                </div>
                <a class="dcchub-hint" target="_blank" href="https://docs.cookiehub.com/getting-started/release-notes/cookiehub-2.7" style="margin-left:45px;">What is Consent mode v2?</a> <a class="dcchub-hint" target="_blank" href="https://docs.cookiehub.com/getting-started/release-notes/cookiehub-2.7#what-are-the-next-steps" style="margin-left:45px;">How to upgrade to CookieHub 2.7?</a>

                <h3 class="dcchub-header small" style="margin-top:85px;">3rd party tags</h3>
                <p class="dcchub-p" style="margin-top:20px;">Paste Javascript tags that CookieHub should conditionally load based on user consent below.</p>
                <a class="dcchub-hint" target="_blank" href="https://docs.cookiehub.com/installation/wordpress#what-are-3rd-party-tags" style="margin-top:10px;">More information about 3rd. party tags</a>

                <ul class="dcchub-tab-buttons" style="margin-top:48px; margin-bottom:0px;">
                    <li class="active" data-tab="#dcchub_analytics" style="z-index: 1;">Analytics</li>
                    <li data-tab="#dcchub_marketing" style="z-index: 1;">Marketing</li>
                    <li data-tab="#dcchub_preferences" style="z-index: 1;">Preference</li>
                    <?php 
                    if($hasOther) {
                        printf('<li data-tab="#dcchub_others" style="z-index: 1;">Others</li>');
                    }
                    ?>
                    
                </ul>
                <div class="dcchub-wrap dcchub-box wide" style="margin-top:-1px; padding: 0px">
                    <ul class="dcchub-tabs-content">
                        <li id="dcchub_analytics" class="active"> 
                        <?php self::tagsBox( 'dcchub_analytics_head', 'dcchub_analytics_body', $options ); ?>
                        </li>
                        <li id="dcchub_marketing">
                        <?php self::tagsBox( 'dcchub_marketing_head', 'dcchub_marketing_body', $options ); ?>
                        </li>
                        <li id="dcchub_preferences">
                        <?php self::tagsBox( 'dcchub_preferences_head', 'dcchub_preferences_body', $options ); ?>
                        </li>
                        <?php 
                        if($hasOther) {
                            printf('<li id="dcchub_others">');
                            self::tagsBox( 'dcchub_other_head', 'dcchub_other_body', $options );
                            printf('</li>');
                        }
                        ?>
                    </ul>
                </div>
                <button class="dcchub-button" style="min-width: 137px;" id="dcchub_advanced_settings_form_submit" >
                    SAVE
                </button>
            </form>
        </div>
        <?php
    }

    static function tagsBox($sectionHead, $sectionBody, $options) {
        printf(
            '<p class="dcchub-p" style="margin-top:0px;">Head</p>
            <textarea class="dcchub_script" name="%s">%s</textarea>
            <p class="dcchub-p" style="margin-top:24px;">Body</p>
            <textarea class="dcchub_script" name="%s">%s</textarea>',
            $sectionHead,
            isset( $options[$sectionHead] ) ? esc_textarea( stripslashes( $options[$sectionHead] ) ) : '',
            $sectionBody,
            isset( $options[$sectionBody] ) ? esc_textarea( stripslashes( $options[$sectionBody] ) ) : ''
        );
    }
}
<?php
class DCCHUBRegisterDomain
{
    public static function dcchub_register_page_html() {
        $domainCodeNonce = wp_create_nonce('register_domain_code_nonce');
        $registerAccountNonce = wp_create_nonce('register_account_nonce');
		?>
        <h3 class="dcchub-header page-header">Thank you for installing the CookieHub Wordpress Plugin</h3>
        <div class="dcchub-register-domain">
            <div class="dcchub-wrap dcchub-box">
                <form id="dcchub-domain_code_form" method="POST">
                    <input type="hidden" name="register_domain_code_nonce" value="<?php echo esc_attr($domainCodeNonce); ?>" />
                    <h3 class="dcchub-header">Already have an account?</h3>
                    <p class="dcchub-separator"></p>
                    <p class="dcchub-p">If you have already created an account, type your 8 character domain code below.</p>
                    <div class="dcchub-horizontal">
                        <input type="text" class="dcchub-input-box" id="dcchub_domain_code" name="dcchub_domain_code" maxlength='8' />
                        <button class="dcchub-button" style="margin-left:8px; min-width: 105px" id="domain_code_form_submit" >
                            SAVE
                        </button>
                        <a class="dcchub-hint center" style="margin-left:24px;" target="_blank" href="https://docs.cookiehub.com/installation/wordpress#how-do-i-find-my-domain-code">How do I find my domain code?</a>
                    </div>
                </form>
            </div>
            
            <div class="dcchub-wrap dcchub-box">
                <h3 class="dcchub-header">Don’t have an account?</h3>
                <p class="dcchub-separator"></p>
                <p class="dcchub-p">Register now for a free CookieHub CMP account.</p>

                <form id="dcchub-domain_register_form" method="POST">
                <input type="hidden" name="register_account_nonce" value="<?php echo esc_attr($registerAccountNonce); ?>" />
                <table style="width: 100%; border-spacing: 0px 25px;">
                    <colgroup>
                    <col span="1" style="width: 30%;">
                    <col span="1" style="width: 70%;">
                    </colgroup>
                    
                    <tbody>
                        <tr>
                            <td>
                                <p class="dcchub-p" >E-mail address:</p>
                            </td>
                            <td>
                                <?php
                                    $current_user = wp_get_current_user();

                                    printf(
                                        '<input type="text" class="dcchub-input-box wide" id="dcchub_email" name="dcchub_email" value="%s"/>',
                                        $current_user != null ? $current_user->user_email : ''
                                    );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p class="dcchub-p" >Domain:</p>
                            </td>
                            <td>
                                <?php
                                    printf(
                                        '<input type="text" class="dcchub-input-box wide" id="dcchub_domain" name="dcchub_domain" value="%s"/>',
                                        get_home_url()
                                    );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            </td>
                            <td>
                                <button class="dcchub-button" id="register_account_form_submit" >
                                    REGISTER
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </form>
            </div>
        </div>
        <?php
    }
}
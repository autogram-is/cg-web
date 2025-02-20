<?php
class DCCHUBToolbar
{
    public static function toolbar() {
        $assets_url = plugins_url('assets', __FILE__);
        ?>
        <div class ="dcchub-toolbar">
            <a target="_blank" href="https://dash.cookiehub.com">
                <img class="dcchub-logo" src="<?php echo esc_url($assets_url.'/ch_banner_logo.png'); ?>" alt="CookieHub logo">
            </a>
            <div class="dcchub-horizontal" style="margin: 0px 0px 0px auto;">
                <a class="dcchub-toolbar-link" style="margin-right: 20px;" href="https://www.cookiehub.com/pricing" target="_blank">
                    <span><img style="vertical-align:bottom; margin-right: 8px;" src="<?php echo esc_url($assets_url.'/credit_card.png'); ?>"></img></span>
                    <span>View our plans</span>
                </a>
                <a class="dcchub-toolbar-link" style="margin-right: 20px; margin-left: 22px;" href="https://dash.cookiehub.com" target="_blank">
                    <span><img style="vertical-align:bottom; margin-right: 8px;" src="<?php echo esc_url($assets_url.'/dashboard.png'); ?>"></img></span>
                    <span>CookieHub Dashboard</span>
                </a>
            </div>
        </div>
        <div id="message-container"></div>
        <?php

        
    }
}

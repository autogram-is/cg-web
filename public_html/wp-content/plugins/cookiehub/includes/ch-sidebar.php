<?php
class DCCHUBSidebar
{
    public static function sidebar() {
        ?>
        <div class="dcchub-wrap dcchub-box side">
            <h3 class="dcchub-header small">Rating & Reviews</h3>
            <p class="dcchub-separator"></p>
            <p class="dcchub-p">If you are happy with CookieHub please consider leaving us a review and/or rating. A huge thanks in advance!</p>
            <div style="margin-top: 37px; margin-bottom: 10px;"> <!-- +10 margin to account for <a> padding -->
                <a class="dcchub-green-button" target="_blank" href="https://wordpress.org/support/view/plugin-reviews/cookiehub">Leave Rating</a>
            </div>
        </div>
        <div class="dcchub-wrap dcchub-box side">
            <h3 class="dcchub-header small">Having Issues?</h3>
            <p class="dcchub-separator"></p>
            <p class="dcchub-p">If you need help or are having issues with this plugin, please visit the links below.</p>
            <div style="margin-top: 29px; margin-left:21px">
                <ul style="list-style-type: disc">
                    <li><a class="dcchub-p" target="_blank"href="https://docs.cookiehub.com/getting-started">Getting Started</a></li>
                    <li><a class="dcchub-p" target="_blank"href="https://docs.cookiehub.com/installation">Installation</a></li>
                    <li><a class="dcchub-p" target="_blank"href="https://docs.cookiehub.com/installation/wordpress">Wordpress</a></li>
                    <li><a class="dcchub-p" target="_blank"href="https://docs.cookiehub.com/installation/troubleshooting">Troubleshooting Guide</a></li>
                    <li><a class="dcchub-p" target="_blank"href="https://docs.cookiehub.com/getting-started/support">Contact Support</a></li>
                </ul>
            </div>
        </div>
        
        <?php
    }
}

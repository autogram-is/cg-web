<?php

function dcchub_activate() { 
    //nothing
}

register_activation_hook( __FILE__, 'dcchub_activate' );

/**
 * Deactivation hook.
 */
function dcchub_deactivate() {
    //nothing
}

register_deactivation_hook( __FILE__, 'dcchub_deactivate' );
?>
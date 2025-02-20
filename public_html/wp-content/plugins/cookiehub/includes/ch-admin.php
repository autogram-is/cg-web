<?php
class DCCHUBAdmin
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'dcchub_add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'dcchub_page_init' ) );

    }
    /**
     * Add options page
     */
    public function dcchub_add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'CookieHub', 
            'manage_options', 
            'my-setting-admin', 
            array( $this, 'dcchub_display_pages' )
        );
    }
    
    public function dcchub_display_pages() {
        // Set class property
        $this->options = get_option('dcchub_option_name');

        // Display the toolbar
        DCCHUBToolbar::toolbar();

        if (isset( $this->options['dcchub_api_key'] )) {
            DCCHUBSettings::dcchub_register_page_html($this->options);
        }
        else {
            DCCHUBRegisterDomain::dcchub_register_page_html();
        }
    }

    /**
     * Register and add settings
     */
    public function dcchub_page_init()
    {   
        wp_register_style( 'dcchub_style', plugins_url( '/css/dcchub-admin.css?1.2.0', __FILE__ ));
        wp_enqueue_style('dcchub_style');     
        wp_register_script( 'dcchub_test', plugins_url( '/js/dcchub-test.js?1.2.0', __FILE__ ),  array ('jquery') ,'', false);
        wp_enqueue_script('dcchub_test');
        wp_localize_script('my-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

    }
}

if( is_admin() )
{    
    $dcchub_admin = new DCCHUBAdmin();
}

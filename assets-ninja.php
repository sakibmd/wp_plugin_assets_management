<?php

/**
 * Plugin Name:       Assets Ninja
 * Plugin URI:        https://sakibmd.xyz/
 * Description:       Assets Management in Depth
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Sakib Mohammed
 * Author URI:        https://sakibmd.xyz/
 * License:           GPL v2 or later
 * License URI:
 * Text Domain:       assets-ninja
 * Domain Path:       /languages
 */

define("ASN_ASSETS_DIR", plugin_dir_url(__FILE__) . "/assets/");
define("ASN_ASSETS_ADMIN_DIR", plugin_dir_url(__FILE__) . "/assets/admin/");
define("ASN_ASSETS_PUBLIC_DIR", plugin_dir_url(__FILE__) . "/assets/public/");

class AssetsNinja
{

    private $version;

    public function __construct()
    {
        $this->version = time(); //cache busting

        add_action('init', array($this, 'asn_init'));

        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('wp_enqueue_scripts', array($this, 'load_front_assets'), 15);
        add_action('admin_enqueue_scripts', array($this, 'load_admin_assets'));

        add_shortcode('inlineImage', array($this, 'asn_shortcode_bg_image'));
    }

    public function load_textdomain()
    {
        load_plugin_textdomain('assets-ninja', false, plugin_dir_url(__FILE__) . "/languages");
    }

    public function asn_init()
    {
        wp_deregister_style('bootstrap-css');
        wp_register_style('bootstrap-css', '//cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css');

        //wp_deregister_script('tinyslider-js');
        //wp_register_script('tinyslider-js','//cdn.jsdelivr.net/npm/tiny-slider@2.8.5/dist/tiny-slider.min.js',null,'1.0',true);
    }

    public function load_admin_assets($screen)
    {
        $_screen = get_current_screen();

        // jodi screen specfic kore dekhate chai.
        if ('options-general.php' == $screen) {
            wp_enqueue_script('asn-admin-js', ASN_ASSETS_ADMIN_DIR . "js/admin.js", array('jquery'), $this->version, true);
        }
        //access pages
        if ('edit.php' == $screen && 'page' == $_screen->post_type) {
            wp_enqueue_script('asn-admin-js', ASN_ASSETS_ADMIN_DIR . "js/admin.js", array('jquery'), $this->version, true);
        }
        //access posts->category
        if ('edit-tags.php' == $screen && 'category' == $_screen->taxonomy) {
            wp_enqueue_script('asn-admin-js', ASN_ASSETS_ADMIN_DIR . "js/admin.js", array('jquery'), $this->version, true);
        }
    }

    public function load_front_assets()
    {

        /*
        wp_enqueue_style('asn-main-css', ASN_ASSETS_PUBLIC_DIR . "css/main.css", null, $this->version);
        wp_enqueue_script('asn-main-js', ASN_ASSETS_PUBLIC_DIR . "js/main.js", array('jquery', 'asn-another-js'), $this->version, true);
        wp_enqueue_script('asn-another-js', ASN_ASSETS_PUBLIC_DIR . "js/another.js", array('jquery'), $this->version, true);
         */

        wp_enqueue_style('asn-main-css', ASN_ASSETS_PUBLIC_DIR . "css/main.css", null, $this->version);

        $js_files = array(
            'asn-main-js' => array('path' => ASN_ASSETS_PUBLIC_DIR . "js/main.js", 'dep' => array('jquery')),
            'asn-another-js' => array('path' => ASN_ASSETS_PUBLIC_DIR . "js/another.js", 'dep' => array('jquery')),
        );
        foreach ($js_files as $handle => $fileinfo) {
            wp_enqueue_script($handle, $fileinfo['path'], $fileinfo['dep'], $this->version, true);
        }

        //send data from php file to js file  using ** wp_localize_script() **
        $myInfo = array(
            'name' => 'Sakib',
            'age' => 24,
        );
        $translated_string = array(
            'greeting' => __('Hello World', 'assets-ninja'),
        );
        wp_localize_script('asn-main-js', 'myInfo', $myInfo);
        wp_localize_script('asn-main-js', 'translaton', $translated_string);

        $attachment_image_src = wp_get_attachment_image_src(40, 'medium');

        $shortcode_image = <<<EOD
        #bgmedia{
            background-image:url($attachment_image_src[0]);
        }
EOD;

        wp_add_inline_style('asn-main-css', $shortcode_image);

    }

    public function asn_shortcode_bg_image($attributes)
    {
        $shortcode_output = <<<EOD

<div id="bgmedia"></div>
EOD;

        return $shortcode_output;
    }
}

new AssetsNinja();

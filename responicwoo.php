<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.fiqhidayat.com
 * @since             1.0.0
 * @package           Responicwoo
 *
 * @wordpress-plugin
 * Plugin Name:       Responicwoo
 * Plugin URI:        https://labs.id
 * Description:       Responic Whatsapp Notification for WooCommerce
 * Version:           1.2.5
 * Author:            Responic Teams
 * Author URI:        https://www.salesloo.id
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       responicwoo
 * Domain Path:       /languages
 */

use Salesloo\Response;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('RESPONICWOO_VERSION', '1.2.5');
define('RESPONICWOO_URL', plugin_dir_url(__FILE__));
define('RESPONICWOO_PATH', plugin_dir_path(__FILE__));
define('RESPONICWOO_ROOT', __FILE__);

require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/labsdotid/responicwoo',
    __FILE__,
    'responicwoo'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('release');

class Responicwoo
{
    /**
     * Instance
     */
    private static $_instance = null;

    public $setting;

    public $admin;

    public $notification;

    /**
     * run
     *
     * @return Responicwoo An instance of class
     */
    public static function run()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        add_action('plugins_loaded', [$this, 'on_plugins_loaded']);
    }

    /**
     * Load Textdomain
     *
     * Load plugin localization files.
     *
     * Fired by `init` action hook.
     *
     * @access public
     */
    public function i18n()
    {
        load_plugin_textdomain(
            'responicwoo',
            false,
            RESPONICWOO_PATH . '/languages'
        );
    }

    /**
     * On Plugins Loaded
     *
     * Checks if woocommerce has loaded, and performs some compatibility checks.
     *
     * @access public
     */
    public function on_plugins_loaded()
    {

        if ($this->is_compatible()) {
            $this->i18n();
            $this->load_scripts();
            $this->init();
            $this->install_hooks();
        }
    }

    public function load_scripts()
    {
        require_once RESPONICWOO_PATH . '/inc/setting.php';
        require_once RESPONICWOO_PATH . '/inc/admin.php';
        require_once RESPONICWOO_PATH . '/inc/woo-customer.php';
        require_once RESPONICWOO_PATH . '/inc/woo-order.php';
        require_once RESPONICWOO_PATH . '/inc/whatsapp.php';
        require_once RESPONICWOO_PATH . '/inc/notification.php';
    }

    public function init()
    {
        $this->setting      = new Responicwoo\Setting();
        $this->admin        = new Responicwoo\Admin();
        $this->notification = new Responicwoo\Notification();
    }

    public function install_hooks()
    {
        add_action('admin_enqueue_scripts', [$this->admin, 'enqueue_styles']);
        //add_action('admin_enqueue_scripts', [$this->admin, 'enqueue_scripts']);

        add_action('admin_menu', [$this->setting, 'add_menu_page']);
        add_action('admin_init', [$this->setting, 'on_save']);

        add_action('woocommerce_order_status_changed', [$this->notification, 'order_status_changed'], 10, 4);
    }

    /**
     * Compatibility Checks
     *
     * @access public
     */
    public function is_compatible()
    {
        // Check if Woocommerce installed and activated
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_main_plugin']);
            return false;
        }

        return true;
    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have WooCommerce installed or activated.
     *
     * @access public
     */
    public function admin_notice_missing_main_plugin()
    {

        if (isset($_GET['activate'])) unset($_GET['activate']);

        $message = sprintf(
            /* translators: 1: Responicwoo 2: WooCommerce */
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'responicwoo'),
            '<strong>' . esc_html__('Responicwoo', 'responicwoo') . '</strong>',
            '<strong>' . esc_html__('WooCommerce', 'responicwoo') . '</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
}

/**
 * debuging function
 */
if (!function_exists('__debug')) {
    function __debug()
    {
        echo '<pre>';
        print_r(func_get_args());
        echo '</pre>';
    }
}

Responicwoo::run();

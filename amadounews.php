<?php
/*
Plugin Name: Amadou News
Plugin URI: https://github.com/webamadou/amadouposts.git
Description: This is the bases for creating a plugin in wordpress. This will create a plugin to register user's email for a newsletter
Version: 0.1
Author: Papa Amadou Abdoulaye Ba
Author URI: https://github.com/webamadou/
License: GPL2
*/

class AmadouNews
{
    public function __construct(){
        include_once plugin_dir_path( __FILE__ ).'/newsletter.php';
        include_once plugin_dir_path( __FILE__ ).'/EmailsList.php';
        new Newsletter();
        register_activation_hook(__FILE__, array($this, 'activation'));
        register_uninstall_hook(__FILE__, [$this,'uninstall']);
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_loaded', array('Newsletter', 'insertion'));
    }
    /**
     * activation() : static function loaded when the plugin is activated. It will create the DB where emails will be saved
     */
    public static function activation(){
        global $wpdb;

        $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}amadou_newsletter (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(255) NOT NULL, saved_at TIMESTAMP );");
    }

    /**
     * This function will be loaded when the plugin is being uninstalled
     */
    public static function uninstall(){
        return true;
    }
    //Adding a menus
    public function add_admin_menu(){
        $page_title = __('List of email addresses available for your news letter','amadounews');
        $menu_title = __('Amadou News','amadounews');
        add_menu_page($page_title, $menu_title, 'manage_options', 'amadounews', [$this,'menu_page'],'',5);
//        add_submenu_page('settings-amadou', 'Settings', 'Settings', 'manage_options', 'zero', 'menu_page');
    }

    public function menu_page(){
        $emails_rows    = new EmailsList();
        $headertitle    = __('List of email addresses available for your newsletter','Amadou News');
        $emails         = __('EMAILS','Amadou News');
        $save_at        = __('SAVED AT','Amadou News');
        ?>
        <div>
            <h3><?php echo $headertitle ?></h3>
        <?php
            $emails_rows->prepare_items();
            $emails_rows->display();
        ?>
        </div>
        <?php
    }
}

new AmadouNews();
<?php

include_once plugin_dir_path( __FILE__ ).'/newsletterwidget.php';
class Newsletter
{
    public function __construct()
    {
        add_action('widgets_init', function(){register_widget('NewsLetterWidget');});
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
    public static function uninstall()
    {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}amadou_newsletter;");
    }

    /**
     * Insertion this will save the entered email to the database
     */
    public function insertion()
    {
        if (isset($_POST['amadounews']) && !empty($_POST['amadounews'])) {
            global $wpdb;
            $email = $_POST['amadounews'];

            //Each email address will be saved once
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}amadou_newsletter WHERE email = '$email'");
            if (is_null($row)) {
                $wpdb->insert("{$wpdb->prefix}amadou_newsletter", array('email' => $email,'saved_at' => date('Y-m-d H:i:s')));
                $instance['feedback'] = __('Your email was saved successfully!  You will now received all the good things from Amadou','amadounews') ;
            } else {
                $instance['feedback'] = __('Your email is already saved. You will now received all the good things from Amadou','amadounews') ;
            }
        }
    }

}


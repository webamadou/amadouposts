<?php
class NewsLetterWidget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct('amadou_news', 'Amadou News', ['Get the last news from Amadou']);
    }

    /**
     * Using the WP form method to display the config form when adding the widget to a widget area
     * @param array $instance
     * @return string|void
     */
    public function form($instance)
    {
        //If we already have a title lets use it otherwise title is empty
        $title          = isset($instance['title']) ? $instance['title'] : '';
        $button_label   = isset($instance['button_label']) ? $instance['button_label'] : '';
        ?>
        <!-- buildig up the form -->
        <p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo  $title; ?>" />
            <hr/>
            <label for="<?php echo $this->get_field_name( 'button_label' ); ?>"><?php _e( 'Label for the submit button:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'button_label' ); ?>" name="<?php echo $this->get_field_name( 'button_label' ); ?>" type="text" value="<?php echo  $button_label; ?>" />
        </p>
        <?php
    }


    public function widget($args, $instance)
    {
        echo @$args['before_widget'];
        echo @$args['before_title'];
        echo apply_filters('widget_title', @$instance['title']);
        echo @$args['after_title'];
        ?>
        <form action="" method="post">
            <p>
                <label for="amadounews">Don't miss any awesome news from amadou!</label>
                <input id="amadounews" name="amadounews" type="email" placeholder="Enter your email address"/>
            </p>
            <button type="submit" name="save">Subscribe</button>
        </form>
        <?php
        echo @$args['after_widget'];
    }
}
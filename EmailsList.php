<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class EmailsList extends WP_List_Table {

    protected $emails_rows = [];
    public function __construct() {

        parent::__construct( [
            'singular' => __( 'Email', 'amadounews' ), //singular name of the listed records
            'plural'   => __( 'Customers', 'amadounews' ), //plural name of the listed records
            'ajax'     => false //should this table support ajax?
        ] );
    }

    public function get_email_listo(){
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}amadou_newsletter",ARRAY_A);
    }

    public static function get_email_list( $per_page = 5, $page_number = 1 ) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}amadou_newsletter";
        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    public static function delete_email( $id ) {
        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}amadou_newsletter",
            [ 'id' => $id ],
            [ '%d' ]
        );
    }

    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}amadou_newsletter";

        return $wpdb->get_var( $sql );
    }

    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    public function get_columns(){
        return [
                'cb'        => '<input type="checkbox" />',
                'email'     => __('EMAIL','amadounews'),
                'saved_at'  =>__('SAVED AT','amadounews')
               ];
    }

    public function get_sortable_columns()
    {
        return [
          'email'   => ['email',false],
          'saved_at' => ['saved_at',false]
        ];
    }

    public function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'email':
                return $item[ $column_name ];
            case 'saved_at':
                return date('d-m-Y H:i', strtotime($item[ $column_name ]));
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_name( $item ) {

        // create a nonce
        $delete_nonce = wp_create_nonce( 'sp_delete_email' );

        $title = '<strong>' . $item['email'] . '</strong>';

        $actions = [
            'delete' => sprintf( '<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
        ];

        return $title . $this->row_actions( $actions );
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        return [ 'bulk-delete' => 'Delete' ];
    }

    public function process_bulk_action() {
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, 'sp_delete_email' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::delete_email( absint( $_GET['email'] ) );

                wp_redirect( esc_url( add_query_arg() ) );
                exit;
            }

        }

        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = esc_sql( $_POST['bulk-delete'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::delete_email( $id );

            }

            wp_redirect( esc_url( add_query_arg() ) );
            exit;
        }
    }

    public function prepare_items(){
        $columns    = $this->get_columns();
        $hidden     = [];
        $sortable   = $this->get_sortable_columns();
        $this->_column_headers = [$columns, $hidden, $sortable];
        $this->process_bulk_action();
        $per_page     = $this->get_items_per_page( 'emails_per_page', 12 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ] );

        $this->items = self::get_email_list($per_page,$current_page);
    }
}
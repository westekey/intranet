<?php
/**
 * Intranet Notifications System
 * Advanced notification management for the intranet
 *
 * @package OceanWP WordPress theme
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get user notifications
 *
 * @param int $user_id User ID
 * @param array $args Query arguments
 * @return array Notifications
 */
function oceanwp_get_user_notifications( $user_id, $args = array() ) {
    $defaults = array(
        'number' => 20,
        'offset' => 0,
        'status' => 'all', // all, read, unread
        'type'   => 'all', // all, mention, comment, announcement, etc.
    );

    $args = wp_parse_args( $args, $defaults );

    // Get notifications from database
    global $wpdb;
    $table_name = $wpdb->prefix . 'intranet_notifications';

    $where = array( 'user_id = %d' );
    $where_values = array( $user_id );

    if ( 'read' === $args['status'] ) {
        $where[] = 'is_read = 1';
    } elseif ( 'unread' === $args['status'] ) {
        $where[] = 'is_read = 0';
    }

    if ( 'all' !== $args['type'] ) {
        $where[] = 'type = %s';
        $where_values[] = $args['type'];
    }

    $where_clause = implode( ' AND ', $where );

    $query = $wpdb->prepare(
        "SELECT * FROM {$table_name} WHERE {$where_clause} ORDER BY created_at DESC LIMIT %d OFFSET %d",
        array_merge( $where_values, array( $args['number'], $args['offset'] ) )
    );

    $results = $wpdb->get_results( $query, ARRAY_A );

    // Format notifications
    $notifications = array();
    if ( $results ) {
        foreach ( $results as $row ) {
            $notifications[] = array(
                'id'       => $row['id'],
                'type'     => $row['type'],
                'message'  => $row['message'],
                'time'     => strtotime( $row['created_at'] ),
                'read'     => (bool) $row['is_read'],
                'category' => $row['category'],
                'link'     => $row['link'],
                'data'     => maybe_unserialize( $row['data'] ),
            );
        }
    } else {
        // Sample notifications for demo
        $notifications = array(
            array(
                'id'       => 1,
                'type'     => 'announcement',
                'message'  => '<strong>Jean Dupont</strong> a publié une nouvelle annonce : <em>Réunion mensuelle</em>',
                'time'     => current_time( 'timestamp' ) - 7200,
                'read'     => false,
                'category' => 'Annonces',
                'link'     => '#',
                'data'     => array(),
            ),
            array(
                'id'       => 2,
                'type'     => 'comment',
                'message'  => '<strong>Marie Martin</strong> a commenté votre document',
                'time'     => current_time( 'timestamp' ) - 14400,
                'read'     => false,
                'category' => 'Commentaires',
                'link'     => '#',
                'data'     => array(),
            ),
            array(
                'id'       => 3,
                'type'     => 'mention',
                'message'  => '<strong>Pierre Durand</strong> vous a mentionné dans une discussion',
                'time'     => current_time( 'timestamp' ) - 28800,
                'read'     => true,
                'category' => 'Mentions',
                'link'     => '#',
                'data'     => array(),
            ),
            array(
                'id'       => 4,
                'type'     => 'event',
                'message'  => 'Rappel : <strong>Formation interne</strong> commence dans 2 heures',
                'time'     => current_time( 'timestamp' ) - 43200,
                'read'     => true,
                'category' => 'Événements',
                'link'     => '#',
                'data'     => array(),
            ),
        );
    }

    return $notifications;
}

/**
 * Get unread notifications count
 *
 * @param int $user_id User ID
 * @return int Count
 */
function oceanwp_get_unread_notifications_count( $user_id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'intranet_notifications';

    $count = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM {$table_name} WHERE user_id = %d AND is_read = 0",
        $user_id
    ) );

    // Return demo count if table doesn't exist
    if ( null === $count ) {
        return 2;
    }

    return (int) $count;
}

/**
 * Mark notification as read
 *
 * @param int $notification_id Notification ID
 * @return bool Success
 */
function oceanwp_mark_notification_read( $notification_id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'intranet_notifications';

    return $wpdb->update(
        $table_name,
        array( 'is_read' => 1 ),
        array( 'id' => $notification_id ),
        array( '%d' ),
        array( '%d' )
    );
}

/**
 * Mark all user notifications as read
 *
 * @param int $user_id User ID
 * @return bool Success
 */
function oceanwp_mark_all_notifications_read( $user_id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'intranet_notifications';

    return $wpdb->update(
        $table_name,
        array( 'is_read' => 1 ),
        array( 'user_id' => $user_id ),
        array( '%d' ),
        array( '%d' )
    );
}

/**
 * Delete notification
 *
 * @param int $notification_id Notification ID
 * @return bool Success
 */
function oceanwp_delete_notification( $notification_id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'intranet_notifications';

    return $wpdb->delete(
        $table_name,
        array( 'id' => $notification_id ),
        array( '%d' )
    );
}

/**
 * Create notification
 *
 * @param int $user_id User ID
 * @param string $type Notification type
 * @param string $message Notification message
 * @param array $args Additional arguments
 * @return int|false Notification ID or false
 */
function oceanwp_create_notification( $user_id, $type, $message, $args = array() ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'intranet_notifications';

    $defaults = array(
        'category' => '',
        'link'     => '',
        'data'     => array(),
    );

    $args = wp_parse_args( $args, $defaults );

    $result = $wpdb->insert(
        $table_name,
        array(
            'user_id'    => $user_id,
            'type'       => $type,
            'message'    => $message,
            'category'   => $args['category'],
            'link'       => $args['link'],
            'data'       => maybe_serialize( $args['data'] ),
            'is_read'    => 0,
            'created_at' => current_time( 'mysql' ),
        ),
        array( '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s' )
    );

    if ( $result ) {
        return $wpdb->insert_id;
    }

    return false;
}

/**
 * Get notification icon based on type
 *
 * @param string $type Notification type
 * @return string Icon HTML
 */
function oceanwp_get_notification_icon( $type ) {
    $icons = array(
        'announcement' => '<i class="fa fa-bullhorn"></i>',
        'comment'      => '<i class="fa fa-comment"></i>',
        'mention'      => '<i class="fa fa-at"></i>',
        'event'        => '<i class="fa fa-calendar"></i>',
        'like'         => '<i class="fa fa-heart"></i>',
        'document'     => '<i class="fa fa-file-alt"></i>',
        'message'      => '<i class="fa fa-envelope"></i>',
        'task'         => '<i class="fa fa-tasks"></i>',
        'alert'        => '<i class="fa fa-exclamation-triangle"></i>',
        'success'      => '<i class="fa fa-check-circle"></i>',
    );

    return isset( $icons[ $type ] ) ? $icons[ $type ] : '<i class="fa fa-bell"></i>';
}

/**
 * Create notifications table on theme activation
 */
function oceanwp_create_notifications_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'intranet_notifications';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        type varchar(50) NOT NULL,
        message text NOT NULL,
        category varchar(100),
        link varchar(255),
        data text,
        is_read tinyint(1) DEFAULT 0,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY user_id (user_id),
        KEY is_read (is_read),
        KEY type (type)
    ) {$charset_collate};";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

// Create table on theme switch
add_action( 'after_switch_theme', 'oceanwp_create_notifications_table' );

/**
 * AJAX handler to mark notification as read
 */
function oceanwp_ajax_mark_notification_read() {
    check_ajax_referer( 'intranet-notifications', 'nonce' );

    $notification_id = isset( $_POST['notification_id'] ) ? intval( $_POST['notification_id'] ) : 0;

    if ( $notification_id ) {
        $success = oceanwp_mark_notification_read( $notification_id );
        wp_send_json_success( array( 'marked' => $success ) );
    }

    wp_send_json_error();
}
add_action( 'wp_ajax_mark_notification_read', 'oceanwp_ajax_mark_notification_read' );

/**
 * AJAX handler to mark all notifications as read
 */
function oceanwp_ajax_mark_all_notifications_read() {
    check_ajax_referer( 'intranet-notifications', 'nonce' );

    $user_id = get_current_user_id();

    if ( $user_id ) {
        $success = oceanwp_mark_all_notifications_read( $user_id );
        wp_send_json_success( array( 'marked' => $success ) );
    }

    wp_send_json_error();
}
add_action( 'wp_ajax_mark_all_notifications_read', 'oceanwp_ajax_mark_all_notifications_read' );

/**
 * AJAX handler to delete notification
 */
function oceanwp_ajax_delete_notification() {
    check_ajax_referer( 'intranet-notifications', 'nonce' );

    $notification_id = isset( $_POST['notification_id'] ) ? intval( $_POST['notification_id'] ) : 0;

    if ( $notification_id ) {
        $success = oceanwp_delete_notification( $notification_id );
        wp_send_json_success( array( 'deleted' => $success ) );
    }

    wp_send_json_error();
}
add_action( 'wp_ajax_delete_notification', 'oceanwp_ajax_delete_notification' );

/**
 * AJAX handler to get new notifications
 */
function oceanwp_ajax_get_new_notifications() {
    check_ajax_referer( 'intranet-notifications', 'nonce' );

    $user_id = get_current_user_id();
    $last_check = isset( $_POST['last_check'] ) ? intval( $_POST['last_check'] ) : 0;

    if ( $user_id ) {
        $notifications = oceanwp_get_user_notifications( $user_id, array(
            'number' => 10,
            'status' => 'unread',
        ) );

        // Filter notifications newer than last check
        if ( $last_check ) {
            $notifications = array_filter( $notifications, function( $notification ) use ( $last_check ) {
                return $notification['time'] > $last_check;
            } );
        }

        wp_send_json_success( array(
            'notifications' => $notifications,
            'count'         => count( $notifications ),
        ) );
    }

    wp_send_json_error();
}
add_action( 'wp_ajax_get_new_notifications', 'oceanwp_ajax_get_new_notifications' );

<?php
/**
 * Notifications dropdown for header
 *
 * @package OceanWP WordPress theme
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get current user
$current_user = wp_get_current_user();
if ( ! $current_user->ID ) {
    return;
}

// Get user notifications (this would be from a custom notifications system)
$notifications = oceanwp_get_user_notifications( $current_user->ID );
$unread_count = oceanwp_get_unread_notifications_count( $current_user->ID );
?>

<div id="intranet-notifications" class="intranet-notifications-wrapper">
    <button class="notifications-toggle" aria-label="<?php _e( 'Notifications', 'oceanwp' ); ?>">
        <i class="fa fa-bell"></i>
        <?php if ( $unread_count > 0 ) : ?>
            <span class="notifications-badge"><?php echo $unread_count > 9 ? '9+' : $unread_count; ?></span>
        <?php endif; ?>
    </button>

    <div class="notifications-dropdown">
        <div class="notifications-header">
            <h3><?php _e( 'Notifications', 'oceanwp' ); ?></h3>
            <div class="notifications-actions">
                <button class="mark-all-read" title="<?php _e( 'Tout marquer comme lu', 'oceanwp' ); ?>">
                    <i class="fa fa-check-double"></i>
                </button>
                <button class="notifications-settings" title="<?php _e( 'Paramètres', 'oceanwp' ); ?>">
                    <i class="fa fa-cog"></i>
                </button>
            </div>
        </div>

        <div class="notifications-tabs">
            <button class="tab-btn active" data-tab="all">
                <?php _e( 'Toutes', 'oceanwp' ); ?>
            </button>
            <button class="tab-btn" data-tab="unread">
                <?php _e( 'Non lues', 'oceanwp' ); ?>
                <?php if ( $unread_count > 0 ) : ?>
                    <span class="tab-badge"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </button>
            <button class="tab-btn" data-tab="mentions">
                <?php _e( 'Mentions', 'oceanwp' ); ?>
            </button>
        </div>

        <div class="notifications-content">
            <div class="notifications-list" data-tab-content="all">
                <?php if ( ! empty( $notifications ) ) : ?>
                    <?php foreach ( $notifications as $notification ) : ?>
                        <div class="notification-item <?php echo ! $notification['read'] ? 'unread' : ''; ?>"
                             data-id="<?php echo esc_attr( $notification['id'] ); ?>">

                            <div class="notification-icon <?php echo esc_attr( $notification['type'] ); ?>">
                                <?php echo oceanwp_get_notification_icon( $notification['type'] ); ?>
                            </div>

                            <div class="notification-content">
                                <div class="notification-text">
                                    <?php echo wp_kses_post( $notification['message'] ); ?>
                                </div>
                                <div class="notification-meta">
                                    <span class="notification-time">
                                        <i class="fa fa-clock"></i>
                                        <?php echo human_time_diff( $notification['time'], current_time( 'timestamp' ) ); ?>
                                        <?php _e( 'ago', 'oceanwp' ); ?>
                                    </span>
                                    <?php if ( ! empty( $notification['category'] ) ) : ?>
                                        <span class="notification-category">
                                            <?php echo esc_html( $notification['category'] ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ( ! $notification['read'] ) : ?>
                                <button class="mark-read" title="<?php _e( 'Marquer comme lu', 'oceanwp' ); ?>">
                                    <i class="fa fa-check"></i>
                                </button>
                            <?php endif; ?>

                            <button class="delete-notification" title="<?php _e( 'Supprimer', 'oceanwp' ); ?>">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="no-notifications">
                        <i class="fa fa-bell-slash"></i>
                        <p><?php _e( 'Aucune notification pour le moment', 'oceanwp' ); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="notifications-list" data-tab-content="unread" style="display: none;">
                <!-- Unread notifications filtered by JavaScript -->
            </div>

            <div class="notifications-list" data-tab-content="mentions" style="display: none;">
                <!-- Mention notifications filtered by JavaScript -->
            </div>
        </div>

        <div class="notifications-footer">
            <a href="<?php echo esc_url( home_url( '/notifications' ) ); ?>" class="view-all-notifications">
                <?php _e( 'Voir toutes les notifications', 'oceanwp' ); ?>
                <i class="fa fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

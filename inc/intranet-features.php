<?php
/**
 * Intranet Features
 * Register and enqueue modern intranet features
 *
 * @package OceanWP WordPress theme
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue intranet scripts and styles
 */
function oceanwp_enqueue_intranet_assets() {
    $theme_version = '4.1.3'; // Match theme version

    // Enqueue intranet dashboard scripts
    wp_enqueue_script(
        'intranet-dashboard',
        get_template_directory_uri() . '/assets/src/js/intranet-dashboard.js',
        array( 'jquery' ),
        $theme_version,
        true
    );

    // Enqueue notifications scripts
    wp_enqueue_script(
        'intranet-notifications',
        get_template_directory_uri() . '/assets/src/js/intranet-notifications.js',
        array( 'jquery' ),
        $theme_version,
        true
    );

    // Enqueue dark mode scripts
    wp_enqueue_script(
        'dark-mode',
        get_template_directory_uri() . '/assets/src/js/dark-mode.js',
        array( 'jquery' ),
        $theme_version,
        true
    );

    // Localize scripts with AJAX data
    wp_localize_script(
        'intranet-notifications',
        'oceanwpLocalize',
        array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'intranet-notifications' ),
        )
    );

    // Add inline script for current user data
    if ( is_user_logged_in() ) {
        $current_user = wp_get_current_user();
        $user_data = array(
            'id'    => $current_user->ID,
            'name'  => $current_user->display_name,
            'email' => $current_user->user_email,
            'role'  => implode( ', ', $current_user->roles ),
        );

        wp_add_inline_script(
            'intranet-dashboard',
            'var intranetUser = ' . wp_json_encode( $user_data ) . ';',
            'before'
        );
    }
}
add_action( 'wp_enqueue_scripts', 'oceanwp_enqueue_intranet_assets' );

/**
 * Add dark mode toggle to header
 */
function oceanwp_add_dark_mode_toggle() {
    ?>
    <!-- Dark Mode Toggle will be added by JavaScript -->
    <?php
}
add_action( 'wp_footer', 'oceanwp_add_dark_mode_toggle' );

/**
 * Add notifications icon to header
 */
function oceanwp_add_notifications_to_header() {
    if ( is_user_logged_in() ) {
        get_template_part( 'partials/header/notifications' );
    }
}
add_action( 'ocean_after_header_inner', 'oceanwp_add_notifications_to_header' );

/**
 * Register custom dashboard page
 */
function oceanwp_register_dashboard_page() {
    // Create dashboard page on theme activation if it doesn't exist
    $dashboard_page = get_page_by_path( 'dashboard' );

    if ( ! $dashboard_page ) {
        $dashboard_page_id = wp_insert_post(
            array(
                'post_title'   => __( 'Dashboard', 'oceanwp' ),
                'post_name'    => 'dashboard',
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => '',
                'page_template' => 'templates/dashboard.php',
            )
        );

        // Update page template
        if ( $dashboard_page_id && ! is_wp_error( $dashboard_page_id ) ) {
            update_post_meta( $dashboard_page_id, '_wp_page_template', 'templates/dashboard.php' );
        }
    }
}
add_action( 'after_switch_theme', 'oceanwp_register_dashboard_page' );

/**
 * Register employee directory page
 */
function oceanwp_register_employee_directory_page() {
    // Create employee directory page on theme activation if it doesn't exist
    $directory_page = get_page_by_path( 'annuaire' );

    if ( ! $directory_page ) {
        $directory_page_id = wp_insert_post(
            array(
                'post_title'    => __( 'Annuaire des Employés', 'oceanwp' ),
                'post_name'     => 'annuaire',
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_content'  => '',
                'page_template' => 'templates/employee-directory.php',
            )
        );

        // Update page template
        if ( $directory_page_id && ! is_wp_error( $directory_page_id ) ) {
            update_post_meta( $directory_page_id, '_wp_page_template', 'templates/employee-directory.php' );
        }
    }
}
add_action( 'after_switch_theme', 'oceanwp_register_employee_directory_page' );

/**
 * Add custom body classes for intranet features
 */
function oceanwp_add_intranet_body_classes( $classes ) {
    // Add intranet class
    if ( is_user_logged_in() ) {
        $classes[] = 'intranet-enabled';
    }

    // Add dashboard class
    if ( is_page_template( 'templates/dashboard.php' ) ) {
        $classes[] = 'page-dashboard';
    }

    // Add employee directory class
    if ( is_page_template( 'templates/employee-directory.php' ) ) {
        $classes[] = 'page-employee-directory';
    }

    return $classes;
}
add_filter( 'body_class', 'oceanwp_add_intranet_body_classes' );

/**
 * Add custom admin bar items for intranet
 */
function oceanwp_add_intranet_admin_bar_items( $wp_admin_bar ) {
    if ( ! is_user_logged_in() ) {
        return;
    }

    // Add dashboard link
    $wp_admin_bar->add_node(
        array(
            'id'     => 'intranet-dashboard',
            'title'  => '<span class="ab-icon dashicons-dashboard"></span>' . __( 'Dashboard', 'oceanwp' ),
            'href'   => home_url( '/dashboard' ),
            'parent' => 'site-name',
        )
    );

    // Add employee directory link
    $wp_admin_bar->add_node(
        array(
            'id'     => 'employee-directory',
            'title'  => '<span class="ab-icon dashicons-groups"></span>' . __( 'Annuaire', 'oceanwp' ),
            'href'   => home_url( '/annuaire' ),
            'parent' => 'site-name',
        )
    );

    // Add dark mode toggle
    $wp_admin_bar->add_node(
        array(
            'id'     => 'dark-mode-toggle',
            'title'  => '<span class="ab-icon dashicons-admin-appearance"></span>' . __( 'Mode Sombre', 'oceanwp' ),
            'href'   => '#',
            'meta'   => array(
                'class'   => 'dark-mode-admin-bar-toggle',
                'onclick' => 'if(window.DarkMode){window.DarkMode.toggle();return false;}',
            ),
        )
    );
}
add_action( 'admin_bar_menu', 'oceanwp_add_intranet_admin_bar_items', 100 );

/**
 * Add custom menu items for intranet
 */
function oceanwp_add_intranet_menu_items() {
    // Register custom menu location for intranet features
    register_nav_menu( 'intranet-menu', __( 'Intranet Menu', 'oceanwp' ) );
}
add_action( 'after_setup_theme', 'oceanwp_add_intranet_menu_items' );

/**
 * Add welcome message on first login
 */
function oceanwp_show_welcome_message() {
    if ( is_user_logged_in() ) {
        $user_id = get_current_user_id();
        $shown = get_user_meta( $user_id, 'intranet_welcome_shown', true );

        if ( ! $shown ) {
            ?>
            <div class="intranet-welcome-message" style="display: none;">
                <div class="welcome-content">
                    <h2><?php _e( 'Bienvenue sur l\'intranet!', 'oceanwp' ); ?></h2>
                    <p><?php _e( 'Découvrez toutes les nouvelles fonctionnalités modernes:', 'oceanwp' ); ?></p>
                    <ul>
                        <li><?php _e( '📊 Dashboard interactif avec widgets personnalisables', 'oceanwp' ); ?></li>
                        <li><?php _e( '🔔 Notifications en temps réel', 'oceanwp' ); ?></li>
                        <li><?php _e( '👥 Annuaire d\'employés avec recherche avancée', 'oceanwp' ); ?></li>
                        <li><?php _e( '🌙 Mode sombre pour plus de confort', 'oceanwp' ); ?></li>
                    </ul>
                    <button class="btn-close-welcome"><?php _e( 'Commencer', 'oceanwp' ); ?></button>
                </div>
            </div>
            <script>
                jQuery(function($) {
                    $('.intranet-welcome-message').fadeIn();
                    $('.btn-close-welcome').on('click', function() {
                        $('.intranet-welcome-message').fadeOut();
                        $.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                            action: 'dismiss_welcome_message',
                            nonce: '<?php echo wp_create_nonce( 'intranet-welcome' ); ?>'
                        });
                    });
                });
            </script>
            <?php
        }
    }
}
add_action( 'wp_footer', 'oceanwp_show_welcome_message' );

/**
 * AJAX handler to dismiss welcome message
 */
function oceanwp_dismiss_welcome_message() {
    check_ajax_referer( 'intranet-welcome', 'nonce' );

    $user_id = get_current_user_id();
    if ( $user_id ) {
        update_user_meta( $user_id, 'intranet_welcome_shown', true );
        wp_send_json_success();
    }

    wp_send_json_error();
}
add_action( 'wp_ajax_dismiss_welcome_message', 'oceanwp_dismiss_welcome_message' );

/**
 * Add custom CSS for welcome message
 */
function oceanwp_welcome_message_styles() {
    ?>
    <style>
        .intranet-welcome-message {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .welcome-content {
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .welcome-content h2 {
            font-size: 32px;
            margin: 0 0 20px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-content p {
            font-size: 18px;
            color: #6b7280;
            margin-bottom: 20px;
        }

        .welcome-content ul {
            text-align: left;
            list-style: none;
            padding: 0;
            margin: 0 0 30px 0;
        }

        .welcome-content ul li {
            padding: 10px 0;
            font-size: 16px;
            color: #374151;
        }

        .btn-close-welcome {
            padding: 15px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-close-welcome:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
        }
    </style>
    <?php
}
add_action( 'wp_head', 'oceanwp_welcome_message_styles' );

/**
 * Add meta description for better SEO
 */
function oceanwp_add_intranet_meta() {
    if ( is_page_template( 'templates/dashboard.php' ) ) {
        echo '<meta name="description" content="' . esc_attr__( 'Dashboard interactif de l\'intranet avec widgets et statistiques en temps réel', 'oceanwp' ) . '">';
    }

    if ( is_page_template( 'templates/employee-directory.php' ) ) {
        echo '<meta name="description" content="' . esc_attr__( 'Annuaire des employés avec recherche avancée et informations de contact', 'oceanwp' ) . '">';
    }
}
add_action( 'wp_head', 'oceanwp_add_intranet_meta' );

<?php
/**
 * Template Name: Intranet Dashboard
 * Description: Modern dashboard for intranet with interactive widgets
 *
 * @package OceanWP WordPress theme
 */

get_header(); ?>

<div id="intranet-dashboard" class="content-area">
    <div class="container">

        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="welcome-section">
                <h1 class="dashboard-title">
                    <?php
                    $current_user = wp_get_current_user();
                    printf( __( 'Bienvenue, %s!', 'oceanwp' ), esc_html( $current_user->display_name ) );
                    ?>
                </h1>
                <p class="dashboard-subtitle">
                    <?php echo date_i18n( 'l j F Y' ); ?>
                </p>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <button class="quick-action-btn" id="new-announcement">
                    <i class="fa fa-bullhorn"></i>
                    <span><?php _e( 'Nouvelle annonce', 'oceanwp' ); ?></span>
                </button>
                <button class="quick-action-btn" id="new-document">
                    <i class="fa fa-file-alt"></i>
                    <span><?php _e( 'Nouveau document', 'oceanwp' ); ?></span>
                </button>
                <button class="quick-action-btn" id="new-event">
                    <i class="fa fa-calendar-plus"></i>
                    <span><?php _e( 'Nouvel événement', 'oceanwp' ); ?></span>
                </button>
            </div>
        </div>

        <!-- Dashboard Widgets Grid -->
        <div class="dashboard-grid">

            <!-- Statistics Widget -->
            <div class="dashboard-widget stats-widget">
                <div class="widget-header">
                    <h3><i class="fa fa-chart-line"></i> <?php _e( 'Statistiques', 'oceanwp' ); ?></h3>
                </div>
                <div class="widget-content">
                    <div class="stats-grid">
                        <div class="stat-card stat-users">
                            <div class="stat-icon">
                                <i class="fa fa-users"></i>
                            </div>
                            <div class="stat-details">
                                <span class="stat-value" data-count="<?php echo count_users()['total_users']; ?>">0</span>
                                <span class="stat-label"><?php _e( 'Utilisateurs actifs', 'oceanwp' ); ?></span>
                            </div>
                        </div>

                        <div class="stat-card stat-documents">
                            <div class="stat-icon">
                                <i class="fa fa-file-alt"></i>
                            </div>
                            <div class="stat-details">
                                <span class="stat-value" data-count="<?php echo wp_count_posts( 'page' )->publish; ?>">0</span>
                                <span class="stat-label"><?php _e( 'Documents', 'oceanwp' ); ?></span>
                            </div>
                        </div>

                        <div class="stat-card stat-posts">
                            <div class="stat-icon">
                                <i class="fa fa-newspaper"></i>
                            </div>
                            <div class="stat-details">
                                <span class="stat-value" data-count="<?php echo wp_count_posts()->publish; ?>">0</span>
                                <span class="stat-label"><?php _e( 'Actualités', 'oceanwp' ); ?></span>
                            </div>
                        </div>

                        <div class="stat-card stat-events">
                            <div class="stat-icon">
                                <i class="fa fa-calendar-check"></i>
                            </div>
                            <div class="stat-details">
                                <span class="stat-value" data-count="12">0</span>
                                <span class="stat-label"><?php _e( 'Événements à venir', 'oceanwp' ); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Announcements Widget -->
            <div class="dashboard-widget announcements-widget">
                <div class="widget-header">
                    <h3><i class="fa fa-bullhorn"></i> <?php _e( 'Annonces récentes', 'oceanwp' ); ?></h3>
                    <a href="#" class="view-all"><?php _e( 'Voir tout', 'oceanwp' ); ?></a>
                </div>
                <div class="widget-content">
                    <div class="announcements-list">
                        <?php
                        $announcements = get_posts( array(
                            'post_type' => 'post',
                            'posts_per_page' => 5,
                            'category_name' => 'annonces'
                        ) );

                        if ( $announcements ) :
                            foreach ( $announcements as $announcement ) : ?>
                                <div class="announcement-item">
                                    <div class="announcement-icon">
                                        <i class="fa fa-bullhorn"></i>
                                    </div>
                                    <div class="announcement-content">
                                        <h4 class="announcement-title">
                                            <a href="<?php echo get_permalink( $announcement->ID ); ?>">
                                                <?php echo esc_html( $announcement->post_title ); ?>
                                            </a>
                                        </h4>
                                        <p class="announcement-excerpt">
                                            <?php echo wp_trim_words( $announcement->post_content, 15 ); ?>
                                        </p>
                                        <span class="announcement-date">
                                            <i class="fa fa-clock"></i>
                                            <?php echo human_time_diff( strtotime( $announcement->post_date ), current_time( 'timestamp' ) ); ?>
                                            <?php _e( 'ago', 'oceanwp' ); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach;
                        else : ?>
                            <p class="no-items"><?php _e( 'Aucune annonce pour le moment.', 'oceanwp' ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Calendar Widget -->
            <div class="dashboard-widget calendar-widget">
                <div class="widget-header">
                    <h3><i class="fa fa-calendar-alt"></i> <?php _e( 'Calendrier', 'oceanwp' ); ?></h3>
                </div>
                <div class="widget-content">
                    <div id="intranet-calendar"></div>

                    <div class="upcoming-events">
                        <h4><?php _e( 'Événements à venir', 'oceanwp' ); ?></h4>
                        <div class="events-list">
                            <div class="event-item">
                                <div class="event-date">
                                    <span class="day">15</span>
                                    <span class="month">Nov</span>
                                </div>
                                <div class="event-details">
                                    <h5><?php _e( 'Réunion d\'équipe', 'oceanwp' ); ?></h5>
                                    <span class="event-time"><i class="fa fa-clock"></i> 14:00 - 16:00</span>
                                </div>
                            </div>

                            <div class="event-item">
                                <div class="event-date">
                                    <span class="day">20</span>
                                    <span class="month">Nov</span>
                                </div>
                                <div class="event-details">
                                    <h5><?php _e( 'Formation interne', 'oceanwp' ); ?></h5>
                                    <span class="event-time"><i class="fa fa-clock"></i> 10:00 - 12:00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links Widget -->
            <div class="dashboard-widget links-widget">
                <div class="widget-header">
                    <h3><i class="fa fa-link"></i> <?php _e( 'Liens utiles', 'oceanwp' ); ?></h3>
                </div>
                <div class="widget-content">
                    <div class="quick-links-grid">
                        <a href="/annuaire" class="quick-link-card">
                            <i class="fa fa-address-book"></i>
                            <span><?php _e( 'Annuaire', 'oceanwp' ); ?></span>
                        </a>
                        <a href="/documents" class="quick-link-card">
                            <i class="fa fa-folder-open"></i>
                            <span><?php _e( 'Documents', 'oceanwp' ); ?></span>
                        </a>
                        <a href="/wiki" class="quick-link-card">
                            <i class="fa fa-book"></i>
                            <span><?php _e( 'Wiki', 'oceanwp' ); ?></span>
                        </a>
                        <a href="/support" class="quick-link-card">
                            <i class="fa fa-life-ring"></i>
                            <span><?php _e( 'Support', 'oceanwp' ); ?></span>
                        </a>
                        <a href="/ressources" class="quick-link-card">
                            <i class="fa fa-download"></i>
                            <span><?php _e( 'Ressources', 'oceanwp' ); ?></span>
                        </a>
                        <a href="/parametres" class="quick-link-card">
                            <i class="fa fa-cog"></i>
                            <span><?php _e( 'Paramètres', 'oceanwp' ); ?></span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Activity Feed Widget -->
            <div class="dashboard-widget activity-widget">
                <div class="widget-header">
                    <h3><i class="fa fa-stream"></i> <?php _e( 'Activité récente', 'oceanwp' ); ?></h3>
                </div>
                <div class="widget-content">
                    <div class="activity-feed">
                        <div class="activity-item">
                            <div class="activity-avatar">
                                <?php echo get_avatar( 1, 40 ); ?>
                            </div>
                            <div class="activity-details">
                                <p><strong>Jean Dupont</strong> a publié un nouveau document</p>
                                <span class="activity-time"><i class="fa fa-clock"></i> Il y a 2 heures</span>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-avatar">
                                <?php echo get_avatar( 2, 40 ); ?>
                            </div>
                            <div class="activity-details">
                                <p><strong>Marie Martin</strong> a mis à jour une page</p>
                                <span class="activity-time"><i class="fa fa-clock"></i> Il y a 4 heures</span>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-avatar">
                                <?php echo get_avatar( 3, 40 ); ?>
                            </div>
                            <div class="activity-details">
                                <p><strong>Pierre Durand</strong> a créé un événement</p>
                                <span class="activity-time"><i class="fa fa-clock"></i> Hier</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shortcuts Widget -->
            <div class="dashboard-widget shortcuts-widget">
                <div class="widget-header">
                    <h3><i class="fa fa-star"></i> <?php _e( 'Raccourcis favoris', 'oceanwp' ); ?></h3>
                </div>
                <div class="widget-content">
                    <div class="shortcuts-list">
                        <a href="#" class="shortcut-item">
                            <i class="fa fa-envelope"></i>
                            <span><?php _e( 'Messages', 'oceanwp' ); ?></span>
                            <span class="badge">3</span>
                        </a>
                        <a href="#" class="shortcut-item">
                            <i class="fa fa-tasks"></i>
                            <span><?php _e( 'Tâches', 'oceanwp' ); ?></span>
                            <span class="badge">7</span>
                        </a>
                        <a href="#" class="shortcut-item">
                            <i class="fa fa-comments"></i>
                            <span><?php _e( 'Discussions', 'oceanwp' ); ?></span>
                        </a>
                        <a href="#" class="shortcut-item">
                            <i class="fa fa-chart-bar"></i>
                            <span><?php _e( 'Rapports', 'oceanwp' ); ?></span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php get_footer(); ?>

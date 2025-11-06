<?php
/**
 * Template Name: Employee Directory
 * Description: Modern employee directory with advanced search and filtering
 *
 * @package OceanWP WordPress theme
 */

get_header(); ?>

<div id="employee-directory" class="content-area">
    <div class="container">

        <!-- Directory Header -->
        <div class="directory-header">
            <div class="header-content">
                <h1 class="directory-title">
                    <i class="fa fa-address-book"></i>
                    <?php _e( 'Annuaire des Employés', 'oceanwp' ); ?>
                </h1>
                <p class="directory-subtitle">
                    <?php
                    $user_count = count_users();
                    printf( __( '%s collaborateurs dans l\'organisation', 'oceanwp' ), $user_count['total_users'] );
                    ?>
                </p>
            </div>

            <?php if ( current_user_can( 'edit_users' ) ) : ?>
                <div class="directory-actions">
                    <button class="btn-primary" id="add-employee">
                        <i class="fa fa-user-plus"></i>
                        <?php _e( 'Ajouter un employé', 'oceanwp' ); ?>
                    </button>
                    <button class="btn-secondary" id="export-directory">
                        <i class="fa fa-download"></i>
                        <?php _e( 'Exporter', 'oceanwp' ); ?>
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Search and Filters -->
        <div class="directory-search-wrapper">
            <div class="search-bar">
                <i class="fa fa-search"></i>
                <input
                    type="text"
                    id="employee-search"
                    class="search-input"
                    placeholder="<?php _e( 'Rechercher par nom, département, poste...', 'oceanwp' ); ?>"
                    autocomplete="off"
                />
                <button class="clear-search" id="clear-search" style="display: none;">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="filters-row">
                <div class="filter-group">
                    <label><?php _e( 'Département', 'oceanwp' ); ?></label>
                    <select id="filter-department" class="filter-select">
                        <option value=""><?php _e( 'Tous', 'oceanwp' ); ?></option>
                        <option value="direction"><?php _e( 'Direction', 'oceanwp' ); ?></option>
                        <option value="rh"><?php _e( 'Ressources Humaines', 'oceanwp' ); ?></option>
                        <option value="it"><?php _e( 'Informatique', 'oceanwp' ); ?></option>
                        <option value="marketing"><?php _e( 'Marketing', 'oceanwp' ); ?></option>
                        <option value="ventes"><?php _e( 'Ventes', 'oceanwp' ); ?></option>
                        <option value="finance"><?php _e( 'Finance', 'oceanwp' ); ?></option>
                    </select>
                </div>

                <div class="filter-group">
                    <label><?php _e( 'Localisation', 'oceanwp' ); ?></label>
                    <select id="filter-location" class="filter-select">
                        <option value=""><?php _e( 'Toutes', 'oceanwp' ); ?></option>
                        <option value="paris"><?php _e( 'Paris', 'oceanwp' ); ?></option>
                        <option value="lyon"><?php _e( 'Lyon', 'oceanwp' ); ?></option>
                        <option value="marseille"><?php _e( 'Marseille', 'oceanwp' ); ?></option>
                        <option value="remote"><?php _e( 'Télétravail', 'oceanwp' ); ?></option>
                    </select>
                </div>

                <div class="filter-group">
                    <label><?php _e( 'Rôle', 'oceanwp' ); ?></label>
                    <select id="filter-role" class="filter-select">
                        <option value=""><?php _e( 'Tous', 'oceanwp' ); ?></option>
                        <option value="manager"><?php _e( 'Manager', 'oceanwp' ); ?></option>
                        <option value="senior"><?php _e( 'Senior', 'oceanwp' ); ?></option>
                        <option value="junior"><?php _e( 'Junior', 'oceanwp' ); ?></option>
                    </select>
                </div>

                <div class="view-switcher">
                    <button class="view-btn active" data-view="grid" title="<?php _e( 'Vue grille', 'oceanwp' ); ?>">
                        <i class="fa fa-th"></i>
                    </button>
                    <button class="view-btn" data-view="list" title="<?php _e( 'Vue liste', 'oceanwp' ); ?>">
                        <i class="fa fa-list"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Alphabet Quick Filter -->
        <div class="alphabet-filter">
            <button class="alphabet-btn active" data-letter="all"><?php _e( 'Tous', 'oceanwp' ); ?></button>
            <?php foreach ( range( 'A', 'Z' ) as $letter ) : ?>
                <button class="alphabet-btn" data-letter="<?php echo $letter; ?>"><?php echo $letter; ?></button>
            <?php endforeach; ?>
        </div>

        <!-- Employee Grid -->
        <div class="employees-grid" data-view="grid">
            <?php
            $users = get_users( array(
                'orderby' => 'display_name',
                'order'   => 'ASC',
            ) );

            if ( $users ) :
                foreach ( $users as $user ) :
                    $user_meta = get_user_meta( $user->ID );
                    $department = isset( $user_meta['department'][0] ) ? $user_meta['department'][0] : 'it';
                    $position = isset( $user_meta['position'][0] ) ? $user_meta['position'][0] : 'Développeur';
                    $location = isset( $user_meta['location'][0] ) ? $user_meta['location'][0] : 'paris';
                    $phone = isset( $user_meta['phone'][0] ) ? $user_meta['phone'][0] : '';
                    $bio = isset( $user_meta['description'][0] ) ? $user_meta['description'][0] : '';
                    ?>

                    <div class="employee-card"
                         data-name="<?php echo esc_attr( $user->display_name ); ?>"
                         data-department="<?php echo esc_attr( $department ); ?>"
                         data-location="<?php echo esc_attr( $location ); ?>"
                         data-position="<?php echo esc_attr( $position ); ?>"
                         data-user-id="<?php echo esc_attr( $user->ID ); ?>">

                        <div class="card-header">
                            <div class="avatar-wrapper">
                                <?php echo get_avatar( $user->ID, 80 ); ?>
                                <span class="status-indicator online"></span>
                            </div>
                            <div class="quick-actions">
                                <button class="action-btn" title="<?php _e( 'Envoyer un message', 'oceanwp' ); ?>">
                                    <i class="fa fa-envelope"></i>
                                </button>
                                <button class="action-btn" title="<?php _e( 'Appeler', 'oceanwp' ); ?>">
                                    <i class="fa fa-phone"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <h3 class="employee-name"><?php echo esc_html( $user->display_name ); ?></h3>
                            <p class="employee-position"><?php echo esc_html( $position ); ?></p>

                            <div class="employee-meta">
                                <div class="meta-item">
                                    <i class="fa fa-building"></i>
                                    <span><?php echo esc_html( ucfirst( $department ) ); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fa fa-map-marker-alt"></i>
                                    <span><?php echo esc_html( ucfirst( $location ) ); ?></span>
                                </div>
                            </div>

                            <div class="employee-contact">
                                <a href="mailto:<?php echo esc_attr( $user->user_email ); ?>" class="contact-item">
                                    <i class="fa fa-envelope"></i>
                                    <span><?php echo esc_html( $user->user_email ); ?></span>
                                </a>
                                <?php if ( $phone ) : ?>
                                    <a href="tel:<?php echo esc_attr( $phone ); ?>" class="contact-item">
                                        <i class="fa fa-phone"></i>
                                        <span><?php echo esc_html( $phone ); ?></span>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <?php if ( $bio ) : ?>
                                <p class="employee-bio"><?php echo esc_html( wp_trim_words( $bio, 15 ) ); ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="card-footer">
                            <button class="btn-view-profile" data-user-id="<?php echo esc_attr( $user->ID ); ?>">
                                <?php _e( 'Voir le profil', 'oceanwp' ); ?>
                                <i class="fa fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                <?php endforeach;
            else : ?>
                <div class="no-employees">
                    <i class="fa fa-users-slash"></i>
                    <p><?php _e( 'Aucun employé trouvé', 'oceanwp' ); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Loading Spinner -->
        <div class="directory-loading" style="display: none;">
            <div class="spinner"></div>
            <p><?php _e( 'Chargement...', 'oceanwp' ); ?></p>
        </div>

        <!-- No Results -->
        <div class="no-results" style="display: none;">
            <i class="fa fa-search"></i>
            <h3><?php _e( 'Aucun résultat', 'oceanwp' ); ?></h3>
            <p><?php _e( 'Essayez avec d\'autres critères de recherche', 'oceanwp' ); ?></p>
        </div>

    </div>
</div>

<!-- Employee Profile Modal -->
<div id="employee-modal" class="employee-modal">
    <div class="modal-overlay"></div>
    <div class="modal-container">
        <button class="modal-close">
            <i class="fa fa-times"></i>
        </button>
        <div class="modal-content">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<?php get_footer(); ?>

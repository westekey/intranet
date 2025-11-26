<?php
/**
 * Custom Modern Styles & Scripts Enqueue
 *
 * Ce fichier permet d'intégrer automatiquement les fichiers CSS et JavaScript modernes
 * dans le thème WordPress OceanWP.
 *
 * INSTRUCTIONS D'INSTALLATION :
 *
 * Option 1 : Ajouter dans functions.php du thème enfant
 * Copiez tout le contenu de ce fichier (sauf les balises PHP d'ouverture/fermeture)
 * et collez-le à la fin de votre fichier functions.php du thème enfant.
 *
 * Option 2 : Créer un plugin custom
 * 1. Créez un dossier : wp-content/plugins/custom-modern-styles/
 * 2. Copiez ce fichier dedans et renommez-le en custom-modern-styles.php
 * 3. Activez le plugin dans WordPress
 *
 * @package CustomModern
 * @version 1.0.0
 */

// Sécurité : empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue Custom Modern Styles and Scripts
 *
 * Cette fonction charge les fichiers CSS et JavaScript modernes
 * sur le front-end du site.
 */
function custom_modern_enqueue_assets() {
    // Version du fichier (changer pour forcer le rechargement du cache)
    $version = '1.0.0';

    // Charger le CSS moderne
    wp_enqueue_style(
        'custom-modern-styles',
        get_template_directory_uri() . '/assets/css/custom-modern-styles.css',
        array(), // Dépendances (vide = aucune)
        $version,
        'all' // Media type
    );

    // Charger le JavaScript moderne
    wp_enqueue_script(
        'custom-modern-interactions',
        get_template_directory_uri() . '/assets/src/js/custom-modern-interactions.js',
        array(), // Pas de dépendance jQuery nécessaire
        $version,
        true // Charger dans le footer pour de meilleures performances
    );

    // Optionnel : Ajouter des variables PHP accessibles depuis JavaScript
    $custom_vars = array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('custom-modern-nonce'),
        'siteUrl' => get_site_url(),
        'themePath' => get_template_directory_uri(),
    );

    wp_localize_script('custom-modern-interactions', 'customModernVars', $custom_vars);
}

// Priorité 20 : Charger après le thème principal mais avant certains plugins
add_action('wp_enqueue_scripts', 'custom_modern_enqueue_assets', 20);

/**
 * Enqueue Custom Modern Styles for Admin (optionnel)
 *
 * Si vous voulez appliquer les styles dans l'admin WordPress également.
 */
function custom_modern_enqueue_admin_assets($hook) {
    // Ne charger que sur certaines pages admin si nécessaire
    // if ($hook != 'post.php' && $hook != 'post-new.php') {
    //     return;
    // }

    $version = '1.0.0';

    wp_enqueue_style(
        'custom-modern-admin-styles',
        get_template_directory_uri() . '/assets/css/custom-modern-styles.css',
        array(),
        $version
    );
}

// Décommentez la ligne suivante si vous voulez les styles dans l'admin
// add_action('admin_enqueue_scripts', 'custom_modern_enqueue_admin_assets');

/**
 * Ajouter un attribut defer ou async au script JavaScript (optionnel)
 *
 * Améliore les performances en chargeant le JS de manière asynchrone.
 */
function custom_modern_add_async_attribute($tag, $handle) {
    // Liste des scripts à charger en async
    $async_scripts = array(
        'custom-modern-interactions'
    );

    if (in_array($handle, $async_scripts)) {
        return str_replace(' src', ' defer src', $tag);
        // Ou utilisez 'async' au lieu de 'defer' selon vos besoins
        // return str_replace(' src', ' async src', $tag);
    }

    return $tag;
}

add_filter('script_loader_tag', 'custom_modern_add_async_attribute', 10, 2);

/**
 * Ajouter une classe au body pour cibler spécifiquement les pages
 * avec les effets modernes activés (optionnel)
 */
function custom_modern_body_class($classes) {
    $classes[] = 'custom-modern-enabled';

    // Ajouter des classes conditionnelles
    if (is_front_page()) {
        $classes[] = 'modern-home';
    }

    if (is_single()) {
        $classes[] = 'modern-single';
    }

    if (is_archive() || is_category() || is_tag()) {
        $classes[] = 'modern-archive';
    }

    return $classes;
}

add_filter('body_class', 'custom_modern_body_class');

/**
 * Ajouter des métadonnées dans le <head> pour les effets CSS
 */
function custom_modern_head_meta() {
    ?>
    <!-- Custom Modern Styles Meta -->
    <meta name="color-scheme" content="light dark">
    <style id="custom-modern-inline">
        /* Préchargement des effets critiques */
        .page-header,
        .blog-entry-inner,
        h6 {
            will-change: transform, opacity;
        }
    </style>
    <?php
}

add_action('wp_head', 'custom_modern_head_meta', 1);

/**
 * Précharger les fichiers critiques (optionnel)
 *
 * Améliore les performances en indiquant au navigateur de précharger
 * les ressources importantes.
 */
function custom_modern_preload_assets() {
    ?>
    <!-- Preload Custom Modern Assets -->
    <link rel="preload" href="<?php echo get_template_directory_uri(); ?>/assets/css/custom-modern-styles.css" as="style">
    <link rel="preload" href="<?php echo get_template_directory_uri(); ?>/assets/src/js/custom-modern-interactions.js" as="script">
    <?php
}

// Décommentez si vous voulez activer le préchargement
// add_action('wp_head', 'custom_modern_preload_assets', 0);

/**
 * Options de customization via l'API Customizer WordPress (optionnel)
 *
 * Permet aux utilisateurs de modifier les couleurs via l'interface WordPress.
 */
function custom_modern_customizer_settings($wp_customize) {
    // Ajouter une section
    $wp_customize->add_section('custom_modern_section', array(
        'title' => __('Styles Modernes', 'oceanwp'),
        'priority' => 30,
    ));

    // Couleur principale
    $wp_customize->add_setting('custom_modern_primary_color', array(
        'default' => '#c8d9e6',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control(
        $wp_customize,
        'custom_modern_primary_color',
        array(
            'label' => __('Couleur Principale', 'oceanwp'),
            'section' => 'custom_modern_section',
            'settings' => 'custom_modern_primary_color',
        )
    ));

    // Activer/Désactiver les effets JavaScript
    $wp_customize->add_setting('custom_modern_enable_js', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));

    $wp_customize->add_control('custom_modern_enable_js', array(
        'label' => __('Activer les effets JavaScript', 'oceanwp'),
        'section' => 'custom_modern_section',
        'type' => 'checkbox',
    ));
}

add_action('customize_register', 'custom_modern_customizer_settings');

/**
 * Sortir les variables CSS personnalisées depuis le Customizer
 */
function custom_modern_output_customizer_css() {
    $primary_color = get_theme_mod('custom_modern_primary_color', '#c8d9e6');

    ?>
    <style id="custom-modern-customizer-css">
        :root {
            --color-primary: <?php echo esc_attr($primary_color); ?>;
            --color-primary-light: <?php echo esc_attr(custom_modern_lighten_color($primary_color, 10)); ?>;
            --color-primary-dark: <?php echo esc_attr(custom_modern_darken_color($primary_color, 10)); ?>;
        }
    </style>
    <?php
}

add_action('wp_head', 'custom_modern_output_customizer_css', 100);

/**
 * Fonction utilitaire : Éclaircir une couleur hexadécimale
 */
function custom_modern_lighten_color($hex, $percent) {
    $hex = str_replace('#', '', $hex);

    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }

    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    $r = min(255, $r + (255 - $r) * $percent / 100);
    $g = min(255, $g + (255 - $g) * $percent / 100);
    $b = min(255, $b + (255 - $b) * $percent / 100);

    return '#' . sprintf('%02x%02x%02x', $r, $g, $b);
}

/**
 * Fonction utilitaire : Assombrir une couleur hexadécimale
 */
function custom_modern_darken_color($hex, $percent) {
    $hex = str_replace('#', '', $hex);

    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }

    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    $r = max(0, $r - $r * $percent / 100);
    $g = max(0, $g - $g * $percent / 100);
    $b = max(0, $b - $b * $percent / 100);

    return '#' . sprintf('%02x%02x%02x', $r, $g, $b);
}

/**
 * Conditionner le chargement du JavaScript selon les préférences
 */
function custom_modern_conditional_js_loading() {
    $enable_js = get_theme_mod('custom_modern_enable_js', true);

    if (!$enable_js) {
        wp_dequeue_script('custom-modern-interactions');
    }
}

add_action('wp_enqueue_scripts', 'custom_modern_conditional_js_loading', 21);

/**
 * Ajouter un widget dans le dashboard WordPress pour les infos (optionnel)
 */
function custom_modern_dashboard_widget() {
    wp_add_dashboard_widget(
        'custom_modern_info',
        '🎨 Styles Modernes Activés',
        'custom_modern_dashboard_widget_content'
    );
}

function custom_modern_dashboard_widget_content() {
    ?>
    <div class="custom-modern-widget">
        <p><strong>✅ Styles et scripts modernes activés avec succès !</strong></p>
        <ul>
            <li>✨ Animations CSS avancées</li>
            <li>🎭 Effets JavaScript interactifs</li>
            <li>📱 Design responsive</li>
            <li>♿ Accessibilité optimisée</li>
        </ul>
        <p>
            <a href="<?php echo admin_url('customize.php?autofocus[section]=custom_modern_section'); ?>" class="button button-primary">
                Personnaliser les couleurs
            </a>
        </p>
        <p style="font-size: 12px; color: #666;">
            Version 1.0.0 | <a href="<?php echo get_template_directory_uri(); ?>/MODERN-CUSTOMIZATION-README.md" target="_blank">Documentation</a>
        </p>
    </div>
    <?php
}

add_action('wp_dashboard_setup', 'custom_modern_dashboard_widget');

/**
 * Notification de succès dans l'admin après activation
 */
function custom_modern_admin_notice() {
    $screen = get_current_screen();

    if ($screen->id === 'dashboard') {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <strong>🎨 Styles Modernes :</strong>
                Les fichiers CSS et JavaScript personnalisés sont actifs.
                <a href="<?php echo get_template_directory_uri(); ?>/MODERN-CUSTOMIZATION-README.md" target="_blank">
                    Voir la documentation
                </a>
            </p>
        </div>
        <?php
    }
}

// Décommentez pour afficher la notification (une fois seulement)
// add_action('admin_notices', 'custom_modern_admin_notice');

/**
 * ============================================
 * FIN DU FICHIER
 * ============================================
 *
 * Si vous ajoutez ce code dans functions.php, n'incluez pas
 * la balise de fermeture PHP ci-dessous.
 */

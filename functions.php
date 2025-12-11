<?php
/**
 * BeBeats Theme Functions
 * 
 * @package BeBeats
 */

/**
 * Enqueue styles and scripts
 */
function bebeats_enqueue_styles() {
    // Enqueue Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', array(), '5.3.2');
    
    // Enqueue fonts CSS
    wp_enqueue_style('bebeats-fonts', get_template_directory_uri() . '/fonts/fonts.css', array(), '1.0');
    
    // Enqueue theme stylesheet (style.css avec en-têtes WordPress contient déjà tout le CSS)
    wp_enqueue_style('bebeats-style', get_stylesheet_uri(), array('bootstrap-css', 'bebeats-fonts'), '1.0');
    
    // Enqueue page-specific styles
    if (is_page('connexion') || is_page('inscription') || is_page('welcome') || 
        is_page('inscription-fan') || is_page('inscription-fan-step1') || 
        is_page('inscription-fan-step2') || is_page('inscription-fan-step3') || 
        is_page('inscription-fan-step4') || is_page('inscription-artiste') ||
        is_page('inscription-artiste-step1') || is_page('inscription-artiste-step2') ||
        is_page('inscription-artiste-step3') || is_page('inscription-artiste-step4') ||
        is_page('inscription-artiste-step5')) {
        wp_enqueue_style('bebeats-auth-style', get_template_directory_uri() . '/css/auth.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('auth-start') || is_page('debut-authentification')) {
        wp_enqueue_style('bebeats-auth-start-style', get_template_directory_uri() . '/css/pages/auth-start.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('event')) {
        wp_enqueue_style('bebeats-event-style', get_template_directory_uri() . '/css/pages/event.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('contribuer')) {
        wp_enqueue_style('bebeats-contribuer-style', get_template_directory_uri() . '/css/pages/contribuer.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('recherche')) {
        wp_enqueue_style('bebeats-recherche-style', get_template_directory_uri() . '/css/pages/recherche.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('reglages')) {
        wp_enqueue_style('bebeats-reglages-style', get_template_directory_uri() . '/css/pages/reglages.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('profil')) {
        wp_enqueue_style('bebeats-profil-style', get_template_directory_uri() . '/css/pages/profil.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('feed')) {
        wp_enqueue_style('bebeats-feed-style', get_template_directory_uri() . '/css/pages/feed.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('resultats') || is_search()) {
        wp_enqueue_style('bebeats-resultats-style', get_template_directory_uri() . '/css/pages/resultats.css', array('bebeats-style'), '1.0');
    }
}

function bebeats_enqueue_scripts() {
    // Enqueue Bootstrap JS
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array(), '5.3.2', true);
    
    // Enqueue custom scripts
    wp_enqueue_script('bebeats-search', get_template_directory_uri() . '/js/search.js', array('jquery'), '1.0', true);
    
    // Passer l'URL d'accueil à JavaScript
    wp_localize_script('bebeats-search', 'bebeatsAjax', array(
        'homeUrl' => home_url('/')
    ));
    
    // Page-specific scripts
    
    if (is_page('reglages')) {
        wp_enqueue_script('bebeats-toggles', get_template_directory_uri() . '/js/toggles.js', array('jquery'), '1.0', true);
        wp_enqueue_script('bebeats-file-preview', get_template_directory_uri() . '/js/file-preview.js', array(), '1.0', true);
    }
    
    if (is_page('feed')) {
        wp_enqueue_script('bebeats-feed', get_template_directory_uri() . '/js/feed.js', array('jquery'), '1.0', true);
        wp_localize_script('bebeats-feed', 'bebeatsFeed', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bebeats_reaction_action')
        ));
    }
    
    
    // Enqueue file-preview.js pour les pages d'inscription
    if (is_page('inscription-fan-step3') || is_page('inscription-artiste-step3') || is_page('inscription-artiste-step4')) {
        wp_enqueue_script('bebeats-file-preview', get_template_directory_uri() . '/js/file-preview.js', array(), '1.0', true);
    }
}

// Hook into WordPress
add_action('wp_enqueue_scripts', 'bebeats_enqueue_styles');
add_action('wp_enqueue_scripts', 'bebeats_enqueue_scripts');

/**
 * Theme setup
 */
function bebeats_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    // Register navigation menus if needed
    register_nav_menus(array(
        'primary' => __('Menu Principal', 'bebeats'),
    ));
}
add_action('after_setup_theme', 'bebeats_setup');

/**
 * Traitement du formulaire de connexion
 */
function bebeats_handle_login() {
    // Vérifier le nonce
    if (!isset($_POST['bebeats_login_nonce']) || !wp_verify_nonce($_POST['bebeats_login_nonce'], 'bebeats_login_action')) {
        wp_redirect(home_url('/connexion?error=1'));
        exit;
    }
    
    // Récupérer les données du formulaire
    $username = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Vérifier que les champs ne sont pas vides
    if (empty($username) || empty($password)) {
        wp_redirect(home_url('/connexion?error=1'));
        exit;
    }
    
    // Tenter de connecter l'utilisateur
    // WordPress peut utiliser email ou username pour se connecter
    $user = wp_authenticate($username, $password);
    
    if (is_wp_error($user)) {
        // Erreur d'authentification
        wp_redirect(home_url('/connexion?error=1'));
        exit;
    } else {
        // Connexion réussie
        wp_clear_auth_cookie();
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true); // true pour "remember me"
        
        // Déclencher l'action de connexion WordPress
        do_action('wp_login', $user->user_login, $user);
        
        // Rediriger vers la page de bienvenue avec le nom d'utilisateur
        wp_redirect(home_url('/welcome?user=' . urlencode($user->user_login)));
        exit;
    }
}
add_action('admin_post_bebeats_login', 'bebeats_handle_login');
add_action('admin_post_nopriv_bebeats_login', 'bebeats_handle_login');

/**
 * Créer la table de base de données pour les inscriptions
 */
function bebeats_create_registration_table() {
    global $wpdb;
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    $table_name = $wpdb->prefix . 'bebeats_registrations';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        registration_key varchar(100) NOT NULL,
        user_type varchar(20) NOT NULL DEFAULT 'fan',
        email varchar(255) NOT NULL,
        username varchar(100) NOT NULL,
        password_hash varchar(255) NOT NULL,
        genres text,
        profile_photo varchar(255),
        banner varchar(255),
        description text,
        sounds text,
        step_completed int(1) DEFAULT 1,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY registration_key (registration_key),
        KEY email (email),
        KEY username (username)
    ) $charset_collate;";
    
    dbDelta($sql);
    
    // Ajouter la colonne sounds si elle n'existe pas déjà (pour les mises à jour)
    $column_exists = $wpdb->get_results($wpdb->prepare(
        "SHOW COLUMNS FROM $table_name LIKE %s",
        'sounds'
    ));
    
    if (empty($column_exists)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN sounds text AFTER description");
    }
}

// Créer la table à l'activation du thème
add_action('after_switch_theme', 'bebeats_create_registration_table');
add_action('after_switch_theme', 'bebeats_create_posts_table');
add_action('after_switch_theme', 'bebeats_create_post_reactions_table');

/**
 * Créer la table de base de données pour les posts
 */
function bebeats_create_posts_table() {
    global $wpdb;
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    $table_name = $wpdb->prefix . 'bebeats_posts';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        post_type varchar(20) NOT NULL DEFAULT 'post',
        content text,
        media_url varchar(500),
        media_type varchar(20),
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_id (user_id),
        KEY created_at (created_at),
        KEY post_type (post_type)
    ) $charset_collate;";
    
    dbDelta($sql);
}

/**
 * Créer la table de base de données pour les réactions aux posts
 */
function bebeats_create_post_reactions_table() {
    global $wpdb;
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    $table_name = $wpdb->prefix . 'bebeats_post_reactions';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL,
        user_id bigint(20) NOT NULL,
        reaction_type varchar(20) NOT NULL,
        comment_text text,
        parent_comment_id bigint(20) DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY post_id (post_id),
        KEY user_id (user_id),
        KEY reaction_type (reaction_type),
        KEY parent_comment_id (parent_comment_id)
    ) $charset_collate;";
    
    dbDelta($sql);
}

/**
 * Traitement de l'étape 1 d'inscription Fan
 */
function bebeats_handle_register_fan_step1() {
    // Vérifier le nonce
    if (!isset($_POST['bebeats_register_fan_step1_nonce']) || !wp_verify_nonce($_POST['bebeats_register_fan_step1_nonce'], 'bebeats_register_fan_step1_action')) {
        wp_redirect(home_url('/inscription-fan-step1?error=1'));
        exit;
    }
    
    // Récupérer les données
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $username = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Validation
    if (empty($email) || empty($username) || empty($password)) {
        wp_redirect(home_url('/inscription-fan-step1?error=1'));
        exit;
    }
    
    // Vérifier si l'email existe déjà
    if (email_exists($email)) {
        wp_redirect(home_url('/inscription-fan-step1?error=2'));
        exit;
    }
    
    // Vérifier si le username existe déjà
    if (username_exists($username)) {
        wp_redirect(home_url('/inscription-fan-step1?error=3'));
        exit;
    }
    
    // Générer une clé unique pour cette inscription
    $registration_key = wp_generate_password(32, false);
    
    // Stocker les données dans la base de données
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_registrations';
    
    // Créer la table si elle n'existe pas
    bebeats_create_registration_table();
    
    // Stocker le mot de passe de manière sécurisée (base64 pour éviter les problèmes de caractères spéciaux)
    // Note: Ceci est temporaire et sera supprimé après création du compte
    $password_encoded = base64_encode($password);
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'registration_key' => $registration_key,
            'user_type' => 'fan',
            'email' => $email,
            'username' => $username,
            'password_hash' => $password_encoded, // Stockage temporaire sécurisé
            'step_completed' => 1
        ),
        array('%s', '%s', '%s', '%s', '%s', '%d')
    );
    
    if ($result === false) {
        wp_redirect(home_url('/inscription-fan-step1?error=1'));
        exit;
    }
    
    // Stocker la clé dans un cookie pour les étapes suivantes
    setcookie('bebeats_registration_key', $registration_key, time() + 3600, '/');
    
    // Rediriger vers l'étape 2
    wp_redirect(home_url('/inscription-fan-step2?key=' . urlencode($registration_key)));
    exit;
}
add_action('admin_post_bebeats_register_fan_step1', 'bebeats_handle_register_fan_step1');
add_action('admin_post_nopriv_bebeats_register_fan_step1', 'bebeats_handle_register_fan_step1');

/**
 * Traitement de l'étape 2 d'inscription Fan (genres musicaux)
 */
function bebeats_handle_register_fan_step2() {
    // Vérifier le nonce
    if (!isset($_POST['bebeats_register_fan_step2_nonce']) || !wp_verify_nonce($_POST['bebeats_register_fan_step2_nonce'], 'bebeats_register_fan_step2_action')) {
        wp_redirect(home_url('/inscription-fan-step2?error=1'));
        exit;
    }
    
    // Récupérer la clé d'inscription (priorité: POST > GET > COOKIE)
    $registration_key = '';
    if (isset($_POST['registration_key']) && !empty($_POST['registration_key'])) {
        $registration_key = sanitize_text_field($_POST['registration_key']);
    } elseif (isset($_GET['key']) && !empty($_GET['key'])) {
        $registration_key = sanitize_text_field($_GET['key']);
    } elseif (isset($_COOKIE['bebeats_registration_key']) && !empty($_COOKIE['bebeats_registration_key'])) {
        $registration_key = sanitize_text_field($_COOKIE['bebeats_registration_key']);
    }
    
    if (empty($registration_key)) {
        wp_redirect(home_url('/inscription-fan-step1?error=1'));
        exit;
    }
    
    // Récupérer les genres sélectionnés
    $genres = isset($_POST['genres']) ? array_map('sanitize_text_field', $_POST['genres']) : array();
    
    // Vérifier qu'exactement 2 genres sont sélectionnés
    if (count($genres) !== 2) {
        wp_redirect(home_url('/inscription-fan-step2?key=' . urlencode($registration_key) . '&error=1'));
        exit;
    }
    
    // Stocker les genres dans la base de données
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_registrations';
    
    $genres_json = json_encode($genres);
    
    $result = $wpdb->update(
        $table_name,
        array(
            'genres' => $genres_json,
            'step_completed' => 2
        ),
        array('registration_key' => $registration_key),
        array('%s', '%d'),
        array('%s')
    );
    
    if ($result === false) {
        wp_redirect(home_url('/inscription-fan-step2?key=' . urlencode($registration_key) . '&error=1'));
        exit;
    }
    
    // Rediriger vers l'étape 3
    wp_redirect(home_url('/inscription-fan-step3?key=' . urlencode($registration_key)));
    exit;
}
add_action('admin_post_bebeats_register_fan_step2', 'bebeats_handle_register_fan_step2');
add_action('admin_post_nopriv_bebeats_register_fan_step2', 'bebeats_handle_register_fan_step2');

/**
 * Traitement de l'étape 3 d'inscription Fan (profil)
 */
function bebeats_handle_register_fan_step3() {
    // Vérifier le nonce
    if (!isset($_POST['bebeats_register_fan_step3_nonce']) || !wp_verify_nonce($_POST['bebeats_register_fan_step3_nonce'], 'bebeats_register_fan_step3_action')) {
        wp_redirect(home_url('/inscription-fan-step3?error=1'));
        exit;
    }
    
    // Récupérer la clé d'inscription (priorité: POST > GET > COOKIE)
    $registration_key = '';
    if (isset($_POST['registration_key']) && !empty($_POST['registration_key'])) {
        $registration_key = sanitize_text_field($_POST['registration_key']);
    } elseif (isset($_GET['key']) && !empty($_GET['key'])) {
        $registration_key = sanitize_text_field($_GET['key']);
    } elseif (isset($_COOKIE['bebeats_registration_key']) && !empty($_COOKIE['bebeats_registration_key'])) {
        $registration_key = sanitize_text_field($_COOKIE['bebeats_registration_key']);
    }
    
    if (empty($registration_key)) {
        wp_redirect(home_url('/inscription-fan-step1?error=1'));
        exit;
    }
    
    // Récupérer les données
    $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
    
    // Gérer l'upload de la photo de profil
    $profile_photo = '';
    if (!empty($_FILES['profile_photo']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $upload = wp_handle_upload($_FILES['profile_photo'], array('test_form' => false));
        if (!isset($upload['error'])) {
            $profile_photo = $upload['url'];
        }
    }
    
    // Gérer l'upload de la bannière
    $banner = '';
    if (!empty($_FILES['banner']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $upload = wp_handle_upload($_FILES['banner'], array('test_form' => false));
        if (!isset($upload['error'])) {
            $banner = $upload['url'];
        }
    }
    
    // Mettre à jour la base de données
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_registrations';
    
    $update_data = array();
    $update_format = array();
    
    if (!empty($profile_photo)) {
        $update_data['profile_photo'] = $profile_photo;
        $update_format[] = '%s';
    }
    
    if (!empty($banner)) {
        $update_data['banner'] = $banner;
        $update_format[] = '%s';
    }
    
    $update_data['description'] = $description;
    $update_format[] = '%s';
    
    $update_data['step_completed'] = 3;
    $update_format[] = '%d';
    
    $result = $wpdb->update(
        $table_name,
        $update_data,
        array('registration_key' => $registration_key),
        $update_format,
        array('%s')
    );
    
    // Créer le compte utilisateur WordPress
    $registration = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE registration_key = %s",
        $registration_key
    ));
    
    if ($registration) {
        // Décoder le mot de passe
        $password = base64_decode($registration->password_hash);
        
        // Créer l'utilisateur WordPress
        $user_id = wp_create_user(
            $registration->username,
            $password,
            $registration->email
        );
        
        if (!is_wp_error($user_id)) {
            // Mettre à jour les meta utilisateur
            update_user_meta($user_id, 'bebeats_user_type', 'fan');
            update_user_meta($user_id, 'bebeats_genres', json_decode($registration->genres, true));
            if (!empty($registration->description)) {
                update_user_meta($user_id, 'description', $registration->description);
            }
            if (!empty($registration->profile_photo)) {
                update_user_meta($user_id, 'bebeats_profile_photo', $registration->profile_photo);
            }
            if (!empty($registration->banner)) {
                update_user_meta($user_id, 'bebeats_banner', $registration->banner);
            }
            
            // Connecter l'utilisateur
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            
            // Marquer l'inscription comme complétée
            $wpdb->update(
                $table_name,
                array('step_completed' => 4),
                array('registration_key' => $registration_key),
                array('%d'),
                array('%s')
            );
            
            // Supprimer le cookie
            setcookie('bebeats_registration_key', '', time() - 3600, '/');
            
            // Rediriger vers l'étape 4
            wp_redirect(home_url('/inscription-fan-step4'));
            exit;
        } else {
            wp_redirect(home_url('/inscription-fan-step3?key=' . urlencode($registration_key) . '&error=1'));
            exit;
        }
    } else {
        wp_redirect(home_url('/inscription-fan-step1?error=1'));
        exit;
    }
}
add_action('admin_post_bebeats_register_fan_step3', 'bebeats_handle_register_fan_step3');
add_action('admin_post_nopriv_bebeats_register_fan_step3', 'bebeats_handle_register_fan_step3');

/**
 * Traitement de l'étape 1 d'inscription Artiste
 */
function bebeats_handle_register_artiste_step1() {
    // Vérifier le nonce
    if (!isset($_POST['bebeats_register_artiste_step1_nonce']) || !wp_verify_nonce($_POST['bebeats_register_artiste_step1_nonce'], 'bebeats_register_artiste_step1_action')) {
        wp_redirect(home_url('/inscription-artiste-step1?error=1'));
        exit;
    }
    
    // Récupérer les données
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $username = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Validation
    if (empty($email) || empty($username) || empty($password)) {
        wp_redirect(home_url('/inscription-artiste-step1?error=1'));
        exit;
    }
    
    // Vérifier si l'email existe déjà
    if (email_exists($email)) {
        wp_redirect(home_url('/inscription-artiste-step1?error=2'));
        exit;
    }
    
    // Vérifier si le username existe déjà
    if (username_exists($username)) {
        wp_redirect(home_url('/inscription-artiste-step1?error=3'));
        exit;
    }
    
    // Générer une clé unique pour cette inscription
    $registration_key = wp_generate_password(32, false);
    
    // Stocker les données dans la base de données
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_registrations';
    
    // Créer la table si elle n'existe pas
    bebeats_create_registration_table();
    
    // Stocker le mot de passe de manière sécurisée (base64 pour éviter les problèmes de caractères spéciaux)
    $password_encoded = base64_encode($password);
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'registration_key' => $registration_key,
            'user_type' => 'artiste',
            'email' => $email,
            'username' => $username,
            'password_hash' => $password_encoded,
            'step_completed' => 1
        ),
        array('%s', '%s', '%s', '%s', '%s', '%d')
    );
    
    if ($result === false) {
        wp_redirect(home_url('/inscription-artiste-step1?error=1'));
        exit;
    }
    
    // Stocker la clé dans un cookie pour les étapes suivantes
    setcookie('bebeats_registration_key', $registration_key, time() + 3600, '/');
    
    // Rediriger vers l'étape 2
    wp_redirect(home_url('/inscription-artiste-step2?key=' . urlencode($registration_key)));
    exit;
}
add_action('admin_post_bebeats_register_artiste_step1', 'bebeats_handle_register_artiste_step1');
add_action('admin_post_nopriv_bebeats_register_artiste_step1', 'bebeats_handle_register_artiste_step1');

/**
 * Traitement de l'étape 2 d'inscription Artiste (genres musicaux)
 */
function bebeats_handle_register_artiste_step2() {
    // Vérifier le nonce
    if (!isset($_POST['bebeats_register_artiste_step2_nonce']) || !wp_verify_nonce($_POST['bebeats_register_artiste_step2_nonce'], 'bebeats_register_artiste_step2_action')) {
        wp_redirect(home_url('/inscription-artiste-step2?error=1'));
        exit;
    }
    
    // Récupérer la clé d'inscription
    $registration_key = '';
    if (isset($_POST['registration_key']) && !empty($_POST['registration_key'])) {
        $registration_key = sanitize_text_field($_POST['registration_key']);
    } elseif (isset($_GET['key']) && !empty($_GET['key'])) {
        $registration_key = sanitize_text_field($_GET['key']);
    } elseif (isset($_COOKIE['bebeats_registration_key']) && !empty($_COOKIE['bebeats_registration_key'])) {
        $registration_key = sanitize_text_field($_COOKIE['bebeats_registration_key']);
    }
    
    if (empty($registration_key)) {
        wp_redirect(home_url('/inscription-artiste-step1?error=1'));
        exit;
    }
    
    // Récupérer les genres sélectionnés
    $genres = isset($_POST['genres']) ? array_map('sanitize_text_field', $_POST['genres']) : array();
    
    // Vérifier qu'1 ou 2 genres sont sélectionnés
    if (count($genres) < 1 || count($genres) > 2) {
        wp_redirect(home_url('/inscription-artiste-step2?key=' . urlencode($registration_key) . '&error=1'));
        exit;
    }
    
    // Stocker les genres dans la base de données
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_registrations';
    
    $genres_json = json_encode($genres);
    
    $result = $wpdb->update(
        $table_name,
        array(
            'genres' => $genres_json,
            'step_completed' => 2
        ),
        array('registration_key' => $registration_key),
        array('%s', '%d'),
        array('%s')
    );
    
    if ($result === false) {
        wp_redirect(home_url('/inscription-artiste-step2?key=' . urlencode($registration_key) . '&error=1'));
        exit;
    }
    
    // Rediriger vers l'étape 3
    wp_redirect(home_url('/inscription-artiste-step3?key=' . urlencode($registration_key)));
    exit;
}
add_action('admin_post_bebeats_register_artiste_step2', 'bebeats_handle_register_artiste_step2');
add_action('admin_post_nopriv_bebeats_register_artiste_step2', 'bebeats_handle_register_artiste_step2');

/**
 * Traitement de l'étape 3 d'inscription Artiste (profil)
 */
function bebeats_handle_register_artiste_step3() {
    // Vérifier le nonce
    if (!isset($_POST['bebeats_register_artiste_step3_nonce']) || !wp_verify_nonce($_POST['bebeats_register_artiste_step3_nonce'], 'bebeats_register_artiste_step3_action')) {
        wp_redirect(home_url('/inscription-artiste-step3?error=1'));
        exit;
    }
    
    // Récupérer la clé d'inscription
    $registration_key = '';
    if (isset($_POST['registration_key']) && !empty($_POST['registration_key'])) {
        $registration_key = sanitize_text_field($_POST['registration_key']);
    } elseif (isset($_GET['key']) && !empty($_GET['key'])) {
        $registration_key = sanitize_text_field($_GET['key']);
    } elseif (isset($_COOKIE['bebeats_registration_key']) && !empty($_COOKIE['bebeats_registration_key'])) {
        $registration_key = sanitize_text_field($_COOKIE['bebeats_registration_key']);
    }
    
    if (empty($registration_key)) {
        wp_redirect(home_url('/inscription-artiste-step1?error=1'));
        exit;
    }
    
    // Récupérer les données
    $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
    
    // Gérer l'upload de la photo de profil
    $profile_photo = '';
    if (!empty($_FILES['profile_photo']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $upload = wp_handle_upload($_FILES['profile_photo'], array('test_form' => false));
        if (!isset($upload['error'])) {
            $profile_photo = $upload['url'];
        }
    }
    
    // Gérer l'upload de la bannière
    $banner = '';
    if (!empty($_FILES['banner']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $upload = wp_handle_upload($_FILES['banner'], array('test_form' => false));
        if (!isset($upload['error'])) {
            $banner = $upload['url'];
        }
    }
    
    // Mettre à jour la base de données
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_registrations';
    
    $update_data = array();
    $update_format = array();
    
    if (!empty($profile_photo)) {
        $update_data['profile_photo'] = $profile_photo;
        $update_format[] = '%s';
    }
    
    if (!empty($banner)) {
        $update_data['banner'] = $banner;
        $update_format[] = '%s';
    }
    
    $update_data['description'] = $description;
    $update_format[] = '%s';
    
    $update_data['step_completed'] = 3;
    $update_format[] = '%d';
    
    $result = $wpdb->update(
        $table_name,
        $update_data,
        array('registration_key' => $registration_key),
        $update_format,
        array('%s')
    );
    
    // Rediriger vers l'étape 4
    wp_redirect(home_url('/inscription-artiste-step4?key=' . urlencode($registration_key)));
    exit;
}
add_action('admin_post_bebeats_register_artiste_step3', 'bebeats_handle_register_artiste_step3');
add_action('admin_post_nopriv_bebeats_register_artiste_step3', 'bebeats_handle_register_artiste_step3');

/**
 * Traitement de l'étape 4 d'inscription Artiste (ajout de sons)
 */
function bebeats_handle_register_artiste_step4() {
    // Vérifier le nonce
    if (!isset($_POST['bebeats_register_artiste_step4_nonce']) || !wp_verify_nonce($_POST['bebeats_register_artiste_step4_nonce'], 'bebeats_register_artiste_step4_action')) {
        wp_redirect(home_url('/inscription-artiste-step4?error=1'));
        exit;
    }
    
    // Récupérer la clé d'inscription
    $registration_key = '';
    if (isset($_POST['registration_key']) && !empty($_POST['registration_key'])) {
        $registration_key = sanitize_text_field($_POST['registration_key']);
    } elseif (isset($_GET['key']) && !empty($_GET['key'])) {
        $registration_key = sanitize_text_field($_GET['key']);
    } elseif (isset($_COOKIE['bebeats_registration_key']) && !empty($_COOKIE['bebeats_registration_key'])) {
        $registration_key = sanitize_text_field($_COOKIE['bebeats_registration_key']);
    }
    
    if (empty($registration_key)) {
        wp_redirect(home_url('/inscription-artiste-step1?error=1'));
        exit;
    }
    
    // Gérer l'upload des sons (optionnel)
    $sounds = array();
    if (!empty($_FILES['sounds']['name'][0])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        foreach ($_FILES['sounds']['name'] as $key => $value) {
            if ($_FILES['sounds']['name'][$key]) {
                $file = array(
                    'name' => $_FILES['sounds']['name'][$key],
                    'type' => $_FILES['sounds']['type'][$key],
                    'tmp_name' => $_FILES['sounds']['tmp_name'][$key],
                    'error' => $_FILES['sounds']['error'][$key],
                    'size' => $_FILES['sounds']['size'][$key]
                );
                
                $upload = wp_handle_upload($file, array('test_form' => false));
                if (!isset($upload['error'])) {
                    $sounds[] = $upload['url'];
                }
            }
        }
    }
    
    // Mettre à jour la base de données
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_registrations';
    
    $update_data = array();
    $update_format = array();
    
    if (!empty($sounds)) {
        $update_data['sounds'] = json_encode($sounds); // Stocker les sons en JSON
        $update_format[] = '%s';
    }
    
    $update_data['step_completed'] = 4;
    $update_format[] = '%d';
    
    $result = $wpdb->update(
        $table_name,
        $update_data,
        array('registration_key' => $registration_key),
        $update_format,
        array('%s')
    );
    
    // Créer le compte utilisateur WordPress
    $registration = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE registration_key = %s",
        $registration_key
    ));
    
    if ($registration) {
        // Décoder le mot de passe
        $password = base64_decode($registration->password_hash);
        
        // Créer l'utilisateur WordPress
        $user_id = wp_create_user(
            $registration->username,
            $password,
            $registration->email
        );
        
        if (!is_wp_error($user_id)) {
            // Mettre à jour les meta utilisateur
            update_user_meta($user_id, 'bebeats_user_type', 'artiste');
            update_user_meta($user_id, 'bebeats_genres', json_decode($registration->genres, true));
            if (!empty($registration->description)) {
                update_user_meta($user_id, 'description', $registration->description);
            }
            if (!empty($registration->profile_photo)) {
                update_user_meta($user_id, 'bebeats_profile_photo', $registration->profile_photo);
            }
            if (!empty($registration->banner)) {
                update_user_meta($user_id, 'bebeats_banner', $registration->banner);
            }
            if (!empty($sounds)) {
                update_user_meta($user_id, 'bebeats_sounds', $sounds);
            }
            
            // Connecter l'utilisateur
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            
            // Marquer l'inscription comme complétée
            $wpdb->update(
                $table_name,
                array('step_completed' => 5),
                array('registration_key' => $registration_key),
                array('%d'),
                array('%s')
            );
            
            // Supprimer le cookie
            setcookie('bebeats_registration_key', '', time() - 3600, '/');
            
            // Rediriger vers l'étape 5
            wp_redirect(home_url('/inscription-artiste-step5'));
            exit;
        } else {
            wp_redirect(home_url('/inscription-artiste-step4?key=' . urlencode($registration_key) . '&error=1'));
            exit;
        }
    } else {
        wp_redirect(home_url('/inscription-artiste-step1?error=1'));
        exit;
    }
}
add_action('admin_post_bebeats_register_artiste_step4', 'bebeats_handle_register_artiste_step4');
add_action('admin_post_nopriv_bebeats_register_artiste_step4', 'bebeats_handle_register_artiste_step4');

/**
 * Traitement de la mise à jour du profil
 */
function bebeats_handle_update_profile() {
    // Vérifier que l'utilisateur est connecté
    if (!is_user_logged_in()) {
        wp_redirect(home_url('/auth-start'));
        exit;
    }
    
    // Vérifier le nonce
    if (!isset($_POST['bebeats_update_profile_nonce']) || !wp_verify_nonce($_POST['bebeats_update_profile_nonce'], 'bebeats_update_profile_action')) {
        wp_redirect(home_url('/reglages?error=1'));
        exit;
    }
    
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    
    // Récupérer la description
    $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
    
    // Gérer l'upload de la photo de profil
    if (!empty($_FILES['profile_photo']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $upload = wp_handle_upload($_FILES['profile_photo'], array('test_form' => false));
        if (!isset($upload['error'])) {
            update_user_meta($user_id, 'bebeats_profile_photo', $upload['url']);
        }
    }
    
    // Gérer l'upload de la bannière
    if (!empty($_FILES['banner']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $upload = wp_handle_upload($_FILES['banner'], array('test_form' => false));
        if (!isset($upload['error'])) {
            update_user_meta($user_id, 'bebeats_banner', $upload['url']);
        }
    }
    
    // Mettre à jour la description
    if (!empty($description)) {
        update_user_meta($user_id, 'description', $description);
    }
    
    // Rediriger vers les réglages avec un message de succès
    wp_redirect(home_url('/reglages?success=1'));
    exit;
}
add_action('admin_post_bebeats_update_profile', 'bebeats_handle_update_profile');

/**
 * Traitement de la déconnexion
 */
function bebeats_handle_logout() {
    // Vérifier le nonce
    if (!isset($_POST['bebeats_logout_nonce']) || !wp_verify_nonce($_POST['bebeats_logout_nonce'], 'bebeats_logout_action')) {
        wp_redirect(home_url('/reglages?error=1'));
        exit;
    }
    
    // Déconnecter l'utilisateur
    wp_logout();
    
    // Rediriger vers la page d'accueil
    wp_redirect(home_url('/'));
    exit;
}
add_action('admin_post_bebeats_logout', 'bebeats_handle_logout');

/**
 * Traitement de la création d'un post
 */
function bebeats_handle_create_post() {
    // Vérifier que l'utilisateur est connecté
    if (!is_user_logged_in()) {
        wp_redirect(home_url('/auth-start'));
        exit;
    }
    
    // Vérifier le nonce
    if (!isset($_POST['bebeats_create_post_nonce']) || !wp_verify_nonce($_POST['bebeats_create_post_nonce'], 'bebeats_create_post_action')) {
        wp_redirect(home_url('/contribuer?error=1'));
        exit;
    }
    
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    
    // Récupérer les données
    $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'post';
    
    // Récupérer le contenu selon le type de post
    $content = '';
    if ($post_type === 'post') {
        $content = isset($_POST['content']) ? sanitize_textarea_field($_POST['content']) : '';
    } elseif ($post_type === 'fan-art') {
        // Pour fan-art, le contenu peut venir de 'content' (description)
        $content = isset($_POST['content']) ? sanitize_textarea_field($_POST['content']) : '';
    } elseif ($post_type === 'audio') {
        // Pour audio, le contenu peut venir de 'content' (description)
        $content = isset($_POST['content']) ? sanitize_textarea_field($_POST['content']) : '';
    }
    
    $media_type = '';
    $media_url = '';
    
    // Gérer l'upload du média selon le type
    if ($post_type === 'fan-art' && !empty($_FILES['media']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $upload = wp_handle_upload($_FILES['media'], array('test_form' => false));
        if (!isset($upload['error'])) {
            $media_url = $upload['url'];
            // Déterminer le type de média
            $file_type = wp_check_filetype($upload['file']);
            if (strpos($file_type['type'], 'image') !== false) {
                $media_type = 'image';
            } elseif (strpos($file_type['type'], 'video') !== false) {
                $media_type = 'video';
            } else {
                $media_type = 'image'; // Par défaut
            }
        }
    } elseif ($post_type === 'audio' && !empty($_FILES['media']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $upload = wp_handle_upload($_FILES['media'], array('test_form' => false));
        if (!isset($upload['error'])) {
            $media_url = $upload['url'];
            $media_type = 'audio';
        }
    }
    
    // Validation : au moins du contenu ou un média
    if (empty($content) && empty($media_url)) {
        wp_redirect(home_url('/contribuer?error=2'));
        exit;
    }
    
    // Insérer le post dans la base de données
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_posts';
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'user_id' => $user_id,
            'post_type' => $post_type,
            'content' => $content,
            'media_url' => $media_url,
            'media_type' => $media_type
        ),
        array('%d', '%s', '%s', '%s', '%s')
    );
    
    if ($result === false) {
        wp_redirect(home_url('/contribuer?error=1'));
        exit;
    }
    
    // Rediriger vers le feed avec un message de succès
    wp_redirect(home_url('/feed?success=1'));
    exit;
}
add_action('admin_post_bebeats_create_post', 'bebeats_handle_create_post');

/**
 * Traitement des réactions aux posts (like, commentaire, republier, favoris)
 */
function bebeats_handle_post_reaction() {
    // Vérifier que l'utilisateur est connecté
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Vous devez être connecté'));
        exit;
    }
    
    // Vérifier le nonce
    if (!isset($_POST['bebeats_reaction_nonce']) || !wp_verify_nonce($_POST['bebeats_reaction_nonce'], 'bebeats_reaction_action')) {
        wp_send_json_error(array('message' => 'Erreur de sécurité'));
        exit;
    }
    
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $reaction_type = isset($_POST['reaction_type']) ? sanitize_text_field($_POST['reaction_type']) : '';
    $action = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : 'toggle'; // toggle, add, remove
    $comment_text = isset($_POST['comment_text']) ? sanitize_textarea_field($_POST['comment_text']) : '';
    $parent_comment_id = isset($_POST['parent_comment_id']) ? intval($_POST['parent_comment_id']) : 0;
    
    if (empty($post_id) || empty($reaction_type)) {
        wp_send_json_error(array('message' => 'Données manquantes'));
        exit;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_post_reactions';
    
    // Pour les likes, republier et favoris : toggle (ajouter ou supprimer)
    if (in_array($reaction_type, array('like', 'repost', 'favorite')) && $action === 'toggle') {
        // Vérifier si la réaction existe déjà (sans parent_comment_id pour ces types)
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $table_name WHERE post_id = %d AND user_id = %d AND reaction_type = %s AND (parent_comment_id IS NULL OR parent_comment_id = 0)",
            $post_id, $user_id, $reaction_type
        ));
        
        if ($existing) {
            // Supprimer la réaction
            $wpdb->delete(
                $table_name,
                array('id' => $existing->id),
                array('%d')
            );
            wp_send_json_success(array('action' => 'removed', 'reaction_type' => $reaction_type));
        } else {
            // Ajouter la réaction
            $wpdb->insert(
                $table_name,
                array(
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                    'reaction_type' => $reaction_type,
                    'parent_comment_id' => null
                ),
                array('%d', '%d', '%s', '%d')
            );
            wp_send_json_success(array('action' => 'added', 'reaction_type' => $reaction_type));
        }
    }
    // Pour les commentaires : toujours ajouter
    elseif ($reaction_type === 'comment' && !empty($comment_text)) {
        $wpdb->insert(
            $table_name,
            array(
                'post_id' => $post_id,
                'user_id' => $user_id,
                'reaction_type' => 'comment',
                'comment_text' => $comment_text,
                'parent_comment_id' => $parent_comment_id > 0 ? $parent_comment_id : null
            ),
            array('%d', '%d', '%s', '%s', '%d')
        );
        wp_send_json_success(array('action' => 'added', 'reaction_type' => 'comment'));
    }
    // Pour liker un commentaire
    elseif ($reaction_type === 'comment_like' && $parent_comment_id > 0) {
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $table_name WHERE post_id = %d AND user_id = %d AND reaction_type = %s AND parent_comment_id = %d",
            $post_id, $user_id, 'comment_like', $parent_comment_id
        ));
        
        if ($existing) {
            $wpdb->delete(
                $table_name,
                array('id' => $existing->id),
                array('%d')
            );
            wp_send_json_success(array('action' => 'removed', 'reaction_type' => 'comment_like'));
        } else {
            $wpdb->insert(
                $table_name,
                array(
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                    'reaction_type' => 'comment_like',
                    'parent_comment_id' => $parent_comment_id
                ),
                array('%d', '%d', '%s', '%d')
            );
            wp_send_json_success(array('action' => 'added', 'reaction_type' => 'comment_like'));
        }
    } else {
        wp_send_json_error(array('message' => 'Action invalide'));
    }
}
add_action('wp_ajax_bebeats_post_reaction', 'bebeats_handle_post_reaction');
add_action('wp_ajax_nopriv_bebeats_post_reaction', 'bebeats_handle_post_reaction');

// Fonction pour rechercher des utilisateurs (autocomplete)
function bebeats_search_users() {
    check_ajax_referer('bebeats_search_users_action', 'nonce');
    
    $search_term = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    if (strlen($search_term) < 2) {
        wp_send_json_success(array('users' => array()));
    }
    
    $users = get_users(array(
        'search' => '*' . $search_term . '*',
        'search_columns' => array('user_login', 'user_nicename', 'display_name'),
        'number' => 10
    ));
    
    $results = array();
    foreach ($users as $user) {
        $profile_photo = get_user_meta($user->ID, 'bebeats_profile_photo', true);
        if (empty($profile_photo)) {
            $profile_photo = get_avatar_url($user->ID, array('size' => 50));
        }
        
        $results[] = array(
            'id' => $user->ID,
            'username' => $user->user_login,
            'display_name' => $user->display_name ?: $user->user_login,
            'profile_photo' => $profile_photo
        );
    }
    
    wp_send_json_success(array('users' => $results));
}
add_action('wp_ajax_bebeats_search_users', 'bebeats_search_users');
add_action('wp_ajax_nopriv_bebeats_search_users', 'bebeats_search_users');


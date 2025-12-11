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
    if (is_page('contribuer')) {
        wp_enqueue_script('bebeats-contribuer', get_template_directory_uri() . '/js/contribuer.js', array('jquery'), '1.0', true);
    }
    
    if (is_page('reglages')) {
        wp_enqueue_script('bebeats-toggles', get_template_directory_uri() . '/js/toggles.js', array('jquery'), '1.0', true);
        wp_enqueue_script('bebeats-file-preview', get_template_directory_uri() . '/js/file-preview.js', array(), '1.0', true);
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


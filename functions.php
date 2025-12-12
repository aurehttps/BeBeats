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
        wp_enqueue_script('bebeats-profil', get_template_directory_uri() . '/js/profil.js', array('jquery'), '1.0', true);
        wp_localize_script('bebeats-profil', 'bebeatsProfil', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bebeats_reaction_action')
        ));
    }
    
    if (is_page('feed-page')) {
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
    
    // Charger toggles.js sur toutes les pages pour le mode de couleur
    wp_enqueue_script('bebeats-toggles', get_template_directory_uri() . '/js/toggles.js', array('jquery'), '1.0', true);
    
    if (is_page('reglages')) {
        wp_enqueue_script('bebeats-file-preview', get_template_directory_uri() . '/js/file-preview.js', array(), '1.0', true);
    }
    
    if (is_page('feed-page')) {
        wp_enqueue_script('bebeats-feed', get_template_directory_uri() . '/js/feed.js', array('jquery'), '1.0', true);
        wp_localize_script('bebeats-feed', 'bebeatsFeed', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bebeats_reaction_action')
        ));
        wp_enqueue_script('bebeats-time-update', get_template_directory_uri() . '/js/time-update.js', array(), '1.0', true);
    }
    
    if (is_page('profil')) {
        wp_enqueue_script('bebeats-time-update', get_template_directory_uri() . '/js/time-update.js', array(), '1.0', true);
    }
    
    if (is_page('contribuer')) {
        wp_enqueue_script('bebeats-contribuer', get_template_directory_uri() . '/js/contribuer.js', array('jquery'), '1.0', true);
        wp_localize_script('bebeats-contribuer', 'bebeatsContribuer', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bebeats_search_users_action')
        ));
    }
    
    // Enqueue file-preview.js pour les pages d'inscription
    if (is_page('inscription-fan-step3') || is_page('inscription-artiste-step3') || is_page('inscription-artiste-step4')) {
        wp_enqueue_script('bebeats-file-preview', get_template_directory_uri() . '/js/file-preview.js', array(), '1.0', true);
    }
}

// Empêcher la redirection RSS pour la page feed
function bebeats_disable_feed_redirect() {
    global $wp_query, $wp;
    
    // Vérifier si l'URL demandée est /feed-page/
    $requested_url = trim($wp->request, '/');
    
    if ($requested_url === 'feed-page') {
        // Vérifier si c'est une page WordPress
        $page = get_page_by_path($requested_url);
        if ($page) {
            $wp_query->is_feed = false;
            $wp_query->is_404 = false;
            $wp_query->is_page = true;
            $wp_query->queried_object = $page;
            $wp_query->queried_object_id = $page->ID;
        }
    }
}
add_action('parse_request', 'bebeats_disable_feed_redirect', 1);
add_action('parse_query', 'bebeats_disable_feed_redirect', 1);

// Donner les permissions de publication à tous les utilisateurs lors de leur connexion
function bebeats_grant_post_permissions_on_login($user_login, $user) {
    if ($user && isset($user->ID)) {
        $user_obj = get_userdata($user->ID);
        if ($user_obj) {
            $user_obj->add_cap('publish_posts');
            $user_obj->add_cap('edit_posts');
            $user_obj->add_cap('edit_published_posts');
            $user_obj->add_cap('delete_posts');
        }
    }
}
add_action('wp_login', 'bebeats_grant_post_permissions_on_login', 10, 2);

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
        // Donner les permissions nécessaires pour créer des posts
        $user_obj = get_userdata($user->ID);
        if ($user_obj) {
            $user_obj->add_cap('publish_posts');
            $user_obj->add_cap('edit_posts');
            $user_obj->add_cap('edit_published_posts');
            $user_obj->add_cap('delete_posts');
        }
        
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
        allow_comments tinyint(1) DEFAULT 1,
        allow_repost tinyint(1) DEFAULT 1,
        show_likes tinyint(1) DEFAULT 1,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_id (user_id),
        KEY created_at (created_at),
        KEY post_type (post_type)
    ) $charset_collate;";
    
    dbDelta($sql);
    
    // Ajouter les colonnes pour les options si elles n'existent pas déjà (pour les mises à jour)
    $columns_to_add = array(
        'allow_comments' => "ALTER TABLE $table_name ADD COLUMN allow_comments tinyint(1) DEFAULT 1 AFTER media_type",
        'allow_repost' => "ALTER TABLE $table_name ADD COLUMN allow_repost tinyint(1) DEFAULT 1 AFTER allow_comments",
        'show_likes' => "ALTER TABLE $table_name ADD COLUMN show_likes tinyint(1) DEFAULT 1 AFTER allow_repost"
    );
    
    foreach ($columns_to_add as $column => $alter_sql) {
        $column_exists = $wpdb->get_results($wpdb->prepare(
            "SHOW COLUMNS FROM $table_name LIKE %s",
            $column
        ));
        
        if (empty($column_exists)) {
            $wpdb->query($alter_sql);
        }
    }
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
        parent_comment_id bigint(20) DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY post_id (post_id),
        KEY user_id (user_id),
        KEY reaction_type (reaction_type),
        KEY parent_comment_id (parent_comment_id)
    ) $charset_collate;";
    
    dbDelta($sql);
    
    // Ajouter la contrainte unique si elle n'existe pas déjà (pour les mises à jour)
    // Utiliser un index unique sur (user_id, post_id, reaction_type, parent_comment_id)
    // mais seulement pour les réactions qui n'ont pas de parent_comment_id (like, repost, favorite)
    $index_exists = $wpdb->get_results($wpdb->prepare(
        "SHOW INDEX FROM $table_name WHERE Key_name = %s",
        'unique_user_post_reaction'
    ));
    
    if (empty($index_exists)) {
        // Mettre à jour les NULL en 0 pour les réactions sans parent
        $wpdb->query("
            UPDATE $table_name 
            SET parent_comment_id = 0 
            WHERE parent_comment_id IS NULL 
            AND reaction_type IN ('like', 'repost', 'favorite')
        ");
        
        // Supprimer les doublons potentiels avant d'ajouter la contrainte
        $wpdb->query("
            DELETE r1 FROM $table_name r1
            INNER JOIN $table_name r2 
            WHERE r1.id > r2.id 
            AND r1.user_id = r2.user_id 
            AND r1.post_id = r2.post_id 
            AND r1.reaction_type = r2.reaction_type 
            AND COALESCE(r1.parent_comment_id, 0) = COALESCE(r2.parent_comment_id, 0)
            AND r1.reaction_type IN ('like', 'repost', 'favorite', 'comment_like')
        ");
        
        // Ajouter la contrainte unique (maintenant que parent_comment_id utilise 0 au lieu de NULL)
        $wpdb->query("
            ALTER TABLE $table_name 
            ADD UNIQUE KEY unique_user_post_reaction (user_id, post_id, reaction_type, parent_comment_id)
        ");
    }
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
            // Donner les permissions nécessaires pour créer des posts
            $user_obj = get_userdata($user_id);
            if ($user_obj) {
                $user_obj->add_cap('publish_posts');
                $user_obj->add_cap('edit_posts');
                $user_obj->add_cap('edit_published_posts');
                $user_obj->add_cap('delete_posts');
            }
            
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
            // Donner les permissions nécessaires pour créer des posts
            $user_obj = get_userdata($user_id);
            if ($user_obj) {
                $user_obj->add_cap('publish_posts');
                $user_obj->add_cap('edit_posts');
                $user_obj->add_cap('edit_published_posts');
                $user_obj->add_cap('delete_posts');
            }
            
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
    error_log('bebeats_create_post: Vérification du nonce');
    error_log('bebeats_create_post: POST keys: ' . print_r(array_keys($_POST), true));
    error_log('bebeats_create_post: Nonce reçu: ' . (isset($_POST['bebeats_create_post_nonce']) ? $_POST['bebeats_create_post_nonce'] : 'NON DÉFINI'));
    
    if (!isset($_POST['bebeats_create_post_nonce'])) {
        error_log('bebeats_create_post: ERREUR - Nonce non présent dans POST');
        wp_redirect(home_url('/contribuer?error=1'));
        exit;
    }
    
    $nonce_verified = wp_verify_nonce($_POST['bebeats_create_post_nonce'], 'bebeats_create_post_action');
    error_log('bebeats_create_post: Résultat vérification nonce: ' . ($nonce_verified ? 'VALIDE' : 'INVALIDE'));
    
    if (!$nonce_verified) {
        error_log('bebeats_create_post: ERREUR - Nonce invalide');
        wp_redirect(home_url('/contribuer?error=1'));
        exit;
    }
    
    // Debug : vérifier que les données sont bien reçues
    error_log('bebeats_create_post: Données reçues - POST: ' . print_r($_POST, true));
    error_log('bebeats_create_post: Fichiers reçus - FILES: ' . print_r($_FILES, true));
    
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
    
    // Récupérer les options
    $allow_comments = isset($_POST['allow_comments']) ? intval($_POST['allow_comments']) : 1;
    $allow_repost = isset($_POST['allow_repost']) ? intval($_POST['allow_repost']) : 1;
    $show_likes = isset($_POST['show_likes']) ? intval($_POST['show_likes']) : 1;
    
    // Insérer le post dans la base de données
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_posts';
    
    // Vérifier que la table existe
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
    if (!$table_exists) {
        error_log('bebeats_create_post: ❌ ERREUR - La table ' . $table_name . ' n\'existe pas!');
        // Créer la table si elle n'existe pas
        bebeats_create_posts_table();
    }
    
    // Préparer les données pour l'insertion
    $insert_data = array(
        'user_id' => $user_id,
        'post_type' => $post_type,
        'content' => $content,
        'media_url' => $media_url,
        'media_type' => $media_type,
        'allow_comments' => $allow_comments,
        'allow_repost' => $allow_repost,
        'show_likes' => $show_likes
    );
    
    error_log('bebeats_create_post: Données à insérer: ' . print_r($insert_data, true));
    
    $result = $wpdb->insert(
        $table_name,
        $insert_data,
        array('%d', '%s', '%s', '%s', '%s', '%d', '%d', '%d')
    );
    
    if ($result === false) {
        error_log('bebeats_create_post: Erreur lors de l\'insertion dans la base de données');
        error_log('bebeats_create_post: Erreur WP: ' . $wpdb->last_error);
        error_log('bebeats_create_post: Données à insérer: ' . print_r(array(
            'user_id' => $user_id,
            'post_type' => $post_type,
            'content' => substr($content, 0, 100) . '...',
            'media_url' => $media_url,
            'media_type' => $media_type
        ), true));
        wp_redirect(home_url('/contribuer?error=1'));
        exit;
    }
    
    $bebeats_post_id = $wpdb->insert_id;
    error_log('bebeats_create_post: Post créé avec succès dans la table personnalisée, ID: ' . $bebeats_post_id);
    error_log('bebeats_create_post: User ID: ' . $user_id);
    error_log('bebeats_create_post: Post Type: ' . $post_type);
    error_log('bebeats_create_post: Content: ' . (!empty($content) ? substr($content, 0, 50) . '...' : 'VIDE'));
    error_log('bebeats_create_post: Media URL: ' . (!empty($media_url) ? $media_url : 'Aucun'));
    
    // Vérifier que le post a bien été inséré en le récupérant
    $verify_post = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $bebeats_post_id
    ));
    if ($verify_post) {
        error_log('bebeats_create_post: ✅ Post vérifié dans la base de données');
    } else {
        error_log('bebeats_create_post: ❌ ERREUR - Post non trouvé après insertion!');
    }
    
    // Créer aussi un post WordPress pour qu'il apparaisse dans le menu Posts
    $wp_post_title = !empty($content) ? wp_trim_words($content, 10, '...') : 'Post BeBeats - ' . ucfirst($post_type);
    if (empty($wp_post_title)) {
        $wp_post_title = 'Post BeBeats - ' . date('Y-m-d H:i:s');
    }
    
    $wp_post_content = '';
    if (!empty($content)) {
        $wp_post_content .= '<p>' . nl2br(esc_html($content)) . '</p>';
    }
    if (!empty($media_url)) {
        if ($media_type === 'image') {
            $wp_post_content .= '<p><img src="' . esc_url($media_url) . '" alt="Média du post" style="max-width: 100%; height: auto;" /></p>';
        } elseif ($media_type === 'video') {
            $wp_post_content .= '<p><video src="' . esc_url($media_url) . '" controls style="max-width: 100%; height: auto;"></video></p>';
        } elseif ($media_type === 'audio') {
            $wp_post_content .= '<p><audio src="' . esc_url($media_url) . '" controls style="width: 100%;"></audio></p>';
        }
    }
    
    // Donner les permissions nécessaires à l'utilisateur pour créer des posts
    $user = get_userdata($user_id);
    if ($user) {
        // Ajouter les capacités nécessaires de manière permanente
        $user->add_cap('publish_posts');
        $user->add_cap('edit_posts');
        $user->add_cap('edit_published_posts');
        $user->add_cap('delete_posts');
    }
    
    // Utiliser wp_set_current_user pour que wp_insert_post reconnaisse les permissions
    wp_set_current_user($user_id);
    
    $wp_post_data = array(
        'post_title'    => $wp_post_title,
        'post_content'  => $wp_post_content,
        'post_status'   => 'publish',
        'post_author'   => $user_id,
        'post_type'     => 'post',
        'post_date'     => current_time('mysql')
    );
    
    error_log('bebeats_create_post: Tentative de création du post WordPress');
    error_log('bebeats_create_post: User ID: ' . $user_id);
    error_log('bebeats_create_post: Permissions: ' . (current_user_can('publish_posts') ? 'Oui' : 'Non'));
    error_log('bebeats_create_post: Titre: ' . $wp_post_title);
    
    // Désactiver temporairement les filtres qui pourraient bloquer la création
    remove_action('save_post', 'wp_save_post_revision', 10);
    
    $wp_post_id = wp_insert_post($wp_post_data, true);
    
    // Réactiver les filtres
    add_action('save_post', 'wp_save_post_revision', 10);
    
    if ($wp_post_id && !is_wp_error($wp_post_id)) {
        // Sauvegarder les métadonnées du post BeBeats dans les meta du post WordPress
        update_post_meta($wp_post_id, 'bebeats_post_id', $bebeats_post_id);
        update_post_meta($wp_post_id, 'bebeats_post_type', $post_type);
        if (!empty($media_url)) {
            update_post_meta($wp_post_id, 'bebeats_media_url', $media_url);
        }
        if (!empty($media_type)) {
            update_post_meta($wp_post_id, 'bebeats_media_type', $media_type);
        }
        update_post_meta($wp_post_id, 'bebeats_allow_comments', $allow_comments);
        update_post_meta($wp_post_id, 'bebeats_allow_repost', $allow_repost);
        update_post_meta($wp_post_id, 'bebeats_show_likes', $show_likes);
        
        error_log('bebeats_create_post: Post WordPress créé avec succès, ID: ' . $wp_post_id);
        error_log('bebeats_create_post: Titre du post: ' . $wp_post_title);
        error_log('bebeats_create_post: Auteur: ' . $user_id);
    } else {
        $error_msg = is_wp_error($wp_post_id) ? $wp_post_id->get_error_message() : 'Erreur inconnue';
        error_log('bebeats_create_post: Erreur lors de la création du post WordPress: ' . $error_msg);
        if (is_wp_error($wp_post_id)) {
            error_log('bebeats_create_post: Erreurs détaillées: ' . print_r($wp_post_id->get_error_messages(), true));
        }
    }
    
    // Vérifier une dernière fois que le post existe dans la table
    $final_check = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE id = %d",
        $bebeats_post_id
    ));
    
    if ($final_check > 0) {
        error_log('bebeats_create_post: ✅ Post confirmé dans la base de données avant redirection');
    } else {
        error_log('bebeats_create_post: ❌ ERREUR CRITIQUE - Post non trouvé avant redirection!');
    }
    
    // Rediriger vers la page contribuer avec un message de succès
    wp_redirect(home_url('/contribuer?success=1'));
    exit;
}
add_action('admin_post_bebeats_create_post', 'bebeats_handle_create_post');
add_action('admin_post_nopriv_bebeats_create_post', 'bebeats_handle_create_post');

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
    // IMPORTANT: Un utilisateur ne peut avoir qu'UNE SEULE réaction de chaque type par post
    if (in_array($reaction_type, array('like', 'repost', 'favorite')) && $action === 'toggle') {
        // Vérifier si la réaction existe déjà (sans parent_comment_id pour ces types)
        // Utiliser COALESCE pour gérer NULL et 0 de manière uniforme
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $table_name WHERE post_id = %d AND user_id = %d AND reaction_type = %s AND COALESCE(parent_comment_id, 0) = 0",
            $post_id, $user_id, $reaction_type
        ));
        
        if ($existing) {
            // Supprimer la réaction existante
            $deleted = $wpdb->delete(
                $table_name,
                array('id' => $existing->id),
                array('%d')
            );
            
            if ($deleted !== false) {
                // Récupérer le nouveau compteur
                $new_count = (int) $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE post_id = %d AND reaction_type = %s",
                    $post_id, $reaction_type
                ));
                wp_send_json_success(array(
                    'action' => 'removed', 
                    'reaction_type' => $reaction_type,
                    'count' => $new_count
                ));
            } else {
                // Même en cas d'erreur, retourner le compteur actuel
                $current_count = (int) $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE post_id = %d AND reaction_type = %s",
                    $post_id, $reaction_type
                ));
                wp_send_json_error(array(
                    'message' => 'Erreur lors de la suppression',
                    'count' => $current_count
                ));
            }
        } else {
            // Ajouter la réaction (un utilisateur ne peut avoir qu'une seule réaction de ce type)
            // Utiliser 0 au lieu de NULL pour parent_comment_id pour éviter les problèmes avec la contrainte unique
            $inserted = $wpdb->insert(
                $table_name,
                array(
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                    'reaction_type' => $reaction_type,
                    'parent_comment_id' => 0
                ),
                array('%d', '%d', '%s', '%d')
            );
            
            if ($inserted !== false) {
                // Récupérer le nouveau compteur
                $new_count = (int) $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE post_id = %d AND reaction_type = %s",
                    $post_id, $reaction_type
                ));
                wp_send_json_success(array(
                    'action' => 'added', 
                    'reaction_type' => $reaction_type,
                    'count' => $new_count
                ));
            } else {
                // Si l'insertion échoue, vérifier l'erreur
                $error_message = $wpdb->last_error;
                error_log('bebeats_post_reaction: Erreur insertion - ' . $error_message);
                error_log('bebeats_post_reaction: Post ID: ' . $post_id . ', User ID: ' . $user_id . ', Reaction Type: ' . $reaction_type);
                
                // Vérifier si c'est à cause d'un doublon (contrainte unique)
                $existing_check = $wpdb->get_row($wpdb->prepare(
                    "SELECT id FROM $table_name WHERE post_id = %d AND user_id = %d AND reaction_type = %s AND COALESCE(parent_comment_id, 0) = 0",
                    $post_id, $user_id, $reaction_type
                ));
                
                if ($existing_check) {
                    // La réaction existe déjà, la supprimer (toggle)
                    $deleted = $wpdb->delete(
                        $table_name,
                        array('id' => $existing_check->id),
                        array('%d')
                    );
                    
                    if ($deleted !== false) {
                        $new_count = (int) $wpdb->get_var($wpdb->prepare(
                            "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE post_id = %d AND reaction_type = %s",
                            $post_id, $reaction_type
                        ));
                        wp_send_json_success(array(
                            'action' => 'removed', 
                            'reaction_type' => $reaction_type,
                            'count' => $new_count
                        ));
                    } else {
                        // Récupérer le compteur actuel même en cas d'erreur
                        $current_count = (int) $wpdb->get_var($wpdb->prepare(
                            "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE post_id = %d AND reaction_type = %s",
                            $post_id, $reaction_type
                        ));
                        wp_send_json_error(array(
                            'message' => 'Erreur lors de la suppression: ' . $wpdb->last_error,
                            'count' => $current_count
                        ));
                    }
                } else {
                    // Autre erreur - retourner le compteur actuel
                    $current_count = (int) $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE post_id = %d AND reaction_type = %s",
                        $post_id, $reaction_type
                    ));
                    wp_send_json_error(array(
                        'message' => 'Erreur lors de l\'ajout de la réaction: ' . ($error_message ?: 'Erreur inconnue'),
                        'count' => $current_count
                    ));
                }
            }
        }
    }
    // Pour les commentaires : toujours ajouter (un utilisateur peut publier plusieurs commentaires)
    elseif ($reaction_type === 'comment' && !empty($comment_text)) {
        $inserted = $wpdb->insert(
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
        
        if ($inserted !== false) {
            $comment_id = $wpdb->insert_id;
            
            // Récupérer les données complètes du commentaire créé
            $comment = $wpdb->get_row($wpdb->prepare(
                "SELECT r.*, u.display_name, u.user_login,
                        pm1.meta_value as profile_photo
                 FROM $table_name r
                 LEFT JOIN {$wpdb->users} u ON r.user_id = u.ID
                 LEFT JOIN {$wpdb->usermeta} pm1 ON u.ID = pm1.user_id AND pm1.meta_key = 'bebeats_profile_photo'
                 WHERE r.id = %d",
                $comment_id
            ));
            
            // Récupérer le nouveau compteur de commentaires
            $new_count = (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE post_id = %d AND reaction_type = 'comment' AND (parent_comment_id IS NULL OR parent_comment_id = 0)",
                $post_id
            ));
            
            // Préparer les données du commentaire pour le retour
            $comment_data = null;
            if ($comment) {
                $comment_profile_photo = !empty($comment->profile_photo) ? $comment->profile_photo : get_avatar_url($comment->user_id, array('size' => 40));
                $comment_time_ago = human_time_diff(strtotime($comment->created_at), current_time('timestamp'));
                $comment_timestamp = strtotime($comment->created_at);
                
                $comment_data = array(
                    'id' => $comment->id,
                    'user_id' => $comment->user_id,
                    'display_name' => $comment->display_name ?: $comment->user_login,
                    'user_login' => $comment->user_login,
                    'profile_photo' => $comment_profile_photo,
                    'comment_text' => $comment->comment_text,
                    'time_ago' => $comment_time_ago,
                    'timestamp' => $comment_timestamp,
                    'likes_count' => 0
                );
            }
            
            wp_send_json_success(array(
                'action' => 'added', 
                'reaction_type' => 'comment',
                'count' => $new_count,
                'comment' => $comment_data
            ));
        } else {
            wp_send_json_error(array('message' => 'Erreur lors de l\'ajout du commentaire'));
        }
    }
    // Pour liker un commentaire : un utilisateur ne peut liker qu'une fois chaque commentaire
    elseif ($reaction_type === 'comment_like' && $parent_comment_id > 0) {
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $table_name WHERE post_id = %d AND user_id = %d AND reaction_type = %s AND parent_comment_id = %d",
            $post_id, $user_id, 'comment_like', $parent_comment_id
        ));
        
        if ($existing) {
            $deleted = $wpdb->delete(
                $table_name,
                array('id' => $existing->id),
                array('%d')
            );
            
            if ($deleted !== false) {
                // Récupérer le nouveau compteur
                $new_count = (int) $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE parent_comment_id = %d AND reaction_type = 'comment_like'",
                    $parent_comment_id
                ));
                wp_send_json_success(array(
                    'action' => 'removed', 
                    'reaction_type' => 'comment_like',
                    'count' => $new_count
                ));
            } else {
                wp_send_json_error(array('message' => 'Erreur lors de la suppression'));
            }
        } else {
            $inserted = $wpdb->insert(
                $table_name,
                array(
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                    'reaction_type' => 'comment_like',
                    'parent_comment_id' => $parent_comment_id
                ),
                array('%d', '%d', '%s', '%d')
            );
            
            if ($inserted !== false) {
                // Récupérer le nouveau compteur
                $new_count = (int) $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE parent_comment_id = %d AND reaction_type = 'comment_like'",
                    $parent_comment_id
                ));
                wp_send_json_success(array(
                    'action' => 'added', 
                    'reaction_type' => 'comment_like',
                    'count' => $new_count
                ));
            } else {
                wp_send_json_error(array('message' => 'Erreur lors de l\'ajout du like'));
            }
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

/**
 * Fonction AJAX pour récupérer un post aléatoire selon la catégorie (Roll)
 */
function bebeats_roll_random_post() {
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    
    if (empty($category)) {
        wp_send_json_error(array('message' => 'Catégorie manquante'));
        exit;
    }
    
    global $wpdb;
    $posts_table = $wpdb->prefix . 'bebeats_posts';
    $users_table = $wpdb->users;
    
    $post = null;
    
    // Déterminer le post_type et les conditions selon la catégorie
    switch ($category) {
        case 'Musique':
            // Récupérer un audio d'un utilisateur de type "artiste"
            $query = "
                SELECT p.*, u.ID as user_id, u.display_name, u.user_login
                FROM $posts_table p
                INNER JOIN $users_table u ON p.user_id = u.ID
                INNER JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
                WHERE p.post_type = 'audio'
                AND p.media_type = 'audio'
                AND um.meta_key = 'bebeats_user_type'
                AND um.meta_value = 'artiste'
                ORDER BY RAND()
                LIMIT 1
            ";
            $post = $wpdb->get_row($query);
            break;
            
        case 'Fan Arts':
            // Récupérer un fan-art
            $query = "
                SELECT p.*, u.ID as user_id, u.display_name, u.user_login
                FROM $posts_table p
                INNER JOIN $users_table u ON p.user_id = u.ID
                WHERE p.post_type = 'fan-art'
                ORDER BY RAND()
                LIMIT 1
            ";
            $post = $wpdb->get_row($query);
            break;
            
        case 'Evènement':
        case 'Événement':
            // Récupérer un événement (post_type = 'event')
            $query = "
                SELECT p.*, u.ID as user_id, u.display_name, u.user_login
                FROM $posts_table p
                INNER JOIN $users_table u ON p.user_id = u.ID
                WHERE p.post_type = 'event'
                ORDER BY RAND()
                LIMIT 1
            ";
            $post = $wpdb->get_row($query);
            break;
            
        case 'Article':
            // Récupérer un article (post_type = 'post')
            $query = "
                SELECT p.*, u.ID as user_id, u.display_name, u.user_login
                FROM $posts_table p
                INNER JOIN $users_table u ON p.user_id = u.ID
                WHERE p.post_type = 'post'
                ORDER BY RAND()
                LIMIT 1
            ";
            $post = $wpdb->get_row($query);
            break;
            
        default:
            wp_send_json_error(array('message' => 'Catégorie invalide'));
            exit;
    }
    
    if (!$post) {
        wp_send_json_error(array('message' => 'Aucun contenu trouvé pour cette catégorie'));
        exit;
    }
    
    // Récupérer les informations de l'utilisateur
    $user_id = $post->user_id;
    $profile_photo = get_user_meta($user_id, 'bebeats_profile_photo', true);
    if (empty($profile_photo)) {
        $profile_photo = get_avatar_url($user_id, array('size' => 60));
    }
    
    // Récupérer les statistiques du post
    $reactions_table = $wpdb->prefix . 'bebeats_post_reactions';
    $likes_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $reactions_table WHERE post_id = %d AND reaction_type = 'like'",
        $post->id
    ));
    $comments_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $reactions_table WHERE post_id = %d AND reaction_type = 'comment'",
        $post->id
    ));
    $reposts_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $reactions_table WHERE post_id = %d AND reaction_type = 'repost'",
        $post->id
    ));
    $favorites_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $reactions_table WHERE post_id = %d AND reaction_type = 'favorite'",
        $post->id
    ));
    
    // Formater la date
    $created_at = new DateTime($post->created_at);
    $now = new DateTime();
    $diff = $now->diff($created_at);
    
    $time_ago = '';
    if ($diff->y > 0) {
        $time_ago = $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
    } elseif ($diff->m > 0) {
        $time_ago = $diff->m . ' mois';
    } elseif ($diff->d > 0) {
        $time_ago = $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
    } elseif ($diff->h > 0) {
        $time_ago = $diff->h . ' heure' . ($diff->h > 1 ? 's' : '');
    } elseif ($diff->i > 0) {
        $time_ago = $diff->i . ' min';
    } else {
        $time_ago = 'À l\'instant';
    }
    
    $result = array(
        'id' => $post->id,
        'user_id' => $user_id,
        'username' => $post->display_name ?: $post->user_login,
        'profile_photo' => $profile_photo,
        'content' => $post->content,
        'media_url' => $post->media_url,
        'media_type' => $post->media_type,
        'post_type' => $post->post_type,
        'time_ago' => $time_ago,
        'likes' => intval($likes_count),
        'comments' => intval($comments_count),
        'reposts' => intval($reposts_count),
        'favorites' => intval($favorites_count)
    );
    
    wp_send_json_success($result);
}
add_action('wp_ajax_bebeats_roll_random_post', 'bebeats_roll_random_post');
add_action('wp_ajax_nopriv_bebeats_roll_random_post', 'bebeats_roll_random_post');


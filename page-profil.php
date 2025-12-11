<?php
/**
 * Template Name: Profil Page
 * Template pour la page de profil utilisateur
 */

// Rediriger vers la connexion si l'utilisateur n'est pas connecté
if (!is_user_logged_in()) {
    wp_redirect(home_url('/auth-start'));
    exit;
}

get_header(); 

$current_user = wp_get_current_user();
$user_type = get_user_meta($current_user->ID, 'bebeats_user_type', true);
$profile_photo = get_user_meta($current_user->ID, 'bebeats_profile_photo', true);
$banner = get_user_meta($current_user->ID, 'bebeats_banner', true);
$description = get_user_meta($current_user->ID, 'description', true);
$genres = get_user_meta($current_user->ID, 'bebeats_genres', true);
$sounds = get_user_meta($current_user->ID, 'bebeats_sounds', true);

// Si pas de photo de profil, utiliser l'avatar WordPress par défaut
if (empty($profile_photo)) {
    $profile_photo = get_avatar_url($current_user->ID, array('size' => 200));
}

// Déterminer le badge selon le type d'utilisateur
$badge = ($user_type === 'artiste') ? 'Artiste' : 'Super Admin';

// Formater les genres pour l'affichage
$genres_display = '';
if (!empty($genres) && is_array($genres)) {
    $genres_display = implode(', ', array_map('ucfirst', $genres));
}
?>

<main class="profile-page">
    <div class="profile-wrapper">
        <!-- Bannière de profil avec photo et infos -->
        <div class="profile-banner">
            <?php if (!empty($banner)): ?>
                <img src="<?php echo esc_url($banner); ?>" alt="Bannière de profil" class="banner-image">
            <?php else: ?>
                <div class="banner-placeholder"></div>
            <?php endif; ?>
            
            <!-- Photo de profil en bas à droite -->
            <div class="profile-photo-container">
                <img src="<?php echo esc_url($profile_photo); ?>" alt="<?php echo esc_attr($current_user->display_name); ?>" class="profile-photo">
            </div>
            
            <!-- Titre "Super Admin" au-dessus de la photo -->
            <div class="profile-badge-title"><?php echo esc_html($badge); ?></div>
            
            <!-- Pseudo à gauche de la photo, aligné avec le titre -->
            <h1 class="profile-username"><?php echo esc_html($current_user->display_name ?: $current_user->user_login); ?></h1>
            
            <!-- Description en dessous du pseudo, alignée avec la photo -->
            <?php if (!empty($description)): ?>
                <p class="profile-description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>

        <!-- Contenu principal -->
        <div class="profile-main">

            <!-- Section genres musicaux -->
            <?php if (!empty($genres_display)): ?>
                <div class="profile-section glassmorphism">
                    <h2 class="section-title">Genres musicaux</h2>
                    <div class="genres-list">
                        <?php 
                        $genres_array = is_array($genres) ? $genres : explode(', ', $genres_display);
                        foreach ($genres_array as $genre): 
                        ?>
                            <span class="genre-tag"><?php echo esc_html(ucfirst(trim($genre))); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Section sons (pour les artistes) -->
            <?php if ($user_type === 'artiste' && !empty($sounds) && is_array($sounds)): ?>
                <div class="profile-section glassmorphism">
                    <h2 class="section-title">Mes sons</h2>
                    <div class="sounds-grid">
                        <?php foreach ($sounds as $sound): ?>
                            <div class="sound-card">
                                <div class="sound-icon">🎵</div>
                                <div class="sound-info">
                                    <p class="sound-name"><?php echo esc_html(basename($sound)); ?></p>
                                    <audio controls class="sound-player">
                                        <source src="<?php echo esc_url($sound); ?>" type="audio/mpeg">
                                        Votre navigateur ne supporte pas l'élément audio.
                                    </audio>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>

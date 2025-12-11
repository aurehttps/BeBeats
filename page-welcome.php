<?php
/**
 * Template Name: Welcome Page
 * Template pour la page de bienvenue après connexion
 */

get_header(); ?>

    <!-- Main Content -->
    <main class="welcome-content">
        <div class="welcome-container">
            <div class="welcome-frame">
                <div class="auth-progress-bar">
                    <div class="auth-progress-bar-fill" style="width: 100%;"></div>
                </div>
                <h1 class="welcome-message">Ravis de te revoir sur</h1>
                <h2 class="welcome-platform">BeBeats</h2>
                
                <?php 
                // Récupérer le nom d'utilisateur depuis la session ou les cookies
                $username = '';
                if (is_user_logged_in()) {
                    $current_user = wp_get_current_user();
                    $username = $current_user->user_login;
                } elseif (isset($_COOKIE['bebeats_username'])) {
                    $username = sanitize_text_field($_COOKIE['bebeats_username']);
                } elseif (isset($_GET['user'])) {
                    $username = sanitize_text_field($_GET['user']);
                }
                ?>
                
                <?php if ($username): ?>
                    <p class="welcome-username"><?php echo esc_html($username); ?></p>
                <?php endif; ?>
                
                <p class="welcome-instruction">
                    Rediriges toi vers la page d'accueil et replonges dans l'univers de la musique.
                </p>
                
                <a href="<?php echo esc_url(home_url('/')); ?>" class="welcome-btn">
                    Retour vers la page d'accueil
                </a>
            </div>
        </div>
    </main>

<?php get_footer(); ?>


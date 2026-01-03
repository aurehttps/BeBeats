<?php
/**
 * Template Name: Auth Start Page
 * Template pour la page de commencement du flow d'authentification
 */

get_header(); ?>

    <!-- Main Content -->
    <main class="auth-start-content">
        <div class="auth-start-container">
            <div class="auth-start-frame">
                <div class="auth-progress-bar">
                    <div class="auth-progress-bar-fill" style="width: 0%;"></div>
                </div>
                <h1 class="auth-start-question">As-tu déjà un compte BeBeats ?</h1>
                
                <div class="auth-start-buttons">
                    <a href="<?php echo esc_url(home_url('/inscription')); ?>" class="auth-start-btn auth-btn-inscription">
                        Inscription
                    </a>
                    <a href="<?php echo esc_url(home_url('/connexion')); ?>" class="auth-start-btn auth-btn-connexion">
                        Connexion
                    </a>
                </div>
            </div>
        </div>
    </main>

<?php get_footer(); ?>


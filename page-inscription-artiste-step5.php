<?php
/**
 * Template Name: Inscription Artiste - Étape 5
 * Template pour la confirmation finale
 */

get_header(); ?>

    <!-- Main Content -->
    <main class="auth-content">
        <div class="auth-container">
            <div class="auth-frame">
                <div class="auth-progress-bar">
                    <div class="auth-progress-bar-fill" style="width: 100%;"></div>
                </div>
                
                <h1 class="auth-title">Ton inscription est finie cher artiste !</h1>
                <p class="auth-subtitle">Il est temps d'explorer les travers de la musique !</p>
                
                <div class="welcome-icon">
                    <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M60 20C45.6406 20 34 31.6406 34 46C34 60.3594 45.6406 72 60 72C74.3594 72 86 60.3594 86 46C86 31.6406 74.3594 20 60 20ZM60 64C50.625 64 43 56.375 43 46C43 35.625 50.625 28 60 28C69.375 28 77 35.625 77 46C77 56.375 69.375 64 60 64Z" fill="url(#questionGradient)"/>
                        <path d="M60 80C50.625 80 43 87.625 43 98H77C77 87.625 69.375 80 60 80Z" fill="url(#questionGradient)"/>
                        <defs>
                            <linearGradient id="questionGradient" x1="0" y1="0" x2="0" y2="120" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#A855F7"/>
                                <stop offset="100%" stop-color="#8B5CF6"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                
                <a href="<?php echo esc_url(home_url('/')); ?>" class="auth-submit-btn">Retour vers la page d'accueil</a>
            </div>
        </div>
    </main>

<?php get_footer(); ?>


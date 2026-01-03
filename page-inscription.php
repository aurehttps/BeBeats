<?php
/**
 * Template Name: Inscription Page
 * Template pour la page de choix Fan/Artiste
 */

get_header(); ?>

    <!-- Main Content -->
    <main class="auth-content">
        <div class="auth-container">
            <div class="auth-frame">
                <div class="auth-progress-bar">
                    <div class="auth-progress-bar-fill" style="width: 10%;"></div>
                </div>
                <h1 class="auth-title">Choisis ton profil</h1>
                <p class="auth-subtitle">Comment souhaites-tu utiliser BeBeats ?</p>
                
                <div class="inscription-choices">
                    <a href="<?php echo esc_url(home_url('/inscription-fan-step1')); ?>" class="inscription-choice-btn">
                        <div class="choice-icon">👤</div>
                        <div class="choice-content">
                            <h2 class="choice-title">Fan</h2>
                            <p class="choice-description">Découvre et partage ta passion pour la musique</p>
                        </div>
                    </a>
                    
                    <a href="<?php echo esc_url(home_url('/inscription-artiste-step1')); ?>" class="inscription-choice-btn">
                        <div class="choice-icon">🎵</div>
                        <div class="choice-content">
                            <h2 class="choice-title">Artiste</h2>
                            <p class="choice-description">Partage ta musique et connecte-toi avec tes fans</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </main>

<?php get_footer(); ?>


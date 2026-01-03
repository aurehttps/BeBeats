<?php
/**
 * Template Name: Inscription Artiste - Étape 4
 * Template pour l'ajout de sons
 */

get_header(); ?>

    <!-- Main Content -->
    <main class="auth-content">
        <div class="auth-container">
            <div class="auth-frame">
                <div class="auth-progress-bar">
                    <div class="auth-progress-bar-fill" style="width: 80%;"></div>
                </div>
                
                <a href="<?php echo esc_url(home_url('/inscription-artiste-step3')); ?>" class="auth-back-btn" aria-label="Retour">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                
                <button class="auth-skip-btn" onclick="document.getElementById('artiste-form-step4').submit();">Passer</button>
                
                <h1 class="auth-title">Bienvenu sur BeBeats cher artiste !</h1>
                <p class="auth-subtitle">Tu y es presque !</p>
                <p class="auth-instruction">Ajoute tes premiers sons pour partager ta musique avec la communauté.</p>
                
                <form class="auth-form" id="artiste-form-step4" method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="bebeats_register_artiste_step4">
                    <?php wp_nonce_field('bebeats_register_artiste_step4_action', 'bebeats_register_artiste_step4_nonce'); ?>
                    <?php 
                    $registration_key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : 
                                       (isset($_COOKIE['bebeats_registration_key']) ? sanitize_text_field($_COOKIE['bebeats_registration_key']) : '');
                    if (!empty($registration_key)): ?>
                        <input type="hidden" name="registration_key" value="<?php echo esc_attr($registration_key); ?>">
                    <?php endif; ?>
                    
                    <div class="profile-actions">
                        <label class="profile-action-btn">
                            <input type="file" name="sounds[]" accept="audio/*" multiple class="profile-file-input">
                            <span class="profile-action-icon">🎵</span>
                            <span class="profile-action-text">Ajoutes un/des son(s)</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="auth-submit-btn">Terminer</button>
                </form>
            </div>
        </div>
    </main>

<?php get_footer(); ?>


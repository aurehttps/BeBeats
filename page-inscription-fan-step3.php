<?php
/**
 * Template Name: Inscription Fan - Étape 3
 * Template pour la création du profil
 */

get_header(); ?>

    <!-- Main Content -->
    <main class="auth-content">
        <div class="auth-container">
            <div class="auth-frame">
                <div class="auth-progress-bar">
                    <div class="auth-progress-bar-fill" style="width: 75%;"></div>
                </div>
                
                <a href="<?php echo esc_url(home_url('/inscription-fan-step2')); ?>" class="auth-back-btn" aria-label="Retour">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                
                <h1 class="auth-title">Bienvenu sur BeBeats futur fan !</h1>
                <p class="auth-subtitle">A présent, créés ta page profil BeBeats.</p>
                
                <form class="auth-form" method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="bebeats_register_fan_step3">
                    <?php wp_nonce_field('bebeats_register_fan_step3_action', 'bebeats_register_fan_step3_nonce'); ?>
                    <?php 
                    $registration_key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : 
                                       (isset($_COOKIE['bebeats_registration_key']) ? sanitize_text_field($_COOKIE['bebeats_registration_key']) : '');
                    if (!empty($registration_key)): ?>
                        <input type="hidden" name="registration_key" value="<?php echo esc_attr($registration_key); ?>">
                    <?php endif; ?>
                    
                    <div class="profile-actions">
                        <label class="profile-action-btn">
                            <input type="file" name="profile_photo" accept="image/*" class="profile-file-input">
                            <span class="profile-action-icon">📷</span>
                            <span class="profile-action-text">Ajoutes une photo de profil</span>
                        </label>
                        
                        <label class="profile-action-btn">
                            <input type="file" name="banner" accept="image/*" class="profile-file-input">
                            <span class="profile-action-icon">🖼️</span>
                            <span class="profile-action-text">Ajoutes une bannière</span>
                        </label>
                        
                        <div class="auth-input-group">
                            <textarea 
                                name="description" 
                                id="description"
                                class="auth-textarea" 
                                placeholder="Écris une description"
                                rows="4"
                            ></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" class="auth-submit-btn">Suivant</button>
                </form>
            </div>
        </div>
    </main>

<?php get_footer(); ?>


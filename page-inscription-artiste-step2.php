<?php
/**
 * Template Name: Inscription Artiste - Étape 2
 * Template pour la sélection des genres musicaux
 */

get_header(); ?>

    <!-- Main Content -->
    <main class="auth-content">
        <div class="auth-container">
            <div class="auth-frame">
                <div class="auth-progress-bar">
                    <div class="auth-progress-bar-fill" style="width: 40%;"></div>
                </div>
                
                <a href="<?php echo esc_url(home_url('/inscription-artiste-step1')); ?>" class="auth-back-btn" aria-label="Retour">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                
                <h1 class="auth-title">Bienvenu sur BeBeats cher artiste !</h1>
                <p class="auth-subtitle">Tu y es presque !</p>
                <p class="auth-instruction">Sélectionnes 1 ou 2 styles musicaux qui représente ta musique.</p>
                
                <form class="auth-form" method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="bebeats_register_artiste_step2">
                    <?php wp_nonce_field('bebeats_register_artiste_step2_action', 'bebeats_register_artiste_step2_nonce'); ?>
                    <?php 
                    $registration_key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : 
                                       (isset($_COOKIE['bebeats_registration_key']) ? sanitize_text_field($_COOKIE['bebeats_registration_key']) : '');
                    if (!empty($registration_key)): ?>
                        <input type="hidden" name="registration_key" value="<?php echo esc_attr($registration_key); ?>">
                    <?php endif; ?>
                    
                    <div class="genres-grid">
                        <?php 
                        $genres = array(
                            'Afrobeat', 'Blues', 'Bossa nova', 'Classique', 'Country',
                            'Disco', 'Electro', 'EDM', 'Folk', 'Funk',
                            'Gospel', 'Hip-hop', 'Indie', 'Hyperpop', 'Jazz',
                            'K-pop', 'Métal', 'Monde', 'Latino', '90\'s',
                            'Opéra', 'Pop', 'Pop-rock', 'Punk', 'Rap',
                            'Reggae', 'Reggaeton', 'Rock', 'RnB', 'Samba',
                            'Shatta', 'Soul', 'Techno', 'Trap'
                        );
                        
                        foreach ($genres as $genre): 
                        ?>
                            <label class="genre-btn">
                                <input type="checkbox" name="genres[]" value="<?php echo esc_attr(strtolower($genre)); ?>" class="genre-checkbox">
                                <span class="genre-label"><?php echo esc_html($genre); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="genres-error" id="genres-error" style="display: none;">
                        Veuillez sélectionner 1 ou 2 genres musicaux.
                    </div>
                    
                    <button type="submit" class="auth-submit-btn">Suivant</button>
                </form>
            </div>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.genre-checkbox');
        const form = document.querySelector('.auth-form');
        const errorDiv = document.getElementById('genres-error');
        const submitBtn = document.querySelector('.auth-submit-btn');
        
        // Limiter à 2 sélections maximum
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checked = document.querySelectorAll('.genre-checkbox:checked');
                if (checked.length > 2) {
                    this.checked = false;
                }
                
                // Afficher le nombre de sélections
                const count = document.querySelectorAll('.genre-checkbox:checked').length;
                if (count >= 1) {
                    submitBtn.style.opacity = '1';
                    errorDiv.style.display = 'none';
                } else {
                    submitBtn.style.opacity = '0.6';
                }
            });
        });
        
        // Validation avant soumission
        form.addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('.genre-checkbox:checked');
            if (checked.length < 1 || checked.length > 2) {
                e.preventDefault();
                errorDiv.style.display = 'block';
                return false;
            }
        });
    });
    </script>

<?php get_footer(); ?>


<?php
/**
 * Template Name: Inscription Artiste - Étape 1
 * Template pour la première étape d'inscription Artiste
 */

get_header(); ?>

    <!-- Main Content -->
    <main class="auth-content">
        <div class="auth-container">
            <div class="auth-frame">
                <div class="auth-progress-bar">
                    <div class="auth-progress-bar-fill" style="width: 20%;"></div>
                </div>
                
                <a href="<?php echo esc_url(home_url('/inscription')); ?>" class="auth-back-btn" aria-label="Retour">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                
                <h1 class="auth-title">Bienvenu sur BeBeats cher artiste !</h1>
                <p class="auth-subtitle">Avant de commencer, créer un compte à fin de partager ta passion.</p>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="auth-error-message">
                        <?php 
                        $error = $_GET['error'];
                        if ($error == '1'): 
                            echo 'Une erreur s\'est produite lors de l\'inscription. Veuillez réessayer.';
                        elseif ($error == '2'):
                            echo 'Cette adresse email est déjà utilisée. Veuillez en choisir une autre.';
                        elseif ($error == '3'):
                            echo 'Ce pseudo est déjà utilisé. Veuillez en choisir un autre.';
                        else:
                            echo 'Une erreur s\'est produite lors de l\'inscription. Veuillez réessayer.';
                        endif;
                        ?>
                    </div>
                <?php endif; ?>
                
                <form class="auth-form" method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="bebeats_register_artiste_step1">
                    <?php wp_nonce_field('bebeats_register_artiste_step1_action', 'bebeats_register_artiste_step1_nonce'); ?>
                    
                    <div class="auth-input-group">
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            class="auth-input" 
                            placeholder="Entre une adresse mail"
                            required
                            autocomplete="email"
                        />
                    </div>
                    
                    <div class="auth-input-group">
                        <input 
                            type="text" 
                            name="username" 
                            id="username"
                            class="auth-input" 
                            placeholder="Entrer un pseudo"
                            required
                            autocomplete="username"
                        />
                    </div>
                    
                    <div class="auth-input-group">
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            class="auth-input" 
                            placeholder="Mot de passe"
                            required
                            autocomplete="new-password"
                        />
                    </div>
                    
                    <button type="submit" class="auth-submit-btn">Suivant</button>
                </form>
            </div>
        </div>
    </main>

<?php get_footer(); ?>


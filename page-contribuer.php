<?php
/**
 * Template Name: Contribuer Page
 * Template pour la page Contribuer
 */

get_header(); ?>

    <!-- Main Content -->
    <main class="main-content">
        <?php if (!is_user_logged_in()): ?>
            <div class="contribuer-panel glassmorphism">
                <p style="text-align: center; color: #fff; padding: 2rem;">
                    Vous devez être connecté pour créer un post. 
                    <a href="<?php echo esc_url(home_url('/auth-start')); ?>" style="color: #A855F7;">Connectez-vous</a>
                </p>
            </div>
        <?php else: ?>
        <div class="contribuer-panel glassmorphism">
            <?php if (isset($_GET['error'])): ?>
                <div class="contribuer-error-message">
                    <?php 
                    if ($_GET['error'] == '1') {
                        echo 'Une erreur s\'est produite. Veuillez réessayer.';
                    } elseif ($_GET['error'] == '2') {
                        echo 'Veuillez ajouter du contenu ou un média.';
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <form id="contribuer-form" method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
                <input type="hidden" name="action" value="bebeats_create_post">
                <?php wp_nonce_field('bebeats_create_post_action', 'bebeats_create_post_nonce'); ?>
                <input type="hidden" name="post_type" id="post-type-input" value="fan-art">
                <!-- Champs cachés pour les options -->
                <input type="hidden" name="allow_comments" id="allow-comments-input" value="1">
                <input type="hidden" name="allow_repost" id="allow-repost-input" value="1">
                <input type="hidden" name="show_likes" id="show-likes-input" value="1">
                <!-- Modal pour identifier des personnes -->
                <div class="tag-user-modal" id="tag-user-modal" style="display: none;">
                    <div class="tag-user-modal-content">
                        <div class="tag-user-modal-header">
                            <h3>Identifier une personne</h3>
                            <button type="button" class="tag-user-modal-close" id="tag-user-modal-close">×</button>
                        </div>
                        <div class="tag-user-modal-body">
                            <input 
                                type="text" 
                                id="tag-user-input" 
                                class="tag-user-search-input" 
                                placeholder="Rechercher un utilisateur..."
                                autocomplete="off"
                            >
                            <div class="user-suggestions" id="user-suggestions" style="display: none;"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal pour les options -->
                <div class="options-modal" id="options-modal" style="display: none;">
                    <div class="options-modal-content">
                        <div class="options-modal-header">
                            <h3>Options de publication</h3>
                            <button type="button" class="options-modal-close" id="options-modal-close">×</button>
                        </div>
                        <div class="options-modal-body">
                            <!-- Toggle pour désactiver les commentaires -->
                            <div class="option-item">
                                <div class="option-label">
                                    <span class="option-title">Autoriser les commentaires</span>
                                    <span class="option-description">Permettre aux autres utilisateurs de commenter ce post</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="toggle-comments" class="toggle-input" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            
                            <!-- Toggle pour désactiver la republication -->
                            <div class="option-item">
                                <div class="option-label">
                                    <span class="option-title">Autoriser la republication</span>
                                    <span class="option-description">Permettre aux autres utilisateurs de republier ce post</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="toggle-repost" class="toggle-input" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            
                            <!-- Toggle pour désactiver l'affichage des likes -->
                            <div class="option-item">
                                <div class="option-label">
                                    <span class="option-title">Afficher les likes</span>
                                    <span class="option-description">Afficher le nombre de likes sur ce post</span>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="toggle-likes" class="toggle-input" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Central Content Area - Template de post -->
                <div class="content-area">
                    <div class="post-template glassmorphism" id="post-template">
                        <!-- En-tête du post -->
                        <div class="post-template-header">
                            <?php 
                            $current_user = wp_get_current_user();
                            $profile_photo = get_user_meta($current_user->ID, 'bebeats_profile_photo', true);
                            if (empty($profile_photo)) {
                                $profile_photo = get_avatar_url($current_user->ID, array('size' => 60));
                            }
                            ?>
                            <img src="<?php echo esc_url($profile_photo); ?>" alt="<?php echo esc_attr($current_user->display_name); ?>" class="post-template-avatar">
                            <div class="post-template-author">
                                <span class="post-template-username"><?php echo esc_html($current_user->display_name ?: $current_user->user_login); ?></span>
                            </div>
                        </div>
                        
                        <!-- Zone de contenu -->
                        <div class="post-template-content">
                            <!-- Aperçu du média -->
                            <div id="media-preview" class="media-preview">
                                <div class="media-placeholder">
                                    <svg class="media-placeholder-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="media-placeholder-text">Ajoutez une image ou une vidéo</p>
                                </div>
                            </div>
                            
                            <!-- Zone pour les personnes identifiées -->
                            <div class="tagged-users-container" id="tagged-users" style="display: none;">
                                <div class="tagged-users-label">Identifié :</div>
                                <div class="tagged-users-list" id="tagged-list"></div>
                            </div>
                            
                            <!-- Champ description -->
                            <div class="description-container">
                                <textarea 
                                    name="content" 
                                    id="post-content"
                                    class="post-content-textarea post-content-description" 
                                    placeholder="Ajoutez une description..."
                                    rows="3"
                                ></textarea>
                            </div>
                        </div>
                        
                        <!-- Boutons d'interaction (simulation) -->
                        <div class="post-template-actions">
                            <div class="post-action-item">
                                <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span>0</span>
                            </div>
                            <div class="post-action-item">
                                <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>0</span>
                            </div>
                            <div class="post-action-item">
                                <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span>0</span>
                            </div>
                            <div class="post-action-item">
                                <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                <span>0</span>
                            </div>
                            <div class="post-action-item">
                                <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Right Side Menu -->
            <aside class="side-menu">
                <button type="button" class="menu-item" id="tag-user-btn" aria-label="Identifier une personne">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="menu-label">Identifier une personne</span>
                </button>
                
                <button type="button" class="menu-item" id="options-btn" aria-label="Options">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="menu-label">Options</span>
                </button>
                
                <label class="menu-item menu-item-media" aria-label="Contenu multimédia">
                    <input type="file" name="media" id="media-input" accept="image/*,video/*" style="display: none;">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="menu-label">Contenu multimédia</span>
                </label>
                
                <button type="submit" class="menu-item menu-item-publish" aria-label="Publier">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    <span class="menu-label">Publier</span>
                </button>
            </aside>

            </form>
        </div>
        <?php endif; ?>
    </main>

<?php get_footer(); ?>


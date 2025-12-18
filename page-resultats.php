<?php
/**
 * Template Name: Résultats Page
 * Template pour la page Résultats de recherche
 */

get_header(); 

// Passer le timestamp actuel du serveur à JavaScript pour synchronisation
$server_timestamp = current_time('timestamp');
$current_user_id = is_user_logged_in() ? get_current_user_id() : 0;

// Récupérer le terme de recherche depuis l'URL
$search_term = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';

global $wpdb;
$posts_table = $wpdb->prefix . 'bebeats_posts';
$reactions_table = $wpdb->prefix . 'bebeats_post_reactions';

// Fonction helper pour récupérer les statistiques d'un post
function bebeats_get_post_stats($post_id, $current_user_id, $wpdb, $reactions_table) {
    $likes_count = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(DISTINCT user_id) FROM $reactions_table WHERE post_id = %d AND reaction_type = 'like'",
        $post_id
    ));
    
    $comments_count = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $reactions_table WHERE post_id = %d AND reaction_type = 'comment' AND (parent_comment_id IS NULL OR parent_comment_id = 0)",
        $post_id
    ));
    
    $reposts_count = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(DISTINCT user_id) FROM $reactions_table WHERE post_id = %d AND reaction_type = 'repost'",
        $post_id
    ));
    
    $user_liked = false;
    $user_favorited = false;
    $user_reposted = false;
    
    if ($current_user_id > 0) {
        $user_liked = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $reactions_table WHERE post_id = %d AND user_id = %d AND reaction_type = 'like'",
            $post_id, $current_user_id
        )) > 0;
        
        $user_favorited = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $reactions_table WHERE post_id = %d AND user_id = %d AND reaction_type = 'favorite'",
            $post_id, $current_user_id
        )) > 0;
        
        $user_reposted = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $reactions_table WHERE post_id = %d AND user_id = %d AND reaction_type = 'repost'",
            $post_id, $current_user_id
        )) > 0;
    }
    
    return array(
        'likes_count' => $likes_count,
        'comments_count' => $comments_count,
        'reposts_count' => $reposts_count,
        'user_liked' => $user_liked,
        'user_favorited' => $user_favorited,
        'user_reposted' => $user_reposted
    );
}

// Fonction helper pour afficher un post dans les résultats de recherche
function bebeats_render_search_post($post, $stats, $current_user_id) {
    $time_ago = human_time_diff(strtotime($post->created_at), current_time('timestamp'));
    $post_timestamp = strtotime($post->created_at);
    ?>
    <a href="<?php echo esc_url(home_url('/feed-page#post-' . $post->id)); ?>" class="search-post-link">
        <article class="search-post-item glassmorphism">
            <div class="search-post-header">
                <img src="<?php echo esc_url($post->profile_photo); ?>" alt="<?php echo esc_attr($post->display_name ?: $post->user_login); ?>" class="search-post-avatar">
                <div class="search-post-author-info">
                    <h3 class="search-post-author-name"><?php echo esc_html($post->display_name ?: $post->user_login); ?></h3>
                    <span class="search-post-time" data-timestamp="<?php echo esc_attr($post_timestamp); ?>">Il y a <?php echo esc_html($time_ago); ?></span>
                </div>
                <span class="search-post-type"><?php 
                    echo esc_html(ucfirst(str_replace('-', ' ', $post->post_type))); 
                ?></span>
            </div>
            
            <div class="search-post-content">
                <?php if (!empty($post->content)): ?>
                    <p class="search-post-text"><?php echo nl2br(esc_html($post->content)); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($post->media_url)): ?>
                    <div class="search-post-media">
                        <?php if ($post->media_type === 'image'): ?>
                            <img src="<?php echo esc_url($post->media_url); ?>" alt="Post media" class="search-post-image">
                        <?php elseif ($post->media_type === 'video'): ?>
                            <video controls class="search-post-video">
                                <source src="<?php echo esc_url($post->media_url); ?>" type="video/mp4">
                                Votre navigateur ne supporte pas l'élément vidéo.
                            </video>
                        <?php elseif ($post->media_type === 'audio'): ?>
                            <audio controls class="search-post-audio">
                                <source src="<?php echo esc_url($post->media_url); ?>" type="audio/mpeg">
                                Votre navigateur ne supporte pas l'élément audio.
                            </audio>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="search-post-actions">
                <button class="search-post-action-btn like-btn <?php echo $stats['user_liked'] ? 'active' : ''; ?>" 
                        data-post-id="<?php echo esc_attr($post->id); ?>" 
                        data-reaction-type="like"
                        onclick="event.preventDefault(); event.stopPropagation(); handleSearchReaction(<?php echo esc_js($post->id); ?>, 'like', this);"
                        <?php echo !is_user_logged_in() ? 'disabled' : ''; ?>>
                    <svg class="search-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <span class="search-action-count"><?php echo esc_html($stats['likes_count']); ?></span>
                </button>
                
                <button class="search-post-action-btn comment-btn" 
                        data-post-id="<?php echo esc_attr($post->id); ?>"
                        onclick="event.preventDefault(); event.stopPropagation(); window.location.href='<?php echo esc_url(home_url('/feed-page#post-' . $post->id)); ?>';">
                    <svg class="search-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span class="search-action-count"><?php echo esc_html($stats['comments_count']); ?></span>
                </button>
            </div>
        </article>
    </a>
    <?php
}

// Rechercher les posts correspondants si un terme de recherche est présent
$search_results = array();
if (!empty($search_term)) {
    $search_like = '%' . $wpdb->esc_like($search_term) . '%';
    $search_results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT p.*, u.display_name, u.user_login,
                    (SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = u.ID AND meta_key = 'bebeats_profile_photo' LIMIT 1) as profile_photo
             FROM $posts_table p
             LEFT JOIN {$wpdb->users} u ON p.user_id = u.ID
             WHERE (p.content LIKE %s OR u.display_name LIKE %s OR u.user_login LIKE %s)
             ORDER BY p.created_at DESC
             LIMIT 50",
            $search_like,
            $search_like,
            $search_like
        )
    );
    
    // Si pas de photo de profil, utiliser l'avatar par défaut
    foreach ($search_results as $post) {
        if (empty($post->profile_photo)) {
            $post->profile_photo = get_avatar_url($post->user_id, array('size' => 60));
        }
    }
}
?>

    <!-- Main Content -->
    <main class="search-content">
        <!-- Search Results Section -->
        <section class="search-results" id="search-results-section" style="<?php echo !empty($search_term) ? 'display: block;' : 'display: none;'; ?>">
            <h2 class="results-title">Résultats de recherche</h2>
            <p class="results-query" id="search-query">
                <?php if (!empty($search_term)): ?>
                    Recherche pour : "<?php echo esc_html($search_term); ?>"
                <?php endif; ?>
            </p>
            
            <!-- Résultats de recherche -->
            <?php if (!empty($search_term) && !empty($search_results)): ?>
                <div class="search-results-list">
                    <?php foreach ($search_results as $post): 
                        $stats = bebeats_get_post_stats($post->id, $current_user_id, $wpdb, $reactions_table);
                        bebeats_render_search_post($post, $stats, $current_user_id);
                    endforeach; ?>
                </div>
            <?php elseif (!empty($search_term) && empty($search_results)): ?>
                <div class="search-no-results glassmorphism">
                    <p>Aucun résultat trouvé pour "<?php echo esc_html($search_term); ?>"</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Discovery Card -->
        <section class="discovery-card glassmorphism">
            <div class="discovery-content">
                <div class="discovery-text">
                    <h1 class="discovery-title">Découvrez</h1>
                    <ol class="discovery-steps">
                        <li>Choisissez une catégorie</li>
                        <li>Roll</li>
                        <li>Enjoy</li>
                    </ol>
                    <button class="roll-btn">Roll !</button>
                </div>
                <div class="discovery-graphic">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/discovery-image.png" alt="Découvrez" class="discovery-image">
                </div>
            </div>
        </section>

        <!-- Category Buttons -->
        <section class="categories-grid">
            <button class="category-btn glassmorphism" data-category="Musique">Musique</button>
            <button class="category-btn glassmorphism" data-category="Fan Arts">Fan Arts</button>
            <button class="category-btn glassmorphism" data-category="Evènement">Evènement</button>
            <button class="category-btn glassmorphism" data-category="Article">Article</button>
        </section>

        <!-- Roll Result Display -->
        <section class="roll-result-section" id="roll-result-section" style="display: none;">
            <div class="roll-result-card glassmorphism">
                <div class="roll-result-header">
                    <div class="roll-result-user">
                        <img src="" alt="" class="roll-result-avatar" id="roll-result-avatar">
                        <div class="roll-result-user-info">
                            <span class="roll-result-username" id="roll-result-username"></span>
                            <span class="roll-result-time" id="roll-result-time"></span>
                        </div>
                    </div>
                </div>
                <div class="roll-result-content" id="roll-result-content">
                    <p class="roll-result-text" id="roll-result-text"></p>
                    <div class="roll-result-media" id="roll-result-media"></div>
                </div>
                <div class="roll-result-actions" id="roll-result-actions">
                    <div class="roll-result-stat">
                        <svg class="roll-stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <span id="roll-result-likes">0</span>
                    </div>
                    <div class="roll-result-stat">
                        <svg class="roll-stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span id="roll-result-comments">0</span>
                    </div>
                    <div class="roll-result-stat">
                        <svg class="roll-stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span id="roll-result-reposts">0</span>
                    </div>
                    <div class="roll-result-stat">
                        <svg class="roll-stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                        </svg>
                        <span id="roll-result-favorites">0</span>
                    </div>
                </div>
                <a href="#" id="roll-result-event-link" class="roll-result-event-link" style="display:none; margin-top:1rem; text-decoration:none;">
                    Voir cet évènement dans la page Event
                </a>
            </div>
        </section>
    </main>

    <script>
        // Passer le timestamp du serveur pour synchronisation
        window.bebeatsServerTime = <?php echo $server_timestamp; ?>;
        window.bebeatsClientTime = Math.floor(Date.now() / 1000);
        window.bebeatsTimeOffset = window.bebeatsServerTime - window.bebeatsClientTime;
        
        // Fonction pour gérer les réactions dans les résultats de recherche
        function handleSearchReaction(postId, reactionType, button) {
            if (button.disabled) return;
            
            const formData = new FormData();
            formData.append('action', 'bebeats_post_reaction');
            formData.append('post_id', postId);
            formData.append('reaction_type', reactionType);
            formData.append('action_type', 'toggle');
            formData.append('bebeats_reaction_nonce', '<?php echo wp_create_nonce('bebeats_reaction_action'); ?>');
            
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const countElement = button.querySelector('.search-action-count');
                    if (countElement) {
                        const currentCount = parseInt(countElement.textContent) || 0;
                        
                        if (data.data.action === 'added') {
                            button.classList.add('active');
                            countElement.textContent = currentCount + 1;
                        } else {
                            button.classList.remove('active');
                            countElement.textContent = Math.max(0, currentCount - 1);
                        }
                    } else {
                        if (data.data.action === 'added') {
                            button.classList.add('active');
                        } else {
                            button.classList.remove('active');
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Récupérer le terme de recherche depuis l'URL
            const urlParams = new URLSearchParams(window.location.search);
            const searchTerm = urlParams.get('q');
            const searchResultsSection = document.getElementById('search-results-section');
            const searchQueryElement = document.getElementById('search-query');
            
            // Le contenu "Découvrez" et les catégories sont toujours visibles (pas de JavaScript pour les masquer)
            
            if (searchTerm && searchTerm.trim() !== '') {
                // Afficher les résultats de recherche si un terme de recherche est présent
                if (searchResultsSection) {
                    searchResultsSection.style.display = 'block';
                }
                if (searchQueryElement) {
                    searchQueryElement.textContent = 'Recherche pour : "' + searchTerm + '"';
                }
            } else {
                // Masquer les résultats de recherche s'il n'y a pas de terme de recherche
                if (searchResultsSection) {
                    searchResultsSection.style.display = 'none';
                }
            }

            // Gestion du Roll
            let selectedCategory = null;
            const categoryButtons = document.querySelectorAll('.category-btn');
            const rollBtn = document.querySelector('.roll-btn');
            const rollResultSection = document.getElementById('roll-result-section');

            // Gestion des clics sur les boutons de catégorie
            categoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Retirer la classe active de tous les boutons
                    categoryButtons.forEach(btn => btn.classList.remove('category-btn-active'));
                    // Ajouter la classe active au bouton cliqué
                    this.classList.add('category-btn-active');
                    selectedCategory = this.getAttribute('data-category');
                });
            });

            // Gestion du clic sur le bouton Roll
            if (rollBtn) {
                rollBtn.addEventListener('click', function() {
                    if (!selectedCategory) {
                        alert('Veuillez d\'abord sélectionner une catégorie');
                        return;
                    }

                    if (this.disabled) {
                        return; // Éviter les clics multiples
                    }

                    // Désactiver le bouton pendant la requête
                    this.disabled = true;
                    this.textContent = 'Roll en cours...';

                    // Faire la requête AJAX
                    const formData = new FormData();
                    formData.append('action', 'bebeats_roll_random_post');
                    formData.append('category', selectedCategory);

                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        rollBtn.disabled = false;
                        rollBtn.textContent = 'Roll !';

                        if (data.success) {
                            displayRollResult(data.data);
                        } else {
                            alert(data.data.message || 'Erreur lors du Roll');
                        }
                    })
                    .catch(error => {
                        rollBtn.disabled = false;
                        rollBtn.textContent = 'Roll !';
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue');
                    });
                });
            }

            // Fonction pour afficher le résultat du Roll
            function displayRollResult(post) {
                // Afficher la section de résultat
                if (rollResultSection) {
                    rollResultSection.style.display = 'block';
                    rollResultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }

                // Remplir les informations
                document.getElementById('roll-result-avatar').src = post.profile_photo;
                document.getElementById('roll-result-username').textContent = post.username;
                document.getElementById('roll-result-time').textContent = post.time_ago;

                // Contenu texte
                const textElement = document.getElementById('roll-result-text');
                if (post.content) {
                    textElement.textContent = post.content;
                    textElement.style.display = 'block';
                } else {
                    textElement.style.display = 'none';
                }

                // Média
                const mediaElement = document.getElementById('roll-result-media');
                mediaElement.innerHTML = '';

                if (post.media_url) {
                    if (post.media_type === 'image') {
                        const img = document.createElement('img');
                        img.src = post.media_url;
                        img.alt = 'Média';
                        img.className = 'roll-result-media-image';
                        mediaElement.appendChild(img);
                    } else if (post.media_type === 'video') {
                        const video = document.createElement('video');
                        video.src = post.media_url;
                        video.controls = true;
                        video.className = 'roll-result-media-video';
                        mediaElement.appendChild(video);
                    } else if (post.media_type === 'audio') {
                        const audio = document.createElement('audio');
                        audio.src = post.media_url;
                        audio.controls = true;
                        audio.className = 'roll-result-media-audio';
                        mediaElement.appendChild(audio);
                    }
                }

                // Statistiques
                document.getElementById('roll-result-likes').textContent = post.likes;
                document.getElementById('roll-result-comments').textContent = post.comments;
                document.getElementById('roll-result-reposts').textContent = post.reposts;
                document.getElementById('roll-result-favorites').textContent = post.favorites;

                // Si c'est un évènement, afficher un lien direct vers la page Event avec ancre
                const eventLink = document.getElementById('roll-result-event-link');
                if (post.post_type === 'event') {
                    eventLink.style.display = 'inline-flex';
                    eventLink.href = '<?php echo esc_url( home_url('/event') ); ?>' + '#event-' + post.id;
                } else if (eventLink) {
                    eventLink.style.display = 'none';
                }
            }
        });
    </script>

<?php get_footer(); ?>


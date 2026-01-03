<?php
/**
 * Template Name: Recherche Page
 * Template pour la page Recherche
 */

get_header(); 

// Passer le timestamp actuel du serveur à JavaScript pour synchronisation
$server_timestamp = current_time('timestamp');
$current_user_id = is_user_logged_in() ? get_current_user_id() : 0;

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

// Fonction helper pour afficher un post dans un carrousel (format compact)
function bebeats_render_carousel_post($post, $stats, $current_user_id) {
    $time_ago = human_time_diff(strtotime($post->created_at), current_time('timestamp'));
    $post_timestamp = strtotime($post->created_at);
    ?>
    <a href="<?php echo esc_url(home_url('/feed-page#post-' . $post->id)); ?>" class="carousel-post-link">
        <article class="carousel-post-item">
            <div class="carousel-post-header">
                <span class="carousel-post-time" data-timestamp="<?php echo esc_attr($post_timestamp); ?>">Il y a <?php echo esc_html($time_ago); ?></span>
                <span class="carousel-post-type"><?php 
                    echo esc_html(ucfirst(str_replace('-', ' ', $post->post_type))); 
                ?></span>
            </div>
            
            <div class="carousel-post-content">
                <?php if (!empty($post->content)): ?>
                    <p class="carousel-post-text"><?php echo nl2br(esc_html($post->content)); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($post->media_url)): ?>
                    <div class="carousel-post-media">
                        <?php if ($post->media_type === 'image'): ?>
                            <img src="<?php echo esc_url($post->media_url); ?>" alt="Post media" class="carousel-post-image">
                        <?php elseif ($post->media_type === 'video'): ?>
                            <video controls class="carousel-post-video">
                                <source src="<?php echo esc_url($post->media_url); ?>" type="video/mp4">
                                Votre navigateur ne supporte pas l'élément vidéo.
                            </video>
                        <?php elseif ($post->media_type === 'audio'): ?>
                            <audio controls class="carousel-post-audio">
                                <source src="<?php echo esc_url($post->media_url); ?>" type="audio/mpeg">
                                Votre navigateur ne supporte pas l'élément audio.
                            </audio>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="carousel-post-actions">
                <button class="carousel-post-action-btn like-btn <?php echo $stats['user_liked'] ? 'active' : ''; ?>" 
                        data-post-id="<?php echo esc_attr($post->id); ?>" 
                        data-reaction-type="like"
                        onclick="event.preventDefault(); event.stopPropagation(); handleProfileReaction(<?php echo esc_js($post->id); ?>, 'like', this);">
                    <svg class="carousel-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <span class="carousel-action-count"><?php echo esc_html($stats['likes_count']); ?></span>
                </button>
                
                <button class="carousel-post-action-btn comment-btn" 
                        data-post-id="<?php echo esc_attr($post->id); ?>"
                        onclick="event.preventDefault(); event.stopPropagation(); window.location.href='<?php echo esc_url(home_url('/feed-page#post-' . $post->id)); ?>';">
                    <svg class="carousel-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span class="carousel-action-count"><?php echo esc_html($stats['comments_count']); ?></span>
                </button>
            </div>
        </article>
    </a>
    <?php
}

// Fonction helper pour afficher un post dans le format feed (format complet)
function bebeats_render_feed_carousel_post($post, $stats, $current_user_id, $wpdb) {
    $time_ago = human_time_diff(strtotime($post->created_at), current_time('timestamp'));
    $post_timestamp = strtotime($post->created_at);
    
    // Récupérer les infos utilisateur
    $user_info = $wpdb->get_row($wpdb->prepare(
        "SELECT u.display_name, u.user_login,
                (SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = u.ID AND meta_key = 'bebeats_profile_photo' LIMIT 1) as profile_photo
         FROM {$wpdb->users} u
         WHERE u.ID = %d",
        $post->user_id
    ));
    
    $profile_photo = !empty($user_info->profile_photo) ? $user_info->profile_photo : get_avatar_url($post->user_id, array('size' => 60));
    ?>
    <article class="carousel-feed-post glassmorphism" data-post-id="<?php echo esc_attr($post->id); ?>">
        <div class="post-header">
            <img src="<?php echo esc_url($profile_photo); ?>" alt="<?php echo esc_attr($user_info->display_name ?: $user_info->user_login); ?>" class="post-author-avatar">
            <div class="post-author-info">
                <h3 class="post-author-name"><?php echo esc_html($user_info->display_name ?: $user_info->user_login); ?></h3>
                <span class="post-time" data-timestamp="<?php echo esc_attr($post_timestamp); ?>">Il y a <?php echo esc_html($time_ago); ?></span>
            </div>
        </div>
        
        <div class="post-content">
            <?php if (!empty($post->content)): ?>
                <p class="post-text"><?php echo nl2br(esc_html($post->content)); ?></p>
            <?php endif; ?>
            
            <?php if (!empty($post->media_url)): ?>
                <div class="post-media">
                    <?php if ($post->media_type === 'image'): ?>
                        <img src="<?php echo esc_url($post->media_url); ?>" alt="Post media" class="post-media-image">
                    <?php elseif ($post->media_type === 'video'): ?>
                        <video controls class="post-media-video">
                            <source src="<?php echo esc_url($post->media_url); ?>" type="video/mp4">
                            Votre navigateur ne supporte pas l'élément vidéo.
                        </video>
                    <?php elseif ($post->media_type === 'audio'): ?>
                        <audio controls class="post-media-audio">
                            <source src="<?php echo esc_url($post->media_url); ?>" type="audio/mpeg">
                            Votre navigateur ne supporte pas l'élément audio.
                        </audio>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="post-actions">
            <div class="post-actions-left">
                <button class="post-action-btn like-btn <?php echo $stats['user_liked'] ? 'active' : ''; ?>" 
                        data-post-id="<?php echo esc_attr($post->id); ?>" 
                        data-reaction-type="like"
                        <?php echo !is_user_logged_in() ? 'disabled' : ''; ?>>
                    <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <span class="action-count"><?php echo esc_html($stats['likes_count']); ?></span>
                </button>
                
                <button class="post-action-btn comment-btn" 
                        data-post-id="<?php echo esc_attr($post->id); ?>"
                        <?php echo !is_user_logged_in() ? 'disabled' : ''; ?>>
                    <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span class="action-count"><?php echo esc_html($stats['comments_count']); ?></span>
                </button>
                
                <button class="post-action-btn repost-btn <?php echo $stats['user_reposted'] ? 'active' : ''; ?>" 
                        data-post-id="<?php echo esc_attr($post->id); ?>" 
                        data-reaction-type="repost"
                        <?php echo !is_user_logged_in() ? 'disabled' : ''; ?>>
                    <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span class="action-count"><?php echo esc_html($stats['reposts_count']); ?></span>
                </button>
            </div>
            
            <button class="post-action-btn favorite-btn <?php echo $stats['user_favorited'] ? 'active' : ''; ?>" 
                    data-post-id="<?php echo esc_attr($post->id); ?>" 
                    data-reaction-type="favorite"
                    <?php echo !is_user_logged_in() ? 'disabled' : ''; ?>>
                <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                </svg>
            </button>
        </div>
    </article>
    <?php
}
?>

<main class="recherche-page">
    <div class="recherche-container">
        
        <?php if ($current_user_id > 0): ?>
            <!-- Carrousel: Favoris Fan-Art -->
            <?php
            $favorite_fanart_posts = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT p.*, u.display_name, u.user_login,
                            (SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = u.ID AND meta_key = 'bebeats_profile_photo' LIMIT 1) as profile_photo
                     FROM $posts_table p
                     INNER JOIN $reactions_table r ON p.id = r.post_id AND r.user_id = %d AND r.reaction_type = 'favorite'
                     LEFT JOIN {$wpdb->users} u ON p.user_id = u.ID
                     WHERE p.post_type = 'fan-art'
                     ORDER BY r.created_at DESC
                     LIMIT 20",
                    $current_user_id
                )
            );
            
            if (!empty($favorite_fanart_posts)): ?>
                <div class="profile-section glassmorphism">
                    <h2 class="section-title">Fan-Art</h2>
                    <div class="carousel-container">
                        <button class="carousel-nav-btn carousel-prev" aria-label="Précédent">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div class="carousel-wrapper">
                            <div class="carousel-track" data-carousel="fanart-fav">
                                <?php foreach ($favorite_fanart_posts as $post): 
                                    $stats = bebeats_get_post_stats($post->id, $current_user_id, $wpdb, $reactions_table);
                                    bebeats_render_carousel_post($post, $stats, $current_user_id);
                                endforeach; ?>
                            </div>
                        </div>
                        <button class="carousel-nav-btn carousel-next" aria-label="Suivant">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Carrousel: Favoris Audio -->
            <?php
            $favorite_audio_posts = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT p.*, u.display_name, u.user_login,
                            (SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = u.ID AND meta_key = 'bebeats_profile_photo' LIMIT 1) as profile_photo
                     FROM $posts_table p
                     INNER JOIN $reactions_table r ON p.id = r.post_id AND r.user_id = %d AND r.reaction_type = 'favorite'
                     LEFT JOIN {$wpdb->users} u ON p.user_id = u.ID
                     WHERE p.post_type = 'audio'
                     ORDER BY r.created_at DESC
                     LIMIT 20",
                    $current_user_id
                )
            );
            
            if (!empty($favorite_audio_posts)): ?>
                <div class="profile-section glassmorphism">
                    <h2 class="section-title">Audio</h2>
                    <div class="carousel-container">
                        <button class="carousel-nav-btn carousel-prev" aria-label="Précédent">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div class="carousel-wrapper">
                            <div class="carousel-track" data-carousel="audio-fav">
                                <?php foreach ($favorite_audio_posts as $post): 
                                    $stats = bebeats_get_post_stats($post->id, $current_user_id, $wpdb, $reactions_table);
                                    bebeats_render_carousel_post($post, $stats, $current_user_id);
                                endforeach; ?>
                            </div>
                        </div>
                        <button class="carousel-nav-btn carousel-next" aria-label="Suivant">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Carrousel: Favoris Articles -->
            <?php
            $favorite_article_posts = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT p.*, u.display_name, u.user_login,
                            (SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = u.ID AND meta_key = 'bebeats_profile_photo' LIMIT 1) as profile_photo
                     FROM $posts_table p
                     INNER JOIN $reactions_table r ON p.id = r.post_id AND r.user_id = %d AND r.reaction_type = 'favorite'
                     LEFT JOIN {$wpdb->users} u ON p.user_id = u.ID
                     WHERE p.post_type = 'post'
                     ORDER BY r.created_at DESC
                     LIMIT 20",
                    $current_user_id
                )
            );
            
            if (!empty($favorite_article_posts)): ?>
                <div class="profile-section glassmorphism">
                    <h2 class="section-title">Articles</h2>
                    <div class="carousel-container">
                        <button class="carousel-nav-btn carousel-prev" aria-label="Précédent">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div class="carousel-wrapper">
                            <div class="carousel-track" data-carousel="articles-fav">
                                <?php foreach ($favorite_article_posts as $post): 
                                    $stats = bebeats_get_post_stats($post->id, $current_user_id, $wpdb, $reactions_table);
                                    bebeats_render_carousel_post($post, $stats, $current_user_id);
                                endforeach; ?>
                            </div>
                        </div>
                        <button class="carousel-nav-btn carousel-next" aria-label="Suivant">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="profile-section glassmorphism">
                <p style="text-align: center; color: #fff; padding: 2rem;">
                    Vous devez être connecté pour voir vos favoris. 
                    <a href="<?php echo esc_url(home_url('/auth-start')); ?>" style="color: #A855F7;">Connectez-vous</a>
                </p>
            </div>
        <?php endif; ?>
        
    </div>
</main>

<script>
    // Passer le timestamp du serveur pour synchronisation
    window.bebeatsServerTime = <?php echo $server_timestamp; ?>;
    window.bebeatsClientTime = Math.floor(Date.now() / 1000);
    window.bebeatsTimeOffset = window.bebeatsServerTime - window.bebeatsClientTime;
</script>

<?php get_footer(); ?>


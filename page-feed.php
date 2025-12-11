<?php
/**
 * Template Name: Feed Page
 * Template pour la page Feed (tous les posts)
 */

get_header(); 

$current_user_id = is_user_logged_in() ? get_current_user_id() : 0;
?>

<main class="feed-page">
    <div class="feed-container">
        <?php
        global $wpdb;
        $posts_table = $wpdb->prefix . 'bebeats_posts';
        $reactions_table = $wpdb->prefix . 'bebeats_post_reactions';
        
        // Récupérer tous les posts avec les infos utilisateur
        $posts = $wpdb->get_results(
            "SELECT p.*, u.display_name, u.user_login, 
                    (SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = u.ID AND meta_key = 'bebeats_profile_photo' LIMIT 1) as profile_photo
             FROM $posts_table p
             LEFT JOIN {$wpdb->users} u ON p.user_id = u.ID
             ORDER BY p.created_at DESC
             LIMIT 50"
        );
        
        if (empty($posts)): ?>
            <div class="feed-empty glassmorphism">
                <p>Aucun post pour le moment. Soyez le premier à publier !</p>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): 
                // Récupérer les réactions pour ce post
                $likes_count = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM $reactions_table WHERE post_id = %d AND reaction_type = 'like'",
                    $post->id
                ));
                
                $comments = $wpdb->get_results($wpdb->prepare(
                    "SELECT r.*, u.display_name, u.user_login,
                            pm1.meta_value as profile_photo
                     FROM $reactions_table r
                     LEFT JOIN {$wpdb->users} u ON r.user_id = u.ID
                     LEFT JOIN {$wpdb->usermeta} pm1 ON u.ID = pm1.user_id AND pm1.meta_key = 'bebeats_profile_photo'
                     WHERE r.post_id = %d AND r.reaction_type = 'comment' AND r.parent_comment_id IS NULL
                     ORDER BY r.created_at ASC",
                    $post->id
                ));
                
                $comments_count = count($comments);
                
                $reposts_count = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM $reactions_table WHERE post_id = %d AND reaction_type = 'repost'",
                    $post->id
                ));
                
                // Vérifier si l'utilisateur actuel a liké/favorisé/reposté ce post
                $user_liked = false;
                $user_favorited = false;
                $user_reposted = false;
                
                if ($current_user_id > 0) {
                    $user_liked = $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(*) FROM $reactions_table WHERE post_id = %d AND user_id = %d AND reaction_type = 'like'",
                        $post->id, $current_user_id
                    )) > 0;
                    
                    $user_favorited = $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(*) FROM $reactions_table WHERE post_id = %d AND user_id = %d AND reaction_type = 'favorite'",
                        $post->id, $current_user_id
                    )) > 0;
                    
                    $user_reposted = $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(*) FROM $reactions_table WHERE post_id = %d AND user_id = %d AND reaction_type = 'repost'",
                        $post->id, $current_user_id
                    )) > 0;
                }
                
                // Photo de profil
                $profile_photo = !empty($post->profile_photo) ? $post->profile_photo : get_avatar_url($post->user_id, array('size' => 60));
                
                // Calculer le temps écoulé
                $time_ago = human_time_diff(strtotime($post->created_at), current_time('timestamp'));
            ?>
                <article class="feed-post glassmorphism" data-post-id="<?php echo esc_attr($post->id); ?>">
                    <!-- En-tête du post -->
                    <div class="post-header">
                        <img src="<?php echo esc_url($profile_photo); ?>" alt="<?php echo esc_attr($post->display_name ?: $post->user_login); ?>" class="post-author-avatar">
                        <div class="post-author-info">
                            <h3 class="post-author-name"><?php echo esc_html($post->display_name ?: $post->user_login); ?></h3>
                            <span class="post-time">Il y a <?php echo esc_html($time_ago); ?></span>
                        </div>
                    </div>
                    
                    <!-- Contenu du post -->
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
                    
                    <!-- Actions du post -->
                    <div class="post-actions">
                        <button class="post-action-btn like-btn <?php echo $user_liked ? 'active' : ''; ?>" 
                                data-post-id="<?php echo esc_attr($post->id); ?>" 
                                data-reaction-type="like"
                                <?php echo !is_user_logged_in() ? 'disabled' : ''; ?>>
                            <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span class="action-count"><?php echo esc_html($likes_count); ?></span>
                        </button>
                        
                        <button class="post-action-btn comment-btn" 
                                data-post-id="<?php echo esc_attr($post->id); ?>"
                                <?php echo !is_user_logged_in() ? 'disabled' : ''; ?>>
                            <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span class="action-count"><?php echo esc_html($comments_count); ?></span>
                        </button>
                        
                        <button class="post-action-btn repost-btn <?php echo $user_reposted ? 'active' : ''; ?>" 
                                data-post-id="<?php echo esc_attr($post->id); ?>" 
                                data-reaction-type="repost"
                                <?php echo !is_user_logged_in() ? 'disabled' : ''; ?>>
                            <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span class="action-count"><?php echo esc_html($reposts_count); ?></span>
                        </button>
                        
                        <button class="post-action-btn favorite-btn <?php echo $user_favorited ? 'active' : ''; ?>" 
                                data-post-id="<?php echo esc_attr($post->id); ?>" 
                                data-reaction-type="favorite"
                                <?php echo !is_user_logged_in() ? 'disabled' : ''; ?>>
                            <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Zone de commentaires -->
                    <div class="post-comments" id="comments-<?php echo esc_attr($post->id); ?>" style="display: none;">
                        <?php if (is_user_logged_in()): ?>
                            <div class="comment-form">
                                <textarea class="comment-input" placeholder="Écrire un commentaire..." rows="2"></textarea>
                                <button class="comment-submit-btn" data-post-id="<?php echo esc_attr($post->id); ?>">Publier</button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="comments-list">
                            <?php foreach ($comments as $comment): 
                                $comment_profile_photo = !empty($comment->profile_photo) ? $comment->profile_photo : get_avatar_url($comment->user_id, array('size' => 40));
                                $comment_time_ago = human_time_diff(strtotime($comment->created_at), current_time('timestamp'));
                                
                                // Compter les likes du commentaire
                                $comment_likes = $wpdb->get_var($wpdb->prepare(
                                    "SELECT COUNT(*) FROM $reactions_table WHERE parent_comment_id = %d AND reaction_type = 'comment_like'",
                                    $comment->id
                                ));
                                
                                $user_liked_comment = false;
                                if ($current_user_id > 0) {
                                    $user_liked_comment = $wpdb->get_var($wpdb->prepare(
                                        "SELECT COUNT(*) FROM $reactions_table WHERE parent_comment_id = %d AND user_id = %d AND reaction_type = 'comment_like'",
                                        $comment->id, $current_user_id
                                    )) > 0;
                                }
                            ?>
                                <div class="comment-item" data-comment-id="<?php echo esc_attr($comment->id); ?>">
                                    <img src="<?php echo esc_url($comment_profile_photo); ?>" alt="<?php echo esc_attr($comment->display_name ?: $comment->user_login); ?>" class="comment-avatar">
                                    <div class="comment-content">
                                        <div class="comment-header">
                                            <span class="comment-author"><?php echo esc_html($comment->display_name ?: $comment->user_login); ?></span>
                                            <span class="comment-time">Il y a <?php echo esc_html($comment_time_ago); ?></span>
                                        </div>
                                        <p class="comment-text"><?php echo nl2br(esc_html($comment->comment_text)); ?></p>
                                        <?php if (is_user_logged_in()): ?>
                                            <button class="comment-like-btn <?php echo $user_liked_comment ? 'active' : ''; ?>" 
                                                    data-post-id="<?php echo esc_attr($post->id); ?>"
                                                    data-comment-id="<?php echo esc_attr($comment->id); ?>">
                                                <svg class="comment-like-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                </svg>
                                                <span><?php echo esc_html($comment_likes); ?></span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>


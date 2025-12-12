<?php
/**
 * Template Name: Recherche Page
 * Template pour la page Recherche
 */

get_header(); 

$current_user_id = is_user_logged_in() ? get_current_user_id() : 0;

// Récupérer les favoris si l'utilisateur est connecté
$favorites = array();
if ($current_user_id > 0) {
    global $wpdb;
    $posts_table = $wpdb->prefix . 'bebeats_posts';
    $reactions_table = $wpdb->prefix . 'bebeats_post_reactions';
    
    $favorites = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT p.*, u.display_name, u.user_login, 
                    pm1.meta_value as profile_photo
             FROM $posts_table p
             INNER JOIN $reactions_table r ON p.id = r.post_id AND r.user_id = %d AND r.reaction_type = 'favorite'
             LEFT JOIN {$wpdb->users} u ON p.user_id = u.ID
             LEFT JOIN {$wpdb->usermeta} pm1 ON u.ID = pm1.user_id AND pm1.meta_key = 'bebeats_profile_photo'
             ORDER BY r.created_at DESC",
            $current_user_id
        )
    );
}
?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="recherche-container">
            <h1 class="recherche-title">Mes favoris</h1>
            
            <?php if (!is_user_logged_in()): ?>
                <div class="recherche-empty glassmorphism">
                    <p>Vous devez être connecté pour voir vos favoris.</p>
                    <a href="<?php echo esc_url(home_url('/auth-start')); ?>" class="recherche-login-link">Se connecter</a>
                </div>
            <?php elseif (empty($favorites)): ?>
                <div class="recherche-empty glassmorphism">
                    <p>Vous n'avez pas encore de favoris.</p>
                    <p>Explorez le <a href="<?php echo esc_url(home_url('/feed-page')); ?>" style="color: #A855F7;">Feed</a> pour découvrir des posts et les ajouter à vos favoris !</p>
                </div>
            <?php else: ?>
                <div class="favorites-list">
                    <?php foreach ($favorites as $post): 
                        $profile_photo = !empty($post->profile_photo) ? $post->profile_photo : get_avatar_url($post->user_id, array('size' => 60));
                        $time_ago = human_time_diff(strtotime($post->created_at), current_time('timestamp'));
                    ?>
                        <article class="favorite-post glassmorphism">
                            <div class="post-header">
                                <img src="<?php echo esc_url($profile_photo); ?>" alt="<?php echo esc_attr($post->display_name ?: $post->user_login); ?>" class="post-author-avatar">
                                <div class="post-author-info">
                                    <h3 class="post-author-name"><?php echo esc_html($post->display_name ?: $post->user_login); ?></h3>
                                    <span class="post-time">Il y a <?php echo esc_html($time_ago); ?></span>
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
                                        <?php elseif ($post->media_type === 'audio'): ?>
                                            <audio controls class="post-media-audio">
                                                <source src="<?php echo esc_url($post->media_url); ?>" type="audio/mpeg">
                                                Votre navigateur ne supporte pas l'élément audio.
                                            </audio>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="favorite-post-actions">
                                <a href="<?php echo esc_url(home_url('/feed-page')); ?>" class="view-post-link">Voir le post</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php get_footer(); ?>


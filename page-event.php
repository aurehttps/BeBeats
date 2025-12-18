<?php
/**
 * Template Name: Event Page
 * Template pour la page Event
 */

get_header();

global $wpdb;
$posts_table = $wpdb->prefix . 'bebeats_posts';
$events = $wpdb->get_results("
    SELECT p.*, u.display_name, u.user_login
    FROM {$posts_table} p
    INNER JOIN {$wpdb->users} u ON p.user_id = u.ID
    WHERE p.post_type = 'event'
    ORDER BY p.created_at DESC
    LIMIT 50
");
?>

<main class="event-content">
    <!-- Filter Menu -->
    <aside class="event-filters">
        <button class="filter-btn">Date</button>
        <button class="filter-btn">Lieu</button>
        <button class="filter-btn">Prix</button>
        <button class="filter-btn filter-btn-active">Musique</button>
    </aside>

    <!-- Events List -->
    <section class="events-list">
        <!-- Événements éditoriaux fixes -->
        <article class="event-card">
            <div class="event-image">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/event-firewood.jpg" alt="Firew00d">
            </div>
            <div class="event-details">
                <h2 class="event-title">Firew00d</h2>
                <p class="event-description">Ce groupe venant tout droit de la maison de jeunesse de Ciney a déjà fait ces preuves sur la scène de plusieurs festival. Avec des reprises incroyables.</p>
                <div class="event-info">
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        23/12/2025
                    </span>
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Ciney
                    </span>
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Prix libre
                    </span>
                </div>
            </div>
        </article>

        <article class="event-card">
            <div class="event-image">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/event-bobby-watson.jpg" alt="Bobby Watson">
            </div>
            <div class="event-details">
                <h2 class="event-title">Bobby Watson</h2>
                <p class="event-description">Engagée dans le travail collaboratif, Bobbi Watson s'associe à des artistes d'horizons divers, comme la drag queer Tiny Beast Prince.</p>
                <div class="event-info">
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        23/12/2025
                    </span>
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Ciney
                    </span>
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Prix libre
                    </span>
                </div>
            </div>
        </article>

        <article class="event-card">
            <div class="event-image">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/event-pretty-much-average.jpg" alt="Pretty Much Average">
            </div>
            <div class="event-details">
                <h2 class="event-title">Pretty Much Average</h2>
                <p class="event-description">Avec la sortie de leur nouvel EP "Warm Ankle", Pretty Much Average se sont réveillés à la bourre pour le taf, avec une vilaine gueule de bois.</p>
                <div class="event-info">
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        23/12/2025
                    </span>
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Ciney
                    </span>
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Prix libre
                    </span>
                </div>
            </div>
        </article>

        <article class="event-card">
            <div class="event-image">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/event-iamwill.jpg" alt="IAMWILL">
            </div>
            <div class="event-details">
                <h2 class="event-title">IAMWILL</h2>
                <p class="event-description">Après deux premiers singles remarqués, « OCEANS BLOOM » et « GRASS IS GREEN », l'artiste s'apprête à franchir une nouvelle étape.</p>
                <div class="event-info">
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        23/12/2025
                    </span>
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Ciney
                    </span>
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Prix libre
                    </span>
                </div>
            </div>
        </article>

        <?php if (!empty($events)): ?>
            <?php foreach ($events as $event): 
                $event_date = date_i18n('d/m/Y', strtotime($event->created_at));
                // Utiliser le titre d'évènement dédié s'il existe, sinon fallback sur le contenu
                $event_title = !empty($event->event_title)
                    ? $event->event_title
                    : (!empty($event->content) ? wp_trim_words($event->content, 6, '...') : 'Évènement BeBeats');
            ?>
                <article class="event-card" id="event-<?php echo esc_attr($event->id); ?>">
                    <?php if (!empty($event->media_url) && $event->media_type === 'image'): ?>
                        <div class="event-image">
                            <img src="<?php echo esc_url($event->media_url); ?>" alt="<?php echo esc_attr($event_title); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="event-details">
                        <h2 class="event-title">
                            <?php echo esc_html($event_title); ?>
                        </h2>
                        <?php if (!empty($event->content)): ?>
                            <p class="event-description">
                                <?php echo esc_html(wp_trim_words($event->content, 20, '...')); ?>
                            </p>
                        <?php endif; ?>
                        <div class="event-info">
                            <span class="event-info-item">
                                <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <?php echo esc_html($event_date); ?>
                            </span>
                            <span class="event-info-item">
                                <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Belgique
                            </span>
                            <span class="event-info-item">
                                <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Entrée libre
                            </span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

<?php get_footer(); ?>


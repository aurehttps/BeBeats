<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <!-- Logo -->
                <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/Logo_BeBeats_Deskstop.png" alt="BeBeats Logo" class="logo-img" />
                </a>

                <!-- Navigation Section -->
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <form class="search-container" role="search">
                        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input 
                            type="search" 
                            class="search-input"
                            name="q"
                            placeholder="Chercher dans BeBeats..." 
                            aria-label="Rechercher dans BeBeats"
                        />
                    </form>
                    
                    <button class="notification-btn" aria-label="Notifications" type="button">
                        <svg class="notification-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </button>
                    
                    <button class="settings-btn-header" type="button" onclick="window.location.href='<?php echo esc_url(home_url('/reglages')); ?>'" aria-label="Réglages">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </button>
                    
                    <?php if (is_user_logged_in()): 
                        $current_user = wp_get_current_user();
                        $profile_photo = get_user_meta($current_user->ID, 'bebeats_profile_photo', true);
                        if (empty($profile_photo)) {
                            $profile_photo = get_avatar_url($current_user->ID, array('size' => 44));
                        }
                    ?>
                        <button class="profile-btn-header" type="button" onclick="window.location.href='<?php echo esc_url(home_url('/profil')); ?>'" aria-label="Profil">
                            <img src="<?php echo esc_url($profile_photo); ?>" alt="<?php echo esc_attr($current_user->display_name); ?>" class="profile-btn-img">
                        </button>
                    <?php else: ?>
                        <button class="login-btn" type="button" onclick="window.location.href='<?php echo esc_url(home_url('/auth-start')); ?>'">Connexion</button>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Navigation Menu -->
    <nav class="main-nav" aria-label="Navigation principale">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-item <?php echo is_front_page() ? 'active' : ''; ?>" aria-label="Accueil">
            <div class="nav-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span class="nav-label">Accueil</span>
        </a>
        
        <a href="<?php echo esc_url(home_url('/recherche')); ?>" class="nav-item <?php echo is_page('recherche') ? 'active' : ''; ?>" aria-label="Recherche">
            <div class="nav-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <span class="nav-label">Recherche</span>
        </a>
        
        <a href="<?php echo esc_url(home_url('/contribuer')); ?>" class="nav-item <?php echo is_page('contribuer') ? 'active' : ''; ?>" aria-label="Contribuer">
            <div class="nav-icon nav-icon-gradient-1">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </div>
            <span class="nav-label">Contribuer</span>
        </a>
        
        <a href="<?php echo esc_url(home_url('/event')); ?>" class="nav-item <?php echo is_page('event') ? 'active' : ''; ?>" aria-label="Event">
            <div class="nav-icon nav-icon-gradient-2">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <span class="nav-label">Event</span>
        </a>
        
        <a href="<?php echo esc_url(home_url('/reglages')); ?>" class="nav-item nav-item-settings <?php echo is_page('reglages') ? 'active' : ''; ?>" aria-label="Réglages">
            <div class="nav-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <span class="nav-label">Réglages</span>
        </a>
        
        <a href="<?php echo esc_url(home_url('/auth-start')); ?>" class="nav-item nav-item-login <?php echo is_page('auth-start') ? 'active' : ''; ?>" aria-label="Connexion">
            <div class="nav-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <span class="nav-label">Connexion</span>
        </a>
    </nav>


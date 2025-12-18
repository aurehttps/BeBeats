<?php get_header(); ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Navigation Accueil/Feed -->
        <nav class="home-feed-nav">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-tab <?php echo is_front_page() ? 'active' : ''; ?>">
                Accueil
            </a>
            <a href="<?php echo esc_url(home_url('/feed-page')); ?>" class="nav-tab <?php echo is_page('feed-page') ? 'active' : ''; ?>">
                Feed
            </a>
        </nav>
        
        <!-- Video Section -->
        <section class="video-section">
            <div class="video-container glassmorphism">
                <video src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/BeBeats-MD.mp4" autoplay loop muted playsinline></video>
            </div>
        </section>

        <!-- New Releases Section -->
        <section class="content-section">
            <h2 class="section-title">Les nouveautés</h2>
            <p class="section-description">Sur BeBeats vous allez trouver le meilleur du son belge! Commencez par les nouveautés</p>
            <div class="content-container glassmorphism">
                <div class="content-scroll">
                    <!-- Album 1 -->
                    <div class="content-item">
                        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/Bloodstone.jpeg" alt="Bloostone" class="content-image">
                        <h3 class="content-title">Bloostone</h3>
                        <p class="content-subtitle">Thomas Frank Hopper</p>
                    </div>
                    <!-- Album 2 -->
                    <div class="content-item">
                        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/RAKETKANON.jpeg" alt="RKTKN#1" class="content-image">
                        <h3 class="content-title">RKTKN#1</h3>
                        <p class="content-subtitle">Rakethanon</p>
                    </div>
                    <!-- Album 3 -->
                    <div class="content-item">
                        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/crackhouse.jpeg" alt="Crackhouse" class="content-image">
                        <h3 class="content-title">Crackhouse</h3>
                        <p class="content-subtitle">Highbloo</p>
                    </div>
                    <!-- Album 4 -->
                    <div class="content-item">
                        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/Sold_Out.jpeg" alt="Forever" class="content-image">
                        <h3 class="content-title">Forever</h3>
                        <p class="content-subtitle">Sold Out</p>
                    </div>
                    <!-- Album 5 -->
                    <div class="content-item">
                        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/kapitankorsakov.jpeg" alt="Well Hunger" class="content-image">
                        <h3 class="content-title">Well Hunger</h3>
                        <p class="content-subtitle">Kapitan Korsakov</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Articles Section -->
        <section class="content-section">
            <h2 class="section-title">Nos articles</h2>
            <p class="section-description">L'équipe BeBeats se démène pour vous proposer des articles intéressants sur l'actualité du monde de la musique belge.</p>
            <div class="content-container glassmorphism">
                <div class="content-scroll">
                    <!-- Article 1 -->
                    <div class="content-item">
                        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/Botanique.jpeg" alt="Les Nuits" class="content-image">
                        <h3 class="content-title">Les Nuits</h3>
                        <p class="content-subtitle">Botanique</p>
                    </div>
                    <!-- Article 2 -->
                    <div class="content-item">
                        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/Liberté.jpeg" alt="Le Festival des Libertés" class="content-image">
                        <h3 class="content-title">Le Festival des Libertés</h3>
                        <p class="content-subtitle">Théâtre National</p>
                    </div>
                    <!-- Article 3 -->
                    <div class="content-item">
                        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/FKNY.jpeg" alt="FKNYE" class="content-image">
                        <h3 class="content-title">FKNYE</h3>
                        <p class="content-subtitle">Théâtre National</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

<?php get_footer(); ?>


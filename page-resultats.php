<?php
/**
 * Template Name: Résultats Page
 * Template pour la page Résultats de recherche
 */

get_header(); ?>

    <!-- Main Content -->
    <main class="search-content">
        <!-- Search Results Section -->
        <section class="search-results" id="search-results-section">
            <h2 class="results-title">Résultats de recherche</h2>
            <p class="results-query" id="search-query"></p>
            <!-- Les résultats de recherche apparaîtront ici -->
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
            </div>
        </section>
    </main>

    <script>
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
            }
        });
    </script>

<?php get_footer(); ?>


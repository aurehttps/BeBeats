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
            <button class="category-btn glassmorphism">Musique</button>
            <button class="category-btn glassmorphism">Fan Arts</button>
            <button class="category-btn glassmorphism">Evènement</button>
            <button class="category-btn glassmorphism">Article</button>
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
        });
    </script>

<?php get_footer(); ?>


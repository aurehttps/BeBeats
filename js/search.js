// Script pour gérer la recherche depuis la barre de navigation
document.addEventListener('DOMContentLoaded', function() {
    // Trouver tous les formulaires de recherche
    const searchForms = document.querySelectorAll('form[role="search"]');
    
    searchForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Récupérer la valeur du champ de recherche
            const searchInput = form.querySelector('input[type="search"]');
            const searchTerm = searchInput.value.trim();
            
            // Si un terme de recherche est saisi, rediriger vers la page résultats
            if (searchTerm) {
                // Déterminer le chemin relatif selon la page actuelle
                const currentPath = window.location.pathname;
                let resultsPath = 'pages/resultats.html';
                
                // Si on est déjà dans le dossier pages, le chemin est différent
                if (currentPath.includes('/pages/')) {
                    resultsPath = 'resultats.html';
                }
                
                window.location.href = resultsPath + '?q=' + encodeURIComponent(searchTerm);
            }
        });
    });
});


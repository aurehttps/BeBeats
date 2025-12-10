// Script pour gérer la recherche depuis la barre de navigation
document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour rediriger vers la page de résultats
    function redirectToResults(searchTerm) {
        const trimmedTerm = searchTerm.trim();
        if (!trimmedTerm) {
            console.log('Recherche vide, redirection annulée');
            return;
        }
        
        // Utiliser l'URL WordPress pour la page résultats
        // Si la variable WordPress est disponible, l'utiliser, sinon construire l'URL
        let resultsUrl;
        if (typeof bebeatsAjax !== 'undefined' && bebeatsAjax.homeUrl) {
            resultsUrl = bebeatsAjax.homeUrl + '/resultats?q=' + encodeURIComponent(trimmedTerm);
        } else {
            // Fallback : utiliser la structure WordPress standard
            const homeUrl = window.location.origin;
            resultsUrl = homeUrl + '/resultats?q=' + encodeURIComponent(trimmedTerm);
        }
        
        console.log('Redirection vers:', resultsUrl);
        
        // Rediriger vers la page de résultats
        window.location.href = resultsUrl;
    }
    
    // Trouver tous les formulaires de recherche
    const searchForms = document.querySelectorAll('form[role="search"]');
    
    if (searchForms.length === 0) {
        console.warn('Aucun formulaire de recherche trouvé');
        return;
    }
    
    console.log('Formulaires de recherche trouvés:', searchForms.length);
    
    searchForms.forEach(function(form) {
        // Écouter la soumission du formulaire
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Récupérer la valeur du champ de recherche
            const searchInput = form.querySelector('input[type="search"]');
            if (searchInput) {
                redirectToResults(searchInput.value);
            } else {
                console.error('Champ de recherche introuvable dans le formulaire');
            }
        });
        
        // Écouter aussi la touche Entrée directement sur l'input
        const searchInput = form.querySelector('input[type="search"]');
        if (searchInput) {
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    e.stopPropagation();
                    redirectToResults(this.value);
                }
            });
        } else {
            console.error('Champ input[type="search"] introuvable');
        }
    });
});


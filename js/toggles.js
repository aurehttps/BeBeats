// Gestion des toggles de la page Réglages

document.addEventListener('DOMContentLoaded', function() {
    // Récupérer tous les toggles
    const toggles = document.querySelectorAll('.toggle-input');
    
    // Charger les états sauvegardés depuis localStorage
    toggles.forEach(toggle => {
        const toggleId = toggle.id;
        const savedState = localStorage.getItem(toggleId);
        
        if (savedState !== null) {
            toggle.checked = savedState === 'true';
        }
    });
    
    // Ajouter un écouteur d'événement à chaque toggle
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            // Sauvegarder l'état dans localStorage
            localStorage.setItem(this.id, this.checked);
            
            // Optionnel : Ajouter une logique spécifique pour chaque toggle
            handleToggleChange(this.id, this.checked);
        });
    });
});

// Fonction pour gérer les changements de chaque toggle
function handleToggleChange(toggleId, isChecked) {
    switch(toggleId) {
        case 'toggle-cookies':
            console.log('Cookies:', isChecked ? 'activé' : 'désactivé');
            // Ajouter ici la logique spécifique pour les cookies
            break;
        case 'toggle-langues':
            console.log('Langues:', isChecked ? 'activé' : 'désactivé');
            // Ajouter ici la logique spécifique pour les langues
            break;
        case 'toggle-mode':
            console.log('Mode:', isChecked ? 'activé' : 'désactivé');
            // Ajouter ici la logique spécifique pour le mode
            break;
        case 'toggle-repost':
            console.log('Repost:', isChecked ? 'activé' : 'désactivé');
            // Ajouter ici la logique spécifique pour le repost
            break;
    }
}


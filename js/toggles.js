// Gestion des toggles de la page Réglages

// Fonction pour appliquer le mode de couleur
function applyColorMode(isOrange) {
    const body = document.body;
    const html = document.documentElement;
    
    if (!body) {
        // Si le body n'existe pas encore, appliquer sur html et réessayer
        if (isOrange) {
            html.classList.remove('mode-violet');
            html.classList.add('mode-orange');
        } else {
            html.classList.remove('mode-orange');
            html.classList.add('mode-violet');
        }
        setTimeout(function() {
            applyColorMode(isOrange);
        }, 10);
        return;
    }
    
    if (isOrange) {
        // Mode orange : toggle à droite
        body.classList.remove('mode-violet');
        body.classList.add('mode-orange');
        html.classList.remove('mode-violet');
        html.classList.add('mode-orange');
    } else {
        // Mode violet : toggle à gauche
        body.classList.remove('mode-orange');
        body.classList.add('mode-violet');
        html.classList.remove('mode-orange');
        html.classList.add('mode-violet');
    }
}

// Appliquer le mode de couleur immédiatement (avant DOMContentLoaded)
(function() {
    const savedMode = localStorage.getItem('toggle-mode');
    if (savedMode !== null) {
        applyColorMode(savedMode === 'true');
    } else {
        // Par défaut : mode violet
        applyColorMode(false);
    }
})();

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
    
    // Appliquer le mode de couleur au chargement si le toggle-mode existe
    const modeToggle = document.getElementById('toggle-mode');
    if (modeToggle) {
        const savedMode = localStorage.getItem('toggle-mode');
        if (savedMode !== null) {
            applyColorMode(savedMode === 'true');
        } else {
            // Par défaut : mode violet (toggle à gauche)
            applyColorMode(false);
        }
    } else {
        // Si le toggle n'existe pas sur la page, appliquer le mode sauvegardé
        const savedMode = localStorage.getItem('toggle-mode');
        if (savedMode !== null) {
            applyColorMode(savedMode === 'true');
        } else {
            // Par défaut : mode violet
            applyColorMode(false);
        }
    }
    
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
            console.log('Mode:', isChecked ? 'orange' : 'violet');
            // Changer le mode de couleur des boutons
            applyColorMode(isChecked);
            break;
        case 'toggle-repost':
            console.log('Repost:', isChecked ? 'activé' : 'désactivé');
            // Ajouter ici la logique spécifique pour le repost
            break;
    }
}


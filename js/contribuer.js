// Gestion de la page Contribuer

document.addEventListener('DOMContentLoaded', function() {
    // Gestion des onglets de catégories
    const categoryTabs = document.querySelectorAll('.category-tab');
    
    // Initialiser l'état au chargement (Fan-art est actif par défaut)
    const activeTab = document.querySelector('.category-tab.active');
    if (activeTab) {
        const initialCategory = activeTab.getAttribute('data-category');
        handleCategoryChange(initialCategory);
    }
    
    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Retirer la classe active de tous les onglets
            categoryTabs.forEach(t => t.classList.remove('active'));
            
            // Ajouter la classe active à l'onglet cliqué
            this.classList.add('active');
            
            // Récupérer la catégorie sélectionnée
            const category = this.getAttribute('data-category');
            
            // Logique pour changer le contenu selon la catégorie
            handleCategoryChange(category);
        });
    });
    
    // Gestion des actions du menu latéral
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            const label = this.querySelector('.menu-label')?.textContent || this.getAttribute('aria-label');
            handleMenuAction(label);
        });
    });
});

// Fonction pour gérer le changement de catégorie
function handleCategoryChange(category) {
    console.log('Catégorie sélectionnée:', category);
    
    // Récupérer les boutons à cacher/afficher
    const descriptionBtn = document.querySelector('.menu-item-description');
    const mediaBtn = document.querySelector('.menu-item-media');
    
    // Cacher/afficher les boutons selon la catégorie
    if (category === 'post') {
        // Cacher "Ajouter une description" et "Contenu multimédia" pour Post
        if (descriptionBtn) descriptionBtn.style.display = 'none';
        if (mediaBtn) mediaBtn.style.display = 'none';
    } else {
        // Afficher les boutons pour Fan-art et Audio
        if (descriptionBtn) descriptionBtn.style.display = 'flex';
        if (mediaBtn) mediaBtn.style.display = 'flex';
    }
    
    // Ajouter ici la logique pour changer le contenu selon la catégorie
    // Par exemple, charger différents formulaires ou options
    switch(category) {
        case 'fan-art':
            // Logique pour Fan-art
            break;
        case 'audio':
            // Logique pour Audio
            break;
        case 'post':
            // Logique pour Post
            break;
    }
}

// Fonction pour gérer les actions du menu
function handleMenuAction(action) {
    console.log('Action sélectionnée:', action);
    
    // Ajouter ici la logique pour chaque action
    switch(action) {
        case 'Menu':
            // Ouvrir/fermer le menu
            break;
        case 'Ajouter une description':
            // Ouvrir un éditeur de texte
            break;
        case 'Identifier une personne':
            // Ouvrir un sélecteur de personnes
            break;
        case 'Options':
            // Ouvrir les options
            break;
        case 'Contenu multimédia':
            // Ouvrir un sélecteur de fichiers
            break;
        case 'Publier':
            // Publier le contenu
            break;
    }
}


/**
 * Gestion de la page Profil - Interactions avec les posts
 */

document.addEventListener('DOMContentLoaded', function() {
    // Gestion du scroll vers le post dans le feed
    const urlHash = window.location.hash;
    if (urlHash && urlHash.startsWith('#post-')) {
        // Si on vient du profil, on est déjà sur le feed, donc on scroll
        setTimeout(function() {
            const postElement = document.querySelector(urlHash);
            if (postElement) {
                postElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                // Mise en surbrillance temporaire
                postElement.style.outline = '2px solid rgba(168, 85, 247, 0.5)';
                postElement.style.outlineOffset = '4px';
                setTimeout(function() {
                    postElement.style.outline = '';
                    postElement.style.outlineOffset = '';
                }, 2000);
            }
        }, 500);
    }
});

/**
 * Gérer une réaction depuis le profil (like, repost, favorite)
 */
function handleProfileReaction(postId, reactionType, button) {
    if (button.disabled) return;
    
    const formData = new FormData();
    formData.append('action', 'bebeats_post_reaction');
    formData.append('post_id', postId);
    formData.append('reaction_type', reactionType);
    formData.append('action_type', 'toggle');
    formData.append('bebeats_reaction_nonce', bebeatsProfil.nonce);
    
    fetch(bebeatsProfil.ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const countElement = button.querySelector('.profile-action-count');
            if (countElement) {
                const currentCount = parseInt(countElement.textContent) || 0;
                
                if (data.data.action === 'added') {
                    button.classList.add('active');
                    countElement.textContent = currentCount + 1;
                } else {
                    button.classList.remove('active');
                    countElement.textContent = Math.max(0, currentCount - 1);
                }
            } else {
                // Pour le bouton favoris qui n'a pas de compteur
                if (data.data.action === 'added') {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            }
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}


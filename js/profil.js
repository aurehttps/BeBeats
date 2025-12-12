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
    
    // Initialiser tous les carrousels
    initCarousels();
    
    // Initialiser les interactions des posts Feed (pour le carrousel Feed complet)
    initFeedCarouselInteractions();
});

/**
 * Initialiser les carrousels
 */
function initCarousels() {
    const carouselContainers = document.querySelectorAll('.carousel-container');
    
    carouselContainers.forEach(function(container) {
        const track = container.querySelector('.carousel-track');
        const prevBtn = container.querySelector('.carousel-prev');
        const nextBtn = container.querySelector('.carousel-next');
        const wrapper = container.querySelector('.carousel-wrapper');
        
        if (!track || !prevBtn || !nextBtn || !wrapper) return;
        
        let scrollPosition = 0;
        const scrollStep = 320; // Largeur d'un post + gap (280px + 40px)
        const isFeedCarousel = container.classList.contains('carousel-feed-container');
        
        // Pour les carrousels feed, utiliser scrollBy
        if (isFeedCarousel) {
            const feedScrollStep = 620; // Largeur d'un post feed + gap (600px + 20px)
            
            prevBtn.addEventListener('click', function() {
                wrapper.scrollBy({
                    left: -feedScrollStep,
                    behavior: 'smooth'
                });
            });
            
            nextBtn.addEventListener('click', function() {
                wrapper.scrollBy({
                    left: feedScrollStep,
                    behavior: 'smooth'
                });
            });
            
            // Gérer l'état des boutons selon la position du scroll
            function updateFeedButtons() {
                const maxScroll = wrapper.scrollWidth - wrapper.clientWidth;
                prevBtn.disabled = wrapper.scrollLeft <= 0;
                nextBtn.disabled = wrapper.scrollLeft >= maxScroll - 1; // -1 pour gérer les arrondis
            }
            
            wrapper.addEventListener('scroll', updateFeedButtons);
            updateFeedButtons();
            
            return;
        }
        
        // Pour les autres carrousels (format compact), utiliser transform
        const maxScroll = track.scrollWidth - wrapper.clientWidth;
        
        prevBtn.addEventListener('click', function() {
            scrollPosition = Math.max(0, scrollPosition - scrollStep);
            track.style.transform = `translateX(-${scrollPosition}px)`;
            updateButtons();
        });
        
        nextBtn.addEventListener('click', function() {
            scrollPosition = Math.min(maxScroll, scrollPosition + scrollStep);
            track.style.transform = `translateX(-${scrollPosition}px)`;
            updateButtons();
        });
        
        function updateButtons() {
            prevBtn.disabled = scrollPosition <= 0;
            nextBtn.disabled = scrollPosition >= maxScroll - 10; // -10 pour gérer les arrondis
        }
        
        // Initialiser l'état des boutons
        updateButtons();
        
        // Gérer le redimensionnement de la fenêtre
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                scrollPosition = 0;
                track.style.transform = 'translateX(0)';
                updateButtons();
            }, 250);
        });
    });
}

/**
 * Initialiser les interactions pour les posts du carrousel Feed
 */
function initFeedCarouselInteractions() {
    const feedCarouselPosts = document.querySelectorAll('.carousel-feed-post');
    
    feedCarouselPosts.forEach(function(post) {
        // Gestion des likes, reposts, favoris
        const reactionButtons = post.querySelectorAll('.like-btn, .repost-btn, .favorite-btn');
        reactionButtons.forEach(function(btn) {
            if (btn.hasAttribute('data-listener-attached')) return;
            btn.setAttribute('data-listener-attached', 'true');
            
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (this.disabled) return;
                
                const postId = this.getAttribute('data-post-id');
                const reactionType = this.getAttribute('data-reaction-type');
                
                if (!postId || !reactionType) return;
                
                handleFeedReaction(postId, reactionType, this);
            });
        });
        
        // Gestion des commentaires
        const commentButtons = post.querySelectorAll('.comment-btn');
        commentButtons.forEach(function(btn) {
            if (btn.hasAttribute('data-listener-attached')) return;
            btn.setAttribute('data-listener-attached', 'true');
            
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const postId = this.getAttribute('data-post-id');
                if (!postId) return;
                
                // Rediriger vers la page feed avec l'ancre du post
                window.location.href = bebeatsProfil.homeUrl + 'feed-page#post-' + postId;
            });
        });
    });
}

/**
 * Gérer une réaction pour les posts Feed du carrousel
 */
function handleFeedReaction(postId, reactionType, button) {
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
        if (data.success && data.data) {
            const countElement = button.querySelector('.action-count');
            
            if (countElement && data.data.count !== undefined) {
                countElement.textContent = data.data.count;
            } else if (countElement) {
                const currentCount = parseInt(countElement.textContent) || 0;
                if (data.data.action === 'added') {
                    countElement.textContent = currentCount + 1;
                } else {
                    countElement.textContent = Math.max(0, currentCount - 1);
                }
            }
            
            // Mettre à jour l'état actif
            if (data.data.action === 'added') {
                button.classList.add('active');
            } else {
                button.classList.remove('active');
            }
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

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



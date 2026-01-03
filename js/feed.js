/**
 * Gestion de la page Feed - Interactions avec les posts
 */

// Fonction pour initialiser les event listeners
function initFeedListeners() {
    // Gestion des likes, reposts, favoris (y compris le bouton favoris en bas à gauche)
    const reactionButtons = document.querySelectorAll('.like-btn, .repost-btn, .favorite-btn');
    
    reactionButtons.forEach(btn => {
        // Éviter d'ajouter plusieurs fois le même listener
        if (btn.hasAttribute('data-listener-attached')) return;
        btn.setAttribute('data-listener-attached', 'true');
        
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (this.disabled) return;
            
            const postId = this.getAttribute('data-post-id');
            const reactionType = this.getAttribute('data-reaction-type');
            
            if (!postId || !reactionType) {
                console.error('Données manquantes:', { postId, reactionType });
                return;
            }
            
            handleReaction(postId, reactionType, this);
        });
    });
    
    // Gestion des commentaires
    const commentButtons = document.querySelectorAll('.comment-btn');
    
    commentButtons.forEach(btn => {
        // Éviter d'ajouter plusieurs fois le même listener
        if (btn.hasAttribute('data-listener-attached')) return;
        btn.setAttribute('data-listener-attached', 'true');
        
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const postId = this.getAttribute('data-post-id');
            if (!postId) {
                console.error('Post ID manquant');
                return;
            }
            
            const commentsSection = document.getElementById('comments-' + postId);
            
            if (commentsSection) {
                const isVisible = commentsSection.style.display !== 'none';
                commentsSection.style.display = isVisible ? 'none' : 'block';
            }
        });
    });
    
    // Gestion de la soumission de commentaire
    const commentSubmitButtons = document.querySelectorAll('.comment-submit-btn');
    
    commentSubmitButtons.forEach(btn => {
        // Éviter d'ajouter plusieurs fois le même listener
        if (btn.hasAttribute('data-listener-attached')) return;
        btn.setAttribute('data-listener-attached', 'true');
        
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const postId = this.getAttribute('data-post-id');
            if (!postId) {
                console.error('Post ID manquant');
                return;
            }
            
            const post = this.closest('.feed-post');
            if (!post) {
                console.error('Post parent non trouvé');
                return;
            }
            
            const commentInput = post.querySelector('.comment-input');
            if (!commentInput) {
                console.error('Champ de commentaire non trouvé');
                return;
            }
            
            const commentText = commentInput.value.trim();
            
            if (!commentText) return;
            
            handleComment(postId, commentText, this, commentInput);
        });
    });
    
    // Gestion des likes de commentaires
    const commentLikeButtons = document.querySelectorAll('.comment-like-btn');
    
    commentLikeButtons.forEach(btn => {
        // Éviter d'ajouter plusieurs fois le même listener
        if (btn.hasAttribute('data-listener-attached')) return;
        btn.setAttribute('data-listener-attached', 'true');
        
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (this.disabled) return;
            
            const postId = this.getAttribute('data-post-id');
            const commentId = this.getAttribute('data-comment-id');
            
            if (!postId || !commentId) {
                console.error('Données manquantes:', { postId, commentId });
                return;
            }
            
            handleCommentLike(postId, commentId, this);
        });
    });
}

// Initialiser quand le DOM est prêt
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFeedListeners);
} else {
    // DOM déjà chargé
    initFeedListeners();
}

/**
 * Gérer une réaction (like, repost, favorite)
 */
function handleReaction(postId, reactionType, button) {
    // Vérifier que bebeatsFeed est défini
    if (typeof bebeatsFeed === 'undefined') {
        console.error('bebeatsFeed n\'est pas défini. Vérifiez que le script est correctement chargé.');
        alert('Erreur: Configuration manquante. Veuillez recharger la page.');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'bebeats_post_reaction');
    formData.append('post_id', postId);
    formData.append('reaction_type', reactionType);
    formData.append('action_type', 'toggle');
    formData.append('bebeats_reaction_nonce', bebeatsFeed.nonce);
    
    fetch(bebeatsFeed.ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur réseau: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.data) {
            const countElement = button.querySelector('.action-count');
            
            if (countElement) {
                // Priorité 1: Utiliser le compteur retourné par le serveur
                if (data.data.count !== undefined && data.data.count !== null) {
                    countElement.textContent = data.data.count;
                } 
                // Priorité 2: Calculer manuellement si le compteur n'est pas fourni
                else {
                    const currentCount = parseInt(countElement.textContent) || 0;
                    if (data.data.action === 'added') {
                        countElement.textContent = currentCount + 1;
                    } else if (data.data.action === 'removed') {
                        countElement.textContent = Math.max(0, currentCount - 1);
                    }
                }
                
                // Mettre à jour l'état actif visuel
                if (data.data.action === 'added') {
                    button.classList.add('active');
                } else if (data.data.action === 'removed') {
                    button.classList.remove('active');
                }
            } else {
                // Pour les boutons sans compteur (comme favoris), on met juste à jour l'état actif
                if (data.data.action === 'added') {
                    button.classList.add('active');
                } else if (data.data.action === 'removed') {
                    button.classList.remove('active');
                }
            }
        } else {
            // En cas d'erreur, essayer quand même de mettre à jour le compteur si disponible
            const countElement = button.querySelector('.action-count');
            if (countElement && data.data?.count !== undefined) {
                countElement.textContent = data.data.count;
            }
            console.error('Erreur serveur:', data.data?.message || 'Erreur inconnue');
            if (data.data?.debug) {
                console.error('Détails de l\'erreur:', data.data.debug);
            }
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur de connexion. Veuillez réessayer.');
    });
}

/**
 * Gérer l'ajout d'un commentaire
 */
function handleComment(postId, commentText, button, input) {
    // Vérifier que bebeatsFeed est défini
    if (typeof bebeatsFeed === 'undefined') {
        console.error('bebeatsFeed n\'est pas défini. Vérifiez que le script est correctement chargé.');
        alert('Erreur: Configuration manquante. Veuillez recharger la page.');
        button.disabled = false;
        button.textContent = 'Publier';
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'bebeats_post_reaction');
    formData.append('post_id', postId);
    formData.append('reaction_type', 'comment');
    formData.append('comment_text', commentText);
    formData.append('bebeats_reaction_nonce', bebeatsFeed.nonce);
    
    button.disabled = true;
    button.textContent = 'Publication...';
    
    fetch(bebeatsFeed.ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            button.disabled = false;
            button.textContent = 'Publier';
            
            // Mettre à jour le compteur de commentaires
            const commentBtn = document.querySelector(`.comment-btn[data-post-id="${postId}"]`);
            if (commentBtn) {
                const countElement = commentBtn.querySelector('.action-count');
                if (countElement && data.data.count !== undefined) {
                    countElement.textContent = data.data.count;
                }
            }
            
            // Ajouter le nouveau commentaire dynamiquement
            if (data.data.comment) {
                const commentsSection = document.getElementById('comments-' + postId);
                if (commentsSection) {
                    // S'assurer que la section est visible
                    commentsSection.style.display = 'block';
                    
                    const commentsList = commentsSection.querySelector('.comments-list');
                    if (commentsList) {
                        const commentItem = createCommentElement(data.data.comment, postId);
                        commentsList.appendChild(commentItem);
                        
                        // Réinitialiser les event listeners pour le nouveau bouton like
                        const newLikeBtn = commentItem.querySelector('.comment-like-btn');
                        if (newLikeBtn) {
                            newLikeBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                
                                if (this.disabled) return;
                                
                                const commentId = this.getAttribute('data-comment-id');
                                if (!postId || !commentId) {
                                    console.error('Données manquantes:', { postId, commentId });
                                    return;
                                }
                                
                                handleCommentLike(postId, commentId, this);
                            });
                        }
                    }
                }
            }
        } else {
            alert('Erreur lors de la publication du commentaire: ' + (data.data?.message || 'Erreur inconnue'));
            button.disabled = false;
            button.textContent = 'Publier';
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        button.disabled = false;
        button.textContent = 'Publier';
    });
}

/**
 * Créer un élément commentaire
 */
function createCommentElement(comment, postId) {
    const commentItem = document.createElement('div');
    commentItem.className = 'comment-item';
    commentItem.setAttribute('data-comment-id', comment.id);
    
    const isLoggedIn = typeof bebeatsFeed !== 'undefined';
    
    // Échapper le texte pour éviter les injections XSS
    const escapeHtml = (text) => {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    };
    
    const commentText = escapeHtml(comment.comment_text).replace(/\n/g, '<br>');
    
    commentItem.innerHTML = `
        <img src="${escapeHtml(comment.profile_photo)}" alt="${escapeHtml(comment.display_name)}" class="comment-avatar">
        <div class="comment-content">
            <div class="comment-header">
                <span class="comment-author">${escapeHtml(comment.display_name)}</span>
                <span class="comment-time" data-timestamp="${comment.timestamp}">Il y a ${escapeHtml(comment.time_ago)}</span>
            </div>
            <p class="comment-text">${commentText}</p>
        </div>
        <button class="comment-like-btn" 
                data-post-id="${postId}"
                data-comment-id="${comment.id}"
                ${!isLoggedIn ? 'disabled' : ''}>
            <svg class="comment-like-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <span class="comment-like-count">${comment.likes_count || 0}</span>
        </button>
    `;
    
    return commentItem;
}

/**
 * Gérer le like d'un commentaire
 */
function handleCommentLike(postId, commentId, button) {
    // Vérifier que bebeatsFeed est défini
    if (typeof bebeatsFeed === 'undefined') {
        console.error('bebeatsFeed n\'est pas défini. Vérifiez que le script est correctement chargé.');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'bebeats_post_reaction');
    formData.append('post_id', postId);
    formData.append('reaction_type', 'comment_like');
    formData.append('parent_comment_id', commentId);
    formData.append('action_type', 'toggle');
    formData.append('bebeats_reaction_nonce', bebeatsFeed.nonce);
    
    fetch(bebeatsFeed.ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const countElement = button.querySelector('.comment-like-count') || button.querySelector('span');
            
            if (countElement) {
                // Utiliser le compteur retourné par le serveur si disponible
                if (data.data.count !== undefined) {
                    countElement.textContent = data.data.count;
                } else {
                    const currentCount = parseInt(countElement.textContent) || 0;
                    if (data.data.action === 'added') {
                        countElement.textContent = currentCount + 1;
                    } else {
                        countElement.textContent = Math.max(0, currentCount - 1);
                    }
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


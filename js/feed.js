/**
 * Gestion de la page Feed - Interactions avec les posts
 */

document.addEventListener('DOMContentLoaded', function() {
    // Gestion des likes, reposts, favoris
    const reactionButtons = document.querySelectorAll('.like-btn, .repost-btn, .favorite-btn');
    
    reactionButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.disabled) return;
            
            const postId = this.getAttribute('data-post-id');
            const reactionType = this.getAttribute('data-reaction-type');
            
            handleReaction(postId, reactionType, this);
        });
    });
    
    // Gestion des commentaires
    const commentButtons = document.querySelectorAll('.comment-btn');
    
    commentButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
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
        btn.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const post = this.closest('.feed-post');
            const commentInput = post.querySelector('.comment-input');
            const commentText = commentInput.value.trim();
            
            if (!commentText) return;
            
            handleComment(postId, commentText, this, commentInput);
        });
    });
    
    // Gestion des likes de commentaires
    const commentLikeButtons = document.querySelectorAll('.comment-like-btn');
    
    commentLikeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const commentId = this.getAttribute('data-comment-id');
            
            handleCommentLike(postId, commentId, this);
        });
    });
});

/**
 * Gérer une réaction (like, repost, favorite)
 */
function handleReaction(postId, reactionType, button) {
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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const countElement = button.querySelector('.action-count');
            const currentCount = parseInt(countElement.textContent) || 0;
            
            if (data.data.action === 'added') {
                button.classList.add('active');
                countElement.textContent = currentCount + 1;
            } else {
                button.classList.remove('active');
                countElement.textContent = Math.max(0, currentCount - 1);
            }
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

/**
 * Gérer l'ajout d'un commentaire
 */
function handleComment(postId, commentText, button, input) {
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
            // Recharger la page pour afficher le nouveau commentaire
            location.reload();
        } else {
            alert('Erreur lors de la publication du commentaire');
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
 * Gérer le like d'un commentaire
 */
function handleCommentLike(postId, commentId, button) {
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
            const countElement = button.querySelector('span');
            const currentCount = parseInt(countElement.textContent) || 0;
            
            if (data.data.action === 'added') {
                button.classList.add('active');
                countElement.textContent = currentCount + 1;
            } else {
                button.classList.remove('active');
                countElement.textContent = Math.max(0, currentCount - 1);
            }
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}


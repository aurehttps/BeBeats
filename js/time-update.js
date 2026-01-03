/**
 * Script pour mettre à jour le temps affiché en temps réel
 * Met à jour toutes les minutes les temps relatifs (Il y a X)
 */

(function() {
    'use strict';
    
    /**
     * Fonction pour formater le temps relatif en français (format WordPress)
     */
    function formatTimeAgo(timestamp) {
        // Utiliser le timestamp actuel du serveur (en tenant compte du fuseau horaire WordPress)
        // Si l'offset est disponible, l'utiliser pour synchroniser avec le serveur
        const clientNow = Math.floor(Date.now() / 1000);
        const serverNow = (window.bebeatsTimeOffset !== undefined) 
            ? clientNow + window.bebeatsTimeOffset 
            : clientNow;
        const diff = serverNow - timestamp;
        
        // Moins d'une minute
        if (diff < 60) {
            return 'Il y a moins d\'une minute';
        }
        // Minutes
        else if (diff < 3600) {
            const minutes = Math.floor(diff / 60);
            if (minutes === 1) {
                return 'Il y a 1 minute';
            } else {
                return 'Il y a ' + minutes + ' minutes';
            }
        }
        // Heures
        else if (diff < 86400) {
            const hours = Math.floor(diff / 3600);
            if (hours === 1) {
                return 'Il y a 1 heure';
            } else {
                return 'Il y a ' + hours + ' heures';
            }
        }
        // Jours
        else if (diff < 604800) {
            const days = Math.floor(diff / 86400);
            if (days === 1) {
                return 'Il y a 1 jour';
            } else {
                return 'Il y a ' + days + ' jours';
            }
        }
        // Semaines
        else if (diff < 2592000) {
            const weeks = Math.floor(diff / 604800);
            if (weeks === 1) {
                return 'Il y a 1 semaine';
            } else {
                return 'Il y a ' + weeks + ' semaines';
            }
        }
        // Mois
        else if (diff < 31536000) {
            const months = Math.floor(diff / 2592000);
            if (months === 1) {
                return 'Il y a 1 mois';
            } else {
                return 'Il y a ' + months + ' mois';
            }
        }
        // Années
        else {
            const years = Math.floor(diff / 31536000);
            if (years === 1) {
                return 'Il y a 1 an';
            } else {
                return 'Il y a ' + years + ' ans';
            }
        }
    }
    
    /**
     * Fonction pour mettre à jour tous les temps affichés
     */
    function updateAllTimes() {
        // Mettre à jour les temps des posts (feed et profil)
        const postTimeElements = document.querySelectorAll('.post-time[data-timestamp], .profile-post-time[data-timestamp]');
        postTimeElements.forEach(function(element) {
            const timestamp = parseInt(element.getAttribute('data-timestamp'), 10);
            if (timestamp && !isNaN(timestamp)) {
                element.textContent = formatTimeAgo(timestamp);
            }
        });
        
        // Mettre à jour les temps des commentaires
        const commentTimeElements = document.querySelectorAll('.comment-time[data-timestamp]');
        commentTimeElements.forEach(function(element) {
            const timestamp = parseInt(element.getAttribute('data-timestamp'), 10);
            if (timestamp && !isNaN(timestamp)) {
                element.textContent = formatTimeAgo(timestamp);
            }
        });
    }
    
    // Mettre à jour immédiatement au chargement
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            updateAllTimes();
        });
    } else {
        updateAllTimes();
    }
    
    // Mettre à jour toutes les minutes (60000 ms)
    setInterval(updateAllTimes, 60000);
})();






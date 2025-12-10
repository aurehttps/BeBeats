<?php
/**
 * Template Name: Réglages Page
 * Template pour la page Réglages
 */

get_header(); ?>

    <!-- Main Content -->
    <main class="main-content">
        <section class="settings-panel glassmorphism">
            <div class="settings-grid">
                <div class="setting-item">
                    <label class="setting-label">Cookies</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="toggle-cookies" class="toggle-input">
                        <span class="toggle-slider">
                            <span class="toggle-checkmark">✓</span>
                            <span class="toggle-knob"></span>
                        </span>
                    </label>
                </div>
                
                <div class="setting-item">
                    <label class="setting-label">Langues</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="toggle-langues" class="toggle-input">
                        <span class="toggle-slider">
                            <span class="toggle-checkmark">✓</span>
                            <span class="toggle-knob"></span>
                        </span>
                    </label>
                </div>
                
                <div class="setting-item">
                    <label class="setting-label">Mode</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="toggle-mode" class="toggle-input">
                        <span class="toggle-slider">
                            <span class="toggle-checkmark">✓</span>
                            <span class="toggle-knob"></span>
                        </span>
                    </label>
                </div>
                
                <div class="setting-item">
                    <label class="setting-label">Repost</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="toggle-repost" class="toggle-input">
                        <span class="toggle-slider">
                            <span class="toggle-checkmark">✓</span>
                            <span class="toggle-knob"></span>
                        </span>
                    </label>
                </div>
            </div>
        </section>
    </main>

<?php get_footer(); ?>


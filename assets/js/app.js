/** 
 * Try our best to ensure that everything starts after the DOM has loaded. 
 */
window.addEventListener("DOMContentLoaded", () => {
    initialize_app();
});

/** 
 * All the initialization that is required before the app is properly used. 
 */
function initialize_app() {
    bind_heroic_skill_events();
}

/**
 * Event bindings related to the Heroic Skills section of the app.
 */
function bind_heroic_skill_events() {
    document.getElementById("heroic-skills").addEventListener("click", (e) => {
        const skill_toggle = e.target.closest('.heroic-skill-toggle');
        if (!skill_toggle) return;
        const is_expanded = skill_toggle.getAttribute('aria-expanded') === 'true';
        skill_toggle.setAttribute('aria-expanded', !is_expanded);
        document.getElementById(skill_toggle.getAttribute('aria-controls')).classList.toggle('hidden', is_expanded);
    });
}
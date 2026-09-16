/**
 * @file app.js
 */

/**
 * @typedef {Object} SkillContext
 * @property {HTMLElement} filter_container - The container for all filtering controls
 * @property {HTMLElement} skills_container - The container for all listed Heroic Skills
 */

const heroic_skill_click_actions = {
    ".btn-add-filter-row"       : add_filter_row,
    ".btn-remove-filter-row"    : remove_filter_row,
    ".heroic-skill-toggle"      : toggle_heroic_skill,
}

const heroic_skill_change_actions = {
    ".filter-tag-select"        : filter_heroic_skills, 
}

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
    const heroic_skills = document.getElementById("heroic-skills");
    if(!heroic_skills) return;

    const context = {
        filter_container: heroic_skills.querySelector('.filter-rows-container'),
        skills_container: heroic_skills.querySelector('.skills-container'),
    }
    add_filter_row(null, context);
    
    const dispatcher = (actions) => (e) => {
        for (const [selector, handler] of Object.entries(actions)) {
            const target = e.target.closest(selector);
            if (target) { handler(target, context); break; }
        }
    };

    heroic_skills.addEventListener("click", dispatcher(heroic_skill_click_actions));
    heroic_skills.addEventListener("change", dispatcher(heroic_skill_change_actions));
}

/**
 * Clones the hidden filter-row-prime and appends a new filter row to the container.
 * 
 * @param {HTMLElement|null} target
 * @param {SkillContext} context
 */
function add_filter_row(target, { filter_container }) {
    const prime = filter_container.querySelector('.filter-row-prime');
    if(!prime) return;

    const row = prime.cloneNode(true);
    row.classList.remove('filter-row-prime');
    filter_container.appendChild(row);
}

/**
 * Removes the given row if it isn't the only row in its container.
 * 
 * @param {HTMLElement|null} target
 * @param {SkillContext} context
 */
function remove_filter_row(target, context) {
    const row = target.closest('.filter-row') || target.parentElement;

    if (context.filter_container.children.length > 2) {
        row.remove();
        filter_heroic_skills(target, context);
    }
}

/**
 * Collects selected values across all active filters and filters Heroic Skills using OR logic.
 * 
 * @param {HTMLElement|null} target
 * @param {SkillContext} context
 */
function filter_heroic_skills(target, { filter_container, skills_container }) {
    const selects = filter_container.querySelectorAll('.filter-tag-select');
    const active_tags = new Set( Array.from(selects, s => s.value).filter(Boolean) );

    skills_container.querySelectorAll(".skill").forEach(article => {
        const tags = article.dataset.tags ? article.dataset.tags.split(',').map(t => t.trim()) : [];
        const match = active_tags.size === 0 || Array.from(active_tags).some(tag => tags.includes(tag));
        article.classList.toggle('filtered-out', !match);
    });
}

/**
 * Show or hide the full description of the passed in Heroic Skill that was clicked.
 * 
 * @param {HTMLElement|null} target
 * @param {SkillContext} context
 */
function toggle_heroic_skill(target, { skills_container }) {
    const is_expanded = target.getAttribute('aria-expanded') === 'true';
    const target_id = target.getAttribute('aria-controls');

    target.setAttribute('aria-expanded', !is_expanded);
    if(target_id) {
        skills_container.querySelector('#' + target_id)?.classList.toggle('hidden', is_expanded);
    }
}
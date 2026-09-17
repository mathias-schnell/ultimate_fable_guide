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
    ".heroic-skill-pin"         : toggle_heroic_pin,
    ".heroic-skill-toggle"      : toggle_heroic_skill,
}

const heroic_skill_change_actions = {
    ".filter-include-exclude"   : filter_heroic_skills,
    ".filter-tag-select"        : filter_heroic_skills, 
}

let pin_count = 0;

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
    bind_navigation_events();
    bind_heroic_skill_events();
}

function bind_navigation_events() {
    const nav = document.querySelector(".tab-nav");
    if(!nav) return;
    nav.addEventListener("click", (e) => {
        const tab = e.target.closest(".tab");
        if(!tab) return;
        toggle_nav_tab(nav, tab);
    });
}


/**
 * Make the clicked navigation tab active
 */
function toggle_nav_tab(nav, tab) {
    nav.querySelectorAll(".tab").forEach(t => {
        t.classList.remove("active");
        t.setAttribute("aria-selected", "false");
    });
    tab.classList.add("active");
    tab.setAttribute("aria-selected", "true");
}

/**
 * Event bindings related to the Heroic Skills section of the app.
 */
function bind_heroic_skill_events() {
    const container = document.getElementById("main-container");
    if(!container) return;

    const context = {
        filter_container: container.querySelector('.filter-rows-container'),
        skills_container: container.querySelector('.skills-container'),
    }
    add_filter_row(null, context);
    
    const dispatcher = (actions) => (e) => {
        for (const [selector, handler] of Object.entries(actions)) {
            const target = e.target.closest(selector);
            if (target) { handler(target, context); break; }
        }
    };

    container.addEventListener("click", dispatcher(heroic_skill_click_actions));
    container.addEventListener("change", dispatcher(heroic_skill_change_actions));
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
    } else {
        row.querySelector(".filter-include-exclude").selectedIndex = 0;
        row.querySelector(".filter-tag-select").selectedIndex = 0;
    }
    filter_heroic_skills(target, context);
}

/**
 * Collects selected values across all active filters and filters Heroic Skills using OR logic.
 * 
 * @param {HTMLElement|null} target
 * @param {SkillContext} context
 */
function filter_heroic_skills(target, { filter_container, skills_container }) {
    const includes = new Set();
    const excludes = new Set();
    const filter_rows = filter_container.querySelectorAll('.filter-row:not(.filter-row-prime)');
    
    filter_rows.forEach(row => {
        const mode = row.querySelector(".filter-include-exclude")?.value;
        const tag = row.querySelector(".filter-tag-select")?.value;
        if(!tag) return;
        if(mode === "1") {
            includes.add(tag);
        } else {
            excludes.add(tag);
        }
    });

    skills_container.querySelectorAll(".skill").forEach(article => {
        if(article.getAttribute("aria-pinned") === "true") return;
        const tags = article.dataset.tags ? article.dataset.tags.split(',').map(t => t.trim()) : [];
        const included = includes.size === 0 || [...includes].some(tag => tags.includes(tag));
        const excluded = [...excludes].some(tag => tags.includes(tag));
        const show = included && !excluded;

        article.classList.toggle('filtered-out', !show);
    });
}

/**
 * Show or hide the full description of the passed in Heroic Skill that was clicked.
 * 
 * @param {HTMLElement|null} target
 * @param {SkillContext} context
 */
function toggle_heroic_pin(target, context) {
    const is_pinned = target.getAttribute('aria-checked') === 'true';

    if(is_pinned && pin_count > 0) { 
        pin_count--;
    } else if (!is_pinned && pin_count < 5) {
        pin_count++;
    } else {
        return;
    }

    target.setAttribute("aria-checked", !is_pinned);
    target.closest(".skill").setAttribute("aria-pinned", !is_pinned);
    filter_heroic_skills(target, context);
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
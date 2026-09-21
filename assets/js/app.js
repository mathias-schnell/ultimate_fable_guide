/**
 * @file app.js
 */

/**
 * @typedef {Object} SkillContext
 * @property {HTMLElement} filter_container - The container for all filtering controls
 * @property {HTMLElement} skills_container - The container for all listed Heroic Skills
 */

/** 
 * Defining the actions that will happen to the content area of the app on click 
 */
const content_click_actions = {
    ".class-skills-pin"          : toggle_pin,
    ".class-skills-toggle"       : toggle_item,
    ".heroic-skills-pin"         : toggle_pin,
    ".heroic-skills-toggle"      : toggle_item,
};

/** 
 * Defining the actions that will happen to the filter area of the app on click 
 */
const filter_click_actions = {
    ".btn-add-filter-row"        : add_filter_row,
    ".btn-remove-filter-row"     : remove_filter_row,
};

/** 
 * Defining the actions that will happen to the filter area of the app when something changes 
 */
const filter_change_actions = {
    ".filter-include-exclude"   : filter_content,
    ".filter-tag-select"        : filter_content, 
};

/** 
 * Creates a generic action dispatcher for all the above actions 
 */
const dispatcher = (actions, context) => (e) => {
    for (const [selector, handler] of Object.entries(actions)) {
        const target = e.target.closest(selector);
        if (target) { handler(target, context); break; }
    }
};

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
    const container = document.getElementById("main-container");
    if(!container) return;

    const context = {
        nav_container: container.querySelector('.tab-nav'),
        filter_container: container.querySelector('.filter-rows-container'),
        content_container: container.querySelector('.content-container'),
    }

    bind_navigation_events(context);
    bind_filter_events(context);
    bind_content_events(context);
    toggle_nav_tab(context.nav_container, context.nav_container.children[0]);
    show_content(context.content_container);
}

/**
 * Event bindings releated to the app's navigation
 * 
 * @param {*} param0 
 */
function bind_navigation_events({ nav_container, content_container } = {}) {
    if(!nav_container || !content_container) return;
    nav_container.addEventListener("click", (e) => {
        const tab = e.target.closest(".tab");
        if(!tab) return;
        const content = tab.getAttribute("aria-controls");
        toggle_nav_tab(nav_container, tab);
        show_content(content_container, content);
    });
}

/**
 * Make the clicked navigation tab active
 */
function toggle_nav_tab(nav, tab) {
    if(!nav || !tab) return;
    nav.querySelectorAll(".tab").forEach(t => {
        t.classList.remove("active");
        t.setAttribute("aria-selected", "false");
    });
    tab.classList.add("active");
    tab.setAttribute("aria-selected", "true");
}

/**
 * Event bindings related to the Filter section of the app.
 */
function bind_filter_events(context) {
    context.filter_container?.addEventListener("click", dispatcher(filter_click_actions, context));
    context.filter_container?.addEventListener("change", dispatcher(filter_change_actions, context));
    add_filter_row(null, context);
}

/**
 * Event bindings related to the Content section of the app.
 */
function bind_content_events(context) {
    context.content_container?.addEventListener("click", dispatcher(content_click_actions, context));
}

/**
 * Clones the hidden filter-row-prime and appends a new filter row to the container.
 * 
 * @param {HTMLElement|null} target
 * @param {SkillContext} context
 */
function add_filter_row(target, { filter_container } = {}) {
    if(!filter_container) return;
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
function remove_filter_row(target, context = {}) {
    if(!target || !context.filter_container) return;
    const row = target.closest('.filter-row') || target.parentElement;
    if(!row) return;

    if (context.filter_container.children.length > 2) {
        row.remove();
    } else {
        row.querySelector(".filter-include-exclude").selectedIndex = 0;
        row.querySelector(".filter-tag-select").selectedIndex = 0;
    }
    filter_content(target, context);
}

/**
 * Collects selected values across all active filters and filters Heroic Skills using OR logic.
 * 
 * @param {HTMLElement|null} target
 * @param {SkillContext} context
 */
function filter_content(target, { filter_container, content_container } = {}) {
    if(!filter_container || !content_container) return;
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

    content_container.querySelectorAll("article").forEach(article => {
        if(article.getAttribute("aria-pinned") === "true") return;
        const tags = article.dataset.tags ? article.dataset.tags.split(',').map(t => t.trim()) : [];
        const included = includes.size === 0 || [...includes].some(tag => tags.includes(tag));
        const excluded = [...excludes].some(tag => tags.includes(tag));
        const show = included && !excluded;

        article.classList.toggle('filtered-out', !show);
    });
}

/**
 * Pin the article to the top of the content section.
 * 
 * @param {HTMLElement|null} target
 * @param {SkillContext} context
 */
function toggle_pin(target, context) {
    const is_pinned = target.getAttribute('aria-checked') === 'true';
    target.setAttribute("aria-checked", !is_pinned);
    target.closest("article")?.setAttribute("aria-pinned", String(!is_pinned));
    filter_content(target, context);
}

/**
 * Show or hide the full description of the passed in article.
 * 
 * @param {HTMLElement|null} target
 * @param {SkillContext} context
 */
function toggle_item(target, { content_container } = {}) {
    if(!content_container) return;
    const is_expanded = target.getAttribute('aria-expanded') === 'true';
    const target_id = target.getAttribute('aria-controls');

    target.setAttribute('aria-expanded', !is_expanded);
    if(target_id) {
        content_container.querySelector('#' + target_id)?.classList.toggle('hidden', is_expanded);
    }
}

/**
 * Show the relevant content in the content container and hide the others.
 * 
 * @param {*} container 
 * @param {*} content 
 */
function show_content(content_container, content = null) {
    if(!content_container) return;
    [...content_container.children].forEach(item => item.classList.remove("active"));
    if(content) {
        content_container.querySelector("#" + content)?.classList.add("active");
    } else {
        content_container.children[0]?.classList.add("active");
    }
}
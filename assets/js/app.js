/**
 * @file app.js
 */

/**
 * @typedef {Object} AppContext
 * @property {HTMLElement} nav_container - The container for all the navigation tabs
 * @property {HTMLElement} filter_container - The container for all filtering controls
 * @property {HTMLElement} content_container - The container for the main content
 */

/** 
 * Defining the actions that will happen in the navigation area of the app on click 
 */
const nav_click_actions = {
    ".tab"              : toggle_nav_tab,
    ".nav-left-arrow"   : move_tab_left,
    ".nav-right-arrow"  : move_tab_right
}

/** 
 * Defining the actions that will happen in the content area of the app on click 
 */
const content_click_actions = {
    "[class*='-pin']"            : toggle_pin,
    "[class*='-toggle']"         : toggle_item,
    "[class*='-multitoggle']"    : toggle_all,
};

/** 
 * Defining the actions that will happen in the filter area of the app on click 
 */
const filter_click_actions = {
    ".btn-add-filter-row"       : add_filter_row,
    ".btn-remove-filter-row"    : remove_filter_row,
};

/** 
 * Defining the actions that will happen in the filter area of the app when something changes 
 */
const filter_change_actions = {
    ".filter-mode"              : filter_content,
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
    toggle_nav_tab(context.nav_container.querySelector(".tab"), context);
}

/**
 * Event bindings releated to the app's navigation
 * 
 * @param {AppContext} param0 
 */
function bind_navigation_events(context) {
    context.nav_container?.addEventListener("click", dispatcher(nav_click_actions, context));
}

/**
 * Make the clicked navigation tab active
 */
function toggle_nav_tab(target, context) {
    if(!target || !context.nav_container) return;
    context.nav_container.querySelectorAll(".tab").forEach(tab => {
        tab.classList.remove("active");
        tab.setAttribute("aria-selected", "false");
    });
    target.classList.add("active");
    target.setAttribute("aria-selected", "true");

    const nav = context.nav_container;
    const scroll_to = target.offsetLeft - (nav.clientWidth / 2) + (target.offsetWidth / 2);
    nav.scrollTo({ left: scroll_to, behavior: "smooth" });

    show_content(context.content_container, target.getAttribute("aria-controls"));
}

/**
 * Change the active tab to the one that is to the current active tab's left
 */
function move_tab_left(target, context) {
    const active_tab = context.nav_container.querySelector(".tab.active");
    const prev_tab = active_tab.previousElementSibling;

    if(!prev_tab.classList.contains("nav-left-arrow")) {
        toggle_nav_tab(prev_tab, context);
    }
}

/**
 * Change the active tab to the one that is to the current active tab's right
 */
function move_tab_right(target, context) {
    const active_tab = context.nav_container.querySelector(".tab.active");
    const next_tab = active_tab.nextElementSibling;

    if(!next_tab.classList.contains("nav-right-arrow")) {
        toggle_nav_tab(next_tab, context);
    }
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
 * @param {AppContext} context
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
 * @param {AppContext} context
 */
function remove_filter_row(target, context = {}) {
    if(!target || !context.filter_container) return;
    const row = target.closest('.filter-row') || target.parentElement;
    const rows = context.filter_container.querySelectorAll(".filter-row:not(.filter-row-prime)");
    if(!row) return;

    if (rows.length > 1) {
        row.remove();
    } else {
        row.querySelector(".filter-tag-select").selectedIndex = 0;
    }
    filter_content(target, context);
}

/**
 * Collects selected values across all active filters and filters Heroic Skills using OR logic.
 * 
 * @param {HTMLElement|null} target
 * @param {AppContext} context
 */
function filter_content(target, { filter_container, content_container } = {}) {
    if(!filter_container || !content_container) return;
    const selected_tags = new Set();
    const mode = parseInt(filter_container.querySelector(".filter-mode")?.value);
    const filter_rows = filter_container.querySelectorAll('.filter-row:not(.filter-row-prime)');
    
    filter_rows.forEach(row => {
        const tag = row.querySelector(".filter-tag-select")?.value;
        if(!tag) return;
        selected_tags.add(tag);
    });

    content_container.querySelectorAll("article").forEach(article => {
        if(article.getAttribute("aria-pinned") === "true") return;
        const raw_tags = article.dataset.tags ? article.dataset.tags.split(',').map(t => t.trim()) : [];
        const article_tags = new Set(raw_tags);
        let match = false;
        
        if(selected_tags.size === 0) {
            match = true;
        } else {
            switch (mode) {
                case 1:
                    match = [...selected_tags].every(tag => article_tags.has(tag));
                    break;
                default:
                    match = [...selected_tags].some(tag => article_tags.has(tag));
                    break;
            }
        }
        article.classList.toggle('filtered-out', !match);
    });

    content_container.querySelectorAll("section[class*='-group']").forEach(group => {
        group.classList.toggle("hidden", group.querySelectorAll('article.filtered-out').length === group.querySelectorAll('article').length);
    });
}

/**
 * Toggle all items of the current category to show or hide.
 * 
 * @param {HTMLElement|null} target
 * @param {AppContext} context
 */
function toggle_all(target, { content_container } = {}) {
    if(!content_container) return;
    const key = "." + target.dataset.key + "-toggle";
    const toggles = content_container.querySelectorAll(`.${target.className}`);
    const rows = content_container.querySelectorAll(target.getAttribute("aria-controls"));
    const is_expanded = target.getAttribute('aria-expanded') === 'true';
    const next_state = !is_expanded;

    toggles.forEach(item => {
        item.innerHTML = next_state ? "-" : "+";
        item.setAttribute('aria-expanded', String(next_state));
        if(next_state) {
            item.setAttribute('title', 'Contract All');
        } else {
            item.setAttribute('title', 'Expand All');
        }
    });

    rows.forEach(row => {
        const toggle = row.querySelector(key);
        if (!toggle || (toggle.getAttribute("aria-expanded") === "true") === next_state) return;
        toggle_item(toggle, { content_container }, !is_expanded);
    });
}

/**
 * Pin the article to the top of the content section.
 * 
 * @param {HTMLElement|null} target
 * @param {AppContext} context
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
 * @param {AppContext} context
 * @param {bool|null} forced_state
 */
function toggle_item(target, { content_container } = {}, forced_state = null) {
    if(!target || !content_container) return;
    const target_id = target.getAttribute('aria-controls');
    if(!target_id) return;
    
    const is_expanded = target.getAttribute('aria-expanded') === 'true';
    const new_state = forced_state ?? !is_expanded;

    target.setAttribute('aria-expanded', String(new_state));
    if(new_state) {
        target.setAttribute('title', "Contract");
    } else {
        target.setAttribute('title', "Expand");
    }
    content_container.querySelector('#' + target_id)?.classList.toggle('hidden', !new_state);
}

/**
 * Show the relevant content in the content container and hide the others.
 * 
 * @param {HTMLElement} container 
 * @param {String|null} content 
 */
function show_content(content_container, content) {
    if(!content_container) return;
    [...content_container.children].forEach(item => item.classList.remove("active"));
    if(content) {
        content_container.querySelector("#" + content)?.classList.add("active");
    }
}
/**
 * @file app.js
 */

/**
 * @typedef {Object} AppContext
 * @property {HTMLElement} nav - The container for all the navigation tabs
 * @property {HTMLElement} content - The container for current active content
 * @property {HTMLElement} filter - The container for current active filters
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
    "[class*='-link']"           : copy_link,
    "[class*='-pin']"            : toggle_pin,
    "[class*='-toggle']"         : toggle_item,
    "[class*='-multitoggle']"    : toggle_all,
    "[class*='-col-sort']"       : toggle_col_sort,
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
    ".toggle-bonus-content"                 : toggle_bonus_content,
    ".toggle-description-search"            : filter_content_by_search,
    ".filter-tag-select.filter-cat"         : show_filter_subcat,
    ".filter-tag-select.filter-subcat"      : filter_content_by_tags,
    ".filter-mode"                          : filter_content_by_tags, 
};

/** 
 * Defining the actions that will happen in the filter area of the app when the search input changes 
 */
const filter_input_actions = {
    ".filter-search"                        : debounce(filter_content_by_search),
};

/** 
 * Creates a generic action dispatcher for all the above actions 
 */
const dispatcher = (actions, ctx) => (e) => {
    for (const [selector, handler] of Object.entries(actions)) {
        const target = e.target.closest(selector);
        if (target) { handler(target, ctx); break; }
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

    const ctx = {
        nav: container.querySelector('.page-header'),
        content: container.querySelector('.content-container'),
        filter: container.querySelector('.content-container').querySelector('.filter-container'),
    }

    bind_navigation_events(ctx);
    bind_filter_events(ctx);
    bind_content_events(ctx);
    process_url_params(ctx);
    toggle_bonus_content(document.querySelector("input.toggle-bonus-content"), ctx);
}

/**
 * Check the URL parameters and apply them to the app's state on load.
 * 
 * @param {AppContext} ctx 
 */
function process_url_params(ctx) {
    const params = get_url_params();
    if(params.cat) {
        const container = params.cat + "-container";
        const tab = ctx.nav.querySelector(`.tab[aria-controls="${container}"]`);
        if(tab) {
            toggle_nav_tab(tab, ctx);
            ctx.filter = ctx.content.querySelector("#" + container)?.querySelector('.filter-container');
        } else { 
            toggle_nav_tab(ctx.nav.querySelector(".tab"), ctx);
        }
    } else { 
        toggle_nav_tab(ctx.nav.querySelector(".tab"), ctx);
    }

    if(params.search) {
        const search_input = ctx.filter.querySelector(".filter-search");
        if(search_input) {
            search_input.value = params.search;
            filter_content_by_search(search_input, ctx);
        }
    }

    if(params.open) {
        const slug = params.search.replace(/[^\w\s]/g, '').replaceAll(' ', '-').toLowerCase();
        const article = ctx.content.querySelector("article[data-slug='" + slug + "']");
        if(article) {
            const toggle = article.querySelector("[class*='-toggle']");
            if(toggle) toggle_item(toggle, ctx);
        }
        console.log(slug);
        console.log(article);
    }
}

/**
 * Event bindings releated to the app's navigation
 * 
 * @param {AppContext} param0 
 */
function bind_navigation_events(ctx) {
    ctx.nav?.addEventListener("click", dispatcher(nav_click_actions, ctx));
}

/**
 * Make the clicked navigation tab active
 */
function toggle_nav_tab(target, ctx) {
    if(!target || !ctx.nav) return;
    ctx.nav.querySelectorAll(".tab").forEach(tab => {
        tab.classList.remove("active");
        tab.setAttribute("aria-selected", "false");
    });
    target.classList.add("active");
    target.setAttribute("aria-selected", "true");

    const nav = target.parentElement;
    const scroll_to = target.offsetLeft - (nav.clientWidth / 2) + (target.offsetWidth / 2);
    nav.scrollTo({ left: scroll_to, behavior: "smooth" });

    show_content(ctx.content, target.getAttribute("aria-controls"));
}

/**
 * Change the active tab to the one that is to the current active tab's left
 */
function move_tab_left(target, ctx) {
    const active_tab = ctx.nav.querySelector(".tab.active");
    const prev_tab = active_tab.previousElementSibling;
    if(prev_tab != null) toggle_nav_tab(prev_tab, ctx);
}

/**
 * Change the active tab to the one that is to the current active tab's right
 */
function move_tab_right(target, ctx) {
    const active_tab = ctx.nav.querySelector(".tab.active");
    const next_tab = active_tab.nextElementSibling;
    if(next_tab != null) toggle_nav_tab(next_tab, ctx);
}

/**
 * Debounce a function to limit how often it can be called.
 * 
 * @param {Function} func
 * @param {number} wait
 * @returns {Function}
 */
function debounce(func, wait = 150) {
    let timeout_id;
    return function (...args) {
        clearTimeout(timeout_id);
        timeout_id = setTimeout(() => { func.apply(this, args); }, wait);
    };
}

/**
 * Retrieves the URL parameters as an object.
 * 
 * @returns {array}
 */
function get_url_params() {
    const params = new URLSearchParams(window.location.search);
    const result = {};
    for (const [key, value] of params.entries()) {
        result[key] = value;
    }
    return result;
}

/**
 * Event bindings related to the Filter section of the app.
 */
function bind_filter_events(ctx) {
    const filter_containers = ctx.content.querySelectorAll(".filter-container");

    filter_containers.forEach(container => {
        container.addEventListener("click", dispatcher(filter_click_actions, { nav : ctx.nav, content: ctx.content, filter: container }));
        container.addEventListener("change", dispatcher(filter_change_actions, { nav : ctx.nav, content: ctx.content, filter: container }));
        container.addEventListener("input", dispatcher(filter_input_actions, { nav : ctx.nav, content: ctx.content, filter: container }));
    });
}

/**
 * Event bindings related to the Content section of the app.
 */
function bind_content_events(ctx) {
    ctx.content.addEventListener("click", dispatcher(content_click_actions, ctx));
}

/**
 * Shows/hides the tags in 'filter-subcat' based on the chosen 'filter-cat'
 */
function show_filter_subcat(target, ctx) {
    if(!ctx || !ctx.filter) return;
    const row = target.closest('.filter-row') || target.parentElement;
    if(!row) return;

    const cat = target.value;
    const subcat = row.querySelector('.filter-tag-select.filter-subcat');
    if(!subcat) return;

    subcat.selectedIndex = 0;
    subcat.querySelectorAll('option:not(:first-child)').forEach(option => {
        option.hidden = option.dataset.filterCat !== cat;
    });
    if(!cat) filter_content(target, ctx);
}

/**
 * Clones the hidden filter-row-prime and appends a new filter row to the container.
 * 
 * @param {HTMLElement|null} target
 * @param {AppContext} ctx
 */
function add_filter_row(target, ctx = {}) {
    if(!ctx || !ctx.filter) return;
    const first_row = ctx.filter.querySelector('.filter-row');
    if(!first_row) return;
    ctx.filter.appendChild(first_row.cloneNode(true));
}

/**
 * Removes the given row if it isn't the only row in its container.
 * 
 * @param {HTMLElement|null} target
 * @param {AppContext} ctx
 */
function remove_filter_row(target, ctx = {}) {
    if(!target || !ctx || !ctx.filter) return;
    const row = target.closest('.filter-row') || target.parentElement;
    if(!row) return;

    if (ctx.filter.querySelectorAll(".filter-row").length > 1) {
        row.remove();
    } else {
        row.querySelector(".filter-tag-select.filter-cat").selectedIndex = 0;
        row.querySelector(".filter-tag-select.filter-subcat").selectedIndex = 0;
        row.querySelectorAll(".filter-tag-select.filter-subcat option:not(:first-child)").forEach(option => option.hidden = true);
    }
    filter_content(target, ctx);
}

/**
 * Toggles whether Fabula Ultima Bonus content is visible or not
 * 
 * @param {HTMLElement|null} target
 * @param {AppContext} ctx
 */
function toggle_bonus_content (target, ctx = {}) {
    if(!target || !ctx || !ctx.content | !ctx.filter) return;
    const filter_target = ctx.filter.dataset.filterFor;
    const bonus_content = ctx.content.querySelectorAll(`.${filter_target} article[data-tags*='fabula-ultima-bonus`);
    bonus_content.forEach(article => {
        if(target.checked) article.style.display = 'block';
        else article.style.display = 'none';
    });
    filter_content_by_search(target, ctx);
}

/**
 * Filters the content based on the search text input.
 * 
 * @param {HTMLElement|null} target
 * @param {AppContext} ctx
 */
function filter_content_by_search(target, ctx = {}) {
    if(!ctx || !ctx.content || !ctx.filter) return;
    const search_input = ctx.filter.querySelector(".filter-search");
    const search_term = search_input?.value.trim().toLowerCase() ?? "";
    const filter_target = ctx.filter.dataset.filterFor;
    if(search_term.length < 3) {
        ctx.content.querySelectorAll(`.${filter_target} article`).forEach(article => {
            article.classList.remove('filtered-out');
        });
        ctx.content.querySelectorAll(`.${filter_target} section[class*='-group']`).forEach(group => {
            const filtered = group.querySelectorAll('article.filtered-out').length;
            const off = group.querySelectorAll('article.toggled-off').length;
            const total = group.querySelectorAll('article').length;
            group.classList.toggle("hidden", filtered === total || off === total);
        });
        return;
    };

    let search_targets = ["name", "requirements", "summary", "domains", "target", "targets", "duration", "effect", "status-effect", "attribute", "recovery", "event", "suggestions", "accuracy", "damage", "cost", "defense", "m-defense"];
    if(ctx.filter.querySelector("input.toggle-description-search").checked) {
        search_targets.push("description");
        search_targets.push("tag-list");
    }

    ctx.content.querySelectorAll(`.${filter_target} article`).forEach(article => {
        if (article.getAttribute("aria-pinned") === "true") return;
        const is_match = search_targets.some(target => {
            const target_element = article.querySelector(`[class*="${target}"]`);
            if (!target_element) return false;
            const target_text = target_element.textContent.trim().toLowerCase();
            return target_text.includes(search_term);
        });
        article.classList.toggle('filtered-out', !is_match);
    });

    ctx.content.querySelectorAll(`.${filter_target} section[class*='-group']`).forEach(group => {
        const filtered = group.querySelectorAll('article.filtered-out').length;
        const off = group.querySelectorAll('article.toggled-off').length;
        const total = group.querySelectorAll('article').length;
        group.classList.toggle("hidden", filtered === total || off === total);
    });
}

/**
 * Filters content based on the tags selected.
 * 
 * @param {HTMLElement|null} target
 * @param {AppContext} ctx
 */
function filter_content_by_tags(target, ctx = {}) {
    if(!ctx || !ctx.content || !ctx.filter) return;

    const selected_tags = new Set();
    const mode = parseInt(ctx.filter.querySelector(".filter-mode")?.value);
    const filter_rows = ctx.filter.querySelectorAll('.filter-row');
    const filter_target = ctx.filter.dataset.filterFor;
    
    filter_rows.forEach(row => {
        const tag = row.querySelector(".filter-tag-select.filter-subcat")?.value;
        if(!tag) return;
        selected_tags.add(tag);
    });

    ctx.content.querySelectorAll(`.${filter_target} article`).forEach(article => {
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

    ctx.content.querySelectorAll(`.${filter_target} section[class*='-group']`).forEach(group => {
        group.classList.toggle("hidden", group.querySelectorAll('article.filtered-out').length === group.querySelectorAll('article').length);
    });
}

/**
 * Toggle all items of the current category to show or hide.
 * 
 * @param {HTMLElement|null} target
 * @param {AppContext} ctx
 */
function toggle_all(target, ctx = {}) {
    if(!ctx || !ctx.content) return;
    const key = target.dataset.key;
    const group = target.closest('[class$="-group"]');
    const rows = group.querySelectorAll(`article.${key}`);
    const is_expanded = target.getAttribute('aria-expanded') === 'true';
    const next_state = !is_expanded;

    target.innerHTML = next_state ? "⊝" : "⊕";
    target.setAttribute('aria-expanded', String(next_state));
    if(next_state) {
        target.setAttribute('title', 'Contract All');
    } else {
        target.setAttribute('title', 'Expand All');
    }

    rows.forEach(row => {
        const toggle = row.querySelector("[class*='-toggle']");
        if (!toggle || (toggle.getAttribute("aria-expanded") === "true") === next_state) return;
        toggle_item(toggle, ctx, !is_expanded, false);
    });
    scroll_to_element(target);
}

/**
 * Copy the link of the selected article to the clipboard.
 * 
 * @param {HTMLElement|null} target 
 * @param {AppContext} ctx 
 */
function copy_link(target, ctx) {
    const link = target.closest("[class*='-link']");
    if (!link || link.classList.contains("is-copied")) return;
    const url = link.getAttribute("data-link");
    if (!url) return;
    navigator.clipboard.writeText(url.toString()).then(() => {
        link.classList.add("is-copied");
        setTimeout(() => {
            link.classList.remove("is-copied");
        }, 1500);
    }).catch(err => {
        console.error('Failed to copy link: ', err);
    });
}

/**
 * Pin the article to the top of the content section.
 * 
 * @param {HTMLElement|null} target
 * @param {AppContext} ctx
 */
function toggle_pin(target, ctx) {
    const is_pinned = target.getAttribute('aria-checked') === 'true';
    target.setAttribute("aria-checked", !is_pinned);
    target.closest("article")?.setAttribute("aria-pinned", String(!is_pinned));
    filter_content(target, ctx);
}

/**
 * Show or hide the full description of the passed in article.
 * 
 * @param {HTMLElement|null} target
 * @param {AppContext} ctx
 * @param {bool|null} forced_state
 * @param {bool} scroll_to
 */
function toggle_item(target, ctx = {}, forced_state = null, scroll_to = true) {
    if(!target || !ctx || !ctx.content) return;
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
    ctx.content.querySelector('#' + target_id)?.classList.toggle('hidden', !new_state);
    if(scroll_to && new_state) scroll_to_element(target);
}

/**
 * Show the relevant content in the content container and hide the others.
 * 
 * @param {HTMLElement} container 
 * @param {String|null} content 
 */
function show_content(container, content) {
    if(!container) return;
    [...container.children].forEach(item => item.classList.remove("active"));
    if(content) {
        container.querySelector("#" + content)?.classList.add("active");
    }
}

/**
 * Scroll the window to a specific element with an optional offset.
 * 
 * @param {HTMLElement|null} element 
 * @param {number} offset 
 * @returns 
 */
function scroll_to_element(element, offset = 50, delay = 350) {
    if (!element) return;
    setTimeout(() => {
        const ele_pos = element.getBoundingClientRect().top;
        const offset_pos = ele_pos + window.scrollY - offset;
        window.scrollTo({ top: offset_pos, behavior: 'smooth'});
    }, delay);
}

/**
 * Toggle the sort order of the content based on the clicked column header.
 * 
 * @param {HTMLElement} target 
 * @param {AppContext} ctx
 */
function toggle_col_sort(target, ctx) {
    if(!target || !ctx) return;
    const sort_by = target.dataset.sortBy;
    if(!sort_by) return;

    const new_sort_order = target.dataset.sortDesc !== "true";
    const active_tab = ctx.nav.querySelector(".tab.active");
    if(!active_tab) return;

    const container = ctx.content.querySelector("." + active_tab.getAttribute("aria-controls"));
    if(!container) return;

    const groups = container.querySelectorAll("[class$='-group']");
    groups.forEach(group => {
        const columns = group.querySelectorAll("[class$='-col-sort']");
        columns.forEach(col => col.dataset.sortDesc = (col.dataset.sortBy === sort_by) ? String(new_sort_order) : "");
        const articles = Array.from(group.querySelectorAll("article"));
        articles.sort((a, b) => {
            const str_a = a.querySelector("." + sort_by)?.textContent.trim() ?? '';
            const str_b = b.querySelector("." + sort_by)?.textContent.trim() ?? '';
            const comp = str_a.localeCompare(str_b, undefined, { numeric: true, sensitivity: 'base' });
            return new_sort_order ? -comp : comp;
        });
        articles.forEach(article => group.appendChild(article));
    });
}
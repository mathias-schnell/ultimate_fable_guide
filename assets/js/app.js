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
 * Defining the actions that will happen in the content area of the app on click 
 */
const content_click_actions = {
    "[class*='-link']"           : copy_link,
    "[class*='-toggle']"         : toggle_item,
    "[class*='-row'] > *"        : toggle_item
};

/** 
 * Defining the actions that will happen in the filter area of the app when something changes 
 */
const filter_change_actions = {
    ".toggle-description-search"            : filter_content_by_search,
    ".filter-mode"                          : filter_content_by_tags, 
};

/** 
 * Defining the actions that will happen in the filter area of the app when the search input changes 
 */
const filter_input_actions = {
    ".search-basic"                        : debounce(filter_content_by_search),
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
        filter: container.querySelector('.filter-container'),
    }

    bind_filter_events(ctx);
    bind_content_events(ctx);
    //process_url_params(ctx);
    //filter_content_by_search(null, ctx);
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
    }
}

/**
 * Debounce a function to limit how often it can be called.
 * 
 * @param {Function} func
 * @param {number} wait
 * @returns {Function}
 */
function debounce(func, wait = 250) {
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
    ctx.filter.addEventListener("change", dispatcher(filter_change_actions, { nav : ctx.nav, content: ctx.content, filter: ctx.filter }));
    ctx.filter.addEventListener("input", dispatcher(filter_input_actions, { nav : ctx.nav, content: ctx.content, filter: ctx.filter }));
}

/**
 * Event bindings related to the Content section of the app.
 */
function bind_content_events(ctx) {
    ctx.content.addEventListener("click", dispatcher(content_click_actions, ctx));
}

/**
 * Filters the content based on the search text input.
 * 
 * @param {HTMLElement|null} target
 * @param {AppContext} ctx
 */
function filter_content_by_search(target, ctx = {}) {
    if(!ctx || !ctx.content || !ctx.filter) return;
    const search_input = ctx.filter.querySelector(".filter-basic input[type='text']");
    const search_term = search_input?.value.trim().toLowerCase() ?? "";

    if(search_term.length < 3) {
        let root_items = ctx.content.querySelectorAll("[data-depth='1'] > *");
        for(let i = 2; i < 10; i++) {
            if(root_items.length <= 0) break;
            root_items.forEach(item => {
                const toggle = item.querySelector('.menu-toggle');
                toggle_item(toggle, ctx, false, false);
                item.setAttribute("aria-hidden", false);
            });
            root_items = ctx.content.querySelectorAll("[data-depth='" + i + "'] > *");
        }
        
        ctx.content.querySelectorAll("article").forEach(article => {
            article.setAttribute('aria-hidden', false);
            article.closest('.menu-item').setAttribute('aria-hidden', false);
            const article_toggle = article.querySelector("[class*='toggle']");
            const menu_toggle = article.closest('.menu-item').querySelector('.menu-toggle');
            toggle_item(article_toggle, ctx, false, false);
            toggle_item(menu_toggle, ctx, false, false);
        });
        return;
    };

    let search_targets = ["label", "requirements", "summary", "domains", "target", "targets", "duration", "effect", "status-effect", "attribute", "recovery", "event", "suggestions", "accuracy", "damage", "cost", "defense", "m-defense"];
    // if(ctx.filter.querySelector("input.toggle-description-search").checked) {
    //     search_targets.push("description");
         search_targets.push("tag-list");
    // }

    ctx.content.querySelectorAll("article").forEach(article => {
        const is_match = search_targets.some(target => {
            const target_element = article.querySelector(`[class*="${target}"]`);
            if (!target_element) return false;
            const target_text = target_element.textContent.trim().toLowerCase();
            return target_text.includes(search_term);
        });
        article.setAttribute('aria-hidden', !is_match);
        if(is_match) {
            const article_toggle = article.querySelector("[class*='toggle']");
            const menu_toggle = article.closest('.menu-item').querySelector('.menu-toggle');
            toggle_item(article_toggle, ctx, true, false);
            toggle_item(menu_toggle, ctx, true, false);
        }
    });

    let root_items = ctx.content.querySelectorAll("[data-depth='1'] > *");
    for(let i = 2; i < 10; i++) {
        if(root_items.length <= 0) break;
        root_items.forEach(item => {
            const visible_items = item.querySelectorAll("article[aria-hidden='false']");
            const has_visible_items = visible_items.length > 0;

            if(has_visible_items) {
                const toggle = item.querySelector('.menu-toggle');
                toggle_item(toggle, ctx, true, false);
            }

            item.setAttribute("aria-hidden", !has_visible_items);
        });
        root_items = ctx.content.querySelectorAll("[data-depth='" + i + "'] > *");
    }
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
 * Show or hide the full description of the passed in article.
 * 
 * @param {HTMLElement|null} target
 * @param {AppContext} ctx
 * @param {bool|null} forced_state
 * @param {bool} scroll_to
 */
function toggle_item(target, ctx = {}, forced_state = null, scroll_to = true) {
    if(!target || !ctx || !ctx.content) return;
    let target_id = target.getAttribute('aria-controls');
    if(!target_id) {
        target = target.closest("[class*='row']")?.querySelector("[class*='toggle']");
        target_id = target?.getAttribute('aria-controls');
    }
    if(!target_id || target.hasAttribute('disabled')) return;
    
    const is_expanded = target.getAttribute('aria-expanded') === 'true';
    const new_state = forced_state ?? !is_expanded;
    const content_el = ctx.content.querySelector('#' + target_id);

    target.setAttribute('aria-expanded', String(new_state));

    const icon = target.querySelector('.expand-contract-icon');
    if(icon && icon.dataset.expandIcon && icon.dataset.contractIcon) {
        icon.classList.toggle('svg-' + icon.dataset.expandIcon, !new_state);
        icon.classList.toggle('svg-' + icon.dataset.contractIcon, new_state);
    }

    if(content_el) {
        if (new_state) {
            content_el.removeAttribute('inert');
            content_el.removeAttribute('aria-hidden');
        } else {
            content_el.setAttribute('inert', '');
            content_el.setAttribute('aria-hidden', 'true');
        }
    }
    
    if(scroll_to && new_state) scroll_to_element(target);
}

/**
 * Scroll the window to a specific element with an optional offset.
 * 
 * @param {HTMLElement|null} element 
 * @param {number} offset 
 * @returns 
 */
function scroll_to_element(element, offset = 75, delay = 350) {
    if (!element) return;
    setTimeout(() => {
        const ele_pos = element.getBoundingClientRect().top;
        const offset_pos = ele_pos + window.scrollY - offset;
        window.scrollTo({ top: offset_pos, behavior: 'smooth'});
    }, delay);
}
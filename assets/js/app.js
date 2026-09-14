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

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('filter-rows-container');
    const template = document.getElementById('filter-row-template');
    const addBtn = document.getElementById('add-filter-btn');
    const skillArticles = document.querySelectorAll('article.skill');

    // Add initial default row on page load
    addFilterRow();

    /**
     * Clones the template and appends a new filter row to the container.
     */
    function addFilterRow() {
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('.filter-row');
        const select = row.querySelector('.tag-select');
        const addBtn = row.querySelector('.btn-add-row');
        const removeBtn = row.querySelector('.btn-remove-row');

        // Trigger filter update when selection changes
        select.addEventListener('change', filterSkills);

        // Event Listener: Add new row button
        addBtn.addEventListener('click', () => {
            addFilterRow();
        });

        // Remove row event listener
        removeBtn.addEventListener('click', () => {
            // Ensure at least one empty row remains
            if (container.children.length > 1) {
                row.remove();
            }
            filterSkills();
        });

        container.appendChild(row);
    }

    /**
     * Collects selected values across all active dropdowns
     * and filters skill articles using OR logic.
     */
    function filterSkills() {
        // Collect all non-empty selected tag values
        const selects = container.querySelectorAll('.tag-select');
        const activeTags = new Set();

        selects.forEach(select => {
            if (select.value) {
                activeTags.add(select.value);
            }
        });

        // Filter articles
        skillArticles.forEach(article => {
            // Retrieve comma-separated tags from data attribute
            const rawTags = article.dataset.tags ? article.dataset.tags.split(',') : [];
            const articleTags = rawTags.map(t => t.trim());

            // OR Logic: Shows if skill matches AT LEAST ONE selected tag
            const isMatch = Array.from(activeTags).some(tag => articleTags.includes(tag));

            // If no filters are chosen, or at least one matches, show the row
            if (activeTags.size === 0 || isMatch) {
                article.classList.remove('filtered-out');
            } else {
                article.classList.add('filtered-out');
            }
        });
    }
});
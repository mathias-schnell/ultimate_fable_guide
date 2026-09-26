<section class="filter-rows-container">
    <select class="filter-mode">
        <option>--- Filter Mode ---</option>
        <option value="0">Show items with ONE of...</option>
        <option value="1">Show items with ALL of...</option>
    </select>
    <div class="filter-row filter-row-prime">
        <select class="filter-tag-select">
            <option value="">-- Select Tag --</option>
            <?php foreach ($filter_tags as $group_label => $tags): ?>
                <optgroup label="<?= htmlspecialchars($group_label) ?>">
                    <?php foreach ($tags as $tag_key => $tag_name): ?>
                        <option value="<?= htmlspecialchars($tag_key) ?>">
                            <?= htmlspecialchars($tag_name) ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
        <button type="button" class="btn-add-filter-row" title="Add filter">&plus;</button>
        <button type="button" class="btn-remove-filter-row" title="Remove filter">&times;</button>
    </div>
</section>
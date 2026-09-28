<?php

function get_nav_tabs(array $sections): string {
    $tabs = "";
    foreach($sections as $key => $label):
        $file_key = str_replace("-", "_", $key);
        if(file_exists(DATA_PATH . "/{$file_key}.json")):
            $tabs .= "<button type='button' class='tab' id='tab-{$key}' aria-selected='false' aria-controls='{$key}-container'> {$label} </button>\n";
        endif;
    endforeach;
    return $tabs;
}

function get_content(array $sections): string {
    $content = "";
    foreach($sections as $key => $label):
        $file_key = str_replace("-", "_", $key);
        $filepath = DATA_PATH . "/{$file_key}.json";
        if (!file_exists($filepath)) continue;
        $data = json_decode(file_get_contents($filepath), true) ?? [];

        $content .= "<div id='{$key}-container' class='{$key}-container'>";
        $content .= get_content_groups($data, $key);
        $content .= "</div>";
    endforeach;
    return $content;
}

function get_content_groups(array $data, string $key): string {
    ob_start();
    
    $universal_tags = $data['schema']['universal-tags'] ?? [];
    $data_key = $data['schema']['key'] ?? "";
    $groups = $data['groups'] ?? [];
    $entry_counter = 0;
    $previous_cols = null;
    ?>
    <section class="<?= $key ?>-group">
    <?php
    foreach ($groups as $i => $group):
        $group_name = $group['name'] ?? '';
        $group_label = $group['label'] ?? '';
        $cols = $group['columns'] ?? [];
        $grid_template = $group['grid-template-columns'] ?? '';
        $group_tags = $group['group-tags'] ?? [];
        $items = $group[$data_key] ?? [];
        $grid_style = !empty($grid_template) ? " style='grid-template-columns: {$grid_template};'" : "";
        $show_header = ($cols !== $previous_cols) || !empty($group_label);
        $previous_cols = $cols;

        if($i > 0 && $show_header):
        ?>
        </section>
        <section class="<?= $key ?>-group">
        <?php
        endif;

        if($show_header):
            render_group_header($key, $cols, $group_label, $grid_style);
        endif;

        foreach ($items as $entry):
            $entry_counter++;
            $combined_tags = array_unique(array_merge($universal_tags, $group_tags, $entry['tags'] ?? []));
            sort($combined_tags);
            $id = "{$key}-description-{$entry_counter}";
            render_row_article($key, $id, $combined_tags, $cols, $entry, $grid_style);
        endforeach;
    endforeach;
    ?>
    </section>
    <?php    
    return ob_get_clean();
}

function render_group_header(string $key, array $cols, string $group_label, string $grid_style): void {
    if(!empty($group_label)):
    ?>
        <h3 class="<?= $key ?>-group-title"><?= htmlspecialchars($group_label) ?></h3>
    <?php
    endif;
    ?>
    <div class="<?= $key ?>-list-header"<?= $grid_style ?>>
        <div>
            <button class="<?= $key ?>-multitoggle" type="button" title="Expand All" data-key="<?= $key ?>" aria-expanded="false" aria-controls="article.<?= $key ?>">⊕</button>
        </div>
        <?php foreach ($cols as $col): ?>
            <div><?= htmlspecialchars(render_unicode_symbols($col['label'])) ?></div>
        <?php endforeach; ?>
        <div></div>
    </div>
    <?php
}

function render_row_article(string $key, string $id, array $combined_tags, array $cols, array $entry, string $grid_style): void {
    $tags_str = htmlspecialchars(implode(', ', $combined_tags));
    ?>
    <article class="<?= $key ?>" data-tags="<?= $tags_str ?>">
        <div class="<?= $key ?>-row"<?= $grid_style ?>>
            <div class="<?= $key ?>-expand">
                <?php if(!empty($entry['description'])): ?>
                    <button class="<?= $key ?>-toggle" type="button" title="Expand" aria-expanded="false" aria-controls="<?= $id ?>">▶</button>
                <?php endif; ?>
            </div>

            <?php foreach ($cols as $col): 
                $field_name = $col['field'] ?? $col['class'];
                $cell_class = $col['class'] ?? $field_name;
            ?>
                <div class="<?= $key . "-" . $cell_class ?>">
                    <?= get_cell_data($field_name, $entry) ?>
                </div>
            <?php endforeach; ?>

            <div class="<?= $key ?>-pin">
                <button class="<?= $key ?>-pin" title="Favorite" type="button" aria-checked="false"></button>
            </div>
        </div>
        
        <?php render_article_description($key, $id, $entry, $combined_tags); ?>
    </article>
    <?php
}

function render_article_description(string $key, string $id, array $entry, array $combined_tags): void {
    global $filter_tags;
    if(!empty($entry['description'])): ?>
        <div class="<?= $key ?>-description-container hidden" id="<?= $id ?>">
            <?php if (!empty($entry['additional_requirements'])): ?>
                <div class="additional-requirements">
                    <strong>Additional Requirements:</strong> <?= htmlspecialchars($entry['additional_requirements']) ?>
                </div>
            <?php endif; ?>

            <div class="description">
                <?= $entry['description'] ?? '' ?>
            </div>

            <?php if (!empty($entry['spell'])): ?>
                <div class="spell-block">
                    <div class="spell-block-header">
                        <div><?= htmlspecialchars($entry['spell']['name']) ?></div>
                        <div>MP: <span><?= $entry['spell']['mp'] ?></span></div>
                        <div>Target: <span><?= htmlspecialchars($entry['spell']['target']) ?></span></div>
                        <div>Duration: <span><?= htmlspecialchars($entry['spell']['duration']) ?></span></div>
                    </div>
                    <div class="spell-block-description">
                        <?= $entry['spell']['description'] ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(!empty($combined_tags)): 
                    $tag_map = array_merge(...array_values($filter_tags));
                    $tag_labels = [];
                    foreach ($combined_tags as $tag):
                        $tag_labels[] = $tag_map[$tag] ?? ucwords(str_replace('-', ' ', $tag));
                    endforeach;
                ?>
                <div class="tag-list">
                    Tags: <?= implode(', ', $tag_labels); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif;
}

function render_unicode_symbols(string $text): string {
    static $symbols = [
        '🗲' => '<svg class="icon icon-spell" viewBox="0 0 24 24" width="1em" height="1em" fill="currentColor" style="display:inline-block; vertical-align:-0.125em;" aria-label="Offensive Spell"><path d="M13 2L4.5 13.5H11L8.5 22L19.5 10.5H13L15.5 2Z"/></svg>',
        '🟅' => '<svg class="icon icon-martial" viewBox="0 0 24 24" width="1em" height="1em" fill="currentColor" style="display:inline-block; vertical-align:-0.125em;" aria-label="Martial"><path d="M12 2C12 7.5 16.5 12 22 12C16.5 12 12 16.5 12 22C12 16.5 7.5 12 2 12C7.5 12 12 7.5 12 2Z"/></svg>',
    ];
    return strtr($text, $symbols);
}

function get_cell_data(string $key, array $entry): string {
    if (!isset($entry[$key])) return "";
    $val = $entry[$key];
    if (is_array($val)) return empty($val) ? "—" : htmlspecialchars(implode(', ', $val));
    return render_unicode_symbols((string)$val);
}
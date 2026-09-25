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
        $cols = $data['schema']['columns'] ?? [];
        $grid_cols = $data['schema']['grid-template-columns'] ?? "";

        $content .= "<div id='{$key}-container' class='{$key}-container'>";
        $content .= get_column_headers($key, $cols, $grid_cols);
        $content .= get_row_data($data, $key, $cols, $grid_cols);
        $content .= "</div>";
    endforeach;
    return $content;
}

function get_column_headers(string $key, array $cols, string $grid_cols): string {
    ob_start();
    ?>
        <div class="<?=$key ?>-list-header" <?=($grid_cols ? "style='grid-template-columns:{$grid_cols};'" : "") ?>>
            <div>
                <button class="<?=$key ?>-multitoggle" type="button" data-key="<?=$key ?>" aria-expanded="false" aria-controls="article.<?=$key ?>">+</button>
            </div>
            <?php foreach($cols as $col): ?>
                <div><?= htmlspecialchars($col['label']) ?></div>
            <?php endforeach; ?>
            <div></div>
        </div>
    <?php
    return ob_get_clean();
}

function get_row_data(array $data, string $key, array $cols, string $grid_cols): string {
    ob_start();
    
    $universal_tags = $data['schema']['universal-tags'] ?? [];
    $data_key = $data['schema']['key'];
    $entry_counter = 0;

    if (isset($data['groups']) && is_array($data['groups'])) {
        foreach ($data['groups'] as $group) {
            $group_tags = $group['group-tags'] ?? [];
            $items = $group[$data_key] ?? $group[$key] ?? [];
            foreach ($items as $entry) {
                $entry_counter++;
                $combined_tags = array_unique(array_merge($universal_tags, $group_tags, $entry['tags'] ?? []));
                $tags_str = htmlspecialchars(implode(', ', $combined_tags));
                $id = "{$key}-description-{$entry_counter}";
                render_row_article($key, $id, $tags_str, $cols, $entry, $grid_cols);
            }
        }
    } else {
        $items = $data[$data_key] ?? $data[$key] ?? [];
        foreach ($items as $entry) {
            $entry_counter++;
            $combined_tags = array_unique(array_merge($universal_tags, $entry['tags'] ?? []));
            $tags_str = htmlspecialchars(implode(', ', $combined_tags));
            $id = "{$key}-description-{$entry_counter}";
            render_row_article($key, $id, $tags_str, $cols, $entry, $grid_cols);
        }
    }
    return ob_get_clean();
}

function render_row_article(string $key, string $id, string $tags_str, array $cols, array $entry, string $grid_cols): void {
    ?>
    <article class="<?= $key ?>" data-tags="<?= $tags_str ?>">
        <div class="<?= $key ?>-row" <?=($grid_cols ? "style='grid-template-columns:{$grid_cols};'" : "") ?>>
            <div class="<?= $key ?>-expand">
                <button class="<?= $key ?>-toggle" type="button" aria-expanded="false" aria-controls="<?= $id ?>">▶</button>
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
                <button class="<?= $key ?>-pin" type="button" aria-checked="false">🖈</button>
            </div>
        </div>

        <div class="<?= $key ?>-description-container hidden" id="<?= $id ?>">
            <div class="<?= $key ?>-description">
                <?= $entry['description'] ?? '' ?>
                
                <?php if (!empty($entry['additional_requirements'])): ?>
                    <p class="additional-requirements">
                        <strong>Additional Requirements:</strong> <?= htmlspecialchars($entry['additional_requirements']) ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($entry['spell'])): ?>
                    <div class="heroic-spell-block">
                        <h4>Spell: <?= htmlspecialchars($entry['spell']['name']) ?> (<?= $entry['spell']['mp'] ?> MP)</h4>
                        <p><strong>Target:</strong> <?= htmlspecialchars($entry['spell']['target']) ?> | <strong>Duration:</strong> <?= htmlspecialchars($entry['spell']['duration']) ?></p>
                        <p><?= $entry['spell']['description'] ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </article>
    <?php
}

function get_cell_data(string $key, array $entry): string {
    if (!isset($entry[$key])) return "";
    $val = $entry[$key];
    if (is_array($val)) return empty($val) ? "—" : htmlspecialchars(implode(', ', $val));
    return htmlspecialchars((string)$val);
}
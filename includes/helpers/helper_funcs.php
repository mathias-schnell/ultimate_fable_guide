<?php

function get_rand_class(): string {
    global $title_icons;
    return $title_icons[array_rand($title_icons)];
}

function scan_dir_tree(string $dir = DATA_PATH): array {
    $result = [];
    if (!is_dir($dir)) return $result;
    $items = array_diff(scandir($dir), array('.', '..'));

    foreach ($items as $item):
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            $json_path = $path . '/data.json';
            $meta = [];
            
            if (file_exists($json_path)) $meta = json_decode(file_get_contents($json_path), true) ?? [];
            $children = scan_dir_tree($path);
            $has_children = !empty($children);
            $key = str_replace("+", "-", strtolower(urlencode($item)));
            
            $result[$key] = $meta;
            $result[$key]['type'] = $has_children ? 'category' : 'leaf';
            $result[$key]['path'] = $path;
            $result[$key]['children'] = $children;
        }
    endforeach;
    return $result;
}

function extract_used_tags(array $group): array {
    $used_tags = [];
    
    if (!empty($group['group-tags']) && is_array($group['group-tags'])):
        foreach ($group['group-tags'] as $tag):
            $used_tags[$tag] = true;
        endforeach;
    endif;

    if (!empty($group['data']) && is_array($group['data'])):
        foreach ($group['data'] as $item):
            if (!empty($item['tags']) && is_array($item['tags'])):
                foreach ($item['tags'] as $tag):
                    $used_tags[$tag] = true;
                endforeach;
            endif;
        endforeach;
    endif;

    return array_keys($used_tags);
}

function get_menu(array $nodes = [], int $level = 1, array $item_order = [], $combined_name = ''): string {
    global $order;
    if(empty($nodes)) $nodes = scan_dir_tree();
    $keys = array_keys($nodes);
    $list_class = $level === 1 ? 'menu-root' : 'menu-nested';

    if ($level == 1) $keys = array_merge($order, array_diff(array_diff(scandir(DATA_PATH), array('.', '..')), $order));
    if (!empty($item_order)) $keys = array_merge($item_order, array_diff($keys, $item_order));
    
    ob_start();
    ?>
    <ul class="<?php echo $list_class; ?>" data-depth="<?php echo $level; ?>">
        <?php 
            foreach ($keys as $key):
                $is_category = $nodes[$key]['type'] === 'category';
                $label = htmlspecialchars($nodes[$key]['label']);
                $name = $label ? preg_replace('/%[0-9a-fA-F]{2}/', '', str_replace("+", "-", strtolower(urlencode($label)))) : $key;
                $name = $combined_name ? $combined_name . '-' . $name : $name;
                $icon_file = $nodes[$key]['icon'] ?? '';
                $always_expand = $nodes[$key]['always-expand'] ?? false;
                $is_container = $nodes[$key]['is-container'] ?? true;
                $show_headers = $nodes[$key]['show-headers'] ?? true;
        ?>
            <li class="menu-item <?= $is_container ? '' : 'no-container'; ?>">
                <?php if ($is_category): ?>
                    <div class="menu-header">
                        <button type="button" class="menu-toggle" aria-controls="<?= $name ?>" aria-expanded="false" <?= $always_expand ? "style='display:none;'" : ''; ?>>
                            <?php if(!$always_expand):?>
                                <span class="menu-icon ui-icon active svg-expand"></span>
                            <?php endif; ?>
                            <span class="menu-label"><?php echo $label; ?></span>
                            <?php if (!empty($icon_file)): ?>
                                <span class="menu-icon ui-icon <?= "svg-" . str_replace(".svg", "", $icon_file); ?>"></span>
                            <?php endif; ?>
                        </button>
                    </div>
                    
                    <div id="<?= $name ?>" class="menu-content" aria-hidden="<?= $always_expand ? 'false' : 'true' ?>">
                        <?php echo get_menu($nodes[$key]['children'], $level + 1, $nodes[$key]['order'] ?? [], $name); ?>
                    </div>
                <?php else: ?>
                    <button type="button" class="menu-toggle" aria-controls="<?= $name ?>-group" aria-expanded="false" <?= $always_expand ? "style='display:none;'" : ''; ?>>
                        <?php if(!$always_expand):?>
                                <span class="menu-icon ui-icon active svg-expand"></span>
                            <?php endif; ?>
                        <span class="menu-label"><?php echo $label; ?></span>
                        <?php if (!empty($icon_file)): ?>
                            <span class="menu-icon ui-icon <?= "svg-" . str_replace(".svg", "", $icon_file); ?>"></span>
                        <?php endif; ?>
                    </button>

                    <div id="<?= $name ?>-group" class="menu-content <?= $name ?>-group" aria-hidden="<?= $always_expand ? 'false' : 'true' ?>">
                        <?php echo get_content($nodes[$key], $key, $name, $show_headers); ?>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php
    return ob_get_clean();
}

function get_content(array $entry, string $key, string $combined_name = '', bool $show_headers = true): string {
    ob_start();

    $counter = 0;
    $sub_labels = $entry['sub-labels'] ?? [];
    $cols = $entry['columns'] ?? [];
    $grid_template = $entry['grid-template-columns'] ?? '';
    $group_tags = $entry['group-tags'] ?? [];
    $items = $entry['data'] ?? [];
    $grid_style = !empty($grid_template) ? " style='grid-template-columns: {$grid_template};'" : "";

    if($show_headers):
        render_group_header($key, $cols, $sub_labels, $grid_style, $combined_name);
    endif;

    foreach ($items as $item):
        $counter++;
        $combined_tags = array_unique(array_merge($group_tags, $item['tags'] ?? []));
        sort($combined_tags);
        render_row_article($key, $counter, $combined_tags, $cols, $item, $grid_style, $combined_name);
    endforeach;
    
    return ob_get_clean();
}

function render_group_header(string $key, array $cols, array $sub_labels, string $grid_style, string $name = ''): void {
   if(!empty($sub_labels)):
    ?>
        <ul class="<?= $name ?>-group-sub-labels">
            <?php foreach ($sub_labels as $sub_label): ?>
                <li><?= htmlspecialchars($sub_label) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php
    endif;
    if(!empty($cols)):
    ?>
    <div class="<?= $name ?>-list-header"<?= $grid_style ?>>
        <div>
            <button class="<?= $name ?>-multitoggle" type="button" data-key="<?= $key ?>" aria-expanded="false" aria-controls="article.<?= $name ?>"></button>
        </div>
        <?php foreach ($cols as $col): ?>
            <div class="<?= $name ?>-col-sort" data-sort-by=<?= $name . "-" . $col['field'] ?>><?= htmlspecialchars($col['label']) ?></div>
        <?php endforeach; ?>
        <div></div>
    </div>
    <?php
    endif;
}


function render_row_article(string $key, int $counter, array $combined_tags, array $cols, array $entry, string $grid_style, string $name = ''): void {
    $aria_str = "";
    $name = $name ?? $key;
    $row_id = "{$name}-row-{$counter}";
    $desc_id = "{$name}-description-{$counter}";
    $class_str = $key;
    $tags_str = htmlspecialchars(implode(', ', $combined_tags));
    $url_params = http_build_query(['cat' => $key, 'search' => strtolower(get_cell_data('name', $entry)), 'open' => true]);
    ?>
    <article id="<?= $row_id ?>" class="<?= $class_str ?>" <?= $aria_str ?> data-slug="<?= slugify(get_cell_data('name', $entry)) ?>" data-tags="<?= $tags_str ?>">
        <div class="<?= $name ?>-row" <?= $grid_style ?>>
            <div class="<?= $name ?>-expand">
                <?php if(!empty($entry['description'])): ?>
                    <button class="<?= $name ?>-toggle" type="button" aria-expanded="false" aria-controls="<?= $desc_id ?>">
                        <span class="menu-icon ui-icon active svg-play-button"></span>
                    </button>
                <?php endif; ?>
            </div>

            <?php foreach ($cols as $col): 
                $field_name = $col['field'] ?? $col['class'];
                $cell_class = $col['class'] ?? $field_name;
            ?>
                <div class="<?= $name . "-" . $cell_class ?>">
                    <?= get_cell_data($field_name, $entry) ?>
                </div>
            <?php endforeach; ?>

            <div class="<?= $name ?>-url">
                <button class="<?= $name ?>-link" type="button" data-link="<?= BASE_URL . "?" . $url_params ?>">
                    <span class="menu-icon ui-icon active svg-share"></span>
                </button>
                <!--<button class="<?= $name ?>-pin" title="Favorite" type="button" aria-checked="false"></button>-->
            </div>
        </div>
        
        <?php render_article_description($key, $counter, $entry, $combined_tags, $name); ?>
    </article>
    <?php
}

function render_article_description(string $key, int $counter, array $entry, array $combined_tags, string $name = ''): void {
    global $filter_tags;
    $name = $name ?? $key;
    $desc_id = "{$name}-description-{$counter}";
    if(!empty($entry['description'])): ?>
        <div id="<?= $desc_id ?>" class="<?= $name ?>-description-container" aria-hidden="true">
            <?php if (!empty($entry['additional_requirements'])): ?>
                <div class="additional-requirements">
                    <strong>Additional Requirements:</strong> <?= get_cell_data('additional_requirements', $entry) ?>
                </div>
            <?php endif; ?>

            <div class="description">
                <?= inject_element_symbols(get_cell_data('description', $entry)) ?>
            </div>

            <?php 
                if (!empty($entry['spell'])):
                    $spell = $entry['spell'];
            ?>
                <div class="spell-block">
                    <div class="spell-block-header">
                        <div><?= get_cell_data('name', $spell) ?></div>
                        <div>MP: <span><?= get_cell_data('mp', $spell) ?></span></div>
                        <div>Target: <span><?= get_cell_data('target', $spell) ?></span></div>
                        <div>Duration: <span><?= get_cell_data('duration', $spell) ?></span></div>
                    </div>
                    <div class="spell-block-description">
                        <?= get_cell_data('description', $spell) ?>
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

function get_filter_section(): string {
    ob_start();
    ?>
    <ul class="filter-root">
        <li class="filter-item filter-basic">
            <div class="search-wrapper">
                <i class="ui-icon active svg-funnel"></i>
                <input type="text" class="search-basic" placeholder="Three letters minimum..." autocomplete="off">
                <i class="ui-icon svg-magnifying-glass"></i>
            </div>
        </li>
            <!--<div>
                <input type="checkbox" class="toggle-bonus-content" autocomplete="off" /> Enable 'Fabula Ultima Bonus' content
            </div>
            <div>
                <input type="checkbox" class="toggle-description-search" autocomplete="off" checked=true /> Search hidden descriptions and tags
            </div>-->
        <!--
            <select class="filter-mode" autocomplete="off">
            <option value="" selected disabled>--- Filter Mode ---</option>
            <option value="0">Match at least one of...</option>
            <option value="1">Match at least all of...</option>
            </select>
                -->
        <!--<div class="filter-row">
            
            <select class="filter-tag-select filter-cat" autocomplete="off">
                                
            </select>
            <select class="filter-tag-select filter-subcat" autocomplete="off">
                                
            </select>
            <button type="button" class="btn-add-filter-row" title="Add filter">&plus;</button>
            <button type="button" class="btn-remove-filter-row" title="Remove filter">&times;</button>
        </div>-->
    </ul>
    <?php

    return ob_get_clean();
}

function get_cell_data(string $key, array $entry): string {
    if (!isset($entry[$key])) return "";
    $val = $entry[$key];

    if (is_array($val)) return empty($val) ? "—" : htmlspecialchars(implode(', ', $val));
    if ($key == 'weapons-only' && !empty($entry['weapons-only'])) return "<span class='ui-icon svg-check-mark'></span>";
    if ($key == 'name'):
        if (!empty($entry['offensive'])) return $val . " <span class='ui-icon svg-electric'></span>";
        if (!empty($entry['martial'])) return $val . " <span class='ui-icon svg-diamonds'></span>";
    endif;
    return (string)$val;
}

function slugify(string $text): string {
    if (class_exists('Normalizer')) {
        $text = Normalizer::normalize($text, Normalizer::FORM_D);
        $text = preg_replace('/\p{Mn}/u', '', $text);
    } else {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
    }
    $text = strtolower($text);
    $text = preg_replace('/[\'’]/u', '', $text);
    $text = preg_replace('/[^a-z0-9\s-]/u', '', $text);
    $text = preg_replace('/[\s-]+/u', '-', $text);
    return trim($text, '-');
}

function inject_element_symbols(string $html): string {
    $pattern = '/<[^>]+>(*SKIP)(*F)|\b(physical|air|bolt|ice|earth|fire|light|dark|poison)\b/i';

    return preg_replace_callback($pattern, function ($matches) {
        $word = $matches[0];
        $type = strtolower($word);
        return sprintf('%s (<i class="elem-icon elem-%s"></i>)', $word,$type);
    }, $html);
}

function debug_print($data) {
    echo "<pre>" . print_r($data, true) . "</pre>";
}
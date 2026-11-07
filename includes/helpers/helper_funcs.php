<?php

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

function get_menu(array $nodes = [], int $level = 1, array $item_order = []): string {
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
                if(!isset($nodes[$key])) continue;
                $is_category = $nodes[$key]['type'] === 'category';
                $label = $nodes[$key]['label'];
                $slug = $nodes[$key]['slug'];
                $expand_icon = $nodes[$key]['expand-icon'];
                $contract_icon = $nodes[$key]['contract-icon'];
                $menu_icon = $nodes[$key]['menu-icon'];
                $always_expand = $nodes[$key]['always-expand'];
                $is_container = $nodes[$key]['is-container'];
                $show_headers = $nodes[$key]['show-headers'];
        ?>
            <li class="menu-item <?= $is_container ? '' : 'no-container'; ?>">
                <?php if ($is_category): ?>
                    <div class="menu-header">
                        <button type="button" class="menu-toggle" aria-controls="<?= $slug ?>" aria-expanded="false" <?= $always_expand ? "style='display:none;' disabled='disabled'" : ''; ?>>
                            <?php if(!$always_expand):?>
                                <span class="expand-contract-icon ui-icon active svg-<?= $expand_icon; ?>" data-expand-icon="<?= $expand_icon; ?>" data-contract-icon="<?= $contract_icon; ?>"></span>
                                <span class="ui-icon inactive svg-<?= $contract_icon ?>"></span>
                            <?php endif; ?>
                            <span class="menu-label"><?php echo $label; ?></span>
                            <?php if (!empty($menu_icon)): ?>
                                <span class="menu-icon ui-icon svg-<?= $menu_icon; ?>"></span>
                            <?php endif; ?>
                        </button>
                    </div>
                    
                    <div id="<?= $slug ?>" class="menu-content" aria-hidden="<?= $always_expand ? 'false' : 'true' ?>">
                        <?php echo get_menu($nodes[$key]['children'], $level + 1, $nodes[$key]['order'] ?? []); ?>
                    </div>
                <?php else: ?>
                    <button type="button" class="menu-toggle" aria-controls="<?= $slug ?>-group" aria-expanded="false" <?= $always_expand ? "style='display:none;' disabled='disabled'" : ''; ?>>
                        <?php if(!$always_expand):?>
                                <span class="expand-contract-icon ui-icon active svg-<?= $expand_icon; ?>" data-expand-icon="<?= $expand_icon; ?>" data-contract-icon="<?= $contract_icon; ?>"></span>
                                <span class="ui-icon inactive svg-<?= $contract_icon ?>"></span>
                            <?php endif; ?>
                        <span class="menu-label"><?php echo $label; ?></span>
                        <?php if (!empty($menu_icon)): ?>
                            <span class="menu-icon ui-icon svg-<?= $menu_icon; ?>"></span>
                        <?php endif; ?>
                    </button>

                    <div id="<?= $slug ?>-group" class="menu-content <?= $slug ?>-group" aria-hidden="<?= $always_expand ? 'false' : 'true' ?>">
                        <?php echo get_content($nodes[$key], $key); ?>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php
    return ob_get_clean();
}

function get_content(array $data, string $key): string {
    ob_start();

    $items = $data['data'];
    $show_headers = $data['show-headers'];
    if($show_headers):
        render_group_header($data, $key);
    endif;

    foreach ($items as $item):
        $combined_tags = array_unique(array_merge($data['group-tags'], $item['tags'] ?? []));
        sort($combined_tags);
        $cols = $data['columns'];
        $grid_style = "style='grid-template-columns: {$data['grid-template-columns']}'";
        render_row_article($item, $combined_tags, $cols, $grid_style);
    endforeach;
    
    return ob_get_clean();
}

function render_group_header(array $data, string $key): void {
    $sub_labels = $data['sub-labels'];
    $cols = $data['columns'];
    $grid_style = "style='grid-template-columns: {$data['grid-template-columns']}'";
    $slug = $data['slug'];

   if(!empty($sub_labels)):
    ?>
        <ul class="<?= $slug ?>-group-sub-labels">
            <?php foreach ($sub_labels as $sub_label): ?>
                <li><?= htmlspecialchars($sub_label) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php
    endif;
    if(!empty($cols)):
    ?>
    <div class="<?= $slug ?>-list-header"<?= $grid_style ?>>
        <div></div>
        <?php foreach ($cols as $col): ?>
            <div class="<?= $slug ?>-col-sort" data-sort-by=<?= $slug . "-" . $col['key'] ?>><?= htmlspecialchars($col['label']) ?></div>
        <?php endforeach; ?>
        <div></div>
    </div>
    <?php
    endif;
}


function render_row_article(array $item, array $combined_tags, array $cols, string $grid_style): void {
    $slug = $item['slug'];
    $row_id = "{$slug}-row";
    $desc_id = "{$slug}-description";
    $tags_str = htmlspecialchars(implode(', ', $combined_tags));
    $expand_icon = $item['expand-icon'];
    $contract_icon = $item['contract-icon'];
    ?>
    <article id="<?= $row_id ?>" class="<?= $slug ?>-row" data-slug="<?= $slug ?>" data-tags="<?= $tags_str ?>">
        <div class="<?= $slug ?>-row" <?= $grid_style ?>>
            <div class="<?= $slug ?>-expand">
                <?php if(!empty($item['description'])): ?>
                    <button class="<?= $slug ?>-toggle" type="button" aria-expanded="false" aria-controls="<?= $desc_id ?>">
                        <span class="expand-contract-icon ui-icon active svg-<?= $expand_icon ?>" data-expand-icon="<?= $expand_icon ?>" data-contract-icon="<?= $contract_icon ?>"></span>
                        <span class="ui-icon inactive svg-<?= $contract_icon ?>"></span>
                    </button>
                <?php endif; ?>
            </div>

            <?php foreach ($cols as $col): 
                $field = $col['key'];
            ?>
                <div class="<?= $slug ?>-<?= $field ?>">
                    <?= get_cell_data($field, $item) ?>
                </div>
            <?php endforeach; ?>

            <div class="<?= $slug ?>-url">
                <button class="<?= $slug ?>-link" type="button" data-link="<?= BASE_URL . "?" . $url_params ?>">
                    <span class="menu-icon ui-icon active svg-share"></span>
                </button>
            </div>
        </div>
        
        <?php render_article_description($item, $combined_tags); ?>
    </article>
    <?php
}

function render_article_description(array $item, array $combined_tags): void {
    global $filter_tags;
    $slug = $item['slug'];
    $desc_id = "{$slug}-description";
    if(!empty($item['description'])): ?>
        <div id="<?= $desc_id ?>" class="<?= $slug ?>-description-container" aria-hidden="true">
            <?php if (!empty($item['additional_requirements'])): ?>
                <div class="additional-requirements">
                    <strong>Additional Requirements:</strong> <?= get_cell_data('additional_requirements', $item) ?>
                </div>
            <?php endif; ?>

            <div class="description">
                <?= get_cell_data('description', $item) ?>
            </div>

            <?php 
                if (!empty($item['spell'])):
                    $spell = $item['spell'];
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
                <input type="text" class="search-basic" placeholder="Three letters minimum..." autocomplete="off" maxlength="50" />
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
    if (is_array($entry[$key])) return empty($entry[$key]) ? "—" : htmlspecialchars(implode(', ', $entry[$key]));
    return (string)$entry[$key];
}

function debug_print($data) {
    echo "<pre>" . print_r($data, true) . "</pre>";
}
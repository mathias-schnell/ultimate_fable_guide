<?php

function get_stylesheets($sections): string {
    $sheets = "";
    foreach($sections as $key => $label):
        if(file_exists(CSS_PATH . "/{$key}.css")):
            $style_url = CSS_URL . "/{$key}.css";
            $sheets .= "<link rel='stylesheet' href='{$style_url}'>\n";
        endif;
    endforeach;
    return $sheets;
}

function get_nav_tabs($sections): string {
    $tabs = "";
    foreach($sections as $key => $label):
        if(is_dir(DATA_PATH . "/{$key}") && count(scandir(DATA_PATH . "/{$key}")) > 2):
            $html_key = str_replace("_", "-", $key);
            $tabs .= "<button type='button' class='tab' id='tab-{$html_key}' aria-selected='false' aria-controls='{$html_key}-container'> {$label} </button>\n"; 
        endif;
    endforeach;
    return $tabs;
}

function get_content($sections, $columns): string {
    $content = "";
    foreach($sections as $key => $label):
        if(is_dir(DATA_PATH . "/{$key}") && count(scandir(DATA_PATH . "/{$key}")) > 2):
            $html_key = str_replace("_", "-", $key);
            $content .= "<div id='{$html_key}-container' class='{$html_key}-container'>";
            $content .= get_column_headers($key, $html_key, $columns);
            $content .= get_row_data($key, $html_key, $columns);
            $content .= "</div>";
        endif;
    endforeach;
    return $content;
}

function get_column_headers($key, $html_key, $columns): string {
    ob_start();
    ?>
        <div class="<?=$html_key ?>-list-header">
            <div></div>
            <?php foreach($columns[$key] as $col): ?>
                <div><?=$col['label'] ?></div>
            <?php endforeach; ?>
            <div></div>
        </div>
    <?php
    return ob_get_clean();
}

function get_row_data($key, $html_key, $columns): string {
    ob_start();
    $json_files = array_slice(scandir(DATA_PATH . "/{$key}"), 2);
    foreach($json_files as $file):
        if (pathinfo($file, PATHINFO_EXTENSION) === 'json'):
            $json_data = file_get_contents(DATA_PATH . "/{$key}/{$file}");
            $data = json_decode($json_data, true);
            $source = $data['source'] ?? [];
            $entries = $data[$html_key] ?? [];
            foreach ($entries as $id => $entry):
                $tags = "source-" . $source['name'] . ', ' . htmlspecialchars(implode(',', $entry['tags']));
                $id = "{$html_key}-description-{$source['name']}-" . ($id + 1);
            ?>
            <article class="<?=$html_key ?>" data-tags="<?=$tags ?>">
                <div class="<?=$html_key ?>-row">
                    <div class="<?=$html_key ?>-expand">
                        <button class="<?=$html_key ?>-toggle"
                                type="button"
                                aria-expanded="false"
                                aria-controls="<?=$id ?>"> 
                            ▶
                        </button>
                    </div>
                    <?php foreach($columns[$key] as $col): ?>
                        <div class=<?=$html_key . "-" . $col['class'] ?>>
                            <?php
                                if($col['class'] == "source"):
                                    echo $source['label'];
                                else:
                                    echo get_cell_data($col['class'], $entry);
                                endif;
                            ?>
                        </div>
                    <?php endforeach; ?>
                    <div class="<?=$html_key ?>-pin">
                        <button class="<?=$html_key ?>-pin"
                                type="button"
                                aria-checked="false">
                            🖈
                        </button>
                    </div>
                </div>
                <div class="<?=$html_key ?>-description-container hidden" id="<?=$id ?>">
                    <div class="<?=$html_key ?>-description">
                        <?=$entry['description'] ?>
                    </div>
                </div>
            </article>
            <?php 
            endforeach;
        endif;
    endforeach;
    return ob_get_clean();
}

function get_cell_data($key, $entry): string {
    $data = "";
    if (isset($entry[$key])):
        if(is_array($entry[$key])):
            $data = implode(', ', $entry[$key]);
        else:
            $data = $entry[$key];
        endif;
    endif;
    return $data;
}
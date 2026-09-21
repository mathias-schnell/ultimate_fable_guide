<?php
    require_once __DIR__ . '/includes/app_conf.php';

    $stylesheets = "<link rel='stylesheet' href='" . CSS_URL . "/reset.css'>\n
                    <link rel='stylesheet' href='" . CSS_URL . "/style.css'>\n";
    $content = "";
    $tab_buttons = "";
    
    foreach($sections as $key => $label):
        if(file_exists(CSS_PATH . "/{$key}.css")):
            $style_url = CSS_URL . "/{$key}.css";
            $stylesheets .= "<link rel='stylesheet' href='{$style_url}'>\n";
        endif;

        if(file_exists($content_path = INCLUDES_PATH . "/content/{$key}.php")):
            $html_key = str_replace("_", "-", $key);
            $tab_buttons .= "<button type='button' class='tab' id='tab-{$html_key}' aria-selected='false' aria-controls='{$html_key}-container'> {$label} </button>\n";

            ob_start();
            include_once($content_path);
            $content .= "<div id='{$html_key}-container' class='{$html_key}-container'>\n" . ob_get_clean() . "\n</div>\n";
        endif;
    endforeach;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Fable Guide</title>
    <?=$stylesheets; ?>
    <script type="module" src="<?=JS_URL?>/app.js"></script>
</head>
<body>
    <main id="main-container" class="container">
        <header class="page-header">
            <nav class="tab-nav">
                <?=$tab_buttons ?>
            </nav>
        </header>
        <?php include_once(INCLUDES_PATH . "/filter.php"); ?>
        <section class="content-container">
            <?=$content ?>
        </section>
    </main>
</body>
</html>
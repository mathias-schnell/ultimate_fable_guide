<?php
    $title_text = "Ultimate Fable Guide";
    $subtitle_text = "Character Compendium";
    $data_subpath = 'cc';

    require_once __DIR__ . '/includes/config/app_conf.php';
    require_once __DIR__ . '/includes/config/data_conf.php';
    require_once __DIR__ . '/includes/helpers/helper_funcs.php';

    $stylesheets = $scripts = "";
    foreach($css_files as $css):
        $file = CSS_PATH . '/' . $css . '.css';
        $cachebust = file_exists($file) ? '?v=' . filemtime($file) : '';
        $stylesheets .= "<link rel='stylesheet' href='" . CSS_URL . "/{$css}.css{$cachebust}'>";
    endforeach;
    foreach($js_files as $js):
        $file = JS_PATH . '/' . $js . '.js';
        $cachebust = file_exists($file) ? '?v=' . filemtime($file) : '';
        $scripts .= "<script type='module' src='" . JS_URL . "/{$js}.js{$cachebust}'></script>";
    endforeach;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Fable Guide</title>
    <?=$stylesheets; ?>
    <?=$scripts; ?>
</head>
<body>
    <main id="main-container" class="container">
        <section class="title-container">
            <span class="title-icon ui-icon <?= $title_icons[0]; ?>"></span>
            <h1 class="app-title">
                <span class="app-title-text"><?= TITLE_TEXT ?></span>
                <span class="app-subtitle">
                    <span class="underline-left"></span>
                    <span class="subtitle-text"><?= SUBTITLE_TEXT ?></span>
                    <span class="underline-right"></span>
                </span>
            </h1>
        </section>
        <section class="filter-container">
            <?php echo get_filter_section(); ?>
        </section>
        <section class="content-container">
            <?php echo get_menu(); ?>
        </section>
    </main>
</body>
</html>
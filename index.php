<?php
    require_once __DIR__ . '/includes/config/app_conf.php';
    require_once __DIR__ . '/includes/config/data_conf.php';
    require_once __DIR__ . '/includes/helpers/helper_funcs.php';

    $stylesheets = "";
    foreach($css_files as $css):
        $file = CSS_PATH . '/' . $css . '.css';
        $cachebust = file_exists($file) ? '?v=' . filemtime($file) : '';
        $stylesheets .= "<link rel='stylesheet' href='" . CSS_URL . "/{$css}.css{$cachebust}'>";
    endforeach;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Fable Guide</title>
    <?=$stylesheets; ?>
    <script type="module" src="<?=JS_URL?>/app.js<?=$cachebust?>"></script>
</head>
<body>
    <main id="main-container" class="container">
        <section class="title-container">
            <span class="title-icon ui-icon <?= $title_icons[0]; ?>"></span>
            <h1 class="app-title">Ultimate Fable Guide</h1>
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
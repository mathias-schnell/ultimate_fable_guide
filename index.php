<?php
    require_once __DIR__ . '/includes/config/app_conf.php';
    require_once __DIR__ . '/includes/config/data_conf.php';
    require_once __DIR__ . '/includes/config/tags_conf.php';
    require_once __DIR__ . '/includes/helpers/helper_funcs.php';

    $stylesheets = "<link rel='stylesheet' href='" . CSS_URL . "/reset.css'>\n
                    <link rel='stylesheet' href='" . CSS_URL . "/style.css'>\n
                    <link rel='stylesheet' href='" . CSS_URL . "/content-general.css'>\n
                    <link rel='stylesheet' href='" . CSS_URL . "/content-specific.css'>\n";
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
                <?=get_nav_tabs($sections); ?>
            </nav>
        </header>
        <?php include_once(INCLUDES_PATH . "/layout/filter.php"); ?>
        <section class="content-container">
            <?=get_content($sections, $columns); ?>
        </section>
    </main>
</body>
</html>
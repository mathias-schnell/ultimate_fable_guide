<?php
    ini_set("display_errors", true);
    error_reporting(E_ALL);
    require_once __DIR__ . '/includes/app_conf.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Fable Guide</title>
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/heroic_skills.css">
    <link rel="stylesheet" href="assets/css/class_skills.css">
    <script type="module" src="assets/js/app.js"></script>
</head>
<body>
    <main id="main-container" class="container">
        <?php include_once(INCLUDES_PATH . "/header.php"); ?>
        <?php include_once(INCLUDES_PATH . "/filter.php"); ?>
        <section class="content-container">
            <?php include_once(INCLUDES_PATH . "/content/heroic_skills.php"); ?>
            <?php include_once(INCLUDES_PATH . "/content/class_skills.php"); ?>
        </section>
    </main>
</body>
</html>
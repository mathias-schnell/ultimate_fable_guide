<?php
    $json_files = scandir(__DIR__ . '/data/heroic_skills/');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Fable Guide</title>
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script type="module" src="assets/js/app.js"></script>
</head>
<body>
    <main id="heroic-skills" class="container">
        <header class="page-header">
            <h1>Heroic Skills</h1>
        </header>
        <section class="skills">
            <div class="skill-list-header">
                <div></div>
                <div>Source</div>
                <div>Name</div>
                <div>Requirements</div>
                <div>Summary</div>
            </div>
            <?php
                foreach($json_files as $file):
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'json'):
                        $json_data = file_get_contents(__DIR__ . '/data/heroic_skills/' . $file);
                        $file = rtrim($file, ".json");
                        $data = json_decode($json_data, true);
                        $source = $data['source'] ?? [];
                        $skills = $data['skills'] ?? [];
                        foreach ($skills as $id => $skill):
                            $id = "heroic-skill-description-" . $file . "-" . $id + 1;
                        ?>
                        <article class="skill" data-classes="<?= htmlspecialchars(implode(',', $skill['classes'])) ?>">
                            <div class="skill-row">
                                <div class="skill-expand">
                                    <button class="heroic-skill-toggle"
                                            type="button"
                                            aria-expanded="false"
                                            aria-controls="<?=$id ?>"> 
                                        ▶
                                    </button>
                                </div>
                                <div class="skill-source">
                                    <?= htmlspecialchars($source['label']) ?>
                                </div>
                                <div class="skill-name">
                                    <?= htmlspecialchars($skill['name']) ?>
                                </div>
                                <div class="skill-requirements">
                                    <?php if (empty($skill['classes'])): ?>
                                        <span>-</span>
                                    <?php else: ?>
                                        <span class="class-tag">
                                            <?= htmlspecialchars(implode(', ', $skill['classes'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="skill-summary">
                                    <?= htmlspecialchars($skill['summary']) ?>
                                </div>
                            </div>
                            <div class="skill-description hidden" id="<?=$id ?>">
                                <?= htmlspecialchars($skill['description']) ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </section>
    </main>
</body>
</html>
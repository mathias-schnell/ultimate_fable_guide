<div class="class-skills-list-header">
    <div></div>
    <div>Source</div>
    <div>Class</div>
    <div>Name</div>
    <div>Max SL</div>
    <div></div>
</div>
<?php
    $json_files = scandir(DATA_PATH . '/class_skills/');
    foreach($json_files as $file):
        if (pathinfo($file, PATHINFO_EXTENSION) === 'json'):
            $json_data = file_get_contents(DATA_PATH . '/class_skills/' . $file);
            $file = rtrim($file, ".json");
            $data = json_decode($json_data, true);
            $source = $data['source'] ?? [];
            $skills = $data['class-skills'] ?? [];
            foreach ($skills as $id => $skill):
                $tags = "source-" . $source['name'] . ', ' . htmlspecialchars(implode(',', $skill['tags']));
                $id = "class-skills-description-" . $file . "-" . $id + 1;
            ?>
            <article class="class-skills" data-tags="<?=$tags ?>">
                <div class="class-skills-row">
                    <div class="class-skills-expand">
                        <button class="class-skills-toggle"
                                type="button"
                                aria-expanded="false"
                                aria-controls="<?=$id ?>"> 
                            ▶
                        </button>
                    </div>
                    <div class="class-skills-source">
                        <?= htmlspecialchars($source['label']) ?>
                    </div>
                    <div class="class-skills-class">
                        <?= htmlspecialchars($skill['class']); ?>
                    </div>
                    <div class="class-skills-name">
                        <?= htmlspecialchars($skill['name']) ?>
                    </div>
                    <div class="class-skills-max-sl">
                        <?= htmlspecialchars($skill['max-sl']) ?>
                    </div>
                    <div class="class-skills-pin">
                        <button class="class-skills-pin"
                                type="button"
                                aria-checked="false">
                            🖈
                        </button>
                    </div>
                </div>
                <div class="class-skills-description-container hidden" id="<?=$id ?>">
                    <div class="class-skills-description"><?= $skill['description'] ?></div>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endforeach; ?>
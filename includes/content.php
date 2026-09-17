<section class="content-container skills-container">
    <div class="skill-list-header">
        <div></div>
        <div>Source</div>
        <div>Name</div>
        <div>Requirements</div>
        <div>Summary</div>
        <div></div>
    </div>
    <?php
        $json_files = scandir(DATA_PATH . '/heroic_skills/');
        foreach($json_files as $file):
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json'):
                $json_data = file_get_contents(DATA_PATH . '/heroic_skills/' . $file);
                $file = rtrim($file, ".json");
                $data = json_decode($json_data, true);
                $source = $data['source'] ?? [];
                $skills = $data['skills'] ?? [];
                foreach ($skills as $id => $skill):
                    $tags = "source-" . $source['name'] . ', ' . htmlspecialchars(implode(',', $skill['tags']));
                    $id = "heroic-skill-description-" . $file . "-" . $id + 1;
                ?>
                <article class="skill" data-tags="<?=$tags ?>">
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
                        <div class="skill-pin">
                            <button class="heroic-skill-pin"
                                    type="button"
                                    aria-checked="false">
                                🖈
                            </button>
                        </div>
                    </div>
                    <div class="skill-description-container hidden" id="<?=$id ?>">
                        <?php if(isset($skill['additional_requirements'])): ?>
                            <div class="skill-additional-requirement">
                                <strong>Additional Requirements: </strong>
                                <span><?= htmlspecialchars($skill['additional_requirements']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="skill-description"><?= $skill['description'] ?></div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
</section>
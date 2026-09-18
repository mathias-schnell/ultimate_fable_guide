<div id="heroic-skills-container" class="heroic-skills-container">
    <div class="heroic-skills-list-header">
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
                $skills = $data['heroic-skills'] ?? [];
                foreach ($skills as $id => $skill):
                    $tags = "source-" . $source['name'] . ', ' . htmlspecialchars(implode(',', $skill['tags']));
                    $id = "heroic-skills-description-" . $file . "-" . $id + 1;
                ?>
                <article class="heroic-skills" data-tags="<?=$tags ?>">
                    <div class="heroic-skills-row">
                        <div class="heroic-skills-expand">
                            <button class="heroic-skills-toggle"
                                    type="button"
                                    aria-expanded="false"
                                    aria-controls="<?=$id ?>"> 
                                ▶
                            </button>
                        </div>
                        <div class="heroic-skills-source">
                            <?= htmlspecialchars($source['label']) ?>
                        </div>
                        <div class="heroic-skills-name">
                            <?= htmlspecialchars($skill['name']) ?>
                        </div>
                        <div class="heroic-skills-requirements">
                            <?php if (empty($skill['classes'])): ?>
                                <span>-</span>
                            <?php else: ?>
                                <span class="class-tag">
                                    <?= htmlspecialchars(implode(', ', $skill['classes'])) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="heroic-skills-summary">
                            <?= htmlspecialchars($skill['summary']) ?>
                        </div>
                        <div class="heroic-skills-pin">
                            <button class="heroic-skills-pin"
                                    type="button"
                                    aria-checked="false">
                                🖈
                            </button>
                        </div>
                    </div>
                    <div class="heroic-skills-description-container hidden" id="<?=$id ?>">
                        <?php if(isset($skill['additional_requirements'])): ?>
                            <div class="heroic-skills-additional-requirement">
                                <strong>Additional Requirements: </strong>
                                <span><?= htmlspecialchars($skill['additional_requirements']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="heroic-skills-description"><?= $skill['description'] ?></div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
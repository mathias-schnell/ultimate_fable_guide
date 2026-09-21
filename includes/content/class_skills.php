<div class="<?=$html_key ?>-list-header">
    <div></div>
    <div>Source</div>
    <div>Class</div>
    <div>Name</div>
    <div>Max SL</div>
    <div></div>
</div>
<?php
    $json_files = array_slice(scandir(DATA_PATH . "/{$key}"), 2);
    foreach($json_files as $file):
        if (pathinfo($file, PATHINFO_EXTENSION) === 'json'):
            $json_data = file_get_contents(DATA_PATH . "/{$key}/{$file}");
            $data = json_decode($json_data, true);
            $source = $data['source'] ?? [];
            $entries = $data[$html_key] ?? [];
            foreach ($entries as $id => $entry):
                $tags = "source-" . $source['name'] . ', ' . htmlspecialchars(implode(',', $entry['tags']));
                $id = "{$html_key}-description-{$source['name']}-" . $id + 1;
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
                    <div class="<?=$html_key ?>-source">
                        <?= htmlspecialchars($source['label']) ?>
                    </div>
                    <div class="<?=$html_key ?>-class">
                        <?= htmlspecialchars($entry['class']); ?>
                    </div>
                    <div class="<?=$html_key ?>-name">
                        <?= htmlspecialchars($entry['name']) ?>
                    </div>
                    <div class="<?=$html_key ?>-max-sl">
                        <?= htmlspecialchars($entry['max-sl']) ?>
                    </div>
                    <div class="<?=$html_key ?>-pin">
                        <button class="<?=$html_key ?>-pin"
                                type="button"
                                aria-checked="false">
                            🖈
                        </button>
                    </div>
                </div>
                <div class="<?=$html_key ?>-description-container hidden" id="<?=$id ?>">
                    <div class="<?=$html_key ?>-description"><?= $entry['description'] ?></div>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endforeach; ?>
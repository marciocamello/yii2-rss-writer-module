<?php
/**
 * @link https://github.com/himiklab/yii2-rss-writer-module
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT
 *
 * @var yii\web\View $this
 * @var array $channel
 * @var array $items
 */
?>
<?= '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL ?>
<rss version="2.0">
    <channel>
        <?php foreach ($channel as $tag => $value) {
            if ($tag == 'pubDate' || $tag == 'lastBuildDate') {
                echo !empty($value) ? "<$tag>$value</$tag>" . PHP_EOL : null;
            } else {
                echo !empty($value) ? "<$tag><![CDATA[$value]]></$tag>" . PHP_EOL : null;
            }
        }; ?>
        <?php foreach ($items as $itemTags): ?>
            <item>
                <?php foreach ($itemTags as $tag => $value) {
                    if ($tag == 'pubDate') {
                        echo !empty($value) ? "<$tag>$value</$tag>" . PHP_EOL : null;
                    } else {
                        echo !empty($value) ? "<$tag><![CDATA[$value]]></$tag>" . PHP_EOL : null;
                    }
                }; ?>
            </item>
        <?php endforeach; ?>
    </channel>
</rss>

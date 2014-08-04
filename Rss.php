<?php
/**
 * @link https://github.com/himiklab/yii2-rss-writer-module
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT
 */

namespace himiklab\rss;

use Yii;
use yii\base\Module;
use yii\base\InvalidConfigException;
use himiklab\rss\Models\Feed;

/**
 * Yii2 module for automatically generation RSS 2.0 feeds.
 *
 * @author HimikLab
 * @package himiklab\rss
 */
class Rss extends Module
{
    public $controllerNamespace = 'himiklab\rss\controllers';

    /** @var string $cacheKeyPrefix */
    public $cacheKeyPrefix = 'rss';

    /** @var int $cacheExpire */
    public $cacheExpire = 86400;

    /** @var int $maxItemsInFeed Maximum number of elements in single feed */
    public $maxItemsInFeed = 2;

    /**
     * @var array $feeds List of IDs and tags which describe the feeds.
     * Tags `title`, `description`, `link` is required.
     * @see http://cyber.law.harvard.edu/rss/rss.html
     *
     * For example,
     *
     * ```php
     * ['rss.xml' => [
     *      'title' => 'feed title',
     *      'description' => 'feed description',
     *      'link' => 'http://your.site.com/',
     *      'language' => 'en-US'
     * ]]
     * ```
     */
    public $feeds = [];

    /**
     * Creates a new item model. Available properties(tags):
     * `title`, `description`, `link`, `content`, `author`, `pubDate`.
     * Value of `pubDate` should be in unix timestamp format.
     * @return models\Item
     */
    public function createNewItem()
    {
        return new models\Item();
    }

    /**
     * Adds a new item to the specified feed.
     * @param string $feedId
     * @param models\Item $item
     * @return boolean False if the addition is unsuccessful for some reason.
     * @throws \yii\base\InvalidConfigException
     */
    public function addItemToFeed($feedId, models\Item $item)
    {
        if (empty($this->feeds[$feedId])) {
            throw new InvalidConfigException("Feed `$feedId` don`t set in `feeds` property.");
        }

        if (!$item->validate()) {
            $errorMessage = '';
            foreach ($item->errors as $error) {
                $errorMessage = implode(PHP_EOL, $error);
            }
            throw new InvalidConfigException("RSS module: $errorMessage");
        }

        Yii::$app->cache->delete("{$this->cacheKeyPrefix}-{$feedId}");
        $model = new Feed;
        $model->setAttributes($item->attributes, false);
        $model->feed_id = $feedId;
        return $model->save();
    }

    /**
     * Removes items from the specified feed and the specified conditions.
     * @param string $feedId
     * @param array $conditions The conditions that will be put in the WHERE part of the DELETE SQL.
     * Please refer to yii\db\ActiveRecord::where() on how to specify this parameter.
     * @return integer|boolean The number of items deleted, or false if the deletion is unsuccessful for some reason.
     * @throws \Exception in case delete failed.
     */
    public function deleteItems($feedId, array $conditions)
    {
        $conditions = array_merge($conditions, ['feed_id' => $feedId]);
        if ($deletedItems = Feed::deleteAll($conditions)) {
            Yii::$app->cache->delete("{$this->cacheKeyPrefix}-{$feedId}");
            return $deletedItems;
        } else {
            return false;
        }
    }
}

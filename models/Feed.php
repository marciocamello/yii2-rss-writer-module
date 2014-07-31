<?php
/**
 * @link https://github.com/himiklab/yii2-rss-writer-module
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT
 */

namespace himiklab\rss\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property string $feed_id
 * @property string $title
 * @property string $description
 * @property string $link
 * @property string $content
 * @property string $author
 * @property string $pubDate
 */
class Feed extends ActiveRecord
{
    const MODULE_NAME = 'rss';

    /** @var \himiklab\rss\Rss $module */
    protected $module;

    public static function tableName()
    {
        return '{{%_rss_feed}}';
    }

    public function init()
    {
        parent::init();
        $this->module = Yii::$app->getModule(self::MODULE_NAME);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // date is stored and displayed in the DATE_RSS format
            $this->pubDate = !empty($this->pubDate) ? date(DATE_RSS, $this->pubDate) : '';

            $maxItemsInFeed = $this->module->maxItemsInFeed;
            if ($insert && $maxItemsInFeed != 0) {
                $itemsCount = $this::find()
                    ->where(['feed_id' => $this->feed_id])
                    ->count();

                if ($itemsCount >= $maxItemsInFeed) {
                    $oldItems = $this::find()
                        ->where(['feed_id' => $this->feed_id])
                        ->orderBy(['id' => SORT_ASC])
                        ->limit($itemsCount - $maxItemsInFeed + 1)
                        ->all();

                    foreach ($oldItems as $oldItem) {
                        /** @var self $oldItem */
                        $oldItem->delete();
                    }
                }
            }
            return true;
        }
        return false;
    }
}

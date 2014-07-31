<?php
/**
 * @link https://github.com/himiklab/yii2-rss-writer-module
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT
 */

namespace himiklab\rss\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use himiklab\rss\models\Feed;

class DefaultController extends Controller
{
    public function actionIndex($id = 0)
    {
        /** @var \himiklab\rss\Rss $module */
        $module = $this->module;
        if (empty($module->feeds[$id])) {
            throw new NotFoundHttpException("RSS feed not found.");
        }

        header('Content-type: text/xml');
        if ($view = Yii::$app->cache->get("{$module->cacheKeyPrefix}-{$id}")) {
            echo $view;
            return;
        }

        $feedItems = Feed::find()
            ->select('title, description, link, content, author, pubDate')
            ->where(['feed_id' => $id])
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        echo $view = $this->renderPartial('index', [
            'channel' => $module->feeds[$id],
            'items' => $feedItems
        ]);
        Yii::$app->cache->set("{$module->cacheKeyPrefix}-{$id}", $view, $module->cacheExpire);
    }
}

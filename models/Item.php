<?php
/**
 * @link https://github.com/himiklab/yii2-rss-writer-module
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT
 */

namespace himiklab\rss\models;

use yii\base\Model;

class Item extends Model
{
    public $title;
    public $description;
    public $link;
    public $content = '';
    public $author = '';
    public $pubDate = '';

    public function rules()
    {
        return [
            [['title', 'description', 'link'], 'required'],
            [['description', 'content'], 'string'],
            [['title', 'link', 'author'], 'string', 'max' => 255],
            [['pubDate'], 'integer'],
        ];
    }
}

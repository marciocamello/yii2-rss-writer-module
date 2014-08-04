RSS Generator Module for Yii2
========================
Yii2 module for automatically generation RSS 2.0 feeds.

Main features:
* automatic caching of rss feeds
* unlimited number of rss feeds
* flexible module configuration

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

* Either run

```
php composer.phar require "himiklab/yii2-rss-writer-module" "*"
```
or add

```json
"himiklab/yii2-rss-writer-module" : "*"
```

to the require section of your application's `composer.json` file.

* Add a new table to your database.

```sql
CREATE TABLE `rss_feed` (
    `id` mediumint(9) NOT NULL,
    `feed_id` varchar(40) NOT NULL,
    `title` varchar(255) NOT NULL,
    `description` text NOT NULL,
    `link` varchar(255) NOT NULL,
    `content` mediumtext NOT NULL,
    `author` varchar(255) NOT NULL DEFAULT '',
    `pubDate` varchar(40) NOT NULL DEFAULT ''
);
```

* Configure the `cache` component of your application's configuration file, for example:

```php
'components' => [
    ...
    'cache' => [
        'class' => 'yii\caching\FileCache',
    ],
    ...
]
```

* Add a new module in `modules` section of your application's configuration file.

```php
'modules' => [
    ...
    'rss' => [
        'class' => 'himiklab\rss\Rss',
        'feeds' => [
            'rss.xml' => [
                'title' => 'feed title',
                'description' => 'feed description',
                'link' => 'http://your.site.com/',
                'language' => 'en-US'
            ],
        ]
    ],
    ...
],
```

* Add a new rule for `urlManager` of your application's configuration file.

```php
'urlManager' => [
    'rules' => [
        '/<id:rss.xml>' => 'rss/default/index',
        ...
    ],
    ...
],
```

* Add a new `<link>` tag to your `<head>` tag.

```html
<link rel="alternate" type="application/rss+xml" title="RSS feed" href="/rss.xml" />
```

Usage
-----
For example:

```php
...
public function beforeSave($insert)
{
    if (parent::beforeSave($insert)) {
        if ($insert) {
            $rss = Yii::$app->getModule('rss');
            $rssItem = $rss->createNewItem();

            $rssItem->title = $this->title;
            $rssItem->description = $this->description;
            $rssItem->link = Url::to($this->url, true);
            $rssItem->pubDate = time();

            return $rss->addItemToFeed('rss.xml', $rssItem);
        }
        return true;
    }
    return false;
}

public function afterDelete()
{
    parent::afterDelete();
    $rss = Yii::$app->getModule('rss');
    
    $rss->deleteItems('rss.xml', ['link' => Url::to($this->url, true)]);
}
```

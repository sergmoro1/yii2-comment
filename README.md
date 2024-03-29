Yii2 module for comment management
==================================

Advantages
----------

Backend comment management. Ready to use widgets for frontend. Used with sergmoro1/yii2-blog-tools module.

* reply to the comment;
* approve comment or send it to archive;
* email notification about new comment.

Installation
------------

The preferred way to install this extension is through composer.

Either run

`composer require --prefer-dist sergmoro1/yii2-comment`

or add

`"sergmoro1/yii2-comment": "~1.0"`

to the require section of your composer.json.

Run migration:
```
php yii migrate --migrationPath=@vendor/sergmoro1/yii2-comment/src/migrations
```

Recomendation
-------------

Use this module in addition to [sergmoro1/yii2-blog-tools](https://github.com/sergmoro1/yii2-blog-tools) module.
Especially take a look `common/models/Comment.php` after `initblog`.

Usage
-----

Set up in `backend/config/main.php` or `common/config/main.php`.

```php
return [
    ...
    'bootstrap' => [
        'comment',
    ],
    'modules' => [
        'lookup' ==> ['class' ==> 'sergmoro1\lookup\Module'],
        'comment' ==> ['class' ==> 'sergmoro1\comment\Module'],
    ],
    ...
```

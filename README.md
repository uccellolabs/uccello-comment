Document Designer is a [Laravel/Ucello](https://github.com/uccellolabs/uccello) Package allowing to add a comment widget linked to any Uccello entity.

## Features

- Comments linked to any Uccello entity
- Create / Edit / Delete comments by users
- Reply to any first level comments


## Installation

#### Package
```bash
composer require uccello/comment
```

### Publish
After installation execute the following :
```bash
php artisan vendor:publish --provider="Uccello\Comment\Providers\AppServiceProvider"
```

#### Use

To add a comment widget add a migration that will execute the following lines :
```php
$widget = Widget::where('label', 'widget.comments')->first();
$module->widgets()->attach($widget->id, ['data' => json_encode(['title' => 'Comments']), 'sequence' => 0]);
```
Note that filling the data field isn't mandatory.

#### Config

You can add the folowing optional setings to your uccello.php config file:
```php
'comment' => [
        'max_height' => 450,
        'show_child' => true,
        'can_edit_parent' => true,
        'can_delete_parent' => false,
        'order_desc' => true,
]
```

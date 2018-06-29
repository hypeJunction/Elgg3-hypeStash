hypeStash
=========
![Elgg 3.0](https://img.shields.io/badge/Elgg-3.0.x-orange.svg?style=flat-square)

API for caching common entity data to reduce DB queries

* Caches entity likes count
* Caches entity comments count
* Caches last comment
* Caches user friends count
* Caches group members count

### Developer Notes

#### Logic

The plugin uses preloader classes to load values from the database on first request.
The value is cached and returned on consequent calls. ``Preloader::up()`` can be
used to define when the cached value should be reset. For example, the value of likes
is constant until a new like annotation is created, or an old is deleted, so we
register our reset function for those events.

#### Helpers

You can use helper functions to retrieve counts using caching framework. 
All available shortcut functions can be found in ``/lib/functions.php``

```php
elgg_get_total_likes($entity);
elgg_get_total_comments($entity);
// etc.
``` 

#### Custom Properties
```php
$stash = \hypeJunction\Stash\Stash::instance();

// Register a new cacheable property
$stash->register(new CustomProperty()); // Custom property must implement Preloader interface

// Get property value
$prop = $stash->get(CustomProperty::PROPERTY, $entity);
```

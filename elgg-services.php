<?php

return [
	'db.stash.cache' => \DI\create(\Elgg\Cache\CompositeCache::class)
		->constructor(
			'stash.cache',
			\DI\get('config'),
			\Elgg\Cache\CompositeCache::CACHE_PERSISTENT | \Elgg\Cache\CompositeCache::CACHE_FILESYSTEM
		),

	'db.stash' => \DI\create(\hypeJunction\Stash\Stash::class)
		->constructor(
			\DI\get('db'),
			\DI\get('db.stash.cache'),
			\DI\get('events')
		),
];

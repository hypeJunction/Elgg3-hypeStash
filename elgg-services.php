<?php

return [
	'db.stash.cache' => \DI\object(\Elgg\Cache\CompositeCache::class)
		->constructor(
			'stash.cache',
			\DI\get('config'),
			ELGG_CACHE_PERSISTENT | ELGG_CACHE_FILESYSTEM
		),

	'db.stash' => \DI\object(\hypeJunction\Stash\Stash::class)
		->constructor(
			\DI\get('db'),
			\DI\get('db.stash.cache'),
			\DI\get('events'),
			\DI\get('hooks')
		),
];
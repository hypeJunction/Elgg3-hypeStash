<?php

require_once __DIR__ . '/lib/functions.php';

return [
	'plugin' => [
		'name' => 'hypeStash',
		'description' => 'API for caching common counters to reduce DB queries',
		'version' => '5.0.0',
	],
	'bootstrap' => \hypeJunction\Stash\Bootstrap::class,
];

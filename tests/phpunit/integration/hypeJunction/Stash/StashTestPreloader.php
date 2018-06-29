<?php

namespace hypeJunction\Stash;

use Elgg\EventsService;
use Elgg\PluginHooksService;
use ElggEntity;

class StashTestPreloader implements Preloader {

	const PROPERTY = 'preloader_test';

	public function getId() {
		return self::PROPERTY;
	}

	public function getPriority() {
		return 500;
	}

	public function up(Stash $stash, EventsService $events, PluginHooksService $hooks) {

	}

	public function preload(ElggEntity $entity) {
		return 5;
	}
}
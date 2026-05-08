<?php

namespace hypeJunction\Stash;

use Elgg\EventsService;
use ElggEntity;

class StashTestPreloader implements Preloader {

	const PROPERTY = 'preloader_test';

	public function getId() {
		return self::PROPERTY;
	}

	public function getPriority() {
		return 500;
	}

	public function up(Stash $stash, EventsService $events) {

	}

	public function preload(ElggEntity $entity) {
		return 5;
	}
}

<?php

namespace hypeJunction\Stash;

use Elgg\EventsService;
use ElggEntity;

class StashTestPreloader implements Preloader {

	const PROPERTY = 'preloader_test';

	/**
     * @return mixed
     */
    public function getId() {
		return self::PROPERTY;
	}

	/**
     * @return mixed
     */
    public function getPriority() {
		return 500;
	}

	/**
     * @param Stash $stash
     * @param EventsService $events
     */
    public function up(Stash $stash, EventsService $events) {

	}

	/**
     * @param ElggEntity $entity
     * @return mixed
     */
    public function preload(ElggEntity $entity) {
		return 5;
	}
}
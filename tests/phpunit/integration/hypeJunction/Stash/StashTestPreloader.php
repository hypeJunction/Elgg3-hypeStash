<?php

namespace hypeJunction\Stash;

use Elgg\EventsService;
use Elgg\PluginHooksService;
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
     * @param PluginHooksService $hooks
     */
    public function up(Stash $stash, EventsService $events, PluginHooksService $hooks) {

	}

	/**
     * @param ElggEntity $entity
     * @return mixed
     */
    public function preload(ElggEntity $entity) {
		return 5;
	}
}
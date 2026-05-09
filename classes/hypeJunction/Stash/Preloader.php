<?php

namespace hypeJunction\Stash;

use Elgg\Collections\CollectionItemInterface;
use Elgg\EventsService;
use ElggEntity;

interface Preloader extends CollectionItemInterface {

	/**
	 * Initialize a preloader
	 * Register flushing logic
	 *
	 * @param Stash         $stash  Stashing service
	 * @param EventsService $events Events service
	 *
	 * @return void
	 */
	public function up(Stash $stash, EventsService $events);

	/**
	 * Preload a property value from database
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return mixed
	 */
	public function preload(ElggEntity $entity);
}
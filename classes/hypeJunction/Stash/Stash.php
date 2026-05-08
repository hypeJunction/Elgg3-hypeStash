<?php

namespace hypeJunction\Stash;

use Elgg\Application\Database;
use Elgg\Cache\BaseCache;
use Elgg\Traits\Cacheable;
use Elgg\Collections\Collection;
use Elgg\Traits\Di\ServiceFacade;
use Elgg\EventsService;
use Elgg\Traits\Loggable;
use ElggEntity;

/**
 * Stash class.
 */
class Stash {

	use ServiceFacade;
	use Loggable;
	use Cacheable;

	/**
	 * @var Collection|Preloader[]
	 */
	protected $props;

	/**
	 * @var Database
	 */
	protected $db;

	/**
	 * @var EventsService
	 */
	protected $events;

	/**
	 * Constructor
	 *
	 * @param Database      $db     Database
	 * @param BaseCache     $cache  Cache
	 * @param EventsService $events Events service
	 */
	public function __construct(
		Database $db,
		BaseCache $cache,
		EventsService $events
	) {
		$this->db = $db;
		$this->cache = $cache;
		$this->events = $events;

		$this->props = new Collection([], Preloader::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function name() {
		return 'db.stash';
	}

	/**
	 * Flush the cache
	 * @return void
	 */
	public function flushCache() {
		$this->cache->clear();
	}

	/**
	 * Register a property preloader
	 *
	 * @param Preloader $preloader Preloader
	 *
	 * @return void
	 */
	public function register(Preloader $preloader) {
		$this->props->add($preloader);

		$preloader->up($this, $this->events);
	}

	/**
	 * Get property value for an entity
	 *
	 * @param string     $prop   Prop name
	 * @param ElggEntity $entity Entity
	 * @param bool       $reload Force reload from the database
	 *
	 * @return mixed
	 */
	public function get($prop, ElggEntity $entity, $reload = false) {
		$class = elgg_extract($prop, $this->props);

		$key = "{$entity->guid}:$prop";

		$value = $this->cache->load($key);

		if ($value === null || $reload) {
			$preloader = new $class();
			/* @var $preloader Preloader */

			$value = $preloader->preload($entity);

			$this->cache->save($key, $value);
		}

		return $value;
	}

	/**
	 * Reset property in order to load the value from the DB on next request
	 *
	 * @param string     $prop   Prop name
	 * @param ElggEntity $entity Entity
	 *
	 * @return void
	 */
	public function reset($prop, ElggEntity $entity) {
		$key = "{$entity->guid}:$prop";

		$this->cache->delete($key);
	}

	/**
	 * Reset all cached props for entity
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return void
	 */
	public function resetAll(ElggEntity $entity) {
		foreach ($this->props as $prop => $preloader) {
			$this->reset($prop, $entity);
		}
	}
}

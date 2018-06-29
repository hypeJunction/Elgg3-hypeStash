<?php

namespace hypeJunction\Stash;

use Elgg\Application\Database;
use Elgg\Cacheable;
use Elgg\Collections\Collection;
use Elgg\Di\ServiceFacade;
use Elgg\EventsService;
use Elgg\Loggable;
use Elgg\PluginHooksService;
use ElggCache;
use ElggEntity;

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
	 * @var PluginHooksService
	 */
	protected $hooks;

	/**
	 * Constructor
	 *
	 * @param Database           $db     Database
	 * @param ElggCache          $cache  Cache
	 * @param EventsService      $events Events service
	 * @param PluginHooksService $hooks  Hooks service
	 */
	public function __construct(
		Database $db,
		ElggCache $cache,
		EventsService $events,
		PluginHooksService $hooks
	) {
		$this->db = $db;
		$this->cache = $cache;
		$this->events = $events;
		$this->hooks = $hooks;

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

		$preloader->up($this, $this->events, $this->hooks);
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

		if (null === $value || $reload) {
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
	 * @note Not all events are triggered after the DB operation is performed,
	 *       so we can't always use get() with forced reload, because properties
	 *       might not have been propagated yet
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
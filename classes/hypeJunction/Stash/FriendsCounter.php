<?php

namespace hypeJunction\Stash;

use Elgg\Event;
use Elgg\EventsService;
use Elgg\PluginHooksService;
use ElggComment;

class FriendsCounter implements Preloader {

	const PROPERTY = 'friends_total';

	/**
	 * {@inheritdoc}
	 */
	public function getId() {
		return self::PROPERTY;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPriority() {
		return 500;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up(Stash $stash, EventsService $events, PluginHooksService $hooks) {
		$callback = function (Event $event) use ($stash) {
			elgg_call(
				ELGG_IGNORE_ACCESS,
				function () use ($event, $stash) {
					$relationship = $event->getObject();
					if (!$relationship instanceof \ElggRelationship) {
						return;
					}

					if ($relationship->getSubtype() !== 'friend') {
						return;
					}

					$entity = get_entity($relationship->guid_one);
					if (!$entity) {
						return;
					}

					/* When relationship before and after events are added to core, use get() */
					$stash->reset(self::PROPERTY, $entity);
				}
			);
		};

		$events->registerHandler('create', 'relationship', $callback);
		$events->registerHandler('delete', 'relationship', $callback);
	}

	/**
	 * {@inheritdoc}
	 */
	public function preload(\ElggEntity $entity) {
		return elgg_call(
			ELGG_IGNORE_ACCESS,
			function () use ($entity) {
				return elgg_get_entities([
					'relationship' => 'friend',
					'relationship_guid' => (int) $entity->guid,
					'count' => true,
				]);
			}
		);
	}
}
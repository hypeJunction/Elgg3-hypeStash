<?php

namespace hypeJunction\Stash;

use Elgg\Event;
use Elgg\EventsService;
use Elgg\PluginHooksService;

class LikesCounter implements Preloader {

	const PROPERTY = 'likes_total';

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
					$annotation = $event->getObject();
					if (!$annotation instanceof \ElggAnnotation) {
						return;
					}

					if ($annotation->name !== 'likes') {
						return;
					}

					$entity = $annotation->getEntity();
					if (!$entity) {
						return;
					}

					/* @todo Once annotation delete:after event exists, use get() with force reload */
					$stash->reset(self::PROPERTY, $entity);
				}
			);
		};

		$events->registerHandler('create:after', 'annotation', $callback);
		$events->registerHandler('delete', 'annotation', $callback);
	}

	/**
	 * {@inheritdoc}
	 */
	public function preload(\ElggEntity $entity) {
		return elgg_call(
			ELGG_IGNORE_ACCESS,
			function () use ($entity) {
				return $entity->countAnnotations('likes');
			}
		);
	}
}
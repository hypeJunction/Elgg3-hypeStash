<?php

namespace hypeJunction\Stash;

use Elgg\Event;
use Elgg\EventsService;
use Elgg\PluginHooksService;
use ElggComment;

class CommentsCounter implements Preloader {

	const PROPERTY = 'comments_total';

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
					$comment = $event->getObject();
					if (!$comment instanceof ElggComment) {
						return;
					}

					if ($comment->getSubtype() !== 'comment') {
						return;
					}

					$entity = $comment->getContainerEntity();
					if (!$entity) {
						return;
					}

					$stash->get(self::PROPERTY, $entity, true);
				}
			);
		};

		$events->registerHandler('create', 'object', $callback);
		$events->registerHandler('delete:after', 'object', $callback);
	}

	/**
	 * {@inheritdoc}
	 */
	public function preload(\ElggEntity $entity) {
		return elgg_call(
			ELGG_IGNORE_ACCESS,
			function () use ($entity) {
				return $entity->countComments();
			}
		);
	}
}
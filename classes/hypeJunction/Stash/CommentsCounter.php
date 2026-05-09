<?php

namespace hypeJunction\Stash;

use Elgg\Event;
use Elgg\EventsService;
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
	public function up(Stash $stash, EventsService $events) {
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
	 *
	 * Counts comments directly via elgg_count_entities instead of
	 * delegating to ElggEntity::countComments(). Elgg 4.1+ wraps
	 * countComments() in a process-global Elgg\Comments\DataService
	 * cache that has no invalidation API — once a count is cached for
	 * a guid, it never refreshes within the same process. The Stash
	 * cache invalidates correctly on create:object/delete:after:object
	 * events, but countComments() would always return the stale
	 * DataService value, defeating the Stash refresh. Querying
	 * directly bypasses DataService entirely.
	 */
	public function preload(\ElggEntity $entity) {
		return elgg_call(
			ELGG_IGNORE_ACCESS,
			function () use ($entity) {
				return elgg_count_entities([
					'type' => 'object',
					'subtype' => 'comment',
					'container_guid' => $entity->guid,
					'distinct' => false,
				]);
			}
		);
	}
}
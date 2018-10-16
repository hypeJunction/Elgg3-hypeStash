<?php

namespace hypeJunction\Stash;

use Elgg\Database\Annotations;
use Elgg\Event;
use Elgg\Includer;
use Elgg\PluginBootstrap;

class Bootstrap extends PluginBootstrap {

	/**
	 * Get plugin root
	 * @return string
	 */
	protected function getRoot() {
		return $this->plugin->getPath();
	}

	/**
	 * {@inheritdoc}
	 */
	public function load() {
		Includer::requireFileOnce($this->getRoot() . '/autoloader.php');
		Includer::requireFileOnce($this->getRoot() . '/lib/functions.php');
	}

	/**
	 * {@inheritdoc}
	 */
	public function boot() {
		elgg_register_event_handler('cache:flush', 'system', function () {
			Stash::instance()->flushCache();
		});

		elgg_register_event_handler('delete', 'all', function(Event $event) {
			$entity = $event->getObject();
			if (!$entity instanceof \ElggEntity) {
				return;
			}

			Stash::instance()->resetAll($entity);
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public function init() {
		Stash::instance()->register(new LikesCounter());
		Stash::instance()->register(new CommentsCounter());
		Stash::instance()->register(new LastComment());
		Stash::instance()->register(new FriendsCounter());
		Stash::instance()->register(new MembersCounter());
	}

	/**
	 * {@inheritdoc}
	 */
	public function ready() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function shutdown() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function activate() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function deactivate() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function upgrade() {

	}

}
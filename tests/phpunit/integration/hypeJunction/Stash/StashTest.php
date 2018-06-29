<?php

namespace hypeJunction\Stash;

use Elgg\IntegrationTestCase;

/**
 * @group hypeJunction
 * @group Stash
 */
class StashTest extends IntegrationTestCase {

	public function up() {
		require_once __DIR__ . '/StashTestPreloader.php';
	}

	public function down() {

	}

	public function testCanRegisterPreloader() {
		$object = $this->createObject();

		Stash::instance()->register(new StashTestPreloader());

		$cache = Stash::instance()->getCache();

		$cache_key = "{$object->guid}:preloader_test";

		$cached_value = $cache->load($cache_key);
		$this->assertNull($cached_value);

		$value = Stash::instance()->get('preloader_test', $object);
		$this->assertEquals(5, $value);

		$cached_value = $cache->load($cache_key);
		$this->assertEquals(5, $cached_value);

		elgg_call(ELGG_IGNORE_ACCESS, function() use ($object) {
			$object->delete();
		});

		$cached_value = $cache->load($cache_key);
		$this->assertNull($cached_value);
	}
}


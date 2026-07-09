<?php

namespace hypeJunction\Stash;

use Elgg\Cache\BaseCache;
use Elgg\Cache\CompositeCache;
use Elgg\IntegrationTestCase;

/**
 * One regression guard per Elgg 7.x migration fix landed on hypestash.
 *
 * Each test asserts the FIXED behavior — i.e. that the 7.x breakage the
 * commit repaired stays repaired. Behavioral counter invalidation
 * (likes/comments/friends/members) is covered by the sibling *Test.php
 * files; this file guards the plumbing those tests ride on.
 *
 * @group hypeJunction
 * @group Stash
 */
class MigrationFixesTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypestash';
	}

	public function up() {}

	public function down() {}

	private function pluginRoot(): string {
		return dirname(__DIR__, 5);
	}

	/**
	 * 2438f2f — the Stash ctor param was retyped from the removed \ElggCache
	 * to \Elgg\Cache\BaseCache. If the old type sneaks back the container
	 * cannot construct 'db.stash' on 7.x (ElggCache no longer exists).
	 */
	public function testStashConstructorAcceptsBaseCache() {
		$param = (new \ReflectionMethod(Stash::class, '__construct'))->getParameters()[1];

		$type = $param->getType();
		$this->assertInstanceOf(\ReflectionNamedType::class, $type);
		$this->assertSame(BaseCache::class, $type->getName());
	}

	/**
	 * 2438f2f — dropping the removed \Elgg\Traits\Cacheable trait means the
	 * cache is now injected explicitly. Prove the live Stash singleton holds
	 * the same CompositeCache the DI container binds as 'db.stash.cache'
	 * (the accessor path StashTest must use now that getCache() is gone).
	 */
	public function testStashCacheIsInjectedCompositeCacheService() {
		$service = elgg()->{'db.stash.cache'};
		$this->assertInstanceOf(CompositeCache::class, $service);

		$prop = new \ReflectionProperty(Stash::class, 'cache');
		$prop->setAccessible(true);
		$injected = $prop->getValue(Stash::instance());

		$this->assertInstanceOf(CompositeCache::class, $injected);
		$this->assertSame($service, $injected);
	}

	/**
	 * d147587 — CommentsCounter::preload must NOT delegate to
	 * ElggEntity::countComments() (Elgg 4.1+ caches that in a process-global
	 * DataService with no invalidation, defeating Stash). It must query
	 * elgg_count_entities directly.
	 */
	public function testCommentsCounterBypassesCountCommentsDataService() {
		$src = file_get_contents($this->pluginRoot() . '/classes/hypeJunction/Stash/CommentsCounter.php');

		$this->assertStringContainsString('elgg_count_entities', $src);
		$this->assertStringNotContainsString('->countComments(', $src);
	}

	/**
	 * 6b23686 — elgg-services.php must use \DI\create (the removed \DI\object
	 * DI helper fatals when the container compiles on 4.x+).
	 */
	public function testElggServicesUsesDiCreateNotDiObject() {
		$src = file_get_contents($this->pluginRoot() . '/elgg-services.php');

		$this->assertStringContainsString('\\DI\\create', $src);
		$this->assertStringNotContainsString('\\DI\\object', $src);
	}

	/**
	 * 489b177 — cache:flush/system must be an EVENT handler (not a plugin hook)
	 * or the stash cache is never dropped on a system cache flush. Prove the
	 * end-to-end wiring: stash a value, fire the event, the key is gone.
	 */
	public function testCacheFlushSystemEventFlushesStashCache() {
		$object = $this->createObject();

		// Populates the '{guid}:likes_total' cache key via the LikesCounter preloader.
		$this->assertSame(0, elgg_get_total_likes($object));

		$prop = new \ReflectionProperty(Stash::class, 'cache');
		$prop->setAccessible(true);
		$cache = $prop->getValue(Stash::instance());

		$key = "{$object->guid}:" . LikesCounter::PROPERTY;
		$this->assertSame(0, $cache->load($key));

		elgg_trigger_event('cache:flush', 'system');

		$this->assertNull($cache->load($key));
	}

	/**
	 * b225072 — composer autoload must be PSR-4 pointing at the class dir.
	 * PSR-0 silently fails under the Elgg 7.x plugin autoloader.
	 */
	public function testComposerAutoloadIsPsr4NotPsr0() {
		$data = json_decode(file_get_contents($this->pluginRoot() . '/composer.json'), true);

		$this->assertArrayHasKey('psr-4', $data['autoload']);
		$this->assertArrayNotHasKey('psr-0', $data['autoload']);
		$this->assertContains('classes/hypeJunction/Stash/', array_values($data['autoload']['psr-4']));
	}

	/**
	 * 3e346e0 — elgg-plugin.php require_once's lib/functions.php at the top so
	 * the git-tracked global helpers exist even before Bootstrap::load runs.
	 * Assert both the source directive and that the helpers are live at boot.
	 */
	public function testGlobalHelpersRequiredFromManifestAndLoaded() {
		$src = file_get_contents($this->pluginRoot() . '/elgg-plugin.php');
		$this->assertStringContainsString("require_once __DIR__ . '/lib/functions.php'", $src);

		$this->assertTrue(function_exists('elgg_get_total_likes'));
		$this->assertTrue(function_exists('elgg_get_total_comments'));
		$this->assertTrue(function_exists('elgg_get_last_comment'));
		$this->assertTrue(function_exists('elgg_get_total_friends'));
		$this->assertTrue(function_exists('elgg_get_total_members'));
	}
}

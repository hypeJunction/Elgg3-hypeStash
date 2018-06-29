<?php

namespace hypeJunction\Stash;

use Elgg\IntegrationTestCase;

/**
 * @group hypeJunction
 * @group Stash
 */
class FriendsTest extends IntegrationTestCase {

	public function up() {

	}

	public function down() {

	}

	public function testFriendsCountIsCacheable() {

		$user = $this->createUser();
		$friend = $this->createUser();

		$total = elgg_get_total_friends($user);
		$this->assertEquals(0, $total);

		$user->addFriend($friend->guid);

		$total = elgg_get_total_friends($user);
		$this->assertEquals(1, $total);

		$rel = check_entity_relationship($user->guid, 'friend', $friend->guid);
		$rel->delete();

		$total = elgg_get_total_friends($user);
		$this->assertEquals(0, $total);
	}
}
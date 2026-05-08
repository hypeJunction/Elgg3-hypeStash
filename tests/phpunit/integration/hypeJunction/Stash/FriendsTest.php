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

		$rels = elgg_get_relationships(['guid_one' => $user->guid, 'relationship' => 'friend', 'guid_two' => $friend->guid, 'limit' => 1]);
		if ($rels) {
			$rels[0]->delete();
		}

		$total = elgg_get_total_friends($user);
		$this->assertEquals(0, $total);
	}
}

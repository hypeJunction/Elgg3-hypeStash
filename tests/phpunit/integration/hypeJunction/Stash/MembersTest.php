<?php

namespace hypeJunction\Stash;

use Elgg\IntegrationTestCase;

/**
 * @group hypeJunction
 * @group Stash
 */
class MembersTest extends IntegrationTestCase {

	public function up() {

	}

	public function down() {

	}

	public function testMembersCountIsCacheable() {

		$group = $this->createGroup();
		$member = $this->createUser();

		$total = elgg_get_total_members($group);
		$this->assertEquals(1, $total);

		$group->join($member);

		$total = elgg_get_total_members($group);
		$this->assertEquals(2, $total);

		$rels = elgg_get_relationships(['guid_one' => $member->guid, 'relationship' => 'member', 'guid_two' => $group->guid, 'limit' => 1]);
		if ($rels) {
			$rels[0]->delete();
		}

		$total = elgg_get_total_members($group);
		$this->assertEquals(1, $total);
	}
}

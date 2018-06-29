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

		$rel = check_entity_relationship($member->guid, 'member', $group->guid);
		$rel->delete();

		$total = elgg_get_total_members($group);
		$this->assertEquals(1, $total);
	}
}
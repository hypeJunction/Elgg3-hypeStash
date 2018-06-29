<?php

namespace hypeJunction\Stash;

use Elgg\IntegrationTestCase;

/**
 * @group hypeJunction
 * @group Stash
 */
class LikesTest extends IntegrationTestCase {

	public function up() {

	}

	public function down() {

	}

	public function testLikesCountIsCacheable() {

		$object = $this->createObject();

		$total = elgg_get_total_likes($object);
		$this->assertEquals(0, $total);

		$annotation = elgg_call(ELGG_IGNORE_ACCESS, function() use ($object) {
			$id = $object->annotate('likes', 1, 0, 7);
			return elgg_get_annotation_from_id($id);
		});

		$total = elgg_get_total_likes($object);
		$this->assertEquals(1, $total);

		elgg_call(ELGG_IGNORE_ACCESS, function() use ($annotation) {
			$annotation->delete();
		});

		$total = elgg_get_total_likes($object);
		$this->assertEquals(0, $total);
	}
}
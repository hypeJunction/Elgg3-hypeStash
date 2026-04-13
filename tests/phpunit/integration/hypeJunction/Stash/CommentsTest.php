<?php

namespace hypeJunction\Stash;

use Elgg\IntegrationTestCase;

/**
 * @group hypeJunction
 * @group Stash
 */
class CommentsTest extends IntegrationTestCase {

	public function up() {

	}

	public function down() {

	}

	public function testCommentsAreCacheable() {

		// Elgg 4.1+ ElggEntity::countComments() short-circuits to 0 when the
		// entity's subtype lacks the 'commentable' capability. createObject()
		// uses faker for the subtype which is non-deterministic — sometimes
		// it picks a commentable subtype (blog, etc.) and the assertion
		// passes, sometimes it doesn't and total stays 0 forever. Force a
		// deterministic commentable subtype for the test.
		_elgg_services()->entity_capabilities->setCapability('object', 'hypestash_test', 'commentable', true);
		$object = $this->createObject(['subtype' => 'hypestash_test']);

		$total = elgg_get_total_comments($object);
		$this->assertEquals(0, $total);

		$this->assertNull(elgg_get_last_comment($object));

		$comment = elgg_call(ELGG_IGNORE_ACCESS, function() use ($object) {
			$comment = new \ElggComment();
			$comment->container_guid = $object->guid;
			$comment->save();

			return $comment;
		});

		$total = elgg_get_total_comments($object);
		$this->assertEquals(1, $total);

		$last_comment = elgg_get_last_comment($object);
		$this->assertInstanceOf(\ElggComment::class, $last_comment);
		$this->assertEquals($comment->guid, $last_comment->guid);

		elgg_call(ELGG_IGNORE_ACCESS, function() use ($comment) {
			$comment->delete();
		});

		$total = elgg_get_total_comments($object);
		$this->assertEquals(0, $total);

		$this->assertNull(elgg_get_last_comment($object));
	}
}
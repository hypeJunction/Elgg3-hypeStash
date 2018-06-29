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

		$object = $this->createObject();

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
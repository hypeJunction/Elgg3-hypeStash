<?php

use hypeJunction\Stash\CommentsCounter;
use hypeJunction\Stash\FriendsCounter;
use hypeJunction\Stash\LastComment;
use hypeJunction\Stash\LikesCounter;
use hypeJunction\Stash\MembersCounter;
use hypeJunction\Stash\Stash;

/**
 * Get total number of likes for entity
 *
 * @param ElggEntity $entity Entity
 *
 * @return int
 */
function elgg_get_total_likes(ElggEntity $entity) {
	return Stash::instance()->get(LikesCounter::PROPERTY, $entity);
}

/**
 * Get total number of comments on entity
 *
 * @param ElggEntity $entity Entity
 *
 * @return int
 */
function elgg_get_total_comments(ElggEntity $entity) {
	return Stash::instance()->get(CommentsCounter::PROPERTY, $entity);
}

/**
 * Get last comment on entity
 *
 * @param ElggEntity $entity Entity
 * @return ElggComment|null
 */
function elgg_get_last_comment(ElggEntity $entity) {
	return Stash::instance()->get(LastComment::PROPERTY, $entity);
}

/**
 * Get total number of friends
 *
 * @param ElggEntity $entity Entity
 *
 * @return int
 */
function elgg_get_total_friends(ElggEntity $entity) {
	return Stash::instance()->get(FriendsCounter::PROPERTY, $entity);
}

/**
 * Get total number of members
 *
 * @param ElggEntity $entity Entity
 *
 * @return int
 */
function elgg_get_total_members(ElggEntity $entity) {
	return Stash::instance()->get(MembersCounter::PROPERTY, $entity);
}
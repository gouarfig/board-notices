<?php

/**
 *
 * Board Notices Manager
 *
 * @version 1.0.0
 * @copyright (c) Fred Quointeau
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\repository;

interface users_interface
{
	// function getUserInfo($field_name);
	function isUserInGroupId($group_id);
	function nonDeletedUserPosts($in_forums = array());
	function approvedUserPosts($in_forums = array());

	function getAllGroups();
	// function getForumIdFromTopicId($topic_id);
	function getLanguages();
	function getStyles($all = false);
	function getRanks();

	function getForumLastReadTime($user_id, $forum_id);
	function trackLastVisit($user_id, $forum_id);
	function clearLastVisit();

}

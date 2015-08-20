<?php

/**
 *
 * Board Notices Manager
 *
 * @version 1.0.0
 * @copyright (c) 2015 Fred Quointeau
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\dac;

interface datalayer_interface
{
	function getNotices($active_only = true);
	function getAllNotices();
	function getActiveNotices();
	function getNoticeFromId($notice_id);
	function moveNotice($action, $notice_id);
	function moveNoticeFirst($notice_id);
	function moveNoticeLast($notice_id);
	function deleteNotice($notice_id);
	function enableNotice($action, $notice_id);
	function saveNewNotice(&$data);
	function saveNotice($notice_id, &$data);

	function getRulesFor($notice_id);
	function deleteRules($rules);
	function updateRules($rules);
	function insertRules($rules);

	function getUserInfo($field_name);
	function isUserInGroupId($group_id);
	function nonDeletedUserPosts($in_forums = array());
	function approvedUserPosts($in_forums = array());

	function getAllGroups();
	function getForumIdFromTopicId($topic_id);
	function getLanguages();
	function getStyles($all = false);
	function getRanks();

	function getForumLastReadTime($user_id, $forum_id);

}

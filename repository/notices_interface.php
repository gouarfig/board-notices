<?php

namespace fq\boardnotices\repository;

interface notices_interface
{
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

	function setForumVisited($user_id, $forum_id);
	function clearForumVisited();
}

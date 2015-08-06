<?php

namespace fq\boardnotices\tests\mock;

use fq\boardnotices\dac\datalayer_interface;

class datalayer_mock implements datalayer_interface
{
	private $return_values;

	public function __construct($return_values = array())
	{
		$this->return_values = $return_values;
	}

	public function setReturnValueForFunction($function, $value)
	{
		$this->return_values[$function] = $value;
	}

	function getNotices($active_only = true)
	{
		return $this->return_values['getNotices'];
	}

	function getAllNotices()
	{
		return $this->return_values['getAllNotices'];
	}

	function getActiveNotices()
	{
		return $this->return_values['getActiveNotices'];
	}

	function getNoticeFromId($notice_id)
	{
		return $this->return_values['getNoticeFromId'];
	}

	function moveNotice($action, $notice_id)
	{
		return $this->return_values['moveNotice'];
	}

	function moveNoticeFirst($notice_id)
	{
		return $this->return_values['moveNoticeFirst'];
	}

	function moveNoticeLast($notice_id)
	{
		return $this->return_values['moveNoticeLast'];
	}

	function deleteNotice($notice_id)
	{
		return $this->return_values['deleteNotice'];
	}

	function enableNotice($action, $notice_id)
	{
		return $this->return_values['enableNotice'];
	}

	function saveNewNotice(&$data)
	{
		return $this->return_values['saveNewNotice'];
	}

	function saveNotice($notice_id, &$data)
	{
		return $this->return_values['saveNotice'];
	}


	function getRulesFor($notice_id)
	{
		return $this->return_values['getRulesFor'];
	}

	function deleteRules($rules)
	{
		return $this->return_values['deleteRules'];
	}

	function updateRules($rules)
	{
		return $this->return_values['updateRules'];
	}

	function insertRules($rules)
	{
		return $this->return_values['insertRules'];
	}


	function getUserInfo($field_name)
	{
		return $this->return_values['getUserInfo'];
	}

	function isUserInGroupId($group_id)
	{
		return $this->return_values['isUserInGroupId'];
	}


	public function nonDeletedUserPosts()
	{
		return $this->return_values['nonDeletedUserPosts'];
	}


	function getAllGroups()
	{
		return $this->return_values['getAllGroups'];
	}

	function getForumIdFromTopicId($topic_id)
	{
		return $this->return_values['getForumIdFromTopicId'];
	}

	function getLanguages()
	{
		return $this->return_values['getLanguages'];
	}

	function getStyles($all = false)
	{
		return $this->return_values['getStyles'];
	}

	function getRanks()
	{
		return $this->return_values['getRanks'];
	}

}

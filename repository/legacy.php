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

namespace fq\boardnotices\repository;

use fq\boardnotices\repository\legacy_interface;

class legacy implements legacy_interface
{
	private $db;
	private $user;
	private $cache;
	private $config;
	private $notices_table;
	private $notices_rules_table;
	private $notices_seen_table;
	private $forums_visited_table;

	private $notices_loaded = false;
	private $active_notices_loaded = false;
	private $notices = array();
	private $rules_loaded = false;
	private $rules = array();
	private $user_loaded = false;
	private $user_row = array();
	private $usergroups_loaded = false;
	private $usergroups = array();
	private $allgroups_loaded = false;
	private $allgroups = array();
	private $visited_forums = array();

	public function __construct(
			\phpbb\db\driver\driver_interface $db,
			\phpbb\user $user,
			\phpbb\cache\service $cache,
			\phpbb\config\config $config,
			$notices_table,
			$notices_rules_table,
			$notices_seen_table,
			$forums_visited_table)
	{
		$this->db = $db;
		$this->user = $user;
		$this->cache = $cache;
		$this->config = $config;
		$this->notices_table = $notices_table;
		$this->notices_rules_table = $notices_rules_table;
		$this->notices_seen_table = $notices_seen_table;
		$this->forums_visited_table = $forums_visited_table;
	}

	private function loadNotices($active_only = true)
	{
		$notices = array();
		$sql_array = array(
			'SELECT' => 'n.*',
			'FROM' => array($this->notices_table => 'n'),
			'WHERE' => $active_only ? 'n.active=1' : '',
			'ORDER_BY' => 'n.notice_order',
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);

		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$notices[] = $row;
		}
		$this->db->sql_freeresult($result);

		return $notices;
	}

	public function getNotices($active_only = true)
	{
		if (!$this->notices_loaded || ($this->active_notices_loaded != $active_only))
		{
			$this->notices = $this->loadNotices($active_only);
			$this->notices_loaded = true;
			$this->active_notices_loaded = $active_only;
		}
		return $this->notices;
	}

	public function getAllNotices()
	{
		return $this->getNotices(false);
	}

	public function getActiveNotices()
	{
		return $this->getNotices(true);
	}

	public function getNoticeFromId($notice_id)
	{
		$notice = null;
		$load_active_only = false;
		if (!$this->notices_loaded || ($this->active_notices_loaded != $load_active_only))
		{
			$this->notices = $this->loadNotices($load_active_only);
			$this->notices_loaded = true;
			$this->active_notices_loaded = $load_active_only;
		}
		foreach ($this->notices as $row)
		{
			if ($row['notice_id'] == $notice_id)
			{
				$notice = $row;
				break;
			}
		}
		return $notice;
	}

	private function cleanNotices()
	{
		$this->notices_loaded = false;
		$this->cache->destroy('_notices');
		$this->cache->destroy('sql', $this->notices_table);
	}

	public function moveNotice($action, $notice_id)
	{
		// Get current order id...
		$sql = "SELECT notice_order as current_order
			FROM {$this->notices_table}
			WHERE notice_id = $notice_id";
		$result = $this->db->sql_query($sql);
		$current_order = (int) $this->db->sql_fetchfield('current_order');
		$this->db->sql_freeresult($result);

		if ($current_order == 0 && $action == 'move_up')
		{
			return;
		}

		// on move_down, switch position with next order_id...
		// on move_up, switch position with previous order_id...
		$switch_order_id = ($action == 'move_down') ? $current_order + 1 : $current_order - 1;

		//
		$sql = "UPDATE $this->notices_table
			SET notice_order = $current_order
			WHERE notice_order = $switch_order_id
				AND notice_id <> $notice_id";
		$this->db->sql_query($sql);
		$move_executed = (bool) $this->db->sql_affectedrows();

		// Only update the other entry too if the previous entry got updated
		if ($move_executed)
		{
			$sql = "UPDATE $this->notices_table
				SET notice_order = $switch_order_id
				WHERE notice_order = $current_order
					AND notice_id = $notice_id";
			$this->db->sql_query($sql);
		}
		$this->cleanNotices();

		return $move_executed;
	}

	public function moveNoticeFirst($notice_id)
	{
		$move_executed = false;
		$notice = $this->getNoticeFromId($notice_id);
		if (!is_null($notice) && ($notice['notice_order'] > 1))
		{
			$sql = "UPDATE $this->notices_table
				SET notice_order = notice_order +1
				WHERE notice_order < {$notice['notice_order']}";
			$this->db->sql_query($sql);
			$move_executed = ($this->db->sql_affectedrows() > 0) ? true : false;

			if ($move_executed)
			{
				$sql = "UPDATE $this->notices_table
					SET notice_order = 1
					WHERE notice_id = $notice_id";
				$this->db->sql_query($sql);
			}
		}
		return $move_executed;
	}

	public function moveNoticeLast($notice_id)
	{
		$move_executed = false;
		$last_order = $this->getNextNoticeOrder() -1;
		$notice = $this->getNoticeFromId($notice_id);
		if (!is_null($notice) && ($notice['notice_order'] > 0) && ($notice['notice_order'] < $last_order))
		{
			$sql = "UPDATE {$this->notices_table}
				SET notice_order = notice_order -1
				WHERE notice_order > {$notice['notice_order']}";
			$this->db->sql_query($sql);
			$move_executed = ($this->db->sql_affectedrows() > 0) ? true : false;

			if ($move_executed)
			{
				$sql = "UPDATE {$this->notices_table}
					SET notice_order = {$last_order}
					WHERE notice_id = {$notice_id}";
				$this->db->sql_query($sql);
			}
		}
		return $move_executed;
	}

	public function deleteNotice($notice_id)
	{
		$deleted = false;
		$notice = $this->getNoticeFromId($notice_id);
		if (!is_null($notice))
		{
			$sql = "DELETE FROM {$this->notices_seen_table} WHERE notice_id=" . (int) $notice_id;
			$this->db->sql_query($sql);

			$sql = "DELETE FROM {$this->notices_rules_table} WHERE notice_id=" . (int) $notice_id;
			$this->db->sql_query($sql);

			$sql = "DELETE FROM {$this->notices_table} WHERE notice_id=" . (int) $notice_id;
			$this->db->sql_query($sql);
			$deleted = $this->db->sql_affectedrows() ? true : false;

			if ($deleted && ($notice['notice_order'] > 0))
			{
				$sql = "UPDATE {$this->notices_table} SET notice_order=notice_order-1 WHERE notice_order>=" . (int) $notice['notice_order'];
				$this->db->sql_query($sql);
			}
		}
		return $deleted;
	}

	public function enableNotice($action, $notice_id)
	{
		$query_done = 0;
		if ($notice_id > 0)
		{
			$sql = "UPDATE {$this->notices_table}"
					. " SET active=" . (($action == 'enable') ? '1' : '0')
					. " WHERE notice_id={$notice_id}";
			$this->db->sql_query($sql);
			$query_done = (bool) $this->db->sql_affectedrows();

			$this->cleanNotices();
		}
		return $query_done;
	}

	private function getNextNoticeId()
	{
		$next_id = 0;
		$load_active_only = false;
		if (!$this->notices_loaded || ($this->active_notices_loaded != $load_active_only))
		{
			$this->notices = $this->loadNotices($load_active_only);
			$this->notices_loaded = true;
			$this->active_notices_loaded = $load_active_only;
		}
		foreach ($this->notices as $row)
		{
			if ($row['notice_id'] > $next_id)
			{
				$next_id = $row['notice_id'];
			}
		}
		return $next_id + 1;
	}

	private function getNextNoticeOrder()
	{
		$next_order = 0;
		$load_active_only = false;
		if (!$this->notices_loaded || ($this->active_notices_loaded != $load_active_only))
		{
			$this->notices = $this->loadNotices($load_active_only);
			$this->notices_loaded = true;
			$this->active_notices_loaded = $load_active_only;
		}
		foreach ($this->notices as $row)
		{
			if ($row['notice_order'] > $next_order)
			{
				$next_order = $row['notice_order'];
			}
		}
		return $next_order + 1;
	}

	public function saveNewNotice(&$data)
	{
		if (!isset($data['notice_order']))
		{
			$data['notice_order'] = $this->getNextNoticeOrder();
		}
		$data['notice_id'] = $this->getNextNoticeId();
		$sql = "INSERT INTO {$this->notices_table} " . $this->db->sql_build_array('INSERT', $data);
		$this->db->sql_query($sql);
		$this->cleanNotices();

		return $data['notice_id'];
	}

	public function saveNotice($notice_id, &$data)
	{
		$notice_id = (int) $notice_id;
		if ($notice_id > 0)
		{
			unset($data['notice_id']);
			unset($data['notice_order']);

			$sql = "UPDATE {$this->notices_table}
				SET " . $this->db->sql_build_array('UPDATE', $data) . "
				WHERE notice_id = " . $notice_id;
			$this->db->sql_query($sql);
			$this->cleanNotices();
		}
	}

	private function loadRules()
	{
		$rules = array();
		$sql_array = array(
			'SELECT' => 'r.*',
			'FROM' => array($this->notices_rules_table => 'r'),
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);

		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rules[$row['notice_id']][] = $row;
		}
		$this->db->sql_freeresult($result);

		return $rules;
	}

	public function getRulesFor($notice_id)
	{
		if (!$this->rules_loaded)
		{
			$this->rules = $this->loadRules();
			$this->rules_loaded = true;
		}
		return !empty($this->rules[$notice_id]) ? $this->rules[$notice_id] : array();
	}

	private function cleanRules()
	{
		$this->rules_loaded = false;
		$this->cache->destroy('_rules');
		$this->cache->destroy('sql', $this->notices_rules_table);
	}

	public function deleteRules($rules)
	{
		if (!is_array($rules))
		{
			$rules = array($rules);
		}
		$sql = "DELETE FROM " . $this->notices_rules_table . " WHERE notice_rule_id IN (" . implode(',', $rules) . ")";
		$result = $this->db->sql_query($sql);
		$this->cleanRules();

		return $result;
	}

	public function updateRules($rules)
	{
		if (!is_array($rules))
		{
			$rules = array($rules);
		}
		foreach ($rules as $rule)
		{
			$notice_rule_id = $rule['notice_rule_id'];
			unset($rule['notice_rule_id']);

			$sql = "UPDATE {$this->notices_rules_table}
				SET " . $this->db->sql_build_array('UPDATE', $rule) . "
				WHERE notice_rule_id = " . $notice_rule_id;
			$this->db->sql_query($sql);
		}
		$this->cleanRules();
	}

	public function insertRules($rules)
	{
		if (!is_array($rules))
		{
			$rules = array($rules);
		}
		foreach ($rules as $rule)
		{
			$sql = "INSERT INTO {$this->notices_rules_table} " . $this->db->sql_build_array('INSERT', $rule);
			$this->db->sql_query($sql);
		}
		$this->cleanRules();
	}

	private function loadUser()
	{
		$user = array();
		$sql_array = array(
			'SELECT' => 'u.*',
			'FROM' => array(USERS_TABLE => 'u'),
			'WHERE' => 'u.user_id=' . (int) $this->user->data['user_id'],
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);

		$result = $this->db->sql_query($sql);
		$user = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $user;
	}

	public function getUserInfo($field_name)
	{
		if (!$this->user_loaded)
		{
			$this->user_row = $this->loadUser();
			$this->user_loaded = true;
		}
		return isset($this->user_row[$field_name]) ? $this->user_row[$field_name] : null;
	}

	private function loadUserGroups()
	{
		$usergroups = array();
		$sql_array = array(
			'SELECT' => 'g.group_id, g.group_name, g.group_type',
			'FROM' => array(GROUPS_TABLE => 'g', USER_GROUP_TABLE => 'ug'),
			'WHERE' => 'ug.user_id=' . (int) $this->user->data['user_id']
			. ' AND g.group_id = ug.group_id'
			. ' AND ug.user_pending = 0',
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);

		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$usergroups[(int) $row['group_id']] = $row;
		}
		$this->db->sql_freeresult($result);

		return $usergroups;
	}

	public function isUserInGroupId($group_id)
	{
		if (!$this->usergroups_loaded)
		{
			$this->usergroups = $this->loadUserGroups();
			$this->usergroups_loaded = true;
		}
		return isset($this->usergroups[$group_id]) ? true : false;
	}

	private function loadUserPosts($include_waiting_for_approval = false, $in_forums = array())
	{
		$userposts = 0;
		$sql_array = array(
			'SELECT' => 'count(p.post_id) AS count',
			'FROM' => array(POSTS_TABLE => 'p'),
			'WHERE' => 'p.poster_id=' . (int) $this->user->data['user_id']
			. (($include_waiting_for_approval) ? ' AND p.post_visibility < 2' : ' AND p.post_visibility = 1')
			. ((!empty($in_forums)) ? ' AND p.forum_id IN (' . implode(',', $in_forums) . ')' : ''),
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);

		$result = $this->db->sql_query($sql);
		if ($row = $this->db->sql_fetchrow($result))
		{
			$userposts = (int) $row['count'];
		}
		$this->db->sql_freeresult($result);

		return $userposts;
	}

	/**
	 * Number of posts, INCLUDING waiting for approval ones
	 */
	public function nonDeletedUserPosts($in_forums = array())
	{
		return $this->loadUserPosts(true, $in_forums);
	}

	/**
	 * Number of posts, NOT including waiting for approval ones
	 */
	public function approvedUserPosts($in_forums = array())
	{
		return $this->loadUserPosts(false, $in_forums);
	}

	private function loadAllGroups()
	{
		$groups = array();
		$sql_array = array(
			'SELECT' => 'g.group_id, g.group_name, g.group_type',
			'FROM' => array(GROUPS_TABLE => 'g'),
			'WHERE' => (!$this->config['coppa_enable']) ? "group_name <> 'REGISTERED_COPPA'" : '',
			'ORDER_BY' => 'g.group_type DESC, g.group_name ASC',
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);

		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($row['group_type'] == GROUP_SPECIAL)
			{
				$row['group_name'] = $this->user->lang['G_' . $row['group_name']];
			}
			$groups[$row['group_id']] = $row['group_name'];
		}
		$this->db->sql_freeresult($result);

		return $groups;
	}

	public function getAllGroups()
	{
		if (!$this->allgroups_loaded)
		{
			$this->allgroups = $this->loadAllGroups();
			$this->allgroups_loaded = true;
		}
		return $this->allgroups;
	}

	public function getForumIdFromTopicId($topic_id)
	{
		$sql_array = array(
			'SELECT' => 't.forum_id',
			'FROM' => array(TOPICS_TABLE => 't'),
			'WHERE' => 't.topic_id = ' . (int) $topic_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		$forum_id = (int) $db->sql_fetchfield('forum_id');
		$db->sql_freeresult($result);

		return $forum_id;
	}

	function getLanguages()
	{
		$sql = 'SELECT lang_iso, lang_local_name
			FROM ' . LANG_TABLE . '
			ORDER BY lang_english_name';
		$result = $this->db->sql_query($sql);

		$languages = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$languages[$row['lang_iso']] = $row['lang_local_name'];
		}
		$this->db->sql_freeresult($result);

		return $languages;
	}

	function getStyles($all = false)
	{
		$sql_where = (!$all) ? 'WHERE style_active = 1 ' : '';
		$sql = 'SELECT style_id, style_name
			FROM ' . STYLES_TABLE . "
			$sql_where
			ORDER BY style_name";
		$result = $this->db->sql_query($sql);

		$styles = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$styles[$row['style_id']] = $row['style_name'];
		}
		$this->db->sql_freeresult($result);

		return $styles;
	}

	function getRanks()
	{
		$sql = 'SELECT rank_id, rank_title
			FROM ' . RANKS_TABLE . '
			ORDER BY rank_special DESC, rank_min ASC, rank_title ASC';
		$result = $this->db->sql_query($sql);

		$ranks = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$ranks[$row['rank_id']] = $row['rank_title'];
		}
		$this->db->sql_freeresult($result);

		return $ranks;
	}

	private function loadForumLastReadTime($user_id)
	{
		static $forums = array();

		if (!isset($forums[$user_id]))
		{
			$forums[$user_id] = array();
			$sql = 'SELECT forum_id, mark_time'
					. ' FROM ' . FORUMS_TRACK_TABLE
					. ' WHERE user_id=' . (int) $user_id;
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$forums[$user_id][$row['forum_id']] = $row['mark_time'];
			}
			$this->db->sql_freeresult($result);
		}
		return $forums[$user_id];
	}

	function getForumLastReadTime($user_id, $forum_id)
	{
		$forums = $this->loadForumLastReadTime($user_id);
		return (isset($forums[$forum_id]) ? $forums[$forum_id] : null);
	}

	function trackLastVisit($user_id, $forum_id)
	{
		if (isset($this->visited_forums[$user_id]))
		{
			$this->visited_forums[$user_id][$forum_id] = time();
		}
	}

	function clearLastVisit()
	{
	}
}
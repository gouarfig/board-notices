<?php

namespace fq\boardnotices\repository;

use fq\boardnotices\repository\users_interface;
use fq\boardnotices\service\constants;

class users implements users_interface
{
	private $db;
	private $user;
	private $cache;
	private $config;
	private $notices_table;
	private $notices_rules_table;
	private $notices_seen_table;
	private $forums_visited_table;

	// private $user_loaded = false;
	// private $user_row = array();
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

	// private function loadUser()
	// {
	// 	$user = array();
	// 	$sql_array = array(
	// 		constants::$SQL_SELECT => 'u.*',
	// 		constants::$SQL_FROM => array(USERS_TABLE => 'u'),
	// 		constants::$SQL_WHERE => 'u.user_id=' . (int) $this->user->data['user_id'],
	// 	);
	// 	$sql = $this->db->sql_build_query(constants::$SQL_SELECT, $sql_array);

	// 	$result = $this->db->sql_query($sql);
	// 	$user = $this->db->sql_fetchrow($result);
	// 	$this->db->sql_freeresult($result);

	// 	return $user;
	// }

	// public function getUserInfo($field_name)
	// {
	// 	if (!$this->user_loaded)
	// 	{
	// 		$this->user_row = $this->loadUser();
	// 		$this->user_loaded = true;
	// 	}
	// 	return isset($this->user_row[$field_name]) ? $this->user_row[$field_name] : null;
	// }

	private function loadUserGroups()
	{
		$usergroups = array();
		$sql_array = array(
			constants::$SQL_SELECT => 'g.group_id, g.group_name, g.group_type',
			constants::$SQL_FROM => array(GROUPS_TABLE => 'g', USER_GROUP_TABLE => 'ug'),
			constants::$SQL_WHERE => 'ug.user_id=' . (int) $this->user->data['user_id']
			. ' AND g.group_id = ug.group_id'
			. ' AND ug.user_pending = 0',
		);
		$sql = $this->db->sql_build_query(constants::$SQL_SELECT, $sql_array);

		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$usergroups[(int) $row['group_id']] = $row;
		}
		$this->db->sql_freeresult($result);

		return $usergroups;
	}

	/**
	 * @param int $group_id
	 * @return bool
	 */
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
			constants::$SQL_SELECT => 'count(p.post_id) AS count',
			constants::$SQL_FROM => array(POSTS_TABLE => 'p'),
			constants::$SQL_WHERE => 'p.poster_id=' . (int) $this->user->data['user_id']
			. (($include_waiting_for_approval) ? ' AND p.post_visibility < 2' : ' AND p.post_visibility = 1')
			. ((!empty($in_forums)) ? ' AND p.forum_id IN (' . implode(',', $in_forums) . ')' : ''),
		);
		$sql = $this->db->sql_build_query(constants::$SQL_SELECT, $sql_array);

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
			constants::$SQL_SELECT => 'g.group_id, g.group_name, g.group_type',
			constants::$SQL_FROM => array(GROUPS_TABLE => 'g'),
			constants::$SQL_WHERE => (!$this->config['coppa_enable']) ? "group_name <> 'REGISTERED_COPPA'" : '',
			'ORDER_BY' => 'g.group_type DESC, g.group_name ASC',
		);
		$sql = $this->db->sql_build_query(constants::$SQL_SELECT, $sql_array);

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

	// public function getForumIdFromTopicId($topic_id)
	// {
	// 	$sql_array = array(
	// 		constants::$SQL_SELECT => 't.forum_id',
	// 		constants::$SQL_FROM => array(TOPICS_TABLE => 't'),
	// 		constants::$SQL_WHERE => 't.topic_id = ' . (int) $topic_id,
	// 	);
	// 	$sql = $this->db->sql_build_query(constants::$SQL_SELECT, $sql_array);
	// 	$result = $db->sql_query($sql);
	// 	$forum_id = (int) $db->sql_fetchfield('forum_id');
	// 	$db->sql_freeresult($result);

	// 	return $forum_id;
	// }

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
			$sql = 'SELECT forum_id, visited'
					. ' FROM ' . $this->forums_visited_table
					. ' WHERE user_id=' . (int) $user_id;
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$forums[$user_id][$row['forum_id']] = $row['visited'];
			}
			$this->db->sql_freeresult($result);
		}
		return $forums[$user_id];
	}

	function getForumsLastReadTime($user_id)
	{
		return $this->loadForumLastReadTime($user_id);
	}

	function getForumLastReadTime($user_id, $forum_id)
	{
		$forums = $this->loadForumLastReadTime($user_id);
		return (!empty($forums[$forum_id]) ? $forums[$forum_id] : null);
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

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

namespace fq\boardnotices;

class datalayer
{
	private $db;
	private $user;
	private $notices_table;
	private $notices_rules_table;
	private $notices_loaded = false;
	private $active_notices_loaded = false;
	private $notices = array();
	private $rules_loaded = false;
	private $rules = array();
	private $user_loaded = false;
	private $user_row = array();
	private $usergroups_loaded = false;
	private $usergroups = array();

	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\user $user, $notices_table, $notices_rules_table)
	{
		$this->db = $db;
		$this->user = $user;
		$this->notices_table = $notices_table;
		$this->notices_rules_table = $notices_rules_table;
	}

	private function loadNotices($active_only = true)
	{
		$notices = array();
		$sql_array = array(
			'SELECT'		=> 'n.*',
			'FROM'			=> array($this->notices_table => 'n'),
			'WHERE'			=> $active_only ? 'n.active=1' : '',
			'ORDER BY'		=> 'n.left_id',
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
	
	private function loadRules()
	{
		$rules = array();
		$sql_array = array(
			'SELECT'		=> 'r.*',
			'FROM'			=> array($this->notices_rules_table => 'r'),
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
		return !empty($this->rules[$notice_id]) ? $this->rules[$notice_id] : Array();
	}

	private function loadUser()
	{
		$user = array();
		$sql_array = array(
			'SELECT'		=> 'u.*',
			'FROM'			=> array(USERS_TABLE => 'u'),
			'WHERE'			=> 'u.user_id=' . (int) $this->user->data['user_id'],
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
			'SELECT'		=> 'g.group_id, g.group_name, g.group_type',
			'FROM'			=> array(GROUPS_TABLE => 'g', USER_GROUP_TABLE => 'ug'),
			'WHERE'			=> 'ug.user_id=' . (int) $this->user->data['user_id']
								. ' AND g.group_id = ug.group_id'
								. ' AND ug.user_pending = 0',
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);

		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$usergroups[(int)$row['group_id']] = $row;
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
	
	private function loadNonDeletedUserPosts()
	{
		$userposts = 0;
		$sql_array = array(
			'SELECT'		=> 'count(p.post_id) AS count',
			'FROM'			=> array(POSTS_TABLE => 'p'),
			'WHERE'			=> 'p.poster_id=' . (int) $this->user->data['user_id']
								. ' AND p.post_visibility < 2',
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
	public function nonDeletedUserPosts()
	{
		return $this->loadNonDeletedUserPosts();
	}
}
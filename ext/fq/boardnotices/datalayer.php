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
	private $notices_table;
	private $notices_rules_table;
	private $notices_loaded = false;
	private $active_notices_loaded = false;
	private $notices = array();
	private $rules_loaded = false;
	private $rules = array();

	public function __construct(\phpbb\db\driver\driver_interface $db, $notices_table, $notices_rules_table)
	{
		$this->db = $db;
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
}

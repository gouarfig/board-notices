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

use fq\boardnotices\repository\boardnotices_interface;

class boardnotices implements boardnotices_interface
{

	private $db;
	private $user;
	private $cache;
	private $notices_table;
	private $notices_rules_table;
	private $notices_seen_table;
	private $notices_loaded = false;
	private $active_notices_loaded = false;
	private $notices = array();
	private $rules_loaded = false;
	private $rules = array();

	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\user $user, \phpbb\cache\service $cache, $notices_table, $notices_rules_table, $notices_seen_table)
	{
		$this->db = $db;
		$this->user = $user;
		$this->cache = $cache;
		$this->notices_table = $notices_table;
		$this->notices_rules_table = $notices_rules_table;
		$this->notices_seen_table = $notices_seen_table;
	}

	/**
	 * @codeCoverageIgnore
	 * @param string $message
	 */
	private function debug($message)
	{
		if (defined('BOARDNOTICES_DEBUG') && BOARDNOTICES_DEBUG)
		{
			echo "<br />{$message}<br />\n";
		}
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
		$notice_id = intval($notice_id);
		$this->getAllNotices();
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
		$this->notices = array();
		$this->notices_loaded = false;
		$this->cache->destroy('_notices');
		$this->cache->destroy('sql', $this->notices_table);
	}

	public function moveNotice($action, $notice_id)
	{
		$move_executed = false;
		$notice_id = intval($notice_id);

		// Get current order id...
		$sql = "SELECT notice_order as current_order
			FROM {$this->notices_table}
			WHERE notice_id = $notice_id";
		$result = $this->db->sql_query($sql);
		$current_order = (int) $this->db->sql_fetchfield('current_order');
		$this->db->sql_freeresult($result);

		// First order is 1
		if ($current_order <= 1 && $action == 'move_up')
		{
			return false;
		}

		// on move_down, switch position with next order_id...
		// on move_up, switch position with previous order_id...
		$switch_order_id = (($action == 'move_down') ? ($current_order + 1) : ($current_order - 1));

		//
		$sql = "UPDATE {$this->notices_table}
			SET notice_order = $current_order
			WHERE notice_order = $switch_order_id
				AND notice_id <> $notice_id";
		$this->db->sql_query($sql);
		$move_executed = ($this->db->sql_affectedrows() > 0) ? true : false;

		// Only update the other entry too if the previous entry got updated
		if ($move_executed)
		{
			$sql = "UPDATE $this->notices_table
				SET notice_order = $switch_order_id
				WHERE notice_order = $current_order
					AND notice_id = $notice_id";
			$this->db->sql_query($sql);

			$this->cleanNotices();
		}

		return $move_executed;
	}

	public function moveNoticeFirst($notice_id)
	{
		$move_executed = false;
		$notice_id = intval($notice_id);
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

				$this->cleanNotices();
			}
		}
		return $move_executed;
	}

	public function moveNoticeLast($notice_id)
	{
		$move_executed = false;
		$notice_id = intval($notice_id);
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

				$this->cleanNotices();
			}
		}
		return $move_executed;
	}

	public function deleteNotice($notice_id)
	{
		$deleted = false;
		$notice_id = intval($notice_id);
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

				$this->cleanNotices();
			}
		}
		return $deleted;
	}

	public function enableNotice($action, $notice_id)
	{
		$query_done = false;
		$notice_id = intval($notice_id);
		if ($notice_id > 0)
		{
			$sql = "UPDATE {$this->notices_table}"
					. " SET active=" . (($action == 'enable') ? '1' : '0')
					. " WHERE notice_id={$notice_id}";
			$this->db->sql_query($sql);
			$query_done = ($this->db->sql_affectedrows() > 0) ? true : false;

			$this->cleanNotices();
		}
		return $query_done;
	}

	private function getNextNoticeId()
	{
		$next_id = 0;
		$this->getAllNotices();
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
		$this->getAllNotices();
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
		$new_id = null;
		if (is_array($data) && !empty($data))
		{
			$data['notice_id'] = $this->getNextNoticeId();
			$data['notice_order'] = $this->getNextNoticeOrder();
			$sql = "INSERT INTO {$this->notices_table} " . $this->db->sql_build_array('INSERT', $data);
			$this->db->sql_query($sql);

			$new_id = $data['notice_id'];
			$this->cleanNotices();
		}

		return $new_id;
	}

	public function saveNotice($notice_id, &$data)
	{
		$saved = false;
		$notice_id = intval($notice_id);
		if (($notice_id > 0) && is_array($data) && !empty($data))
		{
			unset($data['notice_id']);
			unset($data['notice_order']);

			$sql = "UPDATE {$this->notices_table}
				SET " . $this->db->sql_build_array('UPDATE', $data) . "
				WHERE notice_id = " . $notice_id;
			$this->db->sql_query($sql);
			$saved = ($this->db->sql_affectedrows() == 1) ? true : false;

			$this->cleanNotices();
		}
		return $saved;
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
		$notice_id = intval($notice_id);
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

}

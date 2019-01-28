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
use fq\boardnotices\service\constants;

class boardnotices implements boardnotices_interface
{
	/** @var \phpbb\db\driver\driver_interface $db */
	private $db;
	/** @var \phpbb\user $user */
	private $user;
	/** @var \phpbb\cache\service $cache */
	private $cache;
	/** @var \phpbb\config\config $config */
	private $config;

	private $cache_ttl = 0;

	private $notices_table;
	private $notices_rules_table;
	private $notices_seen_table;
	private $forums_visited_table;

	private $notices_loaded = false;
	private $active_notices_loaded = false;
	private $notices = array();
	private $rules_loaded = false;
	private $rules = array();

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

		if (!empty($this->config))
		{
			$this->cache_ttl = !empty($this->config[constants::$CONFIG_SQL_CACHE_TTL])
								? $this->config[constants::$CONFIG_SQL_CACHE_TTL]
								: 86400;
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

	private function getNoticesCacheName($active_only = true)
	{
		if ($active_only)
		{
			return constants::$CONFIG_ACTIVE_NOTICES_CACHE_KEY;
		}
		else
		{
			return constants::$CONFIG_ALL_NOTICES_CACHE_KEY;
		}
	}

	/**
	 * Returns notices
	 *
	 * @param boolean $active_only
	 * @return array
	 */
	public function getNotices($active_only = true)
	{
		if (!$this->notices_loaded || ($this->active_notices_loaded != $active_only))
		{
			$notices = $this->cache->get($this->getNoticesCacheName($active_only));
			if (!empty($notices))
			{
				$this->notices = $notices;
			}
			else
			{
				$this->notices = $this->loadNotices($active_only);
				$this->cache->put($this->getNoticesCacheName($active_only), $this->notices, $this->cache_ttl);
			}
			$this->notices_loaded = true;
			$this->active_notices_loaded = $active_only;
		}
		return $this->notices;
	}

	/**
	 * Returns all notices (including inactive ones)
	 *
	 * @return array
	 */
	public function getAllNotices()
	{
		return $this->getNotices(false);
	}

	/**
	 * Returns all active notices
	 *
	 * @return array
	 */
	public function getActiveNotices()
	{
		return $this->getNotices(true);
	}

	/**
	 * Returns a notice from its ID
	 *
	 * @param int $notice_id
	 * @return array $notice
	 */
	public function getNoticeFromId($notice_id)
	{
		$notice = null;
		$notice_id = (int) $notice_id;
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

	/**
	 * Clears the notices cache
	 *
	 * @return void
	 */
	private function clearNotices()
	{
		$this->notices = array();
		$this->notices_loaded = false;
		$this->cache->destroy(constants::$CONFIG_ACTIVE_NOTICES_CACHE_KEY);
		$this->cache->destroy(constants::$CONFIG_ALL_NOTICES_CACHE_KEY);
		$this->cache->destroy('sql', $this->notices_table);
	}

	/**
	 * Moves the notice to the direction specified in $action:
	 *  move_up' or 'move_down'
	 *
	 * @param string $action
	 * @param int $notice_id
	 * @return bool
	 */
	public function moveNotice($action, $notice_id)
	{
		$move_executed = false;
		$notice_id = (int) $notice_id;

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
			$sql = "UPDATE {$this->notices_table}
				SET notice_order = $switch_order_id
				WHERE notice_order = $current_order
					AND notice_id = $notice_id";
			$this->db->sql_query($sql);

			$this->clearNotices();
		}

		return $move_executed;
	}

	/**
	 * Moves the notice to the first position
	 *
	 * @param int $notice_id
	 * @return bool
	 */
	public function moveNoticeFirst($notice_id)
	{
		$move_executed = false;
		$notice_id = (int) $notice_id;
		$notice = $this->getNoticeFromId($notice_id);
		if (!empty($notice) && ($notice['notice_order'] > 1))
		{
			$sql = "UPDATE {$this->notices_table}
				SET notice_order = notice_order +1
				WHERE notice_order < " . (int) $notice['notice_order'];
			$this->db->sql_query($sql);
			$move_executed = ($this->db->sql_affectedrows() > 0) ? true : false;

			if ($move_executed)
			{
				$sql = "UPDATE {$this->notices_table}
					SET notice_order = 1
					WHERE notice_id = $notice_id";
				$this->db->sql_query($sql);

				$this->clearNotices();
			}
		}
		return $move_executed;
	}

	/**
	 * Moves the notice to the last position
	 *
	 * @param int $notice_id
	 * @return bool
	 */
	public function moveNoticeLast($notice_id)
	{
		$move_executed = false;
		$notice_id = (int) $notice_id;
		$last_order = (int) $this->getNextNoticeOrder() -1;
		$notice = $this->getNoticeFromId($notice_id);
		if (!empty($notice) && ($notice['notice_order'] > 0) && ($notice['notice_order'] < $last_order))
		{
			$sql = "UPDATE {$this->notices_table}
				SET notice_order = notice_order -1
				WHERE notice_order > " . (int) $notice['notice_order'];
			$this->db->sql_query($sql);
			$move_executed = ($this->db->sql_affectedrows() > 0) ? true : false;

			if ($move_executed)
			{
				$sql = "UPDATE {$this->notices_table}
					SET notice_order = {$last_order}
					WHERE notice_id = {$notice_id}";
				$this->db->sql_query($sql);

				$this->clearNotices();
			}
		}
		return $move_executed;
	}

	/**
	 * Delete a notice
	 *
	 * @param int $notice_id
	 * @return bool
	 */
	public function deleteNotice($notice_id)
	{
		$deleted = false;
		$notice_id = (int) $notice_id;
		$notice = $this->getNoticeFromId($notice_id);
		if (!empty($notice))
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
				$sql = "UPDATE {$this->notices_table} SET notice_order=notice_order-1
						WHERE notice_order>=" . (int) $notice['notice_order'];
				$this->db->sql_query($sql);

				$this->clearNotices();
			}
		}
		return $deleted;
	}

	/**
	 * Enable or disable a notice
	 *
	 * @param string $action
	 * @param int $notice_id
	 * @return bool
	 */
	public function enableNotice($action, $notice_id)
	{
		$query_done = false;
		$notice_id = (int) $notice_id;
		if ($notice_id > 0)
		{
			$sql = "UPDATE {$this->notices_table}"
					. " SET active=" . (($action == 'enable') ? '1' : '0')
					. " WHERE notice_id={$notice_id}";
			$this->db->sql_query($sql);
			$query_done = ($this->db->sql_affectedrows() > 0) ? true : false;

			$this->clearNotices();
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

	/**
	 * Insert notice data
	 *
	 * @param array $data
	 * @return int|null
	 */
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
			$this->clearNotices();
		}

		return $new_id;
	}

	/**
	 * Save notice data
	 *
	 * @param int $notice_id
	 * @param array $data
	 * @return bool
	 */
	public function saveNotice($notice_id, &$data)
	{
		$saved = false;
		$notice_id = (int) $notice_id;
		if (($notice_id > 0) && is_array($data) && !empty($data))
		{
			unset($data['notice_id']);
			unset($data['notice_order']);

			$sql = "UPDATE {$this->notices_table}
				SET " . $this->db->sql_build_array('UPDATE', $data) . "
				WHERE notice_id = " . (int) $notice_id;
			$this->db->sql_query($sql);
			$saved = ($this->db->sql_affectedrows() == 1) ? true : false;

			$this->clearNotices();
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

		$result = $this->db->sql_query($sql, $this->cache_ttl);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rules[$row['notice_id']][] = $row;
		}
		$this->db->sql_freeresult($result);

		return $rules;
	}

	/**
	 * Returns all the rules (conditions) for the notice in argument
	 *
	 * @param int $notice_id
	 * @return array $rules
	 */
	public function getRulesFor($notice_id)
	{
		$notice_id = (int) $notice_id;
		if ($notice_id <= 0)
		{
			return null;
		}
		if (!$this->rules_loaded)
		{
			$rules = $this->cache->get(constants::$CONFIG_RULES_CACHE_KEY);
			if (!empty($rules))
			{
				$this->rules = $rules;
			}
			else
			{
				$this->rules = $this->loadRules();
				$this->cache->put(constants::$CONFIG_RULES_CACHE_KEY, $this->rules, $this->cache_ttl);
			}
			$this->rules_loaded = true;
		}
		return !empty($this->rules[$notice_id]) ? $this->rules[$notice_id] : array();
	}

	/**
	 * Purge the rules cache
	 *
	 * @return void
	 */
	private function clearRules()
	{
		$this->rules_loaded = false;
		$this->cache->destroy(constants::$CONFIG_RULES_CACHE_KEY);
		$this->cache->destroy('sql', $this->notices_rules_table);
	}

	/**
	 * Deletes a list of rules, and returns the number of rules deleted, or FALSE if error
	 *
	 * @param int[] $rules
	 * @return int
	 */
	public function deleteRules($rules)
	{
		if (!is_array($rules))
		{
			$rules = array($rules);
		}
		$cleanRules = array();
		foreach ($rules as $rule)
		{
			$rule = (int) $rule;
			if ($rule > 0)
			{
				$cleanRules[] = $rule;
			}
		}
		if (empty($cleanRules))
		{
			return false;
		}
		$sql = "DELETE FROM " . $this->notices_rules_table . " WHERE notice_rule_id IN (" . implode(',', $cleanRules) . ")";
		$this->db->sql_query($sql);
		$this->clearRules();

		return $this->db->sql_affectedrows();
	}

	/**
	 * Update a list of rules
	 *
	 * @param array $rules
	 * @return int $updated
	 */
	public function updateRules($rules)
	{
		$updated = 0;
		if (!is_array($rules))
		{
			$rules = array($rules);
		}
		foreach ($rules as $rule)
		{
			if (is_array($rule))
			{
				$notice_rule_id = (int) $rule['notice_rule_id'];
				unset($rule['notice_rule_id']);

				if ($notice_rule_id > 0)
				{
					$sql = "UPDATE {$this->notices_rules_table}
						SET " . $this->db->sql_build_array('UPDATE', $rule) . "
						WHERE notice_rule_id = " . (int) $notice_rule_id;
					$this->db->sql_query($sql);
					$updated += $this->db->sql_affectedrows();
				}
			}
		}
		$this->clearRules();

		return $updated;
	}

	/**
	 * Saves new rules
	 *
	 * @param array $rules
	 * @return int $rules
	 */
	public function insertRules($rules)
	{
		$inserted = 0;
		if (!is_array($rules))
		{
			$rules = array($rules);
		}
		foreach ($rules as $rule)
		{
			if (is_array($rule))
			{
				$sql = "INSERT INTO {$this->notices_rules_table} " . $this->db->sql_build_array('INSERT', $rule);
				$this->db->sql_query($sql);
				$inserted += $this->db->sql_affectedrows();
			}
		}
		$this->clearRules();

		return $inserted;
	}

	/**
	 * Indicates that this forum has been visited by this user
	 *
	 * @param int $user_id
	 * @param int $forum_id
	 * @return bool
	 */
	public function setForumVisited($user_id, $forum_id)
	{
		$affectedRows = 0;
		$user_id = (int) $user_id;
		$forum_id = (int) $forum_id;

		if (($user_id > 0) && ($forum_id > 0))
		{
			$time = time();
			$sql = "UPDATE {$this->forums_visited_table} SET visited={$time}"
					. " WHERE user_id={$user_id} AND forum_id={$forum_id}";
			$this->db->sql_query($sql);
			$affectedRows = $this->db->sql_affectedrows();

			if ($affectedRows < 1)
			{
				$sql = "INSERT INTO {$this->forums_visited_table} (user_id, forum_id, visited)"
						. " VALUES ({$user_id}, {$forum_id}, {$time})";
				$this->db->sql_query($sql);
				$affectedRows = $this->db->sql_affectedrows();
			}
		}
		return $affectedRows == 1;
	}

	/**
	 * Clears forum visit table
	 *
	 * @return void
	 */
	public function clearForumVisited()
	{
		$sql = "TRUNCATE TABLE {$this->forums_visited_table}";
		$this->db->sql_query($sql);
	}

}

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

use fq\boardnotices\repository\notices_seen_interface;
use fq\boardnotices\service\constants;

class notices_seen implements notices_seen_interface
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
	private $notices_seen_table;

	private $notices_seen_loaded = false;
	private $notices_seen = array();

	public function __construct(
			\phpbb\db\driver\driver_interface $db,
			\phpbb\user $user,
			\phpbb\cache\service $cache,
			\phpbb\config\config $config,
			$notices_table,
			$notices_seen_table)
	{
		$this->db = $db;
		$this->user = $user;
		$this->cache = $cache;
		$this->config = $config;
		$this->notices_table = $notices_table;
		$this->notices_seen_table = $notices_seen_table;

		if (!empty($this->config))
		{
			$this->cache_ttl = !empty($this->config[constants::$CONFIG_SQL_CACHE_TTL])
								? $this->config[constants::$CONFIG_SQL_CACHE_TTL]
								: 86400;
		}
	}

	private function loadDismissedNotices()
	{
		$notices = array();
		$sql_array = array(
			'SELECT' => 'n.*',
			'FROM' => array($this->notices_seen_table => 'n'),
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);

		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$notices[$row['user_id']][$row['notice_id']] = $row;
		}
		$this->db->sql_freeresult($result);

		return $notices;
	}


	/**
	 * Clears the dismissed notices cache
	 *
	 * @return void
	 */
	private function clearNoticesSeenCache()
	{
		$this->notices_seen = array();
		$this->notices_seen_loaded = false;
		$this->cache->destroy(constants::$CONFIG_DISMISSED_CACHE_KEY);
		$this->cache->destroy('sql', $this->notices_seen_table);
	}

	/**
	 * Returns the list of dismissed notices for a user
	 * @param int $user_id
	 * @return array
	 */
	public function getDismissedNotices($user_id)
	{
		$user_id = (int) $user_id;
		if (empty($user_id))
		{
			return null;
		}
		if (!$this->notices_seen_loaded)
		{
			$notices_seen = $this->cache->get(constants::$CONFIG_DISMISSED_CACHE_KEY);
			if (!empty($notices_seen))
			{
				$this->notices_seen = $notices_seen;
			}
			else
			{
				$this->notices_seen = $this->loadDismissedNotices();
				$this->cache->put(constants::$CONFIG_DISMISSED_CACHE_KEY, $this->notices_seen, $this->cache_ttl);
			}
			$this->notices_seen_loaded = true;
		}
		return !empty($this->notices_seen[$user_id])
				? $this->notices_seen[$user_id]
				: array();
	}

	/**
	 * Indicates the user has dismissed a notice (clicked on close)
	 * @param int $notice_id
	 * @param int $user_id
	 * @return boolean
	 */
	public function setNoticeDismissed($notice_id, $user_id)
	{
		$notice_id = (int) $notice_id;
		$user_id = (int) $user_id;

		if (empty($notice_id) || empty($user_id))
		{
			return false;
		}
		$user_notices_seen = $this->getDismissedNotices($user_id);
		if (empty($user_notices_seen) || empty($user_notices_seen[$notice_id]))
		{
			// Insert new record
			$data = array(
				'notice_id' => $notice_id,
				'user_id' => $user_id,
				'seen' => time(),
			);
			$sql = "INSERT INTO {$this->notices_seen_table} " . $this->db->sql_build_array('INSERT', $data);
			$this->db->sql_query($sql);
			$saved = $this->db->sql_affectedrows();
		}
		else
		{
			// Update existing record
			$data = array(
				'seen' => time(),
			);
			$sql = "UPDATE {$this->notices_seen_table}
				SET " . $this->db->sql_build_array('UPDATE', $data) . "
				WHERE notice_id = {$notice_id} AND user_id = {$user_id}";
			$this->db->sql_query($sql);
			$saved = $this->db->sql_affectedrows();
		}
		$this->clearNoticesSeenCache();
		return $saved == 1;
	}

}

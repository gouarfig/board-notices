<?php
/**
 *
 * This file is part of the phpBB Forum Software package.
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * For full copyright and license information, please see
 * the docs/CREDITS.txt file.
 *
 */

namespace fq\boardnotices\tests\repository;

use fq\boardnotices\repository\notices;

class notices_cache_test extends notices_testbase
{
	/**
	 * A new clean database is re-created on each test
	 *
	 * @return boardnotices
	 */
	protected function getBoardNoticesInstance()
	{
		$phpbb_root_path = $this->getRootFolder();
		$language_file_loader = new \phpbb\language\language_file_loader($phpbb_root_path, 'php');
		$language = new \phpbb\language\language($language_file_loader);
		$user = new \phpbb\user($language, '\phpbb\datetime');
		$default_config = array(
			'boardnotices_enabled' => true,
			'boardnotices_default_bgcolor' => 'ECD5D8',
		);
		$config = new \phpbb\config\config($default_config);
		$phpEx = substr(strrchr(__FILE__, '.'), 1);
		$cache_driver = new mock_cache();
		$cache = new \phpbb\cache\service($cache_driver, $config, $this->db, $phpbb_root_path, $phpEx);
		$dac = new notices(
			$this->db,
			$user,
			$cache,
			$config,
			$this->table_prefix . 'notices',
			$this->table_prefix . 'notices_rules',
			$this->table_prefix . 'notices_seen',
			$this->table_prefix . 'forums_visited'
		);

		return $dac;
	}

}

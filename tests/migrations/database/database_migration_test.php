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

namespace fq\boardnotices\tests\migrations;

class database_migration_test extends \phpbb_database_test_case
{
	/** @var \phpbb\db\tools */
	protected $db_tools;

	/** @var string */
	protected $table_prefix;

	static protected function setup_extensions()
	{
		return array('fq/boardnotices');
	}

	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/add_database_changes.xml');
	}

	public function setUp()
	{
		parent::setUp();

		global $table_prefix;

		$this->table_prefix = $table_prefix;
		$db = $this->new_dbal();
		$this->db_tools = new \phpbb\db\tools($db);
	}

	public function test_notices_table()
	{
		$this->assertTrue($this->db_tools->sql_table_exists($this->table_prefix . 'notices'), 'Asserting that column "' . $this->table_prefix . 'notices" does not exist');
	}

	public function test_notices_rules_table()
	{
		$this->assertTrue($this->db_tools->sql_table_exists($this->table_prefix . 'notices_rules'), 'Asserting that column "' . $this->table_prefix . 'notices_rules" does not exist');
	}

	public function test_notices_seen_table()
	{
		$this->assertTrue($this->db_tools->sql_table_exists($this->table_prefix . 'notices_seen'), 'Asserting that column "' . $this->table_prefix . 'notices_seen" does not exist');
	}

	public function _test_preview_key_generated()
	{
		$db = $this->new_dbal();
		$sql = "SELECT config_name, config_value FROM {$this->table_prefix}config";
		$result = $db->sql_query($sql);

		$config = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$config[$row['config_name']] = $row['config_value'];
		}
		$db->sql_freeresult($result);

		$this->assertEquals(10, strlen($config['boardnotices_previewkey']));
	}
}
<?php

namespace fq\boardnotices\tests\repository;

use \fq\boardnotices\repository\users;

abstract class users_testbase extends \phpbb_database_test_case
{
	protected $db;

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
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/boardnotices.xml');
	}

	public function setUp()
	{
		parent::setUp();

		global $table_prefix;

		$this->table_prefix = $table_prefix;
		$this->db = $this->new_dbal();
		$this->db_tools = new \phpbb\db\tools($this->db);
	}

	protected function getRootFolder()
	{
		return dirname(__FILE__) . '/../../../../../';
	}

	/**
	 * A new clean database is re-created on each test
	 *
	 * @return users
	 */
	protected abstract function getUsersRepositoryInstance();

	public function testInstance()
	{
		$dac = $this->getUsersRepositoryInstance();
		$this->assertNotNull($dac);
		$this->assertInstanceOf(users::class, $dac);
		return $dac;
	}
}

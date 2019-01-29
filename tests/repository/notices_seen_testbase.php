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

define('BOARDNOTICES_DEBUG', false);

abstract class notices_seen_testbase extends \phpbb_database_test_case
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
	 * @return \fq\boardnotices\repository\notices_seen
	 */
	protected abstract function getInstance();

	public function testInstance()
	{
		$dac = $this->getInstance();
		$this->assertNotNull($dac);
		return $dac;
	}

	public function testCanSetDismissedTwice()
	{
		$dac = $this->getInstance();
		$user_id = 10;
		$notice_id = 11;

		$notices_seen = $dac->getDismissedNotices($user_id);
		$this->assertEquals(0, count($notices_seen));

		$saved = $dac->setNoticeDismissed($notice_id, $user_id);
		$this->assertTrue($saved);

		$notices_seen = $dac->getDismissedNotices($user_id);
		$this->assertEquals(1, count($notices_seen));

		$saved = $dac->setNoticeDismissed($notice_id, $user_id);
		$this->assertTrue($saved);

		$notices_seen = $dac->getDismissedNotices($user_id);
		$this->assertEquals(1, count($notices_seen));
	}

	public function testInvalidIds()
	{
		$dac = $this->getInstance();

		$seen = $dac->getDismissedNotices('invalid id');
		$this->assertNull($seen);

		$seen = $dac->getDismissedNotices(0);
		$this->assertNull($seen);

		$seen = $dac->getDismissedNotices(null);
		$this->assertNull($seen);

		$saved = $dac->setNoticeDismissed('invalid id', 10);
		$this->assertFalse($saved);

		$saved = $dac->setNoticeDismissed(10, 'invalid id');
		$this->assertFalse($saved);
	}
}

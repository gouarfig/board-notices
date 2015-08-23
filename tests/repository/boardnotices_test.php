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

use fq\boardnotices\repository\boardnotices;

class boardnotices_test extends \phpbb_database_test_case
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

	private function getRootFolder()
	{
		return dirname(__FILE__) . '/../../../../../';
	}

	/**
	 * I don't know what's happening inside phpunit or phpbb tests but I can't build an instance once for all and reuse it.
	 *
	 * @return boardnotices
	 */
	private function getBoardNoticesInstance()
	{
		$user = new \phpbb\user('\phpbb\datetime');
		$cache_driver = new \phpbb\cache\driver\null();
		$default_config = array(
			'boardnotices_enabled' => true,
		);
		$config = new \phpbb\config\config($default_config);
		$phpbb_root_path = $this->getRootFolder();
		$phpEx = substr(strrchr(__FILE__, '.'), 1);
		$cache = new \phpbb\cache\service($cache_driver, $config, $this->db, $phpbb_root_path, $phpEx);
		$dac = new boardnotices($this->db, $user, $cache, $this->table_prefix . 'notices', $this->table_prefix . 'notices_rules', $this->table_prefix . 'notices_seen');

		return $dac;
	}

	public function testInstance()
	{
		$dac = $this->getBoardNoticesInstance();
		$this->assertThat($dac, $this->logicalNot($this->equalTo(null)));
		return $dac;
	}

	public function testGetAllNotices()
	{
		$dac = $this->getBoardNoticesInstance();
		$notices = $dac->getAllNotices();
		$this->assertThat(count($notices), $this->equalTo(2));
	}

	public function testGetActiveNotices()
	{
		$dac = $this->getBoardNoticesInstance();
		$notices = $dac->getActiveNotices();
		$this->assertThat(count($notices), $this->equalTo(1));
	}

	public function testGetNoticeFromId()
	{
		$dac = $this->getBoardNoticesInstance();
		$notice = $dac->getNoticeFromId(2);
		$this->assertThat($notice, $this->logicalNot($this->equalTo(null)));
		$this->assertThat($notice['notice_id'], $this->equalTo(2));
	}

	public function testGetAllNoticesAfterGetActiveNotices()
	{
		$dac = $this->getBoardNoticesInstance();
		$notices = $dac->getActiveNotices();
		$this->assertThat(count($notices), $this->equalTo(1));
		$notices = $dac->getAllNotices();
		$this->assertThat(count($notices), $this->equalTo(2));
	}

	public function testGetInactiveNoticeAfterGetActiveNotices()
	{
		$dac = $this->getBoardNoticesInstance();
		$notices = $dac->getActiveNotices();
		$this->assertThat(count($notices), $this->equalTo(1));
		$notice = $dac->getNoticeFromId(2);
		$this->assertThat($notice, $this->logicalNot($this->equalTo(null)));
		$this->assertThat($notice['notice_id'], $this->equalTo(2));
	}

	public function testMoveUpFirstNotice()
	{
		$dac = $this->getBoardNoticesInstance();
		$moved = $dac->moveNotice('move_up', 1);
		$this->assertFalse($moved);

		// Order shouldn't have changed
		$notice = $dac->getNoticeFromId(1);
		$this->assertThat($notice['notice_order'], $this->equalTo(1));
		$notice = $dac->getNoticeFromId(2);
		$this->assertThat($notice['notice_order'], $this->equalTo(2));
	}

	public function testMoveUpSecondNotice()
	{
		$dac = $this->getBoardNoticesInstance();
		$moved = $dac->moveNotice('move_up', 2);
		$this->assertTrue($moved);

		// Order should have changed
		$notice = $dac->getNoticeFromId(1);
		$this->assertThat($notice['notice_order'], $this->equalTo(2));
		$notice = $dac->getNoticeFromId(2);
		$this->assertThat($notice['notice_order'], $this->equalTo(1));
	}

	public function testMoveDownLastNotice()
	{
		$dac = $this->getBoardNoticesInstance();
		$notices = $dac->getAllNotices();
		$moved = $dac->moveNotice('move_down', 2);
		$this->assertFalse($moved);

		// Order shouldn't have changed
		$notice = $dac->getNoticeFromId(1);
		$this->assertThat($notice['notice_order'], $this->equalTo(1));
		$notice = $dac->getNoticeFromId(2);
		$this->assertThat($notice['notice_order'], $this->equalTo(2));
	}

	public function testMoveDownFirstNotice()
	{
		$dac = $this->getBoardNoticesInstance();
		$moved = $dac->moveNotice('move_down', 1);
		$this->assertTrue($moved);

		// Order should have changed
		$notice = $dac->getNoticeFromId(1);
		$this->assertThat($notice['notice_order'], $this->equalTo(2));
		$notice = $dac->getNoticeFromId(2);
		$this->assertThat($notice['notice_order'], $this->equalTo(1));
	}

	public function testMoveFirstFirstNotice()
	{
		$dac = $this->getBoardNoticesInstance();
		$moved = $dac->moveNoticeFirst(1);
		$this->assertFalse($moved);

		// Order shouldn't have changed
		$notice = $dac->getNoticeFromId(1);
		$this->assertThat($notice['notice_order'], $this->equalTo(1));
		$notice = $dac->getNoticeFromId(2);
		$this->assertThat($notice['notice_order'], $this->equalTo(2));
	}

	public function testMoveFirstSecondNotice()
	{
		$dac = $this->getBoardNoticesInstance();
		$moved = $dac->moveNoticeFirst(2);
		$this->assertTrue($moved);

		// Order should have changed
		$notice = $dac->getNoticeFromId(1);
		$this->assertThat($notice['notice_order'], $this->equalTo(2));
		$notice = $dac->getNoticeFromId(2);
		$this->assertThat($notice['notice_order'], $this->equalTo(1));
	}

	public function testMoveLastFirstNotice()
	{
		$dac = $this->getBoardNoticesInstance();
		$moved = $dac->moveNoticeLast(1);
		$this->assertTrue($moved);

		// Order should have changed
		$notice = $dac->getNoticeFromId(1);
		$this->assertThat($notice['notice_order'], $this->equalTo(2));
		$notice = $dac->getNoticeFromId(2);
		$this->assertThat($notice['notice_order'], $this->equalTo(1));
	}

	function testMoveLastSecondNotice()
	{
		$dac = $this->getBoardNoticesInstance();
		$moved = $dac->moveNoticeLast(2);
		$this->assertFalse($moved);

		// Order shouldn't have changed
		$notice = $dac->getNoticeFromId(1);
		$this->assertThat($notice['notice_order'], $this->equalTo(1));
		$notice = $dac->getNoticeFromId(2);
		$this->assertThat($notice['notice_order'], $this->equalTo(2));
	}

	/**
	 * @depends testMoveLastSecondNotice
	 */
	public function testSaveNewNotice()
	{
		$dac = $this->getBoardNoticesInstance();
		$data1 = array(
			'title' => 'New notice 1',
			'message' => 'New notice 1',
			'message_uid' => '',
			'message_bitfield' => '',
			'message_options' => 0,
			'message_bgcolor' => '',
			'active' => 0,
			'persistent' => 0,
			'dismissable' => 0,
			'reset_after' => 0,
			'last' => 0,
			'notice_order' => 3,
		);
		$dac->saveNewNotice($data1);
		$data2 = array(
			'title' => 'New notice 2',
			'message' => 'New notice 2',
			'message_uid' => '',
			'message_bitfield' => '',
			'message_options' => 0,
			'message_bgcolor' => '',
			'active' => 1,
			'persistent' => 0,
			'dismissable' => 0,
			'reset_after' => 0,
			'last' => 0,
		);
		$dac->saveNewNotice($data2);

		$notices = $dac->getAllNotices();
		$this->assertThat(count($notices), $this->equalTo(4));

		$notice = $dac->getNoticeFromId(4);
		$this->assertThat($notice['notice_order'], $this->equalTo(4));

		$moved = $dac->moveNoticeFirst(4);
		$this->assertTrue($moved);

		// Order should have changed
		$notice = $dac->getNoticeFromId(4);
		$this->assertThat($notice['notice_order'], $this->equalTo(1));
		$notice = $dac->getNoticeFromId(1);
		$this->assertThat($notice['notice_order'], $this->equalTo(2));
		$notice = $dac->getNoticeFromId(2);
		$this->assertThat($notice['notice_order'], $this->equalTo(3));
		$notice = $dac->getNoticeFromId(3);
		$this->assertThat($notice['notice_order'], $this->equalTo(4));

		$moved = $dac->moveNoticeLast(4);
		$this->assertTrue($moved);

		// Order should have changed
		$notice = $dac->getNoticeFromId(1);
		$this->assertThat($notice['notice_order'], $this->equalTo(1));
		$notice = $dac->getNoticeFromId(2);
		$this->assertThat($notice['notice_order'], $this->equalTo(2));
		$notice = $dac->getNoticeFromId(3);
		$this->assertThat($notice['notice_order'], $this->equalTo(3));
		$notice = $dac->getNoticeFromId(4);
		$this->assertThat($notice['notice_order'], $this->equalTo(4));

		$moved = $dac->moveNotice('move_up', 3);
		$this->assertTrue($moved);

		// Order should have changed
		$notice = $dac->getNoticeFromId(1);
		$this->assertThat($notice['notice_order'], $this->equalTo(1));
		$notice = $dac->getNoticeFromId(3);
		$this->assertThat($notice['notice_order'], $this->equalTo(2));
		$notice = $dac->getNoticeFromId(2);
		$this->assertThat($notice['notice_order'], $this->equalTo(3));
		$notice = $dac->getNoticeFromId(4);
		$this->assertThat($notice['notice_order'], $this->equalTo(4));

		$moved = $dac->moveNotice('move_down', 3);
		$this->assertTrue($moved);

		// Order should have changed
		$notice = $dac->getNoticeFromId(1);
		$this->assertThat($notice['notice_order'], $this->equalTo(1));
		$notice = $dac->getNoticeFromId(2);
		$this->assertThat($notice['notice_order'], $this->equalTo(2));
		$notice = $dac->getNoticeFromId(3);
		$this->assertThat($notice['notice_order'], $this->equalTo(3));
		$notice = $dac->getNoticeFromId(4);
		$this->assertThat($notice['notice_order'], $this->equalTo(4));
	}

}
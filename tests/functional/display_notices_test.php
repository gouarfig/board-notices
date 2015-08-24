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

namespace fq\boardnotices\tests\functional;

/**
 * @group functional
 */
class display_notices_test extends \phpbb_functional_test_case
{
    static protected function setup_extensions()
    {
        return array('fq/boardnotices');
    }

    public function test_with_no_notice()
    {
        $crawler = self::request('GET', 'index.php');
        $this->assertCount(0, $crawler->filter('#fq_notice'));
    }

	/**
	 * @depends test_with_no_notice
	 */
	public function test_that_the_panel_is_available()
	{
		$this->login();
		$this->admin_login();

		$this->add_lang_ext('fq/boardnotices', 'boardnotices_acp');

		$crawler = self::request('GET', 'adm/index.php?sid=' . $this->sid . '&i=-fq-boardnotices-acp-board_notices_module&mode=manage');
		$this->assertCount(2, $crawler->filter('h1'));
		$this->assertGreaterThan(0, $crawler->filter('html:contains("' . $this->lang('ACP_BOARD_NOTICES_MANAGER') . '")')->count());
	}

	/**
	 * @depends test_that_the_panel_is_available
	 */
	public function test_submit_blank_notice()
	{
		$this->login();
		$this->admin_login();

		$crawler = self::request('GET', 'adm/index.php?sid=' . $this->sid . '&i=-fq-boardnotices-acp-board_notices_module&mode=manage');
		$buttonCrawlerNode = $crawler->selectButton('add');
		$this->assertCount(1, $buttonCrawlerNode, "'add' button not found");
		$form = $buttonCrawlerNode->form();
		$this->assertThat($form, $this->logicalNot($this->equalTo(null)), "form of the 'add' button not found");
		$crawler = self::submit($form);
		$this->assertThat($crawler, $this->logicalNot($this->equalTo(null)), "Cannot submit form");

		$buttonCrawlerNode = $crawler->selectButton('submit');
		$this->assertCount(1, $buttonCrawlerNode, "'submit' button not found");
		$form = $buttonCrawlerNode->form();
		$this->assertThat($form, $this->logicalNot($this->equalTo(null)), "form of the 'submit' button not found");
		$form['board_notice_active'] = 1;
		$form['board_notice_title'] = 'New test notice';
		$form['board_notice_text'] = 'Welcome {USERNAME}!';
		$form['notice_rule_checked[logged_in]']->tick();
		$form['notice_rule_conditions[logged_in][0]'] = 1;
		$crawler = self::submit($form);
	}

	/**
	 * @depends test_submit_blank_notice
	 */
	public function test_no_notice_is_displayed_for_guest()
	{
        $crawler = self::request('GET', 'index.php');
        $this->assertCount(0, $crawler->filter('#fq_notice'));
	}

	/**
	 * @depends test_submit_blank_notice
	 */
	public function test_notice_is_displayed_when_logged_in()
	{
		$this->login();

        $crawler = self::request('GET', 'index.php');
		$notice = $crawler->filter('#fq_notice');
        $this->assertCount(1, $notice);
		$this->assertTrue(strpos($notice->text(), 'Welcome admin!') !== false);
	}
}
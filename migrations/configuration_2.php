<?php

/**
 *
 * Board Notices Manager extension for the phpBB Forum Software package.
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\migrations;

class configuration_2 extends \phpbb\db\migration\migration
{

	public function effectively_installed()
	{
		return isset($this->config['boardnotices_enabled']);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('boardnotices_enabled', true)),
			array('config.add', array('boardnotices_multiple_notices', false)),
			array('config.add', array('boardnotices_dismissable', false)),
		);
	}

	public function revert_data()
	{
		return array(
			array('config.remove', array('boardnotices_enabled')),
			array('config.remove', array('boardnotices_multiple_notices')),
			array('config.remove', array('boardnotices_dismissable')),
		);
	}
}

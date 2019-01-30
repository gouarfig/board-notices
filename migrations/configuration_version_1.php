<?php

/**
 *
 * Board Notices Manager extension for the phpBB Forum Software package.
 * Records the first internal version in the configuration, for upgrade purposes
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\migrations;

class configuration_version_1 extends \phpbb\db\migration\migration
{
	private $version = 1;

	static public function depends_on()
	{
		return array('\fq\boardnotices\migrations\configuration_2');
	}

	public function effectively_installed()
	{
		return !empty($this->config['boardnotices_version']);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('boardnotices_version', $this->version)),
		);
	}

	public function revert_data()
	{
		return array(
			array('config.remove', array('boardnotices_version')),
		);
	}
}

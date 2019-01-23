<?php

/**
 *
 * Board Notices Manager extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\migrations;

class configuration_3 extends \phpbb\db\migration\migration
{

	public function effectively_installed()
	{
		return array_key_exists('boardnotices_default_bgcolor', $this->config);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('boardnotices_default_bgcolor', 'ECD5D8')),
		);
	}

	public function revert_data()
	{
		return array(
			array('config.remove', array('boardnotices_default_bgcolor')),
		);
	}
}

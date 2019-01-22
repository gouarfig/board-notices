<?php

/**
 *
 * Board Notices Manager extension for the phpBB Forum Software package.
 * Records the first internal version in the configuration, for upgrade purposes
 *
 * @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\migrations;

class configuration_version_2 extends \phpbb\db\migration\migration
{
	private $version = 2;

	static public function depends_on()
	{
		return array(
			'\fq\boardnotices\migrations\configuration_version_1',
			'\fq\boardnotices\migrations\css_style_schema',
		);
	}

	public function effectively_installed()
	{
		return !empty($this->config['boardnotices_version']) && ($this->config['boardnotices_version'] >= 2);
	}

	public function update_data()
	{
		return array(
			array('config.update', array('boardnotices_version', $this->version)),
		);
	}

	public function revert_data()
	{
		return array(
			array('config.update', array('boardnotices_version', $this->version - 1)),
		);
	}
}

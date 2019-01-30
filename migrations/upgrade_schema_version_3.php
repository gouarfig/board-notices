<?php

/**
 *
 * Board Notices Manager extension for the phpBB Forum Software package.
 * Records the first internal version in the configuration, for upgrade purposes
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 3 (GPL-3.0)
 *
 */

namespace fq\boardnotices\migrations;

class upgrade_schema_version_3 extends \phpbb\db\migration\migration
{

	static public function depends_on()
	{
		return array(
			'\fq\boardnotices\migrations\configuration_version_2',
			'\fq\boardnotices\migrations\create_tables',
			'\fq\boardnotices\migrations\css_style_schema',
		);
	}

	public function update_schema()
	{
		return array(
			'change_columns' => array(
				$this->table_prefix . 'notices' => array(
					'message_style' => array('VCHAR:100', ''),
				),
			),
		);
	}

	public function revert_schema()
	{
		// There's no point in changing the column back to 50 characters
		return array();
	}
}

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

class upgrade_acp_module_1 extends \phpbb\db\migration\migration
{

	static public function depends_on()
	{
		return array('\fq\boardnotices\migrations\acp_module');
	}

	public function effectively_installed()
	{
		return false;
	}

	public function update_data()
	{
		return array(
			array('module.remove', array(
					'acp',
					'ACP_BOARD_NOTICES',
					array(
						'module_basename' => '\fq\boardnotices\acp\board_notices_module',
						'modes' => array('manage'),
					),
				)),
		);
	}

}

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

class acp_module extends \phpbb\db\migration\migration
{

	public function update_data()
	{
		return array(
			array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_BOARD_NOTICES')),
			array('module.add', array(
					'acp', 'ACP_BOARD_NOTICES', array(
						'module_basename' => '\fq\boardnotices\acp\board_notices_module',
						'modes' => array('manage'),
					),
				)),
		);
	}

	public function revert_data()
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
			// Remove a new category named ACP_CAT_TEST_MOD to ACP_CAT_DOT_MODS
			array('module.remove', array(
					'acp',
					'ACP_CAT_DOT_MODS',
					'ACP_BOARD_NOTICES'
				)),
		);
	}

}

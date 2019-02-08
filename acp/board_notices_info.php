<?php
/**
*
* Board Notices Manager extension for the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace fq\boardnotices\acp;

class board_notices_info
{
	const TITLE = 'ACP_BOARD_NOTICES';

	function module()
	{
		return array(
			'filename'	=> '\phpbb\boardnotices\acp\board_notices_module',
			'title'		=> TITLE,
			'modes'		=> array(
				'settings'	=> array(
					'title' => TITLE . '_SETTINGS',
					'auth' => 'ext_fq/boardnotices && acl_a_board',
					'cat' => array(TITLE)
				),
				'manage'	=> array(
					'title' => TITLE . '_MANAGE',
					'auth' => 'ext_fq/boardnotices && acl_a_board',
					'cat' => array(TITLE)
				),
			),
		);
	}
}

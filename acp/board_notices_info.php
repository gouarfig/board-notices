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

define('ACP_BOARD_NOTICES', 'ACP_BOARD_NOTICES');

class board_notices_info
{
	function module()
	{
		return array(
			'filename'	=> '\phpbb\boardnotices\acp\board_notices_module',
			'title'		=> ACP_BOARD_NOTICES,
			'modes'		=> array(
				'settings'	=> array(
					'title' => ACP_BOARD_NOTICES . '_SETTINGS',
					'auth' => 'ext_fq/boardnotices && acl_a_board',
					'cat' => array(ACP_BOARD_NOTICES)
				),
				'manage'	=> array(
					'title' => ACP_BOARD_NOTICES . '_MANAGE',
					'auth' => 'ext_fq/boardnotices && acl_a_board',
					'cat' => array(ACP_BOARD_NOTICES)
				),
			),
		);
	}
}

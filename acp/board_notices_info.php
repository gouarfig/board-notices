<?php
/**
*
* Board Notices Manager extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace fq\boardnotices\acp;

class board_notices_info
{
	function module()
	{
		return array(
			'filename'	=> '\phpbb\boardnotices\acp\board_notices_module',
			'title'		=> 'ACP_BOARD_NOTICES',
			'modes'		=> array(
				'manage'	=> array(
					'title' => 'ACP_BOARD_NOTICES_MANAGE',
					'auth' => 'ext_fq/boardnotices && acl_a_board',
					'cat' => array('ACP_BOARD_NOTICES')
				),
			),
		);
	}
}
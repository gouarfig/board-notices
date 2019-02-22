<?php

/**
 *
 * Board Notices Manager
 *
 * @version 1.0.0
 * @copyright (c) Fred Quointeau
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\service\phpbb;

class functions implements functions_interface
{
	public function confirm_box($check, $title = '', $hidden = '', $html_body = 'confirm_body.html', $u_action = '')
	{
		return confirm_box($check, $title, $hidden, $html_body, $u_action);
	}

	public function adm_back_link($u_action)
	{
		return adm_back_link($u_action);
	}
}

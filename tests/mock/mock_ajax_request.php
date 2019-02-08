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

namespace fq\boardnotices\tests\mock;

class mock_ajax_request extends \phpbb_mock_request implements \phpbb\request\request_interface
{
	public function is_ajax()
	{
		return true;
	}
}

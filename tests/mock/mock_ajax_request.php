<?php

namespace fq\boardnotices\tests\mock;

class mock_ajax_request extends \phpbb_mock_request implements \phpbb\request\request_interface
{
	public function is_ajax()
	{
		return true;
	}
}

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

namespace fq\boardnotices\tests\mock\rules;

class mock_rule_1 extends \fq\boardnotices\rules\rule_base implements \fq\boardnotices\rules\rule_interface
{
	public function __construct(\fq\boardnotices\service\serializer $serializer)
	{
		$this->serializer = $serializer;
	}

	public function getDisplayName()
	{
		return "Mock Rule 1";
	}

	public function getType()
	{
		return 'n/a';
	}

	public function isTrue($conditions)
	{
		return true;
	}

	public function getAvailableVars()
	{
		return array();
	}

}

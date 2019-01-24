<?php

/**
 *
 * Board Notices Manager
 *
 * @version 1.0.0
 * @copyright (c) 2015 Fred Quointeau
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\tests\mock_rules;

class mock_rule_2 extends \fq\boardnotices\rules\rule_base implements \fq\boardnotices\rules\rule_interface
{
	private $template_vars = array();

	public function __construct(\fq\boardnotices\service\serializer $serializer)
	{
		$this->serializer = $serializer;
	}

	private function setTemplateVars($value)
	{
		$this->template_vars = array(
			'MOCK2' => $value
		);
	}

	public function getDisplayName()
	{
		return "Mock Rule 2";
	}

	public function getDisplayUnit()
	{
		return 'Mock Unit';
	}

	public function getType()
	{
		return 'n/a';
	}

	public function getDefault()
	{
		return null;
	}

	public function getPossibleValues()
	{
		return null;
	}

	public function validateValues($values)
	{
		return true;
	}

	public function isTrue($conditions)
	{
		return true;
	}

	public function getAvailableVars()
	{
		return array('MOCK2');
	}

	public function getTemplateVars()
	{
		return $this->template_vars;
	}

}

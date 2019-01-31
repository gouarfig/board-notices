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

	/**
	 * Multiple parameters rule
	 * @overriden
	 */
	public function hasMultipleParameters()
	{
		return true;
	}

	public function getDisplayName()
	{
		return "Mock Rule 2";
	}

	public function getDisplayExplain()
	{
		return 'Mock Explanation';
	}

	public function getDisplayUnit()
	{
		return array(
			'between parameter 1 and 2',
			'after parameter 2',
		);
	}

	public function getType()
	{
		return array('list', 'int');
	}

	public function getDefault()
	{
		return array(0, 0);
	}

	public function getPossibleValues()
	{
		return array('en' => 'English', 'fr' => 'French');
	}

	public function validateValues($values)
	{
		return true;
	}

	public function isTrue($conditions)
	{
		return false;
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

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

namespace fq\boardnotices\rules;

abstract class rule_base
{
	/** @var \fq\boardnotices\service\serializer $serializer */
	protected $serializer;
	/** @var \fq\boardnotices\service\phpbb\api_interface $api */
	protected $api;

	protected $template_vars = array();

	/**
	 * Has the rule multiple entries? like: 'not visited [forum] for [n] days'
	 */
	public function hasMultipleParameters()
	{
		return is_array($this->getType());
	}

	public function getDisplayExplain()
	{
		// This is the default when no explain
		return '';
	}

	public function getDisplayUnit()
	{
		// This is the default when no unit
		return '';
	}

	public function getDefault()
	{
		// Default default value
		return null;
	}

	public function getPossibleValues()
	{
		// Default possible values
		return null;
	}

	public function validateValues($values)
	{
		// Default value validation
		return true;
	}

	/**
	 * No variable by default
	 *
	 * @return void
	 */
	public function getAvailableVars()
	{
		return array();
	}

	public function getTemplateVars()
	{
		return $this->template_vars;
	}

	protected function validateUniqueCondition($conditions = null)
	{
		$value = $this->validateConditions($conditions);
		if (is_array($value))
		{
			$value = $value[0];
		}
		return $value;
	}

	protected function validateConditions($conditions = null)
	{
		$values = $conditions;
		if (!empty($conditions) && is_string($conditions))
		{
			$values = $this->serializer->decode($conditions);
			if ($this->serializer->errorDetected())
			{
				$values = $conditions;
			}
		}
		return $values;
	}

	protected function validateArrayOfConditions($conditions = array())
	{
		$conditions = $this->validateConditions($conditions);
		if (!empty($conditions) && !is_array($conditions))
		{
			$conditions = array($conditions);
		}
		return $conditions;
	}

	protected function cleanEmptyStringsFromArray($array = array())
	{
		if (!is_array($array))
		{
			return $array;
		}

		$new_array = array();
		foreach ($array as $key => $value)
		{
			if ($value === '')
			{
				break;
			}
			$new_array[$key] = $value;
		}
		return $new_array;
	}

	protected function getArrayOfConditionsForMultipleIntegers($input)
	{
		if (empty($input))
		{
			return array();
		}
		$list = explode(',', $input);
		$list = array_map('trim', $list);
		$filtered_list = array_values(array_filter($list, 'is_numeric'));
		return array_map('intval', $filtered_list);
	}

	/**
	 * @codeCoverageIgnore
	 */
	protected function includeDisplayFunctions()
	{
		global $phpbb_root_path, $phpEx;

		include "{$phpbb_root_path}includes/functions_display.{$phpEx}";
	}

	/**
	 * @codeCoverageIgnore
	 */
	protected function includeUserFunctions()
	{
		global $phpbb_root_path, $phpEx;

		include "{$phpbb_root_path}includes/functions_user.{$phpEx}";
	}

}

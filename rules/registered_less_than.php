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

use \fq\boardnotices\service\constants;

class registered_less_than extends rule_base implements rule_interface
{
	/** @var \phpbb\user $user */
	private $user;

	public function __construct(\fq\boardnotices\service\serializer $serializer, \phpbb\user $user)
	{
		$this->serializer = $serializer;
		$this->user = $user;
	}

	public function getDisplayName()
	{
		return $this->user->lang('RULE_REGISTERED_LESS_THAN');
	}

	public function getDisplayExplain()
	{
		return '';
	}

	public function getDisplayUnit()
	{
		return $this->user->lang('RULE_DAY(S)');
	}

	public function getType()
	{
		return constants::$RULE_TYPE_INTEGER;
	}

	public function getDefault()
	{
		return 1;
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
		if (!$this->user->data['is_registered'])
		{
			return false;
		}
		$registration_date = $this->getUserRegistrationDate();
		if (empty($registration_date))
		{
			return false;
		}
		$days = $this->validateUniqueCondition($conditions);
		if (empty($days))
		{
			return false;
		}
		$diff = time() - $registration_date;
		$days_diff = floor($diff / 86400);
		// We create the template variable in all cases (so it can be displayed in preview mode)
		$this->setTemplateVars($days_diff);
		return $days_diff <= $days;
	}

	public function getAvailableVars()
	{
		return array('DAYS_SINCE_REGISTRATION');
	}

	public function getTemplateVars()
	{
		return $this->template_vars;
	}

	private function getUserRegistrationDate()
	{
		return isset($this->user->data['user_regdate']) ? $this->user->data['user_regdate'] : null;
	}

	private function setTemplateVars($days)
	{
		$this->template_vars = array(
			'DAYS_SINCE_REGISTRATION' => $days
		);
	}

}

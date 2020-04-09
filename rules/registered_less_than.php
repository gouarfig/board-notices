<?php

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class registered_less_than extends rule_base implements rule_interface
{
	public function __construct(
		\fq\boardnotices\service\serializer $serializer,
		\fq\boardnotices\service\phpbb\api_interface $api)
	{
		$this->serializer = $serializer;
		$this->api = $api;
	}

	public function getDisplayName()
	{
		return $this->api->lang('RULE_REGISTERED_LESS_THAN');
	}

	public function getDisplayUnit()
	{
		return $this->api->lang('RULE_DAY(S)');
	}

	public function getType()
	{
		return constants::$RULE_TYPE_INTEGER;
	}

	public function getDefault()
	{
		return 1;
	}

	public function isTrue($conditions)
	{
		if (!$this->api->isUserRegistered())
		{
			return false;
		}
		$registration_date = $this->api->getUserRegistrationDate();
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

	private function setTemplateVars($days)
	{
		$this->template_vars = array(
			'DAYS_SINCE_REGISTRATION' => $days
		);
	}

}

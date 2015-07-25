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

namespace fq\boardnotices\domain;

class notice
{

	private $properties = array();
	private $rules = array();
	private $template_vars = array();

	public function __construct($properties, $rules)
	{
		$this->properties = $properties;
		$this->rules = $rules;
	}

	private function validateRule($rule_details)
	{
		global $phpbb_container, $phpbb_log, $user;

		$valid = false;
		try
		{
			$rule = $phpbb_container->get("fq.boardnotices.rules.{$rule_details['rule']}");
		} catch (\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException $exc)
		{
			// There's something wrong with our installation as it can't find the object definition
			// At this point it's better to write it down in the error log and forget about notices
			$user_id = (empty($user->data)) ? ANONYMOUS : $user->data['user_id'];
			$user_ip = (empty($user->ip)) ? '' : $user->ip;
			$log_operation = "LOG_BOARD_NOTICE_ERROR";
			$additional_data = Array(
				phpbb_filter_root_path($exc->getFile()),
				$exc->getLine(),
				phpbb_filter_root_path($exc->getMessage())
			);
			$phpbb_log->add('critical', $user_id, $user_ip, $log_operation, time(), $additional_data);

			$rule = null;
		}

		if (!is_null($rule))
		{
			$valid = $rule->isTrue($rule_details['conditions']);
			if ($valid)
			{
				$this->template_vars = array_merge($this->template_vars, $rule->getTemplateVars());
			}
		}
		return $valid;
	}

	public function hasValidatedAllRules()
	{
		$valid = true;
		if (!empty($this->rules))
		{
			foreach ($this->rules as $rule)
			{
				if (!$this->validateRule($rule))
				{
					$valid = false;
					break;
				}
			}
		}
		return $valid;
	}

	public function isLastNotice()
	{
		return $this->properties['last'] ? true : false;
	}

	public function getMessage()
	{
		return $this->properties['message'];
	}

	public function getMessageUid()
	{
		return $this->properties['message_uid'];
	}

	public function getMessageBitfield()
	{
		return $this->properties['message_bitfield'];
	}

	public function getMessageOptions()
	{
		return $this->properties['message_options'];
	}

	public function getMessageBgColor()
	{
		return $this->properties['message_bgcolor'];
	}

	public function getTemplateVars()
	{
		return $this->template_vars;
	}

}

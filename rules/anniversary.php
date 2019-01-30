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

class anniversary extends rule_base implements rule_interface
{
	/** @var \phpbb\user $lang */
	private $user;
	private $template_vars = array();

	public function __construct(\fq\boardnotices\service\serializer $serializer, \phpbb\user $user)
	{
		$this->serializer = $serializer;
		$this->user = $user;
	}

	private function getUserRegistrationDate()
	{
		return isset($this->user->data['user_regdate']) ? $this->user->data['user_regdate'] : null;
	}

	private function anniversary($now, $regdate)
	{
		$anniversary = 0;
		if ($regdate['year'])
		{
			$diff = $now['mon'] - $regdate['mon'];
			if ($diff == 0)
			{
				$diff = ($now['mday'] - $regdate['mday'] < 0) ? 1 : 0;
			} else
			{
				$diff = ($diff < 0) ? 1 : 0;
			}

			$anniversary = max(0, (int) ($now['year'] - $regdate['year'] - $diff));
		}
		return $anniversary;
	}

	private function setTemplateVars($anniversary)
	{
		$this->template_vars = array(
			'ANNIVERSARY' => $anniversary
		);
	}

	public function getDisplayName()
	{
		return $this->user->lang('RULE_ANNIVERSARY');
	}

	public function getDisplayExplain()
	{
		return '';
	}

	public function getDisplayUnit()
	{
		return '';
	}

	public function getType()
	{
		return constants::$RULE_WITH_NO_TYPE;
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
		$valid = false;
		if ($this->user->data['user_type'] != USER_IGNORE)
		{
			$user_regdate = date('r', $this->getUserRegistrationDate());
			$now = $this->user->create_datetime();
			$regdate = $this->user->create_datetime($user_regdate);
			$now = getdate($now->getTimestamp());
			$regdate = getdate($regdate->getTimestamp());
			$valid = (($regdate['mday'] == $now['mday']) && ($regdate['mon'] == $now['mon']) && ($regdate['year'] < $now['year']));
			// We create the 'anniversary' variable in all cases (so it can be displayed in preview mode)
			$this->setTemplateVars($this->anniversary($now, $regdate));
		}
		return $valid;
	}

	public function getAvailableVars()
	{
		return array('ANNIVERSARY');
	}

	public function getTemplateVars()
	{
		return $this->template_vars;
	}

}

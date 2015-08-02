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

namespace fq\boardnotices\rules;

class anniversary implements rule
{

	private $user;
	private $template_vars = array();

	public function __construct(\phpbb\user $user)
	{
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
		return "On user's registration anniversary";
	}

	public function getType()
	{
		return 'n/a';
	}

	public function getPossibleValues()
	{
		return null;
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$user_regdate = date('r', $this->getUserRegistrationDate());
		$now = $this->user->create_datetime();
		$regdate = $this->user->create_datetime($user_regdate);
		$now = getdate($now->getTimestamp());
		$regdate = getdate($regdate->getTimestamp());
		$valid = (($regdate['mday'] == $now['mday']) && ($regdate['mon'] == $now['mon']) && ($regdate['year'] < $now['year']));
		// We create the 'anniversary' variable in all cases (so it can be displayed in preview mode)
		$this->setTemplateVars($this->anniversary($now, $regdate));
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

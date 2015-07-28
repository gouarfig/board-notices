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
		$aniversary = 0;
		if ($regdate['year'])
		{
			$now = $this->user->create_datetime();
			$now = phpbb_gmgetdate($now->getTimestamp() + $now->getOffset());

			$diff = $now['mon'] - $regdate['mon'];
			if ($diff == 0)
			{
				$diff = ($now['mday'] - $regdate['mday'] < 0) ? 1 : 0;
			} else
			{
				$diff = ($diff < 0) ? 1 : 0;
			}

			$aniversary = max(0, (int) ($now['year'] - $regdate['year'] - $diff));
		}
		return $aniversary;
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
		$user_regdate = $this->getUserRegistrationDate();
		$now = $this->user->create_datetime();
		$regdate = $this->user->create_datetime($user_regdate);
		$now = phpbb_gmgetdate($now->getTimestamp() + $now->getOffset());
		$regdate = phpbb_gmgetdate($regdate->getTimestamp() + $regdate->getOffset());
		$valid = (($regdate['mday'] == $now['mday']) && ($regdate['mon'] == $now['mon']));
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

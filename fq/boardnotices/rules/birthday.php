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

class birthday implements rule
{
	private $user;
	private $template_vars = array();
	
	public function __construct(\phpbb\user $user) {
		$this->user = $user;
	}
	
	private function getUserBirthday()
	{
		return isset($this->user->data['user_birthday']) ? $this->user->data['user_birthday'] : '';
	}

	private function age($bday_day, $bday_month, $bday_year)
	{
		$age = 0;
		if ($bday_year)
		{
			$now = $this->user->create_datetime();
			$now = phpbb_gmgetdate($now->getTimestamp() + $now->getOffset());

			$diff = $now['mon'] - $bday_month;
			if ($diff == 0)
			{
				$diff = ($now['mday'] - $bday_day < 0) ? 1 : 0;
			}
			else
			{
				$diff = ($diff < 0) ? 1 : 0;
			}

			$age = max(0, (int) ($now['year'] - $bday_year - $diff));
		}
		return $age;
	}
	
	private function setTemplateVars($age)
	{
		$this->template_vars = array(
			'AGE' => $age
		);
	}
	
	public function isTrue($conditions) {
		$valid = false;
		$user_birthday = $this->getUserBirthday();
		list($bday_day, $bday_month, $bday_year) = array_map('intval', explode('-', $user_birthday));
		$now = $this->user->create_datetime();
		$now = phpbb_gmgetdate($now->getTimestamp() + $now->getOffset());
		$valid = (($bday_day == $now['mday']) && ($bday_month == $now['mon']));
		if ($valid)
		{
			$this->setTemplateVars($this->age($bday_day, $bday_month, $bday_year));
		}
		return $valid;
	}
	
	public function getTemplateVars()
	{
		return $this->template_vars;
	}
}
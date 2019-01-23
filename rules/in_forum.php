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

class in_forum extends rule_base implements rule_interface
{
	/** @var \phpbb\user $lang */
	private $user;
	private $request;

	public function __construct(\fq\boardnotices\service\serializer $serializer, \phpbb\user $user, \phpbb\request\request $request)
	{
		$this->serializer = $serializer;
		$this->user = $user;
		$this->request = $request;
	}

	public function getDisplayName()
	{
		return $this->user->lang('RULE_IN_FORUM');
	}

	public function getDisplayUnit()
	{
		return '';
	}

	public function getType()
	{
		return 'forums';
	}

	public function getDefault()
	{
		return array();
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
		$current_forum_id = $this->request->variable('f', 0);

		$forums = $this->serializer->decode($conditions);
		if ($forums === false)
		{
			// There's only one group
			$forums = array((int) $conditions);
		}
		if (!empty($forums))
		{
			foreach ($forums as $forum_id)
			{
				$valid = ($current_forum_id == $forum_id);
				if ($valid)
				{
					break;
				}
			}
		}
		return $valid;
	}

	public function getAvailableVars()
	{
		return array();
	}

	public function getTemplateVars()
	{
		return array();
	}

}

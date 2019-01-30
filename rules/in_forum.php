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

class in_forum extends rule_base implements rule_interface
{
	/** @var \phpbb\user $lang */
	private $user;
	/** @var \phpbb\request\request $request */
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
		return constants::$RULE_TYPE_FORUMS;
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
		$current_forum_id = $this->request->variable('f', 0);

		$forums = $this->validateArrayOfConditions($conditions);
		$forums = $this->cleanEmptyStringsFromArray($forums);
		if (!empty($forums))
		{
			foreach ($forums as $forum_id)
			{
				if ($current_forum_id == $forum_id)
				{
					return true;
				}
			}
		}
		return false;
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

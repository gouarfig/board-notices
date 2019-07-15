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

class in_topic extends rule_base implements rule_interface
{
	/** @var \phpbb\user $user */
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
		return $this->user->lang('RULE_IN_TOPIC');
	}

	public function getDisplayExplain()
	{
		return $this->user->lang('RULE_IN_TOPIC_EXPLAIN');
	}

	public function getDisplayUnit()
	{
		return '';
	}

	public function getType()
	{
		return constants::$RULE_TYPE_MULTIPLE_INTEGERS;
	}

	public function getDefault()
	{
		return '';
	}

	public function getPossibleValues()
	{
		return null;
	}

	public function validateValues($values)
	{
		return !empty($values) && is_array($values) && !empty($values[0]);
	}

	public function isTrue($conditions)
	{
		$current_topic_id = $this->request->variable('t', 0);
		if ($current_topic_id == 0)
		{
			return false;
		}

		$topics = $this->validateArrayOfConditions($conditions);
		$topics = $this->cleanEmptyStringsFromArray($topics);
		if (!empty($topics) && is_array($topics) && !empty($topics[0]))
		{
			$topic_list = explode(',', $topics[0]);
			if (!empty($topic_list))
			{
				foreach ($topic_list as $forum_id)
				{
					if ($current_topic_id == $forum_id)
					{
						return true;
					}
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

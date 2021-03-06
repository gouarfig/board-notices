<?php

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class has_not_visited_for extends rule_base implements rule_interface
{
	/** @var \fq\boardnotices\repository\users_interface $repository */
	private $repository;

	public function __construct(
		\fq\boardnotices\service\serializer $serializer,
		\fq\boardnotices\service\phpbb\api_interface $api,
		\fq\boardnotices\repository\users_interface $repository)
	{
		$this->serializer = $serializer;
		$this->api = $api;
		$this->repository = $repository;
	}

	/**
	 * Multiple parameters rule
	 * @overriden
	 */
	public function hasMultipleParameters()
	{
		return true;
	}

	public function getDisplayName()
	{
		return $this->api->lang('RULE_HAS_NOT_VISITED_FOR_1');
	}

	public function getDisplayUnit()
	{
		return array(
			$this->api->lang('RULE_HAS_NOT_VISITED_FOR_2'),
			$this->api->lang('RULE_DAY(S)'),
		);
	}

	public function getType()
	{
		return array(
			constants::$RULE_TYPE_FORUMS,
			constants::$RULE_TYPE_INTEGER,
		);
	}

	public function getDefault()
	{
		return array(array(0), array(0));
	}

	public function getPossibleValues()
	{
		return array(null, null);
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$parameters = $this->validateConditions($conditions);
		// $conditions should be at least an array with 2 elements. If not, there's something going on
		if (!is_array($parameters) || (count($parameters) != 2))
		{
			return false;
		}
		$forums = $this->validateArrayOfConditions($parameters[0]);
		$days = $this->validateUniqueCondition($parameters[1]);
		if (empty($forums) || empty($days))
		{
			return false;
		}
		if (!$this->api->isUserRegistered())
		{
			return false;
		}
		$visits = $this->repository->getForumsLastReadTime($this->api->getUserId());
		if (empty($visits))
		{
			return false;
		}
		foreach ($forums as $forum_id)
		{
			if (!empty($visits[$forum_id]))
			{
				if ((int) $visits[$forum_id] + ($days * 24 * 60 * 60) < time())
				{
					// We can stop at the first one that hasn't been visited for n days
					return true;
				}
			}
		}
		return false;
	}

}

<?php

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class has_never_visited extends rule_base implements rule_interface
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

	public function getDisplayName()
	{
		return $this->api->lang('RULE_HAS_NEVER_VISITED');
	}

	public function getType()
	{
		return constants::$RULE_TYPE_FORUMS;
	}

	public function getDefault()
	{
		return array(0);
	}

	public function isTrue($conditions)
	{
		$forums = $this->validateArrayOfConditions($conditions);
		if (empty($forums))
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
			return true;
		}
		foreach ($forums as $forum_id)
		{
			if (!empty($visits[$forum_id]))
			{
				return false;
			}
		}
		return true;
	}

}

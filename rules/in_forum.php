<?php

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class in_forum extends rule_base implements rule_interface
{
	public function __construct(
		\fq\boardnotices\service\serializer $serializer,
		\fq\boardnotices\service\phpbb\api_interface $api)
	{
		$this->serializer = $serializer;
		$this->api = $api;
	}

	public function getDisplayName()
	{
		return $this->api->lang('RULE_IN_FORUM');
	}

	public function getType()
	{
		return constants::$RULE_TYPE_FORUMS;
	}

	public function getDefault()
	{
		return array();
	}

	public function isTrue($conditions)
	{
		$current_forum_id = $this->api->getCurrentForum();

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

}

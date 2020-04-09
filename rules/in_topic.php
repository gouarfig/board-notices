<?php

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class in_topic extends rule_base implements rule_interface
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
		return $this->api->lang('RULE_IN_TOPIC');
	}

	public function getDisplayExplain()
	{
		return $this->api->lang('RULE_IN_TOPIC_EXPLAIN');
	}

	public function getType()
	{
		return constants::$RULE_TYPE_MULTIPLE_INTEGERS;
	}

	public function getDefault()
	{
		return '';
	}

	public function validateValues($values)
	{
		return !empty($values) && is_array($values) && !empty($values[0]);
	}

	public function isTrue($conditions)
	{
		$current_topic_id = $this->api->getCurrentTopic();
		if ($current_topic_id == 0)
		{
			return false;
		}

		$topics = $this->validateUniqueCondition($conditions);
		$topic_list = $this->getArrayOfConditionsForMultipleIntegers($topics);
		if (!empty($topic_list))
		{
			foreach ($topic_list as $topic_id)
			{
				if ($current_topic_id == $topic_id)
				{
					return true;
				}
			}
		}
		return false;
	}

}

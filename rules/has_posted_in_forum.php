<?php

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class has_posted_in_forum extends rule_base implements rule_interface
{
	/** @var \fq\boardnotices\repository\users_interface $data_layer */
	private $data_layer;

	public function __construct(
		\fq\boardnotices\service\serializer $serializer,
		\fq\boardnotices\service\phpbb\api_interface $api,
		\fq\boardnotices\repository\users_interface $data_layer)
	{
		$this->serializer = $serializer;
		$this->api = $api;
		$this->data_layer = $data_layer;
	}

	public function getDisplayName()
	{
		return $this->api->lang('RULE_HAS_POSTED_IN_FORUM');
	}

	public function getDisplayExplain()
	{
		return $this->api->lang('RULE_HAS_POSTED_IN_FORUM_EXPLAIN');
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
		$valid = false;
		$forums = $this->validateArrayOfConditions($conditions);
		$forums = $this->cleanEmptyStringsFromArray($forums);
		$posts = $this->data_layer->approvedUserPosts($forums);
		$valid = ($posts > 0);
		return $valid;
	}

}

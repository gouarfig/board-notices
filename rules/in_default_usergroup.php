<?php

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class in_default_usergroup extends rule_base implements rule_interface
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
		return $this->api->lang('RULE_IN_DEFAULT_USERGROUP');
	}

	public function getType()
	{
		return constants::$RULE_TYPE_LIST;
	}

	public function getDefault()
	{
		return 0;
	}

	public function getPossibleValues()
	{
		return $this->data_layer->getAllGroups();
	}

	public function isTrue($conditions)
	{
		$group_id = $this->validateUniqueCondition($conditions);
		return $this->api->getUserDefaultGroupId() == $group_id;
	}

	public function getAvailableVars()
	{
		return array('GROUPID', 'GROUPNAME');
	}

	public function getTemplateVars()
	{
		// @codeCoverageIgnoreStart
		if (!function_exists('get_group_name'))
		{
			$this->includeUserFunctions();
		}
		// @codeCoverageIgnoreEnd
		return array(
			'GROUPID' => $this->api->getUserDefaultGroupId(),
			'GROUPNAME' => $this->api->getGroupName($this->api->getUserDefaultGroupId()),
		);
	}

}

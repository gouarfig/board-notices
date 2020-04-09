<?php

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class in_usergroup extends rule_base implements rule_interface
{
	/** @var \fq\boardnotices\repository\apis_interface $data_layer */
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
		return $this->api->lang('RULE_IN_USERGROUP');
	}

	public function getType()
	{
		return constants::$RULE_TYPE_MULTIPLE_CHOICE;
	}

	public function getDefault()
	{
		return array();
	}

	public function getPossibleValues()
	{
		return $this->data_layer->getAllGroups();
	}

	public function isTrue($conditions)
	{
		$groups = $this->validateArrayOfConditions($conditions);
		$groups = $this->cleanEmptyStringsFromArray($groups);
		if (!empty($groups))
		{
			foreach ($groups as $group_id)
			{
				if ($this->data_layer->isUserInGroupId($group_id))
				{
					return true;
				}
			}
		}
		return false;
	}

}

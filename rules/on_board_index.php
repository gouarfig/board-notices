<?php

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class on_board_index extends rule_base implements rule_interface
{
	/** @var \phpbb\template\template $template */
	private $template;

	public function __construct(
		\fq\boardnotices\service\serializer $serializer,
		\fq\boardnotices\service\phpbb\api_interface $api,
		\phpbb\template\template $template)
	{
		$this->serializer = $serializer;
		$this->api = $api;
		$this->template = $template;
	}

	public function getDisplayName()
	{
		return $this->api->lang('RULE_ON_BOARD_INDEX');
	}

	public function getType()
	{
		return constants::$RULE_TYPE_YESNO;
	}

	public function getDefault()
	{
		return false;
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$on_board_index_conditions = $this->validateUniqueCondition($conditions);
		$on_board_index = ($this->template->retrieve_var('S_INDEX') === true);
		$valid = ($on_board_index_conditions && $on_board_index) || (!$on_board_index_conditions && !$on_board_index);
		return $valid;
	}

}

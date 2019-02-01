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

class on_board_index extends rule_base implements rule_interface
{
	/** @var \phpbb\user $user */
	private $user;
	/** @var \phpbb\template\template $template */
	private $template;

	public function __construct(\fq\boardnotices\service\serializer $serializer, \phpbb\user $user, \phpbb\template\template $template)
	{
		$this->serializer = $serializer;
		$this->user = $user;
		$this->template = $template;
	}

	public function getDisplayName()
	{
		return $this->user->lang('RULE_ON_BOARD_INDEX');
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
		return constants::$RULE_TYPE_YESNO;
	}

	public function getDefault()
	{
		return false;
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
		$valid = false;
		$on_board_index_conditions = $this->validateUniqueCondition($conditions);
		$on_board_index = ($this->template->retrieve_var('S_INDEX') === true);
		$valid = ($on_board_index_conditions && $on_board_index) || (!$on_board_index_conditions && !$on_board_index);
		return $valid;
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

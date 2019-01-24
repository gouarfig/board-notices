<?php

/**
 *
 * Board Notices Manager
 *
 * @version 1.0.0
 * @copyright (c) 2015 Fred Quointeau
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\rules;

class in_usergroup extends rule_base implements rule_interface
{
	/** @var \fq\boardnotices\service\constants $constants */
	private $constants;
	/** @var \phpbb\user $lang */
	private $user;
	/** @var \fq\boardnotices\repository\legacy_interface $data_layer */
	private $data_layer;

	public function __construct(\fq\boardnotices\service\constants $constants, \fq\boardnotices\service\serializer $serializer, \phpbb\user $user, \fq\boardnotices\repository\legacy_interface $data_layer)
	{
		$this->constants = $constants;
		$this->serializer = $serializer;
		$this->user = $user;
		$this->data_layer = $data_layer;
	}

	public function getDisplayName()
	{
		return $this->user->lang('RULE_IN_USERGROUP');
	}

	public function getDisplayUnit()
	{
		return '';
	}

	public function getType()
	{
		return 'multiple choice';
	}

	public function getDefault()
	{
		return array();
	}

	public function getPossibleValues()
	{
		return $this->data_layer->getAllGroups();
	}

	public function validateValues($values)
	{
		return true;
	}

	public function isTrue($conditions)
	{
		$valid = false;

		$groups = $this->serializer->decode($conditions);
		if ($groups === false)
		{
			// There's only one group
			$groups = array((int) $conditions);
		}
		if (!empty($groups))
		{
			foreach ($groups as $group_id)
			{
				if ($this->data_layer->isUserInGroupId($group_id))
				{
					$valid = true;
					break;
				}
			}
		}
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

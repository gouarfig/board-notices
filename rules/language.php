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

class language extends rule_base implements rule_interface
{

	private $user;
	private $data_layer;

	public function __construct(\phpbb\user $user, \fq\boardnotices\repository\legacy_interface $data_layer)
	{
		$this->user = $user;
		$this->data_layer = $data_layer;
	}

	public function getDisplayName()
	{
		return "User language is either one of these selected languages";
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
		return $this->data_layer->getLanguages();
	}

	public function validateValues($values)
	{
		return true;
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$languages = @unserialize($conditions);
		if ($languages === false)
		{
			// There's only one language
			$languages = array($conditions);
		}
		if (!empty($languages))
		{
			foreach ($languages as $language_id)
			{
				$valid = ($this->user->data['user_lang'] == $language_id);
				if ($valid)
				{
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

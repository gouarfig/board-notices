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

class language implements rule
{

	private $user;

	public function __construct(\phpbb\user $user)
	{
		$this->user = $user;
	}

	public function getDisplayName()
	{
		return "User language is";
	}

	public function getType()
	{
		return 'multiple choice';
	}

	public function getPossibleValues()
	{
		$data_layer = $this->getDataLayer();
		return $data_layer->getLanguages();
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
				if (!$valid)
				{
					break;
				}
			}
		}
		return $valid;
	}

	public function getTemplateVars()
	{
		return array();
	}

	protected function getDataLayer()
	{
		global $phpbb_container;
		static $data_layer = null;

		if (is_null($data_layer))
		{
			$data_layer = $phpbb_container->get('fq.boardnotices.datalayer');
		}
		return $data_layer;
	}

}

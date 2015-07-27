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

class style implements rule
{
	private $user;
	private $data_layer;

	public function __construct(\phpbb\user $user, \fq\boardnotices\datalayer $data_layer)
	{
		$this->user = $user;
		$this->data_layer = $data_layer;
	}

	public function getDisplayName()
	{
		return "User style is either one of these selected styles";
	}

	public function getType()
	{
		return 'multiple choice';
	}

	public function getPossibleValues()
	{
		return $this->data_layer->getStyles();
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$styles = @unserialize($conditions);
		if ($styles === false)
		{
			// There's only one style
			$styles = array((int) $conditions);
		}
		if (!empty($styles))
		{
			foreach ($styles as $style_id)
			{
				if ($this->user->data['user_style'] == $style_id)
				{
					$valid = true;
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

}

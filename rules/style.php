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
	private $request;
	private $config;

	public function __construct(\phpbb\user $user, \fq\boardnotices\datalayer $data_layer, \phpbb\request\request $request, \phpbb\config\config $config)
	{
		$this->user = $user;
		$this->data_layer = $data_layer;
		$this->request = $request;
		$this->config = $config;
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

		$user_style = null;
		if ($this->user->data['user_id'] == ANONYMOUS)
		{
			$user_style = $this->request_cookie(intval($this->user->data['user_style']));
		}
		if (empty($user_style))
		{
			$user_style = $this->user->data['user_style'];
		}

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
				if ($user_style == $style_id)
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

	private function request_cookie($default = null)
	{
		$name = $this->config['cookie_name'] . '_style';

		return $this->request->variable($name, $default, false, \phpbb\request\request_interface::COOKIE);
	}

}

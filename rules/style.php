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

class style extends rule_base implements rule_interface
{
	/** @var \phpbb\user $user */
	private $user;
	/** @var \fq\boardnotices\repository\users_interface $data_layer */
	private $data_layer;
	private $request;
	private $config;

	public function __construct(\fq\boardnotices\service\serializer $serializer, \phpbb\user $user, \fq\boardnotices\repository\users_interface $data_layer, \phpbb\request\request $request, \phpbb\config\config $config)
	{
		$this->serializer = $serializer;
		$this->user = $user;
		$this->data_layer = $data_layer;
		$this->request = $request;
		$this->config = $config;
	}

	public function getDisplayName()
	{
		return $this->user->lang('RULE_STYLE');
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
		return $this->data_layer->getStyles();
	}

	public function isTrue($conditions)
	{
		$valid = false;

		$user_style = null;
		if ($this->user->data['user_id'] == ANONYMOUS)
		{
			$user_style = $this->getStyleFromCookie();
		}
		if (empty($user_style))
		{
			$user_style = $this->user->data['user_style'];
		}

		$styles = $this->validateArrayOfConditions($conditions);
		$styles = $this->cleanEmptyStringsFromArray($styles);
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

	private function getStyleFromCookie($default = null)
	{
		$name = $this->config['cookie_name'] . '_style';

		return $this->request->variable($name, $default, false, \phpbb\request\request_interface::COOKIE);
	}

}

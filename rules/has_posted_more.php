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

class has_posted_more extends rule_base implements rule_interface
{
	public function __construct(
		\fq\boardnotices\service\serializer $serializer,
		\fq\boardnotices\service\phpbb\api_interface $api)
	{
		$this->serializer = $serializer;
		$this->api = $api;
	}

	public function getDisplayName()
	{
		return $this->api->lang('RULE_HAS_POSTED_MORE');
	}

	public function getType()
	{
		return constants::$RULE_TYPE_INTEGER;
	}

	public function getDefault()
	{
		return 0;
	}

	public function isTrue($conditions)
	{
		$posts = $this->validateUniqueCondition($conditions);
		return ($this->api->getUserPostCount() >= $posts);
	}

	public function getAvailableVars()
	{
		return array('POSTS');
	}

	public function getTemplateVars()
	{
		return array('POSTS' => $this->api->getUserPostCount());
	}

}

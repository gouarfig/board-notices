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

class has_never_posted extends rule_base implements rule_interface
{
	/** @var \fq\boardnotices\repository\users_interface $data_layer */
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
		return $this->api->lang('RULE_HAS_NEVER_POSTED');
	}

	public function getDisplayExplain()
	{
		return $this->api->lang('RULE_HAS_NEVER_POSTED_EXPLAIN');
	}

	public function getType()
	{
		return constants::$RULE_WITH_NO_TYPE;
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$posts = $this->data_layer->nonDeletedUserPosts();
		$valid = ($posts == 0);
		return $valid;
	}

}

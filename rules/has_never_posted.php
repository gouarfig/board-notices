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
	/** @var \phpbb\user $user */
	private $user;
	/** @var \fq\boardnotices\repository\users_interface $data_layer */
	private $data_layer;

	public function __construct(\fq\boardnotices\service\serializer $serializer, \phpbb\user $user, \fq\boardnotices\repository\users_interface $data_layer)
	{
		$this->serializer = $serializer;
		$this->user = $user;
		$this->data_layer = $data_layer;
	}

	public function getDisplayName()
	{
		return $this->user->lang('RULE_HAS_NEVER_POSTED');
	}

	public function getDisplayExplain()
	{
		return $this->user->lang('RULE_HAS_NEVER_POSTED_EXPLAIN');
	}

	public function getType()
	{
		return constants::$RULE_WITH_NO_TYPE;
	}

	public function getDefault()
	{
		return null;
	}

	public function getPossibleValues()
	{
		return null;
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$posts = $this->data_layer->nonDeletedUserPosts();
		$valid = ($posts == 0);
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

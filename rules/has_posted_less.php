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

class has_posted_less extends rule_base implements rule_interface
{
	/** @var \phpbb\user $lang */
	private $user;

	public function __construct(\fq\boardnotices\service\serializer $serializer, \phpbb\user $user)
	{
		$this->serializer = $serializer;
		$this->user = $user;
	}

	public function getDisplayName()
	{
		return $this->user->lang('RULE_HAS_POSTED_LESS');
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
		return constants::$RULE_TYPE_INTEGER;
	}

	public function getDefault()
	{
		return 0;
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
		$posts = $this->validateUniqueCondition($conditions);
		return ($this->user->data['user_posts'] <= $posts);
	}

	public function getAvailableVars()
	{
		return array('POSTS');
	}

	public function getTemplateVars()
	{
		return array('POSTS' => $this->user->data['user_posts']);
	}

}

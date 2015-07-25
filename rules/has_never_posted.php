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

class has_never_posted implements rule
{

	private $user;

	public function __construct(\phpbb\user $user)
	{
		$this->user = $user;
	}

	public function getDisplayName()
	{
		return "User has never posted";
	}

	public function getType()
	{
		return 'n/a';
	}

	public function getPossibleValues()
	{
		return null;
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$data_layer = $this->getDataLayer();
		$posts = $data_layer->nonDeletedUserPosts();
		$valid = ($posts == 0);
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

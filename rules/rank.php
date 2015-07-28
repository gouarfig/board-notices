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

class rank implements rule
{
	private $user;
	private $data_layer;

	public function __construct(\phpbb\user $user, \fq\boardnotices\datalayer $data_layer)
	{
		$this->user = $user;
		$this->data_layer = $data_layer;
	}

	private function calculateUserRank($user_posts)
	{
		global $cache;
		$user_rank = 0;
		$ranks = $cache->obtain_ranks();

		if (!empty($ranks['normal']))
		{
			foreach ($ranks['normal'] as $rank_id => $rank)
			{
				if ($user_posts >= $rank['rank_min'])
				{
					$user_rank = $rank_id;
					break;
				}
			}
		}
		return $user_rank;
	}

	public function getDisplayName()
	{
		return "User rank is any of these selected ranks";
	}

	public function getType()
	{
		return 'multiple choice';
	}

	public function getPossibleValues()
	{
		return $this->data_layer->getRanks();
	}

	public function isTrue($conditions)
	{
		$valid = false;

		$user_rank = (int) $this->user->data['user_rank'];
		if ($user_rank == 0)
		{
			$user_rank = $this->calculateUserRank($this->user->data['user_posts']);
		}
		$ranks = @unserialize($conditions);
		if ($ranks === false)
		{
			// There's only one rank
			$ranks = array((int) $conditions);
		}
		if (!empty($ranks))
		{
			foreach ($ranks as $rank_id)
			{
				$valid = ($user_rank == $rank_id);
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
		return array('RANK');
	}

	public function getTemplateVars()
	{
		$user_rank = phpbb_get_user_rank($this->user->data, ($this->user->data['user_id'] == ANONYMOUS) ? false : $this->user->data['user_posts']);
		return array(
			'RANK' => $user_rank['title'],
		);
	}
}

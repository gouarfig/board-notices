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

class rank extends rule_base implements rule_interface
{
	/** @var \fq\boardnotices\repository\users_interface $data_layer */
	private $data_layer;
	/** @var \phpbb\cache\service $cache */
	private $cache;
	private $user_rank = null;

	public function __construct(
		\fq\boardnotices\service\serializer $serializer,
		\fq\boardnotices\service\phpbb\api_interface $api,
		\fq\boardnotices\repository\users_interface $data_layer,
		\phpbb\cache\service $cache)
	{
		$this->serializer = $serializer;
		$this->api = $api;
		$this->data_layer = $data_layer;
		$this->cache = $cache;
	}

	private function calculateUserRank($user_posts)
	{
		$user_rank = 0;
		$ranks = $this->cache->obtain_ranks();

		if (!empty($ranks['normal']))
		{
			foreach ($ranks['normal'] as $rank_id => $rank)
			{
				// The query is ordering them by rank_min DESC
				if ($user_posts >= $rank['rank_min'])
				{
					$user_rank = $rank_id;
					break;
				}
			}
		}
		return $user_rank;
	}

	private function getUserRank()
	{
		if (is_null($this->user_rank))
		{
			// This is for a special rank
			$this->user_rank = (int) $this->api->getUserRankId();
			if ($this->user_rank == 0)
			{
				// If no special rank, there might be a calculated one (normal rank)
				$this->user_rank = $this->calculateUserRank($this->api->getUserPostCount());
			}
		}
		return $this->user_rank;
	}

	public function getDisplayName()
	{
		return $this->api->lang('RULE_RANK');
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
		return $this->data_layer->getRanks();
	}

	public function isTrue($conditions)
	{
		$valid = false;

		$user_rank = $this->getUserRank();
		$ranks = $this->validateArrayOfConditions($conditions);
		$ranks = $this->cleanEmptyStringsFromArray($ranks);
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
		return array('RANKID', 'RANK');
	}

	public function getTemplateVars()
	{
		return array(
			'RANKID' => $this->getUserRank(),
			'RANK' => $this->api->getUserRankIdTitle(),
		);
	}

}

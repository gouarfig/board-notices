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

use \fq\boardnotices\service\constants;

class rank extends rule_base implements rule_interface
{
	/** @var \phpbb\user $lang */
	private $user;
	/** @var \fq\boardnotices\repository\legacy_interface $data_layer */
	private $data_layer;
	private $user_rank = null;

	public function __construct(\fq\boardnotices\service\serializer $serializer, \phpbb\user $user, \fq\boardnotices\repository\legacy_interface $data_layer)
	{
		$this->serializer = $serializer;
		$this->user = $user;
		$this->data_layer = $data_layer;
	}

	private function calculateUserRank($user_posts)
	{
		/** @var \phpbb\cache\service $cache */
		global $cache;
		$user_rank = 0;
		$ranks = $cache->obtain_ranks();

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
			$this->user_rank = (int) $this->user->data['user_rank'];
			if ($this->user_rank == 0)
			{
				// If no special rank, there might be a calculated one (normal rank)
				$this->user_rank = $this->calculateUserRank($this->user->data['user_posts']);
			}
		}
		return $this->user_rank;
	}

	public function getDisplayName()
	{
		return $this->user->lang('RULE_RANK');
	}

	public function getDisplayUnit()
	{
		return '';
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

	public function validateValues($values)
	{
		return true;
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
		// @codeCoverageIgnoreStart
		if (!function_exists('phpbb_get_user_rank'))
		{
			$this->includeDisplayFunctions();
		}
		// @codeCoverageIgnoreEnd
		$user_rank = phpbb_get_user_rank($this->user->data, ($this->user->data['user_id'] == ANONYMOUS) ? false : $this->user->data['user_posts']);
		return array(
			'RANKID' => $this->getUserRank(),
			'RANK' => $user_rank['title'],
		);
	}

}

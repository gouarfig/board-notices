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

namespace fq\boardnotices\repository;

interface notices_seen_interface
{
	function getDismissedNotices($user_id);
	function setNoticeDismissed($notice_id, $user_id);
}

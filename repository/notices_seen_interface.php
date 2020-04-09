<?php

namespace fq\boardnotices\repository;

interface notices_seen_interface
{
	function getDismissedNotices($user_id);
	function setNoticeDismissed($notice_id, $user_id);
}

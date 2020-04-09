<?php

namespace fq\boardnotices\controller;

use fq\boardnotices\service\constants;

define('NO_AUTH_OPERATION', 'NO_AUTH_OPERATION');

class controller
{
	/** @var \phpbb\config\config $config */
	private $config;

	/** @var \phpbb\request\request_interface $request */
	private $request;

	/** @var \phpbb\user $user */
	private $user;

	/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
	private $notices_repository;

	/** @var \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository */
	private $notices_seen_repository;

	/**
	* Constructor
	*
	* @param \phpbb\config\config $config
	* @param \phpbb\request\request_interface $request
	* @param \phpbb\user $user
	* @param \fq\boardnotices\repository\notices_interface $notices_repository
	* @param \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository
	* @access public
	*/
	public function __construct(
		\phpbb\config\config $config,
		\phpbb\request\request_interface $request,
		\phpbb\user $user,
		\fq\boardnotices\repository\notices_interface $notices_repository,
		\fq\boardnotices\repository\notices_seen_interface $notices_seen_repository
	)
	{
		$this->config = $config;
		$this->request = $request;
		$this->user = $user;
		$this->notices_repository = $notices_repository;
		$this->notices_seen_repository = $notices_seen_repository;
	}

	/**
	* Board Notices controller accessed from the URL /boardnotices/close
	*
	* @throws \phpbb\exception\http_exception An http exception
	* @return \Symfony\Component\HttpFoundation\JsonResponse A Symfony JSON Response object
	*/
	public function close_notice()
	{
		// Check the notice_id first
		$notice_id = (int) $this->request->variable('notice_id', 0);
		if (empty($notice_id))
		{
			throw new \phpbb\exception\http_exception(403, NO_AUTH_OPERATION);
		}
		$notice = $this->notices_repository->getNoticeFromId($notice_id);
		if (empty($notice))
		{
			throw new \phpbb\exception\http_exception(403, NO_AUTH_OPERATION);
		}
		// Check the link hash to protect against CSRF/XSRF attacks
		if (!$notice['dismissable'] || !check_link_hash($this->request->variable('hash', ''), constants::$ROUTING_CLOSE_HASH_ID))
		{
			throw new \phpbb\exception\http_exception(403, NO_AUTH_OPERATION);
		}

		// Allow the notice to disappear in preview mode
		$response = true;

		if (!$this->request->variable('preview', 0))
		{
			if ($this->user->data['is_registered'])
			{
				// Close the notice for registered users
				$response = $this->update_board_notice_status($notice_id, $this->user->data['user_id']);
			}
			else
			{
				// Set a cookie for guests
				$response = $this->set_board_notice_cookie($notice_id, $notice['reset_after']);
			}
		}

		// Send a JSON response if an AJAX request was used
		if ($this->request->is_ajax())
		{
			return new \Symfony\Component\HttpFoundation\JsonResponse(array(
				'success' => $response,
			));
		}

		// Redirect the user back to their last viewed page (non-AJAX requests)
		$redirect = $this->request->variable('redirect', $this->user->data['session_page']);
		$redirect = reapply_sid($redirect);
		redirect($redirect);
	}

	/**
	* Set a cookie to keep the notice closed
	*
	* @return bool True
	*/
	private function set_board_notice_cookie($notice_id, $reset_after)
	{
		$notice_id = (int) $notice_id;
		$reset_after = (int) $reset_after;

		if (empty($reset_after))
		{
			$expiry = '+1 year';
		}
		else
		{
			$expiry = "+{$reset_after} day";
		}
		// Store the notice id in a cookie
		$this->user->set_cookie("bnd_{$notice_id}", $notice_id, strtotime($expiry));

		return true;
	}

	/**
	* Close the notice for a registered user
	*
	* @return bool True if successful, false otherwise
	*/
	private function update_board_notice_status($notice_id, $user_id)
	{
		return $this->notices_seen_repository->setNoticeDismissed($notice_id, $user_id);
	}
}

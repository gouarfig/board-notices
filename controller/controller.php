<?php
/**
*
* Board Notices extension for the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace fq\boardnotices\controller;

use fq\boardnotices\service\constants;

class controller
{
	/** @var \phpbb\config\config */
	private $config;

	/** @var \phpbb\request\request */
	private $request;

	/** @var \phpbb\user */
	private $user;

	/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
	private $notices_repository;

	/** @var \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository */
	private $notices_seen_repository;

	/**
	* Constructor
	*
	* @param \phpbb\config\config                $config         Config object
	* @param \phpbb\request\request              $request        Request object
	* @param \phpbb\user                         $user           User object
	* @param \fq\boardnotices\repository\notices_interface $notices_repository
	* @param \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository
	* @access public
	*/
	public function __construct(
		\phpbb\config\config $config,
		\phpbb\request\request $request,
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
	* @access public
	*/
	public function close_notice()
	{
		// Check the notice_id first
		$notice_id = (int) $this->request->variable('notice_id', 0);
		if (empty($notice_id))
		{
			throw new \phpbb\exception\http_exception(403, 'NO_AUTH_OPERATION');
		}
		$notice = $this->notices_repository->getNoticeFromId($notice_id);
		if (empty($notice))
		{
			throw new \phpbb\exception\http_exception(403, 'NO_AUTH_OPERATION');
		}
		// Check the link hash to protect against CSRF/XSRF attacks
		if (!$notice['dismissable'] || !check_link_hash($this->request->variable('hash', ''), constants::$ROUTING_CLOSE_HASH_ID))
		{
			throw new \phpbb\exception\http_exception(403, 'NO_AUTH_OPERATION');
		}

		// Do nothing in preview mode
		if (empty($this->request->variable('preview', 0)))
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
		else
		{
			// Allow the notice to disappear in preview mode
			$response = true;
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

		// We shouldn't get here, but throw an http exception just in case
		throw new \phpbb\exception\http_exception(500, 'GENERAL_ERROR');
	}

	/**
	* Set a cookie to keep the notice closed
	*
	* @return bool True
	* @access private
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
	* @access private
	*/
	private function update_board_notice_status($notice_id, $user_id)
	{
		return $this->notices_seen_repository->setNoticeDismissed($notice_id, $user_id);
	}
}

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

class controller
{
	/** @var \phpbb\config\config */
	private $config;

	/** @var \phpbb\request\request */
	private $request;

	/** @var \phpbb\user */
	private $user;

	/** @var \fq\boardnotices\repository\boardnotices_interface $repository */
	private $repository;

	/**
	* Constructor
	*
	* @param \phpbb\config\config                $config         Config object
	* @param \phpbb\request\request              $request        Request object
	* @param \phpbb\user                         $user           User object
	* @param \fq\boardnotices\repository\boardnotices_interface $repository
	* @access public
	*/
	public function __construct(
		\phpbb\config\config $config,
		\phpbb\request\request $request,
		\phpbb\user $user,
		\fq\boardnotices\repository\boardnotices_interface $repository
	)
	{
		$this->config = $config;
		$this->request = $request;
		$this->user = $user;
		$this->repository = $repository;
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
		// Check the link hash to protect against CSRF/XSRF attacks
		if (!$this->config['board_notices_dismiss'] || !check_link_hash($this->request->variable('hash', ''), 'close_boardnotice'))
		{
			throw new \phpbb\exception\http_exception(403, 'NO_AUTH_OPERATION');
		}

		// Set a cookie
		$response = $this->set_board_notice_cookie();

		// Close the notice for registered users
		if ($this->user->data['is_registered'])
		{
			$response = $this->update_board_notice_status();
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
	private function set_board_notice_cookie()
	{
		// Get board notice data from the DB text object
		// $notice_timestamp = $this->config_text->get('notice_timestamp');

		// Store the notice timestamp/id in a cookie with a 1 year expiration
		// $this->user->set_cookie('baid', $notice_timestamp, strtotime('+1 year'));

		return true;
	}

	/**
	* Close the notice for a registered user
	*
	* @return bool True if successful, false otherwise
	* @access private
	*/
	private function update_board_notice_status()
	{
		// Set notice status to 0 for registered user
		// $sql = 'UPDATE ' . USERS_TABLE . '
		// 	SET board_notices_status = 0
		// 	WHERE user_id = ' . (int) $this->user->data['user_id'] . '
		// 	AND user_type <> ' . USER_IGNORE;
		// $this->db->sql_query($sql);

		// return (bool) $this->db->sql_affectedrows();
	}
}

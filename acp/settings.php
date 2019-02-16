<?php

/**
 *
 * Board Notices Manager extension for the phpBB Forum Software package.
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\acp;

use \fq\boardnotices\service\constants;

class settings
{
	/** @var \fq\boardnotices\service\phpbb\api_interface $api */
	private $api;

	/** @var \fq\boardnotices\service\phpbb\functions_interface $functions */
	private $functions;

	/** @var \phpbb\request\request_interface $request */
	private $request;

	/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
	private $notices_repository;

	public function __construct(
		\fq\boardnotices\service\phpbb\api_interface $api,
		\fq\boardnotices\service\phpbb\functions_interface $functions,
		\phpbb\request\request_interface $request,
		\fq\boardnotices\repository\notices_interface $notices_repository
	)
	{
		$this->api = $api;
		$this->functions = $functions;
		$this->request = $request;
		$this->notices_repository = $notices_repository;
	}

	public function resetForumVisits($id, $mode, $action)
	{
		// Add the board notices ACP lang file
		$this->api->addAdminLanguage();

		// Asks for confirmation first
		if (!$this->functions->confirm_box(true))
		{
			$this->functions->confirm_box(
				false,
				$this->api->lang('RESET_FORUM_VISITS_CONFIRMATION'),
				build_hidden_fields(array(
					'i'			=> $id,
					'mode'		=> $mode,
					'action'	=> $action,
				))
			);
		}
		else
		{
			$this->notices_repository->clearForumVisited();

			if ($this->request->is_ajax())
			{
				trigger_error('RESET_FORUM_VISITS_SUCCESS');
			}
		}
	}
}

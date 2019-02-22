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

	/** @var \phpbb\config\config $config */
	private $config;

	/** @var \phpbb\log\log_interface $log */
	private $log;

	/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
	private $notices_repository;

	public function __construct(
		\fq\boardnotices\service\phpbb\api_interface $api,
		\fq\boardnotices\service\phpbb\functions_interface $functions,
		\phpbb\request\request_interface $request,
		\phpbb\config\config $config,
		\phpbb\log\log_interface $log,
		\fq\boardnotices\repository\notices_interface $notices_repository
	)
	{
		$this->api = $api;
		$this->functions = $functions;
		$this->request = $request;
		$this->config = $config;
		$this->log = $log;
		$this->notices_repository = $notices_repository;
	}

	public function resetForumVisits($id, $mode, $action)
	{
		if (!$this->functions->confirm_box(true))
		{
			// Asks for confirmation first
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


	public function loadSettings()
	{
		return array(
			'boardnotices_enabled' => $this->config[constants::$CONFIG_ENABLED] ? true : false,
			'track_forums_visits' => $this->config[constants::$CONFIG_TRACK_FORUMS_VISITS] ? true : false,
			'boardnotices_default_bgcolor' => $this->config[constants::$CONFIG_DEFAULT_BGCOLOR],
		);
	}

	public function saveSettings($data)
	{
		// Save data to the config
		$this->config->set(constants::$CONFIG_ENABLED, ($data['boardnotices_enabled'] ? true : false));
		$this->config->set(constants::$CONFIG_TRACK_FORUMS_VISITS, ($data['track_forums_visits'] ? true : false));
		$this->config->set(constants::$CONFIG_DEFAULT_BGCOLOR, $data['boardnotices_default_bgcolor']);

		// Logs the settings update
		$this->log->add('admin', $this->api->getUserId(), $this->api->getUserIpAddress(), 'LOG_BOARD_NOTICES_SETTINGS', time(), array());
	}

	public function loadNotices()
	{
		$rawNotices = $this->notices_repository->getAllNotices();
		$notices = [];
		foreach ($rawNotices as $notice)
		{
			$rules = $this->notices_repository->getRulesFor($notice['notice_id']);
			$notice['rulesCount'] = count($rules);
			$notices[] = $notice;
		}
		return $notices;
	}
}

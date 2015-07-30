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

namespace fq\boardnotices\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{

	protected $user = null;
	protected $config = null;
	protected $template = '';
	protected $request;
	protected $data_layer;

	static public function getSubscribedEvents()
	{
		return array(
			'core.page_header_after' => 'display_board_notices',
		);
	}

	/**
	 * Constructor
	 *
	 * @param \phpbb\user $user
	 */
	public function __construct(\phpbb\user $user, \phpbb\config\config $config, \phpbb\template\template $template, \phpbb\request\request $request, \fq\boardnotices\datalayer $data_layer)
	{
		$this->user = $user;
		$this->config = $config;
		$this->template = $template;
		$this->request = $request;
		$this->data_layer = $data_layer;
	}

	/**
	 * Display board notices
	 *
	 * @return null
	 * @access public
	 */
	public function display_board_notices()
	{
		$notices = array();
		$template_vars = $this->getDefaultTemplateVars();

		$preview_key = $this->request->variable('bnpk', '');
		$preview_id = $this->request->variable('bnid', 0);
		if (!empty($preview_key) && !empty($preview_id) && ($preview_key == $this->config['boardnotices_previewkey']))
		{
			// Force the preview of a notice
			$raw_notice = $this->data_layer->getNoticeFromId($preview_id);
			$rules = $this->data_layer->getRulesFor($preview_id);
			$notices[] = new \fq\boardnotices\domain\notice($raw_notice, $rules);
			unset($rules);
			$force_all_rules = true;
			$preview = true;
		}
		else
		{
			// Normal notices mode
			$raw_notices = $this->data_layer->getActiveNotices();
			foreach ($raw_notices as $raw_notice)
			{
				$rules = $this->data_layer->getRulesFor($raw_notice['notice_id']);
				$notices[] = new \fq\boardnotices\domain\notice($raw_notice, $rules);
				unset($rules);
			}
			unset($raw_notices);
			$force_all_rules = false;
			$preview = false;
		}

		$notice_message = '';
		$notice_bgcolor = '';

		foreach ($notices as $notice)
		{
			if ($notice->hasValidatedAllRules($force_all_rules, $preview))
			{
				// Prepare board announcement message for display
				$notice_message = generate_text_for_display(
						$notice->getMessage(), $notice->getMessageUid(), $notice->getMessageBitfield(), $notice->getMessageOptions()
				);
				$notice_bgcolor = $notice->getMessageBgColor();
				$template_vars = array_merge($template_vars, $notice->getTemplateVars());
				break;
			}
		}

		if (!empty($notice_message))
		{
			$notice_message = $this->setTemplateVars($notice_message, $template_vars);

			// Output board announcement to the template
			$this->template->assign_vars(array(
				'S_BOARD_NOTICE' => true,
				'BOARD_NOTICE' => $notice_message,
				'BOARD_NOTICE_BGCOLOR' => $notice_bgcolor,
			));
		}
	}

	private function getDefaultTemplateVars()
	{
		$template_vars = array(
			'SESSIONID' => $this->user->data['session_id'],
			'USERID' => $this->user->data['user_id'],
			'USERNAME' => $this->user->data['username'],
			'LASTVISIT' => $this->user->format_date($this->user->data['user_lastvisit']),
			'LASTPOST' => $this->user->format_date($this->user->data['user_lastpost_time']),
			'REGISTERED' => $this->user->format_date($this->user->data['user_regdate']),
		);
		return $template_vars;
	}

	private function setTemplateVars($notice_message, $template_vars)
	{
		if (!empty($template_vars))
		{
			foreach ($template_vars as $key => $value)
			{
				$notice_message = str_replace('{' . $key . '}', $value, $notice_message);
			}
		}
		return $notice_message;
	}

}

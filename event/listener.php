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
use fq\boardnotices\domain\notice;
use fq\boardnotices\service\constants;

class listener implements EventSubscriberInterface
{
	/** @var \phpbb\user $user */
	private $user = null;
	/** @var \phpbb\config\config $config */
	private $config = null;
	/** @var \phpbb\template\template $template */
	private $template = '';
	/** @var \phpbb\request\request $request */
	private $request;
	/** @var \phpbb\controller\helper $controller_helper */
	private $controller_helper;
	/** @var \phpbb\language\language $language */
	private $language;
	/** @var \fq\boardnotices\repository\boardnotices_interface $repository */
	private $repository;

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
	public function __construct(
		\phpbb\user $user,
		\phpbb\config\config $config,
		\phpbb\template\template $template,
		\phpbb\request\request $request,
		\phpbb\controller\helper $controller_helper,
		\phpbb\language\language $language,
		\fq\boardnotices\repository\boardnotices_interface $repository)
	{
		$this->user = $user;
		$this->config = $config;
		$this->template = $template;
		$this->request = $request;
		$this->controller_helper = $controller_helper;
		$this->language = $language;
		$this->repository = $repository;
	}

	/**
	 * Display board notices. Function called on event 'core.page_header_after'
	 *
	 * @return null
	 * @access public
	 */
	public function display_board_notices()
	{
		$notices = array();
		$template_vars = $this->getDefaultTemplateVars();
		$force_all_rules = false;

		$preview = $this->isPreview();
		if ($preview)
		{
			// Force the preview of a notice
			$preview_id = $this->getPreviewId();
			$raw_notice = $this->repository->getNoticeFromId($preview_id);
			$notices[] = $this->getNotice($raw_notice);
			$force_all_rules = true;
		}
		else if ($this->extensionEnabled())
		{
			// Normal notices mode
			$raw_notices = $this->repository->getActiveNotices();
			foreach ($raw_notices as $raw_notice)
			{
				$notices[] = $this->getNotice($raw_notice);
			}
			unset($raw_notices);
		}

		$notice_message = '';
		$notice_bgcolor = '';
		$notice_style = '';

		foreach ($notices as $notice)
		{
			if ($notice->hasValidatedAllRules($force_all_rules, $preview))
			{
				// Prepare board notice message for display
				$notice_message = generate_text_for_display(
						$notice->getMessage(), $notice->getMessageUid(), $notice->getMessageBitfield(), $notice->getMessageOptions()
				);
				$notice_bgcolor = $notice->getMessageBgColor();
				$notice_style = $notice->getMessageStyle();
				if (empty($notice_bgcolor))
				{
					$notice_bgcolor = $this->config['boardnotices_default_bgcolor'];
				}
				$notice_dismissable = $notice->getDismissable();
				$template_vars = array_merge($template_vars, $notice->getTemplateVars());
				// We stop at the first displayable notice
				break;
			}
		}

		if (!empty($notice_message))
		{
			$this->language->add_lang('boardnotices', 'fq/boardnotices');
			$notice_message = $this->replaceTemplateVars($notice_message, $template_vars);

			// Output board notice to the template
			$this->template->assign_vars(array(
				'S_BOARD_NOTICE' => true,
				'S_BOARD_NOTICE_DISMISS' => $notice_dismissable,
				'BOARD_NOTICE' => $notice_message,
				'BOARD_NOTICE_BGCOLOR' => $notice_bgcolor,
				'BOARD_NOTICE_STYLE' => $notice_style,
				'U_BOARD_NOTICE_CLOSE'	=> $this->controller_helper->route(
					constants::$CONTROLLER_ROUTING_ID,
					array(
						'notice_id' => $notice->getId(),
						'hash' => generate_link_hash(constants::$ROUTING_CLOSE_HASH_ID),
					)
				),
			));
		}

		if ($this->forumVisitedEnabled() && $this->isUserLoggedIn())
		{
			$this->setForumVisited();
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

	private function replaceTemplateVars($notice_message, $template_vars)
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

	private function extensionEnabled()
	{
		return $this->config['boardnotices_enabled'] ? true : false;
	}

	private function forumVisitedEnabled()
	{
		return $this->config['track_forums_visits'] ? true : false;
	}

	private function isUserLoggedIn()
	{
		return (($this->user->data['user_type'] == USER_NORMAL) || ($this->user->data['user_type'] == USER_FOUNDER));
	}

	private function isPreview()
	{
		$preview_key = $this->request->variable('bnpk', '');
		$preview_id = $this->request->variable('bnid', 0);
		return (!empty($preview_key) && !empty($preview_id) && ($preview_key == $this->config['boardnotices_previewkey']));
	}

	private function getPreviewId()
	{
		$preview_id = $this->request->variable('bnid', 0);
		return $preview_id;
	}

	/**
	 * @return notice
	 */
	private function getNotice($raw_notice)
	{
		$rules = $this->repository->getRulesFor($raw_notice['notice_id']);
		return new notice($raw_notice, $rules);
	}

	private function setForumVisited()
	{
		$forum_id = $this->request->variable('f', 0);

		if ($forum_id > 0)
		{
			$this->repository->setForumVisited($this->user->data['user_id'], $forum_id);
		}
	}
}

<?php

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
	/** @var \phpbb\request\request_interface $request */
	private $request;
	/** @var \phpbb\controller\helper $controller_helper */
	private $controller_helper;
	/** @var \phpbb\language\language $language */
	private $language;
	/** @var \fq\boardnotices\repository\notices_interface $notices_repository */
	private $notices_repository;
	/** @var \fq\boardnotices\repository\notices_seen_interface $notices_seen_repository */
	private $notices_seen_repository;

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
		\phpbb\request\request_interface $request,
		\phpbb\controller\helper $controller_helper,
		\phpbb\language\language $language,
		\fq\boardnotices\repository\notices_interface $notices_repository,
		\fq\boardnotices\repository\notices_seen_interface $notices_seen_repository)
	{
		$this->user = $user;
		$this->config = $config;
		$this->template = $template;
		$this->request = $request;
		$this->controller_helper = $controller_helper;
		$this->language = $language;
		$this->notices_repository = $notices_repository;
		$this->notices_seen_repository = $notices_seen_repository;
	}

	/**
	 * Display board notices. Function called on event 'core.page_header_after'
	 *
	 * @return null
	 * @access public
	 */
	public function display_board_notices()
	{
		// We tag the visit first so that we don't display a message WHEN visiting the forum (if any)
		$this->setForumVisited();

		$notices = array();
		$force_all_rules = false;

		$preview = $this->isPreview();
		if ($preview)
		{
			// Force the preview of a notice
			$preview_id = $this->getPreviewId();
			$raw_notice = $this->notices_repository->getNoticeFromId($preview_id);
			$notices[] = $this->getNotice($raw_notice);
			$force_all_rules = true;
		}
		else if ($this->extensionEnabled())
		{
			// Normal notices mode
			$raw_notices = $this->notices_repository->getActiveNotices();
			$dismissed_notices = array();
			if ($this->isUserRegistered())
			{
				$dismissed_notices = $this->notices_seen_repository->getDismissedNotices($this->getUserId());
			}
			else
			{
				// The user is probably a guest, so we get that from the cookies
				$dismissed_notices = $this->getDismissedNoticesFromCookies();
			}
			$notices = $this->getNotices($raw_notices, $dismissed_notices);
			unset($raw_notices);
		}

		if (!empty($notices))
		{
			$this->generateNoticeTemplate($notices, $force_all_rules, $preview);
		}
	}

	private function getNotices(&$raw_notices, $dismissed_notices)
	{
		$notices = array();
		if (!empty($raw_notices))
		{
			foreach ($raw_notices as $raw_notice)
			{
				$notices[] = $this->getNotice($raw_notice, $dismissed_notices);
			}
		}
		return $notices;
	}

	private function generateNoticeTemplate($notices, $force_all_rules, $preview)
	{
		$template_vars = $this->getDefaultTemplateVars();
		$notice_message = '';
		$notice_bgcolor = '';
		$notice_style = '';

		/** @var notice[] $notices */
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
					$notice_bgcolor = $this->config[constants::$CONFIG_DEFAULT_BGCOLOR];
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

			$dismiss_parameters = array(
				'notice_id' => $notice->getId(),
				'hash' => generate_link_hash(constants::$ROUTING_CLOSE_HASH_ID),
			);
			if ($preview)
			{
				$dismiss_parameters['preview'] = 1;
			}
			// Output board notice to the template
			$this->template->assign_vars(array(
				'S_BOARD_NOTICE' => true,
				'S_BOARD_NOTICE_DISMISS' => $notice_dismissable,
				'BOARD_NOTICE' => $notice_message,
				'BOARD_NOTICE_BGCOLOR' => $notice_bgcolor,
				'BOARD_NOTICE_STYLE' => $notice_style,
				'U_BOARD_NOTICE_CLOSE'	=> $this->controller_helper->route(
					constants::$CONTROLLER_ROUTING_ID,
					$dismiss_parameters
				),
			));
		}
	}

	private function getDefaultTemplateVars()
	{
		return array(
			'SESSIONID' => $this->user->data['session_id'],
			'USERID' => $this->getUserId(),
			'USERNAME' => $this->user->data['username'],
			'LASTVISIT' => $this->user->format_date($this->user->data['user_lastvisit']),
			'LASTPOST' => $this->user->format_date($this->user->data['user_lastpost_time']),
			'REGISTERED' => $this->user->format_date($this->user->data['user_regdate']),
		);
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
		return !empty($this->config[constants::$CONFIG_ENABLED]);
	}

	private function forumVisitedEnabled()
	{
		return !empty($this->config[constants::$CONFIG_TRACK_FORUMS_VISITS]);
	}

	private function isUserRegistered()
	{
		return !empty($this->user->data['is_registered']);
	}

	/**
	 * Returns user ID or 0
	 * @return int
	 */
	private function getUserId()
	{
		return isset($this->user->data['user_id']) ? $this->user->data['user_id'] : 0;
	}

	private function isPreview()
	{
		$preview_key = $this->request->variable('bnpk', '');
		$preview_id = $this->request->variable('bnid', 0);
		return (!empty($preview_key) && !empty($preview_id) && ($preview_key == $this->config[constants::$CONFIG_PREVIEW_KEY]));
	}

	private function getPreviewId()
	{
		return $this->request->variable('bnid', 0);
	}

	/**
	 * Creates the notice object
	 * @param array $raw_notice
	 * @param array $dismissed
	 * @return notice
	 */
	private function getNotice($raw_notice, $dismissed = array())
	{
		$rules = $this->notices_repository->getRulesFor($raw_notice['notice_id']);
		return new notice($raw_notice, $rules, $dismissed);
	}

	/**
	 * Sets a user visit to a forum (if enabled)
	 */
	private function setForumVisited()
	{
		if ($this->extensionEnabled() && $this->forumVisitedEnabled() && $this->isUserRegistered())
		{
			$forum_id = (int) $this->request->variable('f', 0);

			if ($forum_id > 0)
			{
				$this->notices_repository->setForumVisited($this->getUserId(), $forum_id);
			}
		}
	}

	private function getDismissedNoticesFromCookies()
	{
		$dismissed = array();
		$prefix = $this->config['cookie_name'] . '_bnd_';
		$cookies = $this->request->variable_names(\phpbb\request\request_interface::COOKIE);
		foreach ($cookies as $cookie_name)
		{
			if (substr($cookie_name, 0, strlen($prefix)) == $prefix)
			{
				// This is ours!
				$cookie_content = (int) $this->request->variable($cookie_name, 0, false, \phpbb\request\request_interface::COOKIE);
				if (!empty($cookie_content))
				{
					// We can fake the time since the reset_after equals to the cookie expiry
					$dismissed[$cookie_content] = array('seen' => time());
				}
			}
		}
		return $dismissed;
	}
}

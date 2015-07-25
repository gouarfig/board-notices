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
use fq\boardnotices\core;

class listener implements EventSubscriberInterface
{

	protected $user = null;
	protected $template = '';

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
	public function __construct(\phpbb\user $user, \phpbb\template\template $template)
	{
		$this->user = $user;
		$this->template = $template;
	}

	protected function getDataLayer()
	{
		global $phpbb_container;
		static $data_layer = null;

		if (is_null($data_layer))
		{
			$data_layer = $phpbb_container->get('fq.boardnotices.datalayer');
		}
		return $data_layer;
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

		$data_layer = $this->getDataLayer();
		$raw_notices = $data_layer->getActiveNotices();
		foreach ($raw_notices as $raw_notice)
		{
			$rules = $data_layer->getRulesFor($raw_notice['notice_id']);
			$notices[] = new \fq\boardnotices\domain\notice($raw_notice, $rules);
			unset($rules);
		}
		unset($raw_notices);

		$notice_message = '';
		$notice_bgcolor = '';

		foreach ($notices as $notice)
		{
			if ($notice->hasValidatedAllRules())
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
			'USERID' => $this->user->data['user_id'],
			'USERNAME' => $this->user->data['username'],
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

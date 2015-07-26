<?php

/**
 *
 * Board Notices Manager extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\acp;

class board_notices_module
{

	private $notice_form_name = 'acp_board_notice';

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var ContainerInterface */
	protected $phpbb_container;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $php_ext;

	/** @var string */
	public $u_action;

	public function main($id, $mode)
	{
		global $config, $db, $request, $template, $user, $phpbb_root_path, $phpEx, $phpbb_container;

		$this->config = $config;
		$this->config_text = $phpbb_container->get('config_text');
		$this->db = $db;
		$this->log = $phpbb_container->get('log');
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $phpEx;

		if ($mode == "manage")
		{
			$action = request_var('action', '');
			if ($this->request->is_set_post('add'))
			{
				$action = 'add';
			}
			if ($this->request->is_set_post('edit'))
			{
				$action = 'edit';
			}
			$notice_id = request_var('id', 0);

			switch ($action)
			{
				case 'add':
					$error = '';
					$data = $this->newBlankNotice();
					if ($this->request->is_set_post('submit'))
					{
						$error = $this->validateNoticeForm($data, true);
						if (empty($error))
						{
							$this->saveNewNotice($data);
						} else
						{
							$this->displayNoticeForm($action, $data, $error);
						}
					} else
					{
						$this->displayNoticeForm($action, $data);
					}
					break;

				case 'edit':
					$error = '';
					$data = $this->loadNotice($notice_id);
					if ($this->request->is_set_post('submit'))
					{
						$error = $this->validateNoticeForm($data, true);
						if (empty($error))
						{
							$this->saveNotice($notice_id, $data);
						} else
						{
							$this->displayNoticeForm($action, $data, $error);
						}
					} else
					{
						$this->displayNoticeForm($action, $data);
					}
					break;

				case 'move_up':
				case 'move_down':
					$this->moveNotice($action, $notice_id);
					break;

				case 'edit_rules':
					$this->displayEditRulesForm($notice_id);
					break;

				default :
					$this->displayManager();
					break;
			}
			return;
		}
	}

	public function displayManager()
	{
		/** @var \fq\boardnotices\datalayer */
		$data_layer = $this->getDataLayer();

		// Add the board announcements ACP lang file
		$this->user->add_lang_ext('fq/boardnotices', 'boardnotices_acp');

		// Load a template from adm/style for our ACP page
		$this->tpl_name = 'board_notices';

		// Set the page title for our ACP page
		$this->page_title = $this->user->lang('ACP_BOARD_NOTICES_MANAGER');

		// Output data to the template
		$this->template->assign_vars(array(
			'ACP_BOARD_NOTICES_MANAGER' => $this->user->lang('ACP_BOARD_NOTICES_MANAGER'),
			'ACP_BOARD_NOTICES_MANAGER_EXPLAIN' => $this->user->lang('ACP_BOARD_NOTICES_MANAGER_EXPLAIN'),
			'BOARD_NOTICE_TITLE' => $this->user->lang('BOARD_NOTICE_TITLE'),
			'BOARD_NOTICE_RULES' => $this->user->lang('BOARD_NOTICE_RULES'),
			'BOARD_NOTICE_ADD' => $this->user->lang('BOARD_NOTICE_ADD'),
			'COLSPAN' => 4,
		));

		$notices = $data_layer->getAllNotices();
		foreach ($notices as $notice)
		{
			$rules = $data_layer->getRulesFor($notice['notice_id']);
			$this->template->assign_block_vars('items', array(
				'S_SPACER' => false,
				'EDIT_RULES_LINK' => $this->u_action . '&amp;action=edit_rules&amp;id=' . $notice['notice_id'],
				'TITLE' => $notice['title'],
				'RULES' => count($rules),
				'ACTIVE' => $notice['active'] ? 'Yes' : 'No',
				'U_EDIT' => $this->u_action . '&amp;action=edit&amp;id=' . $notice['notice_id'],
				'U_DELETE' => $this->u_action . '&amp;action=delete&amp;id=' . $notice['notice_id'],
				'U_MOVE_UP' => $this->u_action . '&amp;action=move_up&amp;id=' . $notice['notice_id'],
				'U_MOVE_DOWN' => $this->u_action . '&amp;action=move_down&amp;id=' . $notice['notice_id'],
			));
			unset($rules);
		}
	}

	public function displayNoticeForm($action, $data, $error = '')
	{
		// Add the posting lang file needed by BBCodes
		$this->user->add_lang(array('posting'));

		// Add the board announcements ACP lang file
		$this->user->add_lang_ext('fq/boardnotices', 'boardnotices_acp');

		// Load a template from adm/style for our ACP page
		$this->tpl_name = 'board_notices_edit';

		// Set the page title for our ACP page
		$this->page_title = $this->user->lang('ACP_BOARD_NOTICE_SETTINGS');

		// Define the name of the form for use as a form key
		add_form_key($this->notice_form_name);

		// Include files needed for displaying BBCodes
		if (!function_exists('display_custom_bbcodes'))
		{
			include($this->phpbb_root_path . 'includes/functions_display.' . $this->php_ext);
		}

		// If form is previewed
		if ($this->request->is_set_post('preview'))
		{
			$error = $this->validateNoticeForm($data);
		}

		// Prepare a fresh announcement preview
		$notice_text_preview = '';
		if ($this->request->is_set_post('preview'))
		{
			$notice_text_preview = generate_text_for_display(
					$data['message'], $data['message_uid'], $data['message_bitfield'], $data['message_options']);
		}

		// prepare the announcement text for editing inside the textbox
		$notice_text_edit = generate_text_for_edit(
				$data['message'], $data['message_uid'], $data['message_options']);

		// Output data to the template
		$this->template->assign_vars(array(
			'BOARD_NOTICES_ENABLED' => true,
			'BOARD_NOTICE_SETTINGS' => $this->user->lang('ACP_BOARD_NOTICE_SETTINGS'),
			'BOARD_NOTICE_SETTINGS_EXPLAIN' => $this->user->lang('ACP_BOARD_NOTICE_SETTINGS_EXPLAIN'),
			'LABEL_BOARD_NOTICE_ACTIVE' => $this->user->lang('LABEL_BOARD_NOTICE_ACTIVE'),
			'LABEL_BOARD_NOTICE_TITLE' => $this->user->lang('LABEL_BOARD_NOTICE_TITLE'),
			'LABEL_BOARD_NOTICE_PREVIEW' => $this->user->lang('LABEL_BOARD_NOTICE_PREVIEW'),
			'LABEL_BOARD_NOTICE_TEXT' => $this->user->lang('LABEL_BOARD_NOTICE_TEXT'),
			'LABEL_BOARD_NOTICE_BGCOLOR' => $this->user->lang('LABEL_BOARD_NOTICE_BGCOLOR'),
			'ERRORS' => $error,
			'NOTICE_ID' => $data['notice_id'] ? $data['notice_id'] : '',
			'BOARD_NOTICE_ACTIVE' => $data['active'],
			'BOARD_NOTICE_TITLE' => $data['title'],
			'BOARD_NOTICE_TEXT' => $notice_text_edit['text'],
			'BOARD_NOTICE_PREVIEW' => $notice_text_preview,
			'BOARD_NOTICE_BGCOLOR' => $data['message_bgcolor'],
			'S_BBCODE_DISABLE_CHECKED' => !$notice_text_edit['allow_bbcode'],
			'S_SMILIES_DISABLE_CHECKED' => !$notice_text_edit['allow_smilies'],
			'S_MAGIC_URL_DISABLE_CHECKED' => !$notice_text_edit['allow_urls'],
			'BBCODE_STATUS' => $this->user->lang('BBCODE_IS_ON'),
			'SMILIES_STATUS' => $this->user->lang('SMILIES_ARE_ON'),
			'IMG_STATUS' => $this->user->lang('IMAGES_ARE_ON'),
			'FLASH_STATUS' => $this->user->lang('FLASH_IS_ON'),
			'URL_STATUS' => $this->user->lang('URL_IS_ON'),
			'S_BBCODE_ALLOWED' => true,
			'S_SMILIES_ALLOWED' => true,
			'S_BBCODE_IMG' => true,
			'S_BBCODE_FLASH' => true,
			'S_LINKS_ALLOWED' => true,
			'U_BACK' => $this->u_action,
			'U_ACTION' => $this->u_action . '&amp;action=' . $action,
		));

		// Assigning custom bbcodes
		display_custom_bbcodes();
	}

	public function displayEditRulesForm($notice_id)
	{
		global $phpbb_container;

		/** @var \fq\boardnotices\datalayer */
		$data_layer = $this->getDataLayer();

		// Add the board announcements ACP lang file
		$this->user->add_lang_ext('fq/boardnotices', 'boardnotices_acp');

		// Load a template from adm/style for our ACP page
		$this->tpl_name = 'board_notices_rules';

		// Set the page title for our ACP page
		$this->page_title = $this->user->lang('ACP_BOARD_NOTICE_RULES');

		// Output data to the template
		$this->template->assign_vars(array(
			'ACP_BOARD_NOTICE_RULES' => $this->user->lang('ACP_BOARD_NOTICE_RULES'),
			'ACP_BOARD_NOTICE_RULES_EXPLAIN' => $this->user->lang('ACP_BOARD_NOTICE_RULES_EXPLAIN'),
			'BOARD_NOTICE_RULE_NAME' => $this->user->lang('BOARD_NOTICE_RULE_NAME'),
			'BOARD_NOTICE_RULE_VALUE' => $this->user->lang('BOARD_NOTICE_RULE_VALUE'),
			'COLSPAN' => 3,
			'BOARD_NOTICE_RULE_ADD' => $this->user->lang('BOARD_NOTICE_RULE_ADD'),
			'U_BACK' => $this->u_action,
			'U_ACTION' => $this->u_action . '&amp;action=' . $action,
		));

		$rules_manager = $phpbb_container->get('fq.boardnotices.domain.rules');
		$all_rules = $rules_manager->getDefinedRules();

		$rules = $data_layer->getRulesFor($notice_id);
		foreach ($rules as $rule)
		{
			$this->template->assign_block_vars('items', array(
				'RULE_ID' => $rule['notice_rule_id'],
				'RULE_NAME' => $rule['rule'],
				'RULE_CONDITIONS' => $this->getDisplayConditions(
						$rules_manager->getRuleType($rule['rule']), $rules_manager->getRuleValues($rule['rule']), $rule['conditions']),
			));
			foreach ($all_rules as $key => $value)
			{
				$this->template->assign_block_vars('items.allrules', array(
					'COMBO_RULE_ID' => $key,
					'COMBO_RULE_SELECTED' => ($rule['rule'] == $key) ? true : false,
					'COMBO_RULE_NAME' => $value,
				));
			}
		}
	}

	private function getDisplayConditions($type, $values, $selected)
	{
		$display = '';
		if (!is_array($selected))
		{
			$selected = array($selected);
		}
		switch ($type)
		{
			case 'list':
			case 'multiple choice':
				$display .= '<select' . (($type == 'multiple choice') ? ' multiple="multiple"' : '') . ' size="10">';
				if (is_array($values) && !empty($values))
				{
					foreach ($values as $key => $value)
					{
						$display .= '<option value="' . $key . '"' . (in_array($key, $selected) ? ' selected' : '') . '>' . $value . '</option>';
					}
				}
				$display .= "</select>";
				break;

			default:
				break;
		}
		return $display;
	}

	public function moveNotice($action, $notice_id)
	{
		/** @var \fq\boardnotices\datalayer */
		$data_layer = $this->getDataLayer();

		$move_executed = $data_layer->moveNotice($action, $notice_id);

		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(array(
				'success' => $move_executed,
			));
		}
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

	private function newBlankNotice()
	{
		$data = array(
			'active' => false,
			'title' => '',
			'message' => '',
			'message_uid' => '',
			'message_bitfield' => '',
			'message_options' => OPTION_FLAG_BBCODE + OPTION_FLAG_SMILIES + OPTION_FLAG_LINKS,
			'message_bgcolor' => '',
		);
		return $data;
	}

	private function loadNotice($notice_id)
	{
		$data_layer = $this->getDataLayer();
		$notice = $data_layer->getNoticeFromId($notice_id);
		return $notice;
	}

	/**
	 * Validates form data and returns the values into $data
	 * Return value of the function is an empty string if the form is valid, or an error message otherwise
	 * @param array $data
	 * @param bool $for_submit
	 * @return string
	 */
	private function validateNoticeForm(&$data, $for_submit = false)
	{
		$error = '';

		// Add the board announcements ACP lang file
		$this->user->add_lang_ext('fq/boardnotices', 'boardnotices_acp');

		// Test if form key is valid
		if (!check_form_key($this->notice_form_name))
		{
			$error = $this->user->lang('FORM_INVALID');
		}

		// Get new values from the form
		$data['title'] = $this->request->variable('board_notice_title', '', true);
		$data['message'] = $this->request->variable('board_notice_text', '', true);
		$data['title'] = $this->request->variable('board_notice_title', '', true);
		$data['message_bgcolor'] = $this->request->variable('board_notice_bgcolor', '', true);

		// Get config options from the form
		$data['active'] = $this->request->variable('board_notice_active', false);

		// Prepare announcement text for storage
		generate_text_for_storage(
				$data['message'], $data['message_uid'], $data['message_bitfield'], $data['message_options'], !$this->request->variable('disable_bbcode', false), !$this->request->variable('disable_magic_url', false), !$this->request->variable('disable_smilies', false)
		);

		if (empty($error) && $for_submit)
		{
			if (empty($data['title']))
			{
				$error .= $this->user->lang('ERROR_EMPTY_TITLE') . "<br />";
			}
			if (empty($data['message']))
			{
				$error .= $this->user->lang('ERROR_EMPTY_MESSAGE') . "<br />";
			}
		}

		return $error;
	}

	private function saveNewNotice(&$data)
	{
		// Add the board announcements ACP lang file
		$this->user->add_lang_ext('fq/boardnotices', 'boardnotices_acp');

		$data_layer = $this->getDataLayer();
		$data_layer->saveNewNotice($data);

		// Log the new notice
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_BOARD_NOTICES_ADDED', time(), array($data['title']));
		// Output message to user for the update
		trigger_error($this->user->lang('BOARD_NOTICE_SAVED') . adm_back_link($this->u_action));
	}

	private function saveNotice($notice_id, &$data)
	{
		// Add the board announcements ACP lang file
		$this->user->add_lang_ext('fq/boardnotices', 'boardnotices_acp');

		$data_layer = $this->getDataLayer();
		$data_layer->saveNotice($notice_id, $data);

		// Log the update
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_BOARD_NOTICES_UPDATED', time(), array($data['title']));
		// Output message to user for the update
		trigger_error($this->user->lang('BOARD_NOTICE_SAVED') . adm_back_link($this->u_action));
	}

}
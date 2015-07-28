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
	private $rules_manager = null;

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
						}
						else
						{
							$this->displayNoticeForm($action, $data, $error);
						}
					}
					else
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
						}
						else
						{
							$this->displayNoticeForm($action, $data, $error);
						}
					}
					else
					{
						$this->displayNoticeForm($action, $data);
					}
					break;

				case 'enable':
				case 'disable':
					$this->enableNotice($action, $notice_id);
					$this->displayManager();
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
		global $phpbb_root_path, $phpEx;

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
			'COLSPAN' => 6,
		));

		$notices = $data_layer->getAllNotices();
		foreach ($notices as $notice)
		{
			$rules = $data_layer->getRulesFor($notice['notice_id']);
			$this->template->assign_block_vars('items', array(
				'S_SPACER' => false,
				'TITLE' => $notice['title'],
				'PREVIEW_LINK' => append_sid("{$phpbb_root_path}index.$phpEx") . "&bnpk=" . $this->config['boardnotices_previewkey'] . "&bnid={$notice['notice_id']}",
				'RULES' => count($rules),
				'ACTIVE' => $notice['active'] ? 'Yes' : 'No',
				'ENABLED' => $notice['active'] ? true : false,
				'U_ENABLE' => $this->u_action . '&amp;action=enable&amp;id=' . $notice['notice_id'],
				'U_DISABLE' => $this->u_action . '&amp;action=disable&amp;id=' . $notice['notice_id'],
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
		//var_dump($data);
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
			'BBCODE_STATUS' => $this->user->lang('BBCODE_IS_ON', '', ''),
			'SMILIES_STATUS' => $this->user->lang('SMILIES_ARE_ON'),
			'IMG_STATUS' => $this->user->lang('IMAGES_ARE_ON'),
			'FLASH_STATUS' => $this->user->lang('FLASH_IS_ON'),
			'URL_STATUS' => $this->user->lang('URL_IS_ON'),
			'S_BBCODE_ALLOWED' => true,
			'S_SMILIES_ALLOWED' => true,
			'S_BBCODE_IMG' => true,
			'S_BBCODE_FLASH' => true,
			'S_LINKS_ALLOWED' => true,
			'L_INFO' => $this->user->lang('L_INFORMATION'),
			'VARIABLES_EXPLAIN' => $this->user->lang('VARIABLES_EXPLAIN'),
			'U_BACK' => $this->u_action,
			'U_ACTION' => $this->u_action . '&amp;action=' . $action,
			'ALLRULES_COLSPAN' => 4,
			'ACP_BOARD_NOTICE_RULES' => $this->user->lang('ACP_BOARD_NOTICE_RULES'),
			'ACP_BOARD_NOTICE_RULES_EXPLAIN' => $this->user->lang('ACP_BOARD_NOTICE_RULES_EXPLAIN'),
			'BOARD_NOTICE_RULE_NAME' => $this->user->lang('BOARD_NOTICE_RULE_NAME'),
			'BOARD_NOTICE_RULE_VALUE' => $this->user->lang('BOARD_NOTICE_RULE_VALUE'),
			'BOARD_NOTICE_RULE_VARIABLES' => $this->user->lang('BOARD_NOTICE_RULE_VARIABLES'),
		));

		// Assigning custom bbcodes
		display_custom_bbcodes();

		$all_rules = $this->getAllRules();
		foreach ($all_rules as $rule_name => $rule_description)
		{
			$this->template->assign_block_vars('allrules', array(
				'NOTICE_RULE_ID' => isset($data['notice_rule_id'][$rule_name]) ? $data['notice_rule_id'][$rule_name] : '',
				'NOTICE_RULE_CHECKED' => isset($data['notice_rule_checked'][$rule_name]) ? true : false,
				'RULE_NAME' => $rule_name,
				'RULE_DESCRIPTION' => $rule_description,
				'RULE_CONDITIONS' => $this->getDisplayConditions(
						$this->rules_manager->getRuleType($rule_name), $this->rules_manager->getRuleValues($rule_name), isset($data['notice_rule_conditions'][$rule_name]) ? $data['notice_rule_conditions'][$rule_name] : array(), "notice_rule_conditions[{$rule_name}]"
				),
				'RULE_VARIABLES' => implode(', ', $this->rules_manager->getAvailableVars($rule_name)),
			));
		}
	}

	private function getDisplayConditions($type, $values, $selected, $input_name)
	{
		$display = '';
		if (!is_array($selected))
		{
			if (!is_null($selected))
			{
				$selected = array($selected);
			}
			else
			{
				$selected = array();
			}
		}
		switch ($type)
		{
			case 'list':
			case 'multiple choice':
				$size = (count($values) < 10) ? count($values) : 10;
				$display .= '<select' . (($type == 'multiple choice') ? ' multiple="multiple"' : '') . ' size="' . $size . '" name="' . $input_name . '[]">';
				if (is_array($values) && !empty($values))
				{
					foreach ($values as $key => $value)
					{
						$display .= '<option value="' . $key . '"' . (in_array($key, $selected) ? ' selected' : '') . '>' . $value . '</option>';
					}
				}
				$display .= "</select>";
				break;

			case 'forums':
				$display .= '<select multiple="multiple" size="10" name="' . $input_name . '[]">';
				$display .= make_forum_select($selected, false, false, true);
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

	public function enableNotice($action, $notice_id)
	{
		/** @var \fq\boardnotices\datalayer */
		$data_layer = $this->getDataLayer();

		$executed = $data_layer->enableNotice($action, $notice_id);

		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(array(
				'success' => $executed,
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
			'notice_rule_id' => array(),
			'notice_rule_checked' => array(),
			'notice_rule_conditions' => array(),
		);
		return $data;
	}

	private function loadNotice($notice_id)
	{
		$data_layer = $this->getDataLayer();
		$notice = $data_layer->getNoticeFromId($notice_id);
		$notice['notice_rule_id'] = array();
		$notice['notice_rule_checked'] = array();
		$notice['notice_rule_conditions'] = array();

		$rules = $data_layer->getRulesFor($notice_id);
		foreach ($rules as $rule)
		{
			$notice['notice_rule_id'][$rule['rule']] = $rule['notice_rule_id'];
			$notice['notice_rule_checked'][$rule['rule']] = 1;
			$conditions = unserialize($rule['conditions']);
			if ($conditions === false)
			{
				$conditions = array($rule['conditions']);
			}
			$notice['notice_rule_conditions'][$rule['rule']] = $conditions;
		}
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

		// Get config for all the rules
		$all_rules = $this->getAllRules();
		foreach ($all_rules as $rule_name => $rule_description)
		{
			$notice_rule_id = $this->request->variable(array('notice_rule_id', $rule_name), 0);
			if ($notice_rule_id > 0)
			{
				$data['notice_rule_id'][$rule_name] = $notice_rule_id;
			}
			$notice_rule_checked = $this->request->variable(array('notice_rule_checked', $rule_name), 0);
			if ($notice_rule_checked)
			{
				$data['notice_rule_checked'][$rule_name] = 1;
			}
			else
			{
				unset($data['notice_rule_checked'][$rule_name]);
			}
			$notice_rule_conditions = $this->request->variable(array('notice_rule_conditions', $rule_name), array(''));
			if (!empty($notice_rule_conditions))
			{
				$data['notice_rule_conditions'][$rule_name] = $notice_rule_conditions;
			}
			else
			{
				unset($data['notice_rule_conditions'][$rule_name]);
			}
		}

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

	private function saveRules($notice_id, &$data)
	{
		$to_delete = array();
		foreach ($data['notice_rule_id'] as $rule_name => $rule_id)
		{
			if (empty($data['notice_rule_checked'][$rule_name]))
			{
				$to_delete[] = $rule_id;
			}
		}
		$to_insert = array();
		$to_update = array();
		foreach ($data['notice_rule_checked'] as $rule_name => $checked)
		{
			if ($checked)
			{
				if (!empty($data['notice_rule_id'][$rule_name]) && ($data['notice_rule_id'][$rule_name] > 0))
				{
					$to_update[] = array(
						'notice_rule_id' => $data['notice_rule_id'][$rule_name],
						'notice_id' => $notice_id,
						'rule' => $rule_name,
						'conditions' => serialize($data['notice_rule_conditions'][$rule_name]),
					);
				}
				else
				{
					$to_insert[] = array(
						'notice_id' => $notice_id,
						'rule' => $rule_name,
						'conditions' => serialize($data['notice_rule_conditions'][$rule_name]),
					);
				}
			}
		}
		$data_layer = $this->getDataLayer();

		if (!empty($to_delete))
		{
			//echo "<br />to delete:"; var_dump($to_delete);
			$data_layer->deleteRules($to_delete);
		}
		if (!empty($to_update))
		{
			//echo "<br />to update:"; var_dump($to_update);
			$data_layer->updateRules($to_update);
		}
		if (!empty($to_insert))
		{
			//echo "<br />to insert:"; var_dump($to_insert);
			$data_layer->insertRules($to_insert);
		}
	}

	private function saveNewNotice(&$data)
	{
		// Add the board announcements ACP lang file
		$this->user->add_lang_ext('fq/boardnotices', 'boardnotices_acp');

		$rules_data = array(
			'notice_rule_id' => $data['notice_rule_id'],
			'notice_rule_checked' => $data['notice_rule_checked'],
			'notice_rule_conditions' => $data['notice_rule_conditions'],
		);
		unset($data['notice_rule_id']);
		unset($data['notice_rule_checked']);
		unset($data['notice_rule_conditions']);

		$data_layer = $this->getDataLayer();
		$notice_id = $data_layer->saveNewNotice($data);
		if ($notice_id > 0)
		{
			$this->saveRules($notice_id, $rules_data);
		}

		// Log the new notice
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_BOARD_NOTICES_ADDED', time(), array($data['title']));
		// Output message to user for the update
		trigger_error($this->user->lang('BOARD_NOTICE_SAVED') . adm_back_link($this->u_action));
	}

	private function saveNotice($notice_id, &$data)
	{
		// Add the board announcements ACP lang file
		$this->user->add_lang_ext('fq/boardnotices', 'boardnotices_acp');

		$rules_data = array(
			'notice_rule_id' => $data['notice_rule_id'],
			'notice_rule_checked' => $data['notice_rule_checked'],
			'notice_rule_conditions' => $data['notice_rule_conditions'],
		);
		unset($data['notice_rule_id']);
		unset($data['notice_rule_checked']);
		unset($data['notice_rule_conditions']);

		$data_layer = $this->getDataLayer();
		$data_layer->saveNotice($notice_id, $data);
		$this->saveRules($notice_id, $rules_data);

		// Log the update
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_BOARD_NOTICES_UPDATED', time(), array($data['title']));
		// Output message to user for the update
		trigger_error($this->user->lang('BOARD_NOTICE_SAVED') . adm_back_link($this->u_action));
	}

	private function getAllRules()
	{
		global $phpbb_container;

		if (is_null($this->rules_manager))
		{
			$this->rules_manager = $phpbb_container->get('fq.boardnotices.domain.rules');
		}
		$all_rules = $this->rules_manager->getDefinedRules();
		return $all_rules;
	}

}

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

	protected $p_master;

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

	/**
	 * I find it rather annoying that this class won't be instanciated on the common phpBB/Symfony model,
	 * but with this unique (and useless) parameter instead.
	 * => No dependency injection is possible here
	 *
	 * @param type $p_master
	 */
	public function __construct(&$p_master)
	{
		$this->p_master = &$p_master;
	}

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
			return $this->manage_module($id, $mode);
		}
		else if ($mode == "settings")
		{
			return $this->settings_module($id, $mode);
		}
	}

	public function manage_module($id, $mode)
	{
		$action = $this->request->variable('action', '');
		if ($this->request->is_set_post('add'))
		{
			$action = 'add';
		}
		if ($this->request->is_set_post('edit'))
		{
			$action = 'edit';
		}
		$notice_id = $this->request->variable('id', 0);

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

			case 'move_first':
				$this->moveNoticeFirst($notice_id);
				break;

			case 'move_last':
				$this->moveNoticeLast($notice_id);
				break;

			case 'delete':
				$this->deleteNotice($notice_id);
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

	public function settings_module($id, $mode)
	{
		$action = $this->request->variable('action', '');
		if ($action == 'reset_forum_visits')
		{
			$this->resetForumVisits($id, $mode, $action);
		}
		else
		{
			if ($this->request->is_set_post('submit'))
			{
				$this->saveSettings();
			}
			else
			{
				$this->displaySettingsForm();
			}
		}
		return;
	}

	/**
	 * Display the notice manager page
	 *
	 * @return void
	 */
	public function displayManager()
	{
		global $phpbb_root_path, $phpEx;

		/** @var \fq\boardnotices\repository\boardnotices_interface */
		$data_layer = $this->getRepository();

		// Add the board notices ACP lang file
		$this->addAdminLanguage();

		// Load a template from adm/style for our ACP page
		$this->tpl_name = 'board_notices';

		// Set the page title for our ACP page
		$this->page_title = $this->user->lang('ACP_BOARD_NOTICES_MANAGER');

		// Output data to the template
		$this->template->assign_vars(array(
			'S_BOARD_NOTICES' => true,
			'BOARD_NOTICE_ADD' => $this->user->lang('BOARD_NOTICE_ADD'),
			'COLSPAN' => 6,
			'ICON_MOVE_FIRST'			=> '<img src="' . $phpbb_root_path . 'ext/fq/boardnotices/adm/images/icon_first.gif" alt="' . $this->user->lang['MOVE_FIRST'] . '" title="' . $this->user->lang['MOVE_FIRST'] . '" />',
			'ICON_MOVE_FIRST_DISABLED'	=> '<img src="' . $phpbb_root_path . 'ext/fq/boardnotices/adm/images/icon_first_disabled.gif" alt="' . $this->user->lang['MOVE_FIRST'] . '" title="' . $this->user->lang['MOVE_FIRST'] . '" />',
			'ICON_MOVE_LAST'			=> '<img src="' . $phpbb_root_path . 'ext/fq/boardnotices/adm/images/icon_last.gif" alt="' . $this->user->lang['MOVE_LAST'] . '" title="' . $this->user->lang['MOVE_LAST'] . '" />',
			'ICON_MOVE_LAST_DISABLED'	=> '<img src="' . $phpbb_root_path . 'ext/fq/boardnotices/adm/images/icon_last_disabled.gif" alt="' . $this->user->lang['MOVE_LAST'] . '" title="' . $this->user->lang['MOVE_LAST'] . '" />',
		));

		$notices = $data_layer->getAllNotices();
		foreach ($notices as $notice)
		{
			$rules = $data_layer->getRulesFor($notice['notice_id']);
			$this->template->assign_block_vars('notices', array(
				'S_SPACER' => false,
				'TITLE' => $notice['title'],
				'PREVIEW_LINK' => append_sid("{$phpbb_root_path}index.$phpEx") . "&bnpk=" . $this->config['boardnotices_previewkey'] . "&bnid=" . (int) $notice['notice_id'],
				'RULES' => count($rules),
				'ACTIVE' => $notice['active'] ? $this->user->lang('YES') : $this->user->lang('NO'),
				'ENABLED' => $notice['active'] ? true : false,
				'U_ENABLE' => $this->u_action . '&amp;action=enable&amp;id=' . (int) $notice['notice_id'],
				'U_DISABLE' => $this->u_action . '&amp;action=disable&amp;id=' . (int) $notice['notice_id'],
				'U_EDIT' => $this->u_action . '&amp;action=edit&amp;id=' . (int) $notice['notice_id'],
				'U_DELETE' => $this->u_action . '&amp;action=delete&amp;id=' . (int) $notice['notice_id'],
				'U_MOVE_UP' => $this->u_action . '&amp;action=move_up&amp;id=' . (int) $notice['notice_id'],
				'U_MOVE_DOWN' => $this->u_action . '&amp;action=move_down&amp;id=' . (int) $notice['notice_id'],
				'U_MOVE_FIRST' => $this->u_action . '&amp;action=move_first&amp;id=' . (int) $notice['notice_id'],
				'U_MOVE_LAST' => $this->u_action . '&amp;action=move_last&amp;id=' . (int) $notice['notice_id'],
			));
			unset($rules);
		}
	}

	/**
	 * Display the notice setup form
	 *
	 * @param string $action
	 * @param array $data
	 * @param string $error
	 * @return void
	 */
	public function displayNoticeForm($action, $data, $error = '')
	{
		// Add the posting lang file needed by BBCodes
		$this->user->add_lang(array('posting'));

		// Add the board notices ACP lang file
		$this->addAdminLanguage();

		// Load a template from adm/style for our ACP page
		$this->tpl_name = 'board_notices_edit';

		// Set the page title for our ACP page
		$this->page_title = $this->user->lang('ACP_BOARD_NOTICE_SETTINGS');

		// Define the name of the form for use as a form key
		add_form_key($this->notice_form_name);

		// Include files needed for displaying BBCodes
		if (!function_exists('display_custom_bbcodes'))
		{
			include "{$this->phpbb_root_path}includes/functions_display.{$this->php_ext}";
		}

		// If form is previewed
		if ($this->request->is_set_post('preview'))
		{
			$error = $this->validateNoticeForm($data);
		}

		// Prepare a fresh notice preview
		$notice_text_preview = '';
		if ($this->request->is_set_post('preview'))
		{
			// @todo The preview message does not replace the extension variables
			$notice_text_preview = generate_text_for_display(
					$data['message'], $data['message_uid'], $data['message_bitfield'], $data['message_options']);
		}

		// prepare the notice text for editing inside the textbox
		$notice_text_edit = generate_text_for_edit(
				$data['message'], $data['message_uid'], $data['message_options']);

		// Output data to the template
		$this->template->assign_vars(array(
			'S_BOARD_NOTICES' => true,
			'ERRORS' => $error,
			'NOTICE_ID' => isset($data['notice_id']) ? $data['notice_id'] : '',
			'BOARD_NOTICE_ACTIVE' => $data['active'],
			'BOARD_NOTICE_TITLE' => $data['title'],
			'BOARD_NOTICE_TEXT' => $notice_text_edit['text'],
			'BOARD_NOTICE_PREVIEW' => $notice_text_preview,
			'BOARD_NOTICE_BGCOLOR' => $data['message_bgcolor'],
			'BOARD_NOTICE_STYLE' => $data['message_style'],
			'S_BBCODE_DISABLE_CHECKED' => !$notice_text_edit['allow_bbcode'],
			'S_SMILIES_DISABLE_CHECKED' => !$notice_text_edit['allow_smilies'],
			'S_MAGIC_URL_DISABLE_CHECKED' => !$notice_text_edit['allow_urls'],
			'S_BBCODE_ALLOWED' => true,
			'S_SMILIES_ALLOWED' => true,
			'S_LINKS_ALLOWED' => true,
			'S_BBCODE_IMG' => true,
			'S_BBCODE_FLASH' => true,
			'U_BACK' => $this->u_action,
			'U_ACTION' => $this->u_action . '&amp;action=' . $action,
			'ALLRULES_COLSPAN' => 4,
		));

		// Assigning custom bbcodes
		display_custom_bbcodes();

		$all_rules = $this->getAllRules();
		foreach ($all_rules as $rule_name => $rule_descriptions)
		{
			if (is_array($rule_descriptions))
			{
				$rule_description = $rule_descriptions['display_name'];
			}
			else
			{
				$rule_description = $rule_descriptions;
			}
			$rule_type = $this->rules_manager->getRuleType($rule_name);
			$rule_selected = isset($data['notice_rule_conditions'][$rule_name])
						? $data['notice_rule_conditions'][$rule_name]
						: $this->rules_manager->getRuleDefaultValue($rule_name);
			if (!is_array($rule_selected))
			{
				if (!is_null($rule_selected))
				{
					$rule_selected = array($rule_selected);
				}
				else
				{
					$rule_selected = array();
				}
			}

			$this->template->assign_block_vars('allrules', array(
				'NOTICE_RULE_ID' => isset($data['notice_rule_id'][$rule_name]) ? $data['notice_rule_id'][$rule_name] : '',
				'NOTICE_RULE_CHECKED' => isset($data['notice_rule_checked'][$rule_name]) ? true : false,
				'RULE_NAME' => $rule_name,
				'RULE_DESCRIPTION' => $rule_description,
				'RULE_UNIT' => (is_array($rule_descriptions)) ? $rule_descriptions['display_unit'] : '',
				// 'RULE_CONDITIONS' => $this->getDisplayConditions(
				// 		$rule_type,
				// 		$this->rules_manager->getRuleValues($rule_name),
				// 		$rule_selected,
				// 		"notice_rule_conditions[{$rule_name}]"
				// ),
				'RULE_TYPE' => $rule_type,
				'RULE_VALUES' => $this->rules_manager->getRuleValues($rule_name),
				'RULE_VALUES_COUNT' => count($this->rules_manager->getRuleValues($rule_name)),
				'RULE_DATA' => $rule_selected,
				'RULE_FORUMS' => ($rule_type == 'forums') ? make_forum_select($rule_selected, false, false, true) : '',
				'RULE_VARIABLES' => implode(', ', $this->rules_manager->getAvailableVars($rule_name)),
			));
		}
	}

	/**
	 * Display the global settings form
	 *
	 * @return void
	 */
	public function displaySettingsForm()
	{
		// Add the board notices ACP lang file
		$this->addAdminLanguage();

		// Load a template from adm/style for our ACP page
		$this->tpl_name = 'board_notices_settings';

		// Set the page title for our ACP page
		$this->page_title = $this->user->lang('ACP_BOARD_NOTICES_MANAGER');

		// Output data to the template
		$this->template->assign_vars(array(
			'S_BOARD_NOTICES' => true,
			'BOARD_NOTICES_ACTIVE' => $this->config['boardnotices_enabled'] ? true : false,
			'BOARD_NOTICE_DEFAULT_BGCOLOR' => $this->config['boardnotices_default_bgcolor'],
			'FORUMS_VISITS_ACTIVE' => $this->config['track_forums_visits'] ? true : false,
			'U_ACTION' => $this->u_action,
		));
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
		if (strpos($type, '|') === false)
		{
			$display .= $this->getSingleDisplayConditions($type, $input_name, $selected, $values);
		}
		else
		{
			$types = explode('|', $type);
			$i = 0;
			foreach ($types as $single_type)
			{
				$display .= $this->getSingleDisplayConditions($single_type, $input_name, $selected[$i], $values[$i], $i);
				$i++;
				$display .= '&nbsp;';
			}
		}
		return $display;
	}

	private function getSingleDisplayConditions($type, $input_name, $selected, $values, $index = 0)
	{
		$display = '';

		if ($index > 0)
		{
			$input_name .= $index;
		}

		switch ($type)
		{
			case 'int':
				$display .= $this->getDisplayIntConditions($input_name, $selected[0]);
				break;

			case 'date':
				$display .= $this->getDisplayDateConditions($input_name, $selected);
				break;

			case 'list':
			case 'multiple choice':
				$display .= $this->getDisplayListConditions($type, $input_name, $values, $selected);
				break;

			case 'forums':
				$display .= $this->getDisplayForumsConditions($input_name, $selected);
				break;

			case 'yesno':
				$display .= $this->getDisplayYesNoConditions($input_name, $selected[0]);
				break;

			default:
				break;
		}
		return $display;
	}

	private function getDisplayIntConditions($input_name, $selected)
	{
		// return '<input type="text" maxlength="5" size="10" name="' . $input_name . '[]" value="' . $selected . '">';
	}

	private function getDisplayDateConditions($input_name, $selected)
	{
		$display = '';
		// $display .= $this->user->lang('DAY') . $this->user->lang('COLON');
		// $display .= '&nbsp;<select name="' . $input_name . '[0]">';
		// $display .= '<option value="0">---</option>';
		// for ($i = 1; $i <= 31; $i++)
		// {
		// 	$display .= '<option value="' . $i . '"' . (($selected[0] == $i) ? ' selected' : '') . '>' . $i . '</option>';
		// }
		// $display .= '</select>&nbsp;';
		// $display .= $this->user->lang('MONTH') . $this->user->lang('COLON');
		// $display .= '&nbsp;<select name="' . $input_name . '[1]">';
		// $display .= '<option value="0">---</option>';
		// for ($i = 1; $i <= 12; $i++)
		// {
		// 	$display .= '<option value="' . $i . '"' . (($selected[1] == $i) ? ' selected' : '') . '>' . $i . '</option>';
		// }
		// $display .= '</select>&nbsp;';
		// $display .= $this->user->lang('YEAR') . $this->user->lang('COLON');
		// $display .= '&nbsp;<select name="' . $input_name . '[2]">';
		// $display .= '<option value="0">---</option>';
		// for ($i = 2015; $i <= 2038; $i++)
		// {
		// 	$display .= '<option value="' . $i . '"' . (($selected[2] == $i) ? ' selected' : '') . '>' . $i . '</option>';
		// }
		// $display .= '</select>&nbsp;';
		return $display;
	}

	private function getDisplayListConditions($type, $input_name, $values, $selected)
	{
		$display = '';
		// $size = (count($values) < 10) ? count($values) : 10;
		// $display .= '<select' . (($type == 'multiple choice') ? ' multiple="multiple"' : '') . ' size="' . $size . '" name="' . $input_name . '[]">';
		// if (is_array($values) && !empty($values))
		// {
		// 	foreach ($values as $key => $value)
		// 	{
		// 		$display .= '<option value="' . $key . '"' . (in_array($key, $selected) ? ' selected' : '') . '>' . $value . '</option>';
		// 	}
		// }
		// $display .= "</select>";
		return $display;
	}

	private function getDisplayForumsConditions($input_name, $selected)
	{
		$display = '';
		// $display .= '<select multiple="multiple" size="10" name="' . $input_name . '[]">';
		// $display .= make_forum_select($selected, false, false, true);
		// $display .= "</select>";
		return $display;
	}

	private function getDisplayYesNoConditions($input_name, $selected)
	{
		$display = '';
		// $display .= '<label><input type="radio" class="radio" id="' . $input_name . '" name="' . $input_name . '[0]" value="1"' . ($selected ? ' checked="checked"' : '') . ' /> ' . $this->user->lang['YES'] . '</label>';
		// $display .= '<label><input type="radio" class="radio" name="' . $input_name . '[0]" value="0"' . (!$selected ? ' checked="checked"' : '') . ' /> ' . $this->user->lang['NO'] . '</label>';
		return $display;
	}

	public function moveNotice($action, $notice_id)
	{
		/** @var \fq\boardnotices\repository\boardnotices $data_layer */
		$data_layer = $this->getRepository();

		$move_executed = $data_layer->moveNotice($action, $notice_id);

		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(array(
				'success' => $move_executed,
			));
		}
	}

	public function moveNoticeFirst($notice_id)
	{
		/** @var \fq\boardnotices\repository\boardnotices $data_layer */
		$data_layer = $this->getRepository();

		$move_executed = $data_layer->moveNoticeFirst($notice_id);

		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(array(
				'success' => $move_executed,
			));
		}
	}

	public function moveNoticeLast($notice_id)
	{
		/** @var \fq\boardnotices\repository\boardnotices $data_layer */
		$data_layer = $this->getRepository();

		$move_executed = $data_layer->moveNoticeLast($notice_id);

		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(array(
				'success' => $move_executed,
			));
		}
	}

	public function deleteNotice($notice_id)
	{
		/** @var \fq\boardnotices\repository\boardnotices $data_layer */
		$data_layer = $this->getRepository();

		$delete_executed = $data_layer->deleteNotice($notice_id);

		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(array(
				'success' => $delete_executed,
			));
		}
	}

	public function enableNotice($action, $notice_id)
	{
		/** @var \fq\boardnotices\repository\boardnotices $data_layer */
		$data_layer = $this->getRepository();

		$executed = $data_layer->enableNotice($action, $notice_id);

		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(array(
				'success' => $executed,
			));
		}
	}

	/**
	 * Returns the current repository
	 *
	 * @global type $phpbb_container
	 * @staticvar \fq\boardnotices\repository\boardnotices $repository
	 * @return \fq\boardnotices\repository\boardnotices
	 */
	protected function getRepository()
	{
		global $phpbb_container;
		static $repository = null;

		if (is_null($repository))
		{
			$repository = $phpbb_container->get('fq.boardnotices.repository.boardnotices');
		}
		return $repository;
	}

	/**
	 * Returns the current serializer service
	 *
	 * @global type $phpbb_container
	 * @staticvar \fq\boardnotices\service\serializer $serializer
	 * @return \fq\boardnotices\service\serializer
	 */
	protected function getSerializer()
	{
		global $phpbb_container;
		static $serializer = null;

		if (is_null($serializer))
		{
			$serializer = $phpbb_container->get('fq.boardnotices.service.serializer');
		}
		return $serializer;
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
			'message_style' => '',
			'notice_rule_id' => array(),
			'notice_rule_checked' => array(),
			'notice_rule_conditions' => array(),
		);
		return $data;
	}

	private function loadNotice($notice_id)
	{
		$data_layer = $this->getRepository();
		$serializer = $this->getSerializer();
		$notice = $data_layer->getNoticeFromId($notice_id);
		$notice['notice_rule_id'] = array();
		$notice['notice_rule_checked'] = array();
		$notice['notice_rule_conditions'] = array();

		$rules = $data_layer->getRulesFor($notice_id);
		foreach ($rules as $rule)
		{
			$notice['notice_rule_id'][$rule['rule']] = $rule['notice_rule_id'];
			$notice['notice_rule_checked'][$rule['rule']] = 1;
			$conditions = $serializer->decode($rule['conditions']);
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
	 * @todo It can be confusing that it's this method to get the data from the form
	 * @param array $data
	 * @param bool $for_submit
	 * @return string
	 */
	private function validateNoticeForm(&$data, $for_submit = false)
	{
		$error = '';

		// Add the board notices ACP lang file
		$this->addAdminLanguage();

		// Test if form key is valid
		if (!check_form_key($this->notice_form_name))
		{
			$error = $this->user->lang('FORM_INVALID');
			return $error;
		}

		// Get new values from the form
		$data['title'] = $this->request->variable('board_notice_title', '', true);
		$data['message'] = $this->request->variable('board_notice_text', '', true);
		$data['title'] = $this->request->variable('board_notice_title', '', true);
		$data['message_bgcolor'] = $this->request->variable('board_notice_bgcolor', '', true);
		$data['message_style'] = $this->request->variable('board_notice_style', '', true);

		// Get config options from the form
		$data['active'] = $this->request->variable('board_notice_active', false);

		if (empty($data['title']) || empty($data['message']))
		{
			if (empty($data['title']))
			{
				$error .= $this->user->lang('ERROR_EMPTY_TITLE') . "<br />";
			}
			if (empty($data['message']))
			{
				$error .= $this->user->lang('ERROR_EMPTY_MESSAGE') . "<br />";
			}
			return $error;
		}

		// Prepare notice text for storage
		generate_text_for_storage(
				$data['message'],
				$data['message_uid'],
				$data['message_bitfield'],
				$data['message_options'],
				!$this->request->variable('disable_bbcode', false),
				!$this->request->variable('disable_magic_url', false),
				!$this->request->variable('disable_smilies', false)
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
		$serializer = $this->getSerializer();
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
						'conditions' => $serializer->encode($data['notice_rule_conditions'][$rule_name]),
					);
				}
				else
				{
					$to_insert[] = array(
						'notice_id' => $notice_id,
						'rule' => $rule_name,
						'conditions' => $serializer->encode($data['notice_rule_conditions'][$rule_name]),
					);
				}
			}
		}
		$data_layer = $this->getRepository();

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
		// Add the board notices ACP lang file
		$this->addAdminLanguage();

		$rules_data = array(
			'notice_rule_id' => $data['notice_rule_id'],
			'notice_rule_checked' => $data['notice_rule_checked'],
			'notice_rule_conditions' => $data['notice_rule_conditions'],
		);
		unset($data['notice_rule_id']);
		unset($data['notice_rule_checked']);
		unset($data['notice_rule_conditions']);

		$data_layer = $this->getRepository();
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
		// Add the board notices ACP lang file
		$this->addAdminLanguage();

		$rules_data = array(
			'notice_rule_id' => $data['notice_rule_id'],
			'notice_rule_checked' => $data['notice_rule_checked'],
			'notice_rule_conditions' => $data['notice_rule_conditions'],
		);
		unset($data['notice_rule_id']);
		unset($data['notice_rule_checked']);
		unset($data['notice_rule_conditions']);

		$data_layer = $this->getRepository();
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

	private function saveSettings()
	{
		$data = array();

		// Add the board notices ACP lang file
		$this->addAdminLanguage();

		// Get config options from the form
		$data['boardnotices_enabled'] = $this->request->variable('board_notices_active', true);
		$data['track_forums_visits'] = $this->request->variable('forums_visits_active', true);
		$data['boardnotices_default_bgcolor'] = $this->request->variable('board_notice_default_bgcolor', '');

		// Save data to the config
		$this->config->set('boardnotices_enabled', ($data['boardnotices_enabled'] ? true : false));
		$this->config->set('track_forums_visits', ($data['track_forums_visits'] ? true : false));
		$this->config->set('boardnotices_default_bgcolor', $data['boardnotices_default_bgcolor']);

		// Logs the settings update
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_BOARD_NOTICES_SETTINGS', time(), array());
		// Output message to user for the update
		trigger_error($this->user->lang('BOARD_NOTICES_SETTINGS_SAVED') . adm_back_link($this->u_action));
	}

	private function resetForumVisits($id, $mode, $action)
	{
		// Add the board notices ACP lang file
		$this->addAdminLanguage();

		if (!confirm_box(true))
		{
			confirm_box(false, $this->user->lang['RESET_FORUM_VISITS_CONFIRMATION'], build_hidden_fields(array(
				'i'			=> $id,
				'mode'		=> $mode,
				'action'	=> $action,
			)));
		}
		else
		{
			$repository = $this->getRepository();
			$repository->clearForumVisited();

			if ($this->request->is_ajax())
			{
				trigger_error('RESET_FORUM_VISITS_SUCCESS');
			}
		}
	}

	private function addAdminLanguage()
	{
		// Keep compatibility with phpBB 3.1 (for now)
		$this->user->add_lang_ext('fq/boardnotices', 'boardnotices_acp');
	}
}

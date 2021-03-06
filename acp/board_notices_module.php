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

const NEW_LINE = "<br />";

class board_notices_module
{

	private $notice_form_name = 'acp_board_notice';

	private $p_master;

	/** @var \fq\boardnotices\domain\rules $rules_manager */
	private $rules_manager;

	/** @var \fq\boardnotices\service\serializer $serializer */
	private $serializer;

	/** @var \fq\boardnotices\repository\notices $notices_repository */
	private $notices_repository;

	/** @var \fq\boardnotices\acp\settings $settings */
	private $settings;

	/** @var \fq\boardnotices\service\phpbb\functions_interface $functions */
	private $functions;

	/** @var \phpbb\config\config $config */
	private $config;

	/** @var \phpbb\log\log $log */
	private $log;

	/** @var \phpbb\request\request $request */
	private $request;

	/** @var \phpbb\template\template $template */
	private $template;

	/** @var \phpbb\user $user */
	private $user;

	/** @var \phpbb\language\language $language */
	private $language;

	/** @var string */
	private $phpbb_root_path;

	/** @var string */
	private $php_ext;

	/** This needs to be kept public, phpBB needs access to these properties */
	/** @var string $u_action */
	public $u_action;
	/** @var string $module_path */
	public $module_path;	// Usually contain "./../includes/acp/"
	/** @var string $tpl_name */
	public $tpl_name;
	/** @var string $page_title */
	public $page_title;

	/**
	 * I find it rather annoying that this class won't be instanciated on the common phpBB/Symfony model,
	 * but with this unique (and useless) parameter instead.
	 * => No dependency injection is possible here
	 *
	 * @param mixed $p_master
	 */
	public function __construct(&$p_master)
	{
		$this->p_master = &$p_master;
	}

	/**
	 * This is the entry point when the user selects the settings or manage menu
	 *
	 * @param string $id
	 * @param string $mode
	 * @return string $error_message
	 */
	public function main($id, $mode)
	{
		global $phpbb_container, $phpbb_root_path, $phpEx;

		// This cannot be injected at this point. Hopefully in a future version :-)
		$this->rules_manager = $phpbb_container->get('fq.boardnotices.domain.rules');
		$this->serializer = $phpbb_container->get('fq.boardnotices.service.serializer');
		$this->notices_repository = $phpbb_container->get('fq.boardnotices.repository.notices');
		$this->settings = $phpbb_container->get('fq.boardnotices.acp.settings');
		$this->functions = $phpbb_container->get('fq.boardnotices.service.phpbb.functions');
		$this->config = $phpbb_container->get('config');
		$this->log = $phpbb_container->get('log');
		$this->request = $phpbb_container->get('request');
		$this->template = $phpbb_container->get('template');
		$this->user = $phpbb_container->get('user');
		$this->language = $phpbb_container->get('language');
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $phpEx;

		// Add the board notices ACP lang file
		$this->addAdminLanguage();

		// This function won't be fired with a unknown $mode so there's no need to send an error at the end
		if ($mode == "manage")
		{
			return $this->manage_module($id, $mode);
		}
		else if ($mode == "settings")
		{
			return $this->settings_module($id, $mode);
		}
	}

	/**
	 * Global settings module of the extension
	 *
	 * @param string $id
	 * @param string $mode
	 * @return void
	 */
	private function settings_module($id, $mode)
	{
		$action = $this->request->variable('action', '');
		if ($action == 'reset_forum_visits')
		{
			$this->settings->resetForumVisits($id, $mode, $action);
		}
		else
		{
			if ($this->request->is_set_post('submit'))
			{
				$this->settings->saveSettings(array(
					'boardnotices_enabled' => $this->request->variable('board_notices_active', true),
					'track_forums_visits' => $this->request->variable('forums_visits_active', true),
					'boardnotices_default_bgcolor' => $this->request->variable('board_notice_default_bgcolor', ''),
				));
				// Output confirmation message to user
				trigger_error($this->language->lang('BOARD_NOTICES_SETTINGS_SAVED') . $this->functions->adm_back_link($this->u_action));
			}
			else
			{
				$this->displaySettingsForm();
			}
		}
	}

	/**
	 * Display the global settings form
	 *
	 * @return void
	 */
	private function displaySettingsForm()
	{
		// Load a template from adm/style for our ACP page
		$this->tpl_name = 'board_notices_settings';

		// Set the page title for our ACP page
		$this->page_title = $this->language->lang('ACP_BOARD_NOTICES_MANAGER');

		$settings = $this->settings->loadSettings();

		// Output data to the template
		$this->template->assign_vars(array(
			'S_BOARD_NOTICES' => true,
			'BOARD_NOTICES_ACTIVE' => $settings['boardnotices_enabled'],
			'FORUMS_VISITS_ACTIVE' => $settings['track_forums_visits'],
			'BOARD_NOTICE_DEFAULT_BGCOLOR' => $settings['boardnotices_default_bgcolor'],
			'U_ACTION' => $this->u_action,
		));
	}

	/**
	 * Admin module to manage the notices
	 *
	 * @param string $id
	 * @param string $mode
	 * @return void
	 */
	private function manage_module($id, $mode)
	{
		/** @var string $action */
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
				$this->addNotice($action);
				break;

			case 'edit':
				$this->editNotice($action, $notice_id);
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
	}

	private function addNotice($action)
	{
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
	}

	private function editNotice($action, $notice_id)
	{
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
	}

	/**
	 * Display the notice manager page
	 *
	 * @return void
	 */
	private function displayManager()
	{
		// Load a template from adm/style for our ACP page
		$this->tpl_name = 'board_notices';

		// Set the page title for our ACP page
		$this->page_title = $this->language->lang('ACP_BOARD_NOTICES_MANAGER');

		// Output data to the template
		$this->template->assign_vars(array(
			'S_BOARD_NOTICES' => true,
			'BOARD_NOTICE_ADD' => $this->language->lang('BOARD_NOTICE_ADD'),
			'COLSPAN' => 6,
			'ICON_MOVE_FIRST'			=> $this->getIconImageTemplate('icon_first.gif', 'MOVE_FIRST'),
			'ICON_MOVE_FIRST_DISABLED'	=> $this->getIconImageTemplate('icon_first_disabled.gif', 'MOVE_FIRST'),
			'ICON_MOVE_LAST'			=> $this->getIconImageTemplate('icon_last.gif', 'MOVE_LAST'),
			'ICON_MOVE_LAST_DISABLED'	=> $this->getIconImageTemplate('icon_last_disabled.gif', 'MOVE_LAST'),
		));

		$notices = $this->settings->loadNotices();
		foreach ($notices as $notice)
		{
			$notice_id = $notice['notice_id'];
			$this->template->assign_block_vars('notices', array(
				'S_SPACER' => false,
				'TITLE' => $notice['title'],
				'PREVIEW_LINK' => append_sid("{$this->phpbb_root_path}index.{$this->php_ext}") . "&bnpk=" . $this->config[constants::$CONFIG_PREVIEW_KEY] . "&bnid=" . (int) $notice_id,
				'RULES' => $notice['rulesCount'],
				'ENABLED' => $notice['active'] ? true : false,
				'DISMISS' => $notice['dismissable'] ? true : false,
				'U_ENABLE' => $this->getIconLinkTemplate('enable', $notice_id),
				'U_DISABLE' => $this->getIconLinkTemplate('disable', $notice_id),
				'U_EDIT' => $this->getIconLinkTemplate('edit', $notice_id),
				'U_DELETE' => $this->getIconLinkTemplate('delete', $notice_id),
				'U_MOVE_UP' => $this->getIconLinkTemplate('move_up', $notice_id),
				'U_MOVE_DOWN' => $this->getIconLinkTemplate('move_down', $notice_id),
				'U_MOVE_FIRST' => $this->getIconLinkTemplate('move_first', $notice_id),
				'U_MOVE_LAST' => $this->getIconLinkTemplate('move_last', $notice_id),
			));
		}
	}

	private function getIconImageTemplate($icon, $title)
	{
		return '<img src="' . $this->phpbb_root_path . 'ext/fq/boardnotices/adm/images/' . $icon . '" title="' . $this->language->lang($title) . '" />';
	}

	private function getIconLinkTemplate($action, $notice_id)
	{
		return $this->u_action . '&amp;action=' . $action . '&amp;id=' . (int) $notice_id;
	}

	/**
	 * Display the notice setup form
	 *
	 * @param string $action
	 * @param array $data
	 * @param string $error
	 * @return void
	 */
	private function displayNoticeForm($action, $data, $error = '')
	{
		// Add the posting lang file needed by BBCodes
		$this->language->add_lang(array('posting'));

		// Load a template from adm/style for our ACP page
		$this->tpl_name = 'board_notices_edit';

		// Set the page title for our ACP page
		$this->page_title = $this->language->lang('ACP_BOARD_NOTICE_SETTINGS');

		// Define the name of the form for use as a form key
		$this->functions->add_form_key($this->notice_form_name);

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
			$notice_text_preview = $this->functions->generate_text_for_display(
					$data['message'], $data['message_uid'], $data['message_bitfield'], $data['message_options']);
		}

		// prepare the notice text for editing inside the textbox
		$notice_text_edit = $this->functions->generate_text_for_edit(
				$data['message'], $data['message_uid'], $data['message_options']);

		// Output data to the template
		$this->template->assign_vars(array(
			'S_BOARD_NOTICES' => true,
			'ERRORS' => $error,
			'NOTICE_ID' => isset($data['notice_id']) ? $data['notice_id'] : '',
			'BOARD_NOTICE_ACTIVE' => $data['active'],
			'BOARD_NOTICE_TITLE' => $data['title'],
			'BOARD_NOTICE_DISMISSABLE' => $data['dismissable'],
			'BOARD_NOTICE_RESET_AFTER' => !empty($data['reset_after']) ? $data['reset_after'] : '',
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
			'MONTH_FULLNAME' => $this->getMonthFullNameArray(),
		));

		// Assigning custom bbcodes
		$this->functions->display_custom_bbcodes();

		$all_rules = $this->settings->getAllRules();
		foreach ($all_rules as $rule_name => $rule_descriptions)
		{
			$this->generateTemplateVariablesForRule($data, $rule_name, $rule_descriptions);
		}
	}

	private function getMonthFullNameArray()
	{
		return array(
			1 => $this->language->lang(array('datetime', 'January')),
			2 => $this->language->lang(array('datetime', 'February')),
			3 => $this->language->lang(array('datetime', 'March')),
			4 => $this->language->lang(array('datetime', 'April')),
			5 => $this->language->lang(array('datetime', 'May')),
			6 => $this->language->lang(array('datetime', 'June')),
			7 => $this->language->lang(array('datetime', 'July')),
			8 => $this->language->lang(array('datetime', 'August')),
			9 => $this->language->lang(array('datetime', 'September')),
			10 => $this->language->lang(array('datetime', 'October')),
			11=> $this->language->lang(array('datetime', 'November')),
			12 => $this->language->lang(array('datetime', 'December')),
		);
	}

	private function generateTemplateVariablesForRule(&$data, $rule_name, $rule_descriptions)
	{
		$rule_explain = '';
		if (is_array($rule_descriptions))
		{
			$rule_description = $rule_descriptions[constants::$RULE_DISPLAY_NAME];
			$rule_explain = $rule_descriptions[constants::$RULE_DISPLAY_EXPLAIN];
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
		$rule_values = $this->rules_manager->getRuleValues($rule_name);

		$variables = array(
			'NOTICE_RULE_ID' => isset($data['notice_rule_id'][$rule_name]) ? $data['notice_rule_id'][$rule_name] : '',
			'NOTICE_RULE_CHECKED' => isset($data['notice_rule_checked'][$rule_name]) ? true : false,
			'RULE_NAME' => $rule_name,
			'RULE_DESCRIPTION' => $rule_description,
			'RULE_EXPLAIN' => $rule_explain,
			'RULE_VARIABLES' => $this->rules_manager->getAvailableVars($rule_name),
			'RULE_PARAMETERS_COUNT' => is_array($rule_type) ? count($rule_type) : 1,
		);
		if (!$this->rules_manager->ruleHasMultipleParameters($rule_name))
		{
			// Only one parameter
			$variables = array_merge($variables, array('parameters' => array(array(
				'RULE_TYPE' => $rule_type,
				'RULE_VALUES' => $rule_values,
				'RULE_VALUES_COUNT' => (!empty($rule_values) && is_array($rule_values)) ? count($rule_values) : 0,
				'RULE_DATA' => $rule_selected,
				'RULE_FORUMS' => ($rule_type == 'forums') ? make_forum_select($rule_selected, false, false, true) : '',
				'RULE_UNIT' => (is_array($rule_descriptions)) ? $rule_descriptions['display_unit'] : '',
				'PARAMETER_INDEX' => '',	// Only one parameter
			))));
		}
		else
		{
			// Multi parameters rules
			$params = array();
			for ($index=0; $index < count($rule_type); $index++)
			{
				$params[$index] = array(
					'RULE_TYPE' => $rule_type[$index],
					'RULE_VALUES' => $rule_values[$index],
					'RULE_VALUES_COUNT' => (!empty($rule_values[$index]) && is_array($rule_values[$index])) ? count($rule_values[$index]) : 0,
					'RULE_DATA' => $rule_selected[$index],
					'RULE_FORUMS' => ($rule_type[$index] == 'forums') ? make_forum_select($rule_selected[$index], false, false, true) : '',
					'RULE_UNIT' => (is_array($rule_descriptions)) ? $rule_descriptions['display_unit'][$index] : '',
					'PARAMETER_INDEX' => $index,
				);
			}
			$variables = array_merge($variables, array('parameters' => $params));
		}
		$this->template->assign_block_vars('allrules', $variables);
	}

	private function sendResponse($success)
	{
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(array(
				'success' => $success,
			));
		}
	}

	private function moveNotice($action, $notice_id)
	{
		$this->sendResponse(
			$this->notices_repository->moveNotice($action, $notice_id)
		);
	}

	private function moveNoticeFirst($notice_id)
	{
		$this->sendResponse(
			$this->notices_repository->moveNoticeFirst($notice_id)
		);
	}

	private function moveNoticeLast($notice_id)
	{
		$this->sendResponse(
			$this->notices_repository->moveNoticeLast($notice_id)
		);
	}

	private function deleteNotice($notice_id)
	{
		$this->sendResponse(
			$this->notices_repository->deleteNotice($notice_id)
		);
	}

	private function enableNotice($action, $notice_id)
	{
		$this->sendResponse(
			$this->notices_repository->enableNotice($action, $notice_id)
		);
	}

	private function newBlankNotice()
	{
		$data = array(
			'active' => false,
			'title' => '',
			'dismissable' => false,
			'reset_after' => 0,
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
		$notice = $this->notices_repository->getNoticeFromId($notice_id);
		$notice['notice_rule_id'] = array();
		$notice['notice_rule_checked'] = array();
		$notice['notice_rule_conditions'] = array();

		$rules = $this->notices_repository->getRulesFor($notice_id);
		foreach ($rules as $rule)
		{
			$notice['notice_rule_id'][$rule['rule']] = $rule['notice_rule_id'];
			$notice['notice_rule_checked'][$rule['rule']] = 1;
			$conditions = $this->serializer->decode($rule['conditions']);
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

		// Test if form key is valid
		if (!$this->functions->check_form_key($this->notice_form_name))
		{
			return $this->language->lang('FORM_INVALID');
		}

		// Get new values from the form
		$data['active'] = $this->request->variable('board_notice_active', false);
		$data['title'] = $this->request->variable('board_notice_title', '', true);
		$data['dismissable'] = $this->request->variable('board_notice_dismissable', false);
		$data['reset_after'] = (int) $this->request->variable('board_notice_reset_after', 0);
		$data['message'] = $this->request->variable('board_notice_text', '', true);
		$data['title'] = $this->request->variable('board_notice_title', '', true);
		$data['message_bgcolor'] = $this->request->variable('board_notice_bgcolor', '', true);
		$data['message_style'] = $this->request->variable('board_notice_style', '', true);

		if (empty($data['title']) || empty($data['message']))
		{
			if (empty($data['title']))
			{
				$error .= $this->language->lang('ERROR_EMPTY_TITLE') . NEW_LINE;
			}
			if (empty($data['message']))
			{
				$error .= $this->language->lang('ERROR_EMPTY_MESSAGE') . NEW_LINE;
			}
		}

		if (!empty($data['message']))
		{
			// Prepare notice text for storage
			$this->functions->generate_text_for_storage(
					$data['message'],
					$data['message_uid'],
					$data['message_bitfield'],
					$data['message_options'],
					!$this->request->variable('disable_bbcode', false),
					!$this->request->variable('disable_magic_url', false),
					!$this->request->variable('disable_smilies', false)
			);
		}

		// Get config for all the rules
		$all_rules = $this->settings->getAllRules();
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
			$notice_rule_conditions = $this->getNoticeRuleConditionsFromInput($rule_name);
			if (!empty($notice_rule_conditions))
			{
				$data['notice_rule_conditions'][$rule_name] = $notice_rule_conditions;
				if ($notice_rule_checked && !$this->rules_manager->validateRuleValues($rule_name, $notice_rule_conditions))
				{
					$error .= "Invalid value entered for rule {$rule_name}<br />";
				}
			}
			else
			{
				unset($data['notice_rule_conditions'][$rule_name]);
			}
		}

		// In case the parsing of the message failed
		if (empty($error) && empty($data['message']))
		{
			return $this->language->lang('ERROR_EMPTY_MESSAGE') . NEW_LINE;
		}
		return $error;
	}

	private function getNoticeRuleConditionsFromInput($rule_name)
	{
		$notice_param_count = $this->request->variable(array('notice_rule_param_count', $rule_name), 1);
		if ($notice_param_count == 1)
		{
			return $this->request->variable(array('notice_rule_conditions', $rule_name), array(''));
		}
		// Multi-parameters (cannot use an array of arrays, it's not supported by the sanitizer)
		$notice_rule_conditions = array();
		for ($index=0; $index < $notice_param_count; $index++)
		{
			$notice_rule_conditions[$index] = $this->request->variable(array('notice_rule_conditions' . $index, $rule_name), array(''));
		}
		return $notice_rule_conditions;
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
						'conditions' => $this->getRuleConditions($data, $rule_name),
					);
				}
				else
				{
					$to_insert[] = array(
						'notice_id' => $notice_id,
						'rule' => $rule_name,
						'conditions' => $this->getRuleConditions($data, $rule_name),
					);
				}
			}
		}
		if (!empty($to_delete))
		{
			$this->notices_repository->deleteRules($to_delete);
		}
		if (!empty($to_update))
		{
			$this->notices_repository->updateRules($to_update);
		}
		if (!empty($to_insert))
		{
			$this->notices_repository->insertRules($to_insert);
		}
	}

	private function getRuleConditions(&$data, $ruleName)
	{
		if (array_key_exists($ruleName, $data['notice_rule_conditions']))
		{
			return $this->serializer->encode($data['notice_rule_conditions'][$ruleName]);
		}
		return $this->serializer->encode(null);
	}

	private function saveNewNotice(&$data)
	{
		$rules_data = array(
			'notice_rule_id' => $data['notice_rule_id'],
			'notice_rule_checked' => $data['notice_rule_checked'],
			'notice_rule_conditions' => $data['notice_rule_conditions'],
		);
		unset($data['notice_rule_id']);
		unset($data['notice_rule_checked']);
		unset($data['notice_rule_conditions']);

		$notice_id = $this->notices_repository->saveNewNotice($data);
		if ($notice_id > 0)
		{
			$this->saveRules($notice_id, $rules_data);
		}

		// Log the new notice
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_BOARD_NOTICES_ADDED', time(), array($data['title']));
		// Output message to user for the update
		trigger_error($this->language->lang('BOARD_NOTICE_SAVED') . $this->functions->adm_back_link($this->u_action));
	}

	private function saveNotice($notice_id, &$data)
	{
		$rules_data = array(
			'notice_rule_id' => $data['notice_rule_id'],
			'notice_rule_checked' => $data['notice_rule_checked'],
			'notice_rule_conditions' => $data['notice_rule_conditions'],
		);
		unset($data['notice_rule_id']);
		unset($data['notice_rule_checked']);
		unset($data['notice_rule_conditions']);

		$this->notices_repository->saveNotice($notice_id, $data);
		$this->saveRules($notice_id, $rules_data);

		// Log the update
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_BOARD_NOTICES_UPDATED', time(), array($data['title']));
		// Output message to user for the update
		trigger_error($this->language->lang('BOARD_NOTICE_SAVED') . $this->functions->adm_back_link($this->u_action));
	}

	private function addAdminLanguage()
	{
		$this->language->add_lang('boardnotices_acp', 'fq/boardnotices');
	}
}

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
			$notice_id = request_var('id', 0);
			
			switch ($action) {
				case 'move_up':
				case 'move_down':
					$this->moveNotice($action, $notice_id);
					break;
				
				default :
					$this->displayManager();
					break;
			}
			return;
		}
		
		// Add the posting lang file needed by BBCodes
		$this->user->add_lang(array('posting'));

		// Add the board announcements ACP lang file
		$this->user->add_lang_ext('fq/boardnotices', 'boardnotices_acp');

		// Load a template from adm/style for our ACP page
		$this->tpl_name = 'board_notices';

		// Set the page title for our ACP page
		$this->page_title = 'L_ACP_BOARD_NOTICES_SETTINGS';

		// Define the name of the form for use as a form key
		$form_name = 'acp_board_notices';
		add_form_key($form_name);

		// Set an empty error string
		$error = '';

		// Include files needed for displaying BBCodes
		if (!function_exists('display_custom_bbcodes'))
		{
			include($this->phpbb_root_path . 'includes/functions_display.' . $this->php_ext);
		}

		// Get all board announcement data from the config_text table in the database
		$data = $this->config_text->get_array(array(
			'announcement_text',
			'announcement_uid',
			'announcement_bitfield',
			'announcement_options',
			'announcement_bgcolor',
		));

		// If form is submitted or previewed
		if ($this->request->is_set_post('submit') || $this->request->is_set_post('preview'))
		{
			// Test if form key is valid
			if (!check_form_key($form_name))
			{
				$error = $this->user->lang('FORM_INVALID');
			}

			// Get new announcement text and bgcolor values from the form
			$data['announcement_text'] = $this->request->variable('board_announcements_text', '', true);
			$data['announcement_bgcolor'] = $this->request->variable('board_announcements_bgcolor', '', true);

			// Get config options from the form
			$enable_announcements = $this->request->variable('board_announcements_enable', false);
			$allow_guests = $this->request->variable('board_announcements_guests', false);
			$dismiss_announcements = $this->request->variable('board_announcements_dismiss', false);

			// Prepare announcement text for storage
			generate_text_for_storage(
				$data['announcement_text'],
				$data['announcement_uid'],
				$data['announcement_bitfield'],
				$data['announcement_options'],
				!$this->request->variable('disable_bbcode', false),
				!$this->request->variable('disable_magic_url', false),
				!$this->request->variable('disable_smilies', false)
			);

			// Store the announcement text and settings if submitted with no errors
			if (empty($error) && $this->request->is_set_post('submit'))
			{
				// Store the config enable/disable state
				$this->config->set('board_announcements_enable', $enable_announcements);
				$this->config->set('board_announcements_guests', $allow_guests);
				$this->config->set('board_announcements_dismiss', $dismiss_announcements);

				// Store the announcement settings to the config_table in the database
				$this->config_text->set_array(array(
					'announcement_text'			=> $data['announcement_text'],
					'announcement_uid'			=> $data['announcement_uid'],
					'announcement_bitfield'		=> $data['announcement_bitfield'],
					'announcement_options'		=> $data['announcement_options'],
					'announcement_bgcolor'		=> $data['announcement_bgcolor'],
					'announcement_timestamp'	=> time(),
				));

				// Log the announcement update
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'BOARD_ANNOUNCEMENTS_UPDATED_LOG');

				// Output message to user for the announcement update
				trigger_error($this->user->lang('BOARD_ANNOUNCEMENTS_UPDATED') . adm_back_link($this->u_action));
			}
		}

		// Prepare a fresh announcement preview
		$announcement_text_preview = '';
		if ($this->request->is_set_post('preview'))
		{
			$announcement_text_preview = generate_text_for_display($data['announcement_text'], $data['announcement_uid'], $data['announcement_bitfield'], $data['announcement_options']);
		}

		// prepare the announcement text for editing inside the textbox
		$announcement_text_edit = generate_text_for_edit($data['announcement_text'], $data['announcement_uid'], $data['announcement_options']);

		// Output data to the template
		$this->template->assign_vars(array(
			'ERRORS'						=> $error,
			'BOARD_ANNOUNCEMENTS_ENABLED'	=> (isset($enable_announcements)) ? $enable_announcements : $this->config['board_announcements_enable'],
			'BOARD_ANNOUNCEMENTS_GUESTS'	=> (isset($allow_guests)) ? $allow_guests: $this->config['board_announcements_guests'],
			'BOARD_ANNOUNCEMENTS_DISMISS'	=> (isset($dismiss_announcements)) ? $dismiss_announcements : $this->config['board_announcements_dismiss'],
			'BOARD_ANNOUNCEMENTS_TEXT'		=> $announcement_text_edit['text'],
			'BOARD_ANNOUNCEMENTS_PREVIEW'	=> $announcement_text_preview,
			'BOARD_ANNOUNCEMENTS_BGCOLOR'	=> $data['announcement_bgcolor'],

			'S_BBCODE_DISABLE_CHECKED'		=> !$announcement_text_edit['allow_bbcode'],
			'S_SMILIES_DISABLE_CHECKED'		=> !$announcement_text_edit['allow_smilies'],
			'S_MAGIC_URL_DISABLE_CHECKED'	=> !$announcement_text_edit['allow_urls'],

			'BBCODE_STATUS'			=> $this->user->lang('BBCODE_IS_ON', '<a href="' . append_sid("{$this->phpbb_root_path}faq.{$this->php_ext}", 'mode=bbcode') . '">', '</a>'),
			'SMILIES_STATUS'		=> $this->user->lang('SMILIES_ARE_ON'),
			'IMG_STATUS'			=> $this->user->lang('IMAGES_ARE_ON'),
			'FLASH_STATUS'			=> $this->user->lang('FLASH_IS_ON'),
			'URL_STATUS'			=> $this->user->lang('URL_IS_ON'),

			'S_BBCODE_ALLOWED'		=> true,
			'S_SMILIES_ALLOWED'		=> true,
			'S_BBCODE_IMG'			=> true,
			'S_BBCODE_FLASH'		=> true,
			'S_LINKS_ALLOWED'		=> true,

			'S_BOARD_ANNOUNCEMENTS'	=> true,

			'U_ACTION'				=> $this->u_action,
		));

		// Assigning custom bbcodes
		display_custom_bbcodes();
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
		$this->page_title = $this->user->lang('L_ACP_BOARD_NOTICES_MANAGER');
		
		// Output data to the template
		$this->template->assign_vars(array(
			'L_ACP_BOARD_NOTICES_MANAGER'			=> $this->user->lang('L_ACP_BOARD_NOTICES_MANAGER'),
			'L_ACP_BOARD_NOTICES_MANAGER_EXPLAIN'	=> $this->user->lang('L_ACP_BOARD_NOTICES_MANAGER_EXPLAIN'),
			'L_TITLE'								=> $this->user->lang('L_TITLE'),
			'L_ICON_ADD'							=> $this->user->lang('L_ADD'),
			'COLSPAN'								=> 3,
		));
		
		$notices = $data_layer->getAllNotices();
		foreach ($notices as $notice) {
			$this->template->assign_block_vars('items', array(
				'S_SPACER'		=> false,
				'TITLE'			=> $notice['title'],
				'ACTIVE'		=> $notice['active'] ? 'Yes' : 'No',
				'U_EDIT'		=> $this->u_action . '&amp;action=edit&amp;id=' . $notice['notice_id'],
				'U_DELETE'		=> $this->u_action . '&amp;action=delete&amp;id=' . $notice['notice_id'],
				'U_MOVE_UP'		=> $this->u_action . '&amp;action=move_up&amp;id=' . $notice['notice_id'],
				'U_MOVE_DOWN'	=> $this->u_action . '&amp;action=move_down&amp;id=' . $notice['notice_id'],
			));
		}
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
				'success'	=> $move_executed,
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
}

<?php

/**
 *
 * Board Notices Manager
 *
 * @version 1.0.0
 * @copyright (c) Fred Quointeau
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\service\phpbb;

class functions implements functions_interface
{
	public function confirm_box($check, $title = '', $hidden = '', $html_body = 'confirm_body.html', $u_action = '')
	{
		return confirm_box($check, $title, $hidden, $html_body, $u_action);
	}

	public function adm_back_link($u_action)
	{
		return adm_back_link($u_action);
	}

	public function display_custom_bbcodes()
	{
		if (!function_exists('display_custom_bbcodes'))
		{
			global $phpbb_root_path, $phpEx;
			include_once "{$phpbb_root_path}includes/functions_display.{$phpEx}";
		}
		display_custom_bbcodes();
	}

	public function add_form_key($form_name, $template_variable_suffix = '')
	{
		add_form_key($form_name, $template_variable_suffix);
	}

	public function check_form_key($form_name, $timespan = false)
	{
		return check_form_key($form_name, $timespan);
	}

	public function generate_text_for_storage(&$text, &$uid, &$bitfield, &$flags, $allow_bbcode = false, $allow_urls = false, $allow_smilies = false, $allow_img_bbcode = true, $allow_flash_bbcode = true, $allow_quote_bbcode = true, $allow_url_bbcode = true, $mode = 'post')
	{
		return generate_text_for_storage($text, $uid, $bitfield, $flags, $allow_bbcode, $allow_urls, $allow_smilies, $allow_img_bbcode, $allow_flash_bbcode, $allow_quote_bbcode, $allow_url_bbcode, $mode);
	}
}

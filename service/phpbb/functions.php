<?php

namespace fq\boardnotices\service\phpbb;

/**
 * @codeCoverageIgnore
 */
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

	public function generate_text_for_edit($text, $uid, $flags)
	{
		return generate_text_for_edit($text, $uid, $flags);
	}

	public function generate_text_for_display($text, $uid, $bitfield, $flags, $censor_text = true)
	{
		return generate_text_for_display($text, $uid, $bitfield, $flags, $censor_text = true);
	}

	public function get_group_name($group_id)
	{
		return get_group_name($group_id);
	}

	public function phpbb_get_user_rank($user_data, $user_posts)
	{
		return phpbb_get_user_rank($user_data, $user_posts);
	}
}

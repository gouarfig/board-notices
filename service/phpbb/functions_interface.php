<?php

namespace fq\boardnotices\service\phpbb;

interface functions_interface
{
	/**
	* Build Confirm box
	* @param boolean $check True for checking if confirmed (without any additional parameters) and false for displaying the confirm box
	* @param string $title Title/Message used for confirm box.
	*		message text is _CONFIRM appended to title.
	*		If title cannot be found in user->lang a default one is displayed
	*		If title_CONFIRM cannot be found in user->lang the text given is used.
	* @param string $hidden Hidden variables
	* @param string $html_body Template used for confirm box
	* @param string $u_action Custom form action
	* @return boolean
	*/
	function confirm_box($check, $title = '', $hidden = '', $html_body = 'confirm_body.html', $u_action = '');

	/**
	* Generate back link for acp pages
	* @param string $u_action
	* @return string
	*/
	function adm_back_link($u_action);

	/**
	* Assign/Build custom bbcodes for display in screens supporting using of bbcodes
	* The custom bbcodes buttons will be placed within the template block 'custom_tags'
	*/
	function display_custom_bbcodes();

	/**
	* Add a secret token to the form (requires the S_FORM_TOKEN template variable)
	* @param string  $form_name The name of the form; has to match the name used in check_form_key, otherwise no restrictions apply
	* @param string  $template_variable_suffix A string that is appended to the name of the template variable to which the form elements are assigned
	*/
	function add_form_key($form_name, $template_variable_suffix = '');

	/**
	 * Check the form key. Required for all altering actions not secured by confirm_box
	 *
	 * @param	string	$form_name	The name of the form; has to match the name used
	 *								in add_form_key, otherwise no restrictions apply
	 * @param	int		$timespan	The maximum acceptable age for a submitted form
	 *								in seconds. Defaults to the config setting.
	 * @return	bool	True, if the form key was valid, false otherwise
	 */
	function check_form_key($form_name, $timespan = false);

	/**
	* For parsing custom parsed text to be stored within the database.
	* This function additionally returns the uid and bitfield that needs to be stored.
	* Expects $text to be the value directly from $request->variable() and in it's non-parsed form
	*
	* @param string $text The text to be replaced with the parsed one
	* @param string $uid The BBCode uid for this parse
	* @param string $bitfield The BBCode bitfield for this parse
	* @param int $flags The allow_bbcode, allow_urls and allow_smilies compiled into a single integer.
	* @param bool $allow_bbcode If BBCode is allowed (i.e. if BBCode is parsed)
	* @param bool $allow_urls If urls is allowed
	* @param bool $allow_smilies If smilies are allowed
	* @param bool $allow_img_bbcode
	* @param bool $allow_flash_bbcode
	* @param bool $allow_quote_bbcode
	* @param bool $allow_url_bbcode
	* @param string $mode Mode to parse text as, e.g. post or sig
	*
	* @return array	An array of string with the errors that occurred while parsing
	*/
	function generate_text_for_storage(&$text, &$uid, &$bitfield, &$flags, $allow_bbcode = false, $allow_urls = false, $allow_smilies = false, $allow_img_bbcode = true, $allow_flash_bbcode = true, $allow_quote_bbcode = true, $allow_url_bbcode = true, $mode = 'post');

	/**
	* For decoding custom parsed text for edits as well as extracting the flags
	* Expects $text to be the value directly from the database (pre-parsed content)
	* @return array
	*/
	function generate_text_for_edit($text, $uid, $flags);

	/**
	* For display of custom parsed text on user-facing pages
	* Expects $text to be the value directly from the database (stored value)
	* @return string
	*/
	function generate_text_for_display($text, $uid, $bitfield, $flags, $censor_text = true);

	/**
	 * Get group name (defined in functions_user.php)
	 *
	 * @param int $group_id
	 * @return string
	 */
	public function get_group_name($group_id);

	/**
	 * Get user rank title and image
	 *
	 * @param array $user_data the current stored users data
	 * @param int $user_posts the users number of posts
	 *
	 * @return array An associative array containing the rank title (title), the rank image as full img tag (img) and the rank image source (img_src)
	 *
	 * Note: since we do not want to break backwards-compatibility, this function will only properly assign ranks to guests if you call it for them with user_posts == false
	 */
	public function phpbb_get_user_rank($user_data, $user_posts);
}

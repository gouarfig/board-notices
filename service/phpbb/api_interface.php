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

interface api_interface
{
	/**
	 * Check if the user is registered
	 * @return boolean
	 */
	public function isUserRegistered();

	/**
	 * Returns user ID or 0
	 * @return int
	 */
	public function getUserId();

	/**
	 * Returns the user current IP address
	 * @return string
	 */
	public function getUserIpAddress();

	/**
	 * Returns Session ID
	 * @return string
	 */
	public function getSessionId();

	/**
	 * Returns user registration date
	 * @return int|null
	 */
	public function getUserRegistrationDate();

	/**
	 * Returns user birthday date in the format DD-MM-YYYY
	 * @return string
	 */
	public function getUserBirthday();

	/**
	 * Returns user latest post time (as a unix timestamp)
	 *
	 * @return int
	 */
	public function getUserLastPostTime();

	/**
	 * Returns the number of posts
	 *
	 * @return int
	 */
	public function getUserPostCount();

	/**
	* Create a \phpbb\datetime object in the context of the current user
	*
	* @param string $time String in a format accepted by strtotime().
	* @param DateTimeZone $timezone Time zone of the time.
	* @return \phpbb\datetime Date time object linked to the current users locale
	*/
	public function createDateTime($time = 'now', \DateTimeZone $timezone = null);

	/**
	 * Get translated value into current language
	 * @return string	Return localized string or the language key if the translation is not available
	 */
	public function lang();

	/**
	 * Installs admin module language
	 *
	 * @return void
	 */
	public function addAdminLanguage();
}

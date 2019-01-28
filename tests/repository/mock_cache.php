<?php
/**
*
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

namespace fq\boardnotices\tests\repository;

class mock_cache extends \phpbb\cache\driver\base
{
	private $data = array();

	function __construct()
	{
	}

	/**
	* {@inheritDoc}
	*/
	function load()
	{
		return true;
	}

	/**
	* {@inheritDoc}
	*/
	function unload()
	{
	}

	/**
	* {@inheritDoc}
	*/
	function save()
	{
	}

	/**
	* {@inheritDoc}
	*/
	function tidy()
	{
	}

	/**
	* {@inheritDoc}
	*/
	function get($var_name)
	{
		return array_key_exists($var_name, $this->data) ? $this->data[$var_name] : false;
	}

	/**
	* {@inheritDoc}
	*/
	function put($var_name, $var, $ttl = 0)
	{
		$this->data[$var_name] = $var;
	}

	/**
	* {@inheritDoc}
	*/
	function purge()
	{
		$this->data = array();
	}

	/**
	* {@inheritDoc}
	*/
	function destroy($var_name, $table = '')
	{
		unset($this->data[$var_name]);
	}

	/**
	* {@inheritDoc}
	*/
	function _exists($var_name)
	{
		return array_key_exists($var_name, $this->data);
	}

	/**
	* {@inheritDoc}
	*/
	function sql_load($query)
	{
		return false;
	}

	/**
	* {@inheritDoc}
	*/
	function sql_save(\phpbb\db\driver\driver_interface $db, $query, $query_result, $ttl)
	{
		return $query_result;
	}

	/**
	* {@inheritDoc}
	*/
	function sql_exists($query_id)
	{
		return false;
	}

	/**
	* {@inheritDoc}
	*/
	function sql_fetchrow($query_id)
	{
		return false;
	}

	/**
	* {@inheritDoc}
	*/
	function sql_fetchfield($query_id, $field)
	{
		return false;
	}

	/**
	* {@inheritDoc}
	*/
	function sql_rowseek($rownum, $query_id)
	{
		return false;
	}

	/**
	* {@inheritDoc}
	*/
	function sql_freeresult($query_id)
	{
		return false;
	}
}

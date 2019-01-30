<?php

/**
 *
 * Board Notices Manager extension for the phpBB Forum Software package.
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\migrations;

class new_logged_in_rule extends \phpbb\db\migration\migration
{

	static public function depends_on()
	{
		return array('\fq\boardnotices\migrations\create_tables');
	}

	public function effectively_installed()
	{
		$installed = false;
		$sql = "SELECT count(*) AS nb FROM {$this->table_prefix}notices_rules WHERE rule='not_logged_in'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		if (!empty($row) && isset($row['nb']))
		{
			$installed = ($row['nb'] == 0);
		}
		return $installed;
	}

	public function update_data()
	{
		$sql = "UPDATE {$this->table_prefix}notices_rules"
			. " SET rule='logged_in',conditions='" . serialize(array('0')) . "'"
			. " WHERE rule='not_logged_in'";
		$this->db->sql_query($sql);
		return array();
	}

}

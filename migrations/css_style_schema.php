<?php

/**
 *
 * Board Notices Manager
 *
 * @version 1.0.0
 * @copyright (c) 2015 Fred Quointeau
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\migrations;

class css_style_schema extends \phpbb\db\migration\migration
{

	static public function depends_on()
	{
		return array('\fq\boardnotices\migrations\create_tables');
	}

	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'notices', 'message_style');
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'notices' => array(
					'message_style' => array('VCHAR:50', '', 'after' => 'message_bgcolor'),
				),
			)
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'notices' => array(
					'message_style',
				),
			),
		);
	}

}

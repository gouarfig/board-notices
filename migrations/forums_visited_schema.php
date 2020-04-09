<?php

namespace fq\boardnotices\migrations;

class forums_visited_schema extends \phpbb\db\migration\migration
{

	public function effectively_installed()
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . 'forums_visited');
	}

	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'forums_visited' => array(
					'COLUMNS' => array(
						'forum_id' => array('UINT', 0),
						'user_id' => array('UINT', 0),
						'visited' => array('TIMESTAMP', 0),
					),
					'PRIMARY_KEY' => array(
						'forum_id',
						'user_id',
					),
				),
			)
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'forums_visited',
			),
		);
	}

}

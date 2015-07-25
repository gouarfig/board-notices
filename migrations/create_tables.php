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

class create_tables extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . 'notices');
	}

	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'notices' => array(
					'COLUMNS'		=> array(
						'notice_id'			=> array('UINT', null, 'auto_increment'),
						'title'				=> array('VCHAR:50', ''),
						'message'			=> array('MTEXT_UNI', ''),
						'message_uid'		=> array('VCHAR:8', ''),
						'message_bitfield'	=> array('VCHAR:255', ''),
						'message_options'	=> array('UINT', 0),
						'message_bgcolor'	=> array('VCHAR:6', ''),
						'active'			=> array('UINT:1', 0),
						'persistent'		=> array('UINT:1', 1),
						'dismissable'		=> array('UINT:1', 0),
						'reset_after'		=> array('UINT', 0),
						'last'				=> array('UINT:1', 0),
						'notice_order'		=> array('UINT', 1),
					),
					'PRIMARY_KEY'	=> 'notice_id',
					'KEYS'		=> array(
						'active_index'		=> array('INDEX', 'active'),
						'oid'				=> array('INDEX', 'notice_order'),
					),
				),
				$this->table_prefix . 'notices_rules' => array(
					'COLUMNS'		=> array(
						'notice_rule_id'	=> array('UINT', null, 'auto_increment'),
						'notice_id'			=> array('UINT', 0),
						'rule'				=> array('VCHAR:50', ''),
						'conditions'		=> array('MTEXT_UNI', ''),
					),
					'PRIMARY_KEY'	=> 'notice_rule_id',
					'KEYS'		=> array(
						'nid'		=> array('INDEX', 'notice_id'),
					),
				),
				$this->table_prefix . 'notices_seen' => array(
					'COLUMNS'		=> array(
						'notice_id'			=> array('UINT', 0),
						'user_id'			=> array('UINT', 0),
						'seen'				=> array('UINT', 0),
					),
				),
			)
		);
	}
	
//	public function revert_schema()
//	{
//		return array(
//			'drop_tables'		=> array(
//				$this->table_prefix . 'notices',
//				$this->table_prefix . 'notices_rules',
//				$this->table_prefix . 'notices_seen',
//			),
//		);
//	}
}

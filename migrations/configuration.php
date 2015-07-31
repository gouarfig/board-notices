<?php

/**
 *
 * Board Notices Manager extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace fq\boardnotices\migrations;

class configuration extends \phpbb\db\migration\migration
{

	public function effectively_installed()
	{
		return isset($this->config['boardnotices_previewkey']);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('boardnotices_previewkey', $this->generate_preview_key())),
		);
	}

	public function revert_data()
	{
		return array(
			array('config.remove', array('boardnotices_previewkey')),
		);
	}

	private function generate_preview_key()
	{
		// Generates some kind of fairly random key
		$key = mt_rand() . date('r') . $this->config['cookie_name'];
		return sha1($key);
	}
}

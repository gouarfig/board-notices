<?php

namespace fq\boardnotices\migrations;

class forums_visited_settings extends \phpbb\db\migration\migration
{

	public function effectively_installed()
	{
		return (isset($this->config['track_forums_visits']) && !empty($this->config['track_forums_visits']));
	}

	public function update_data()
	{
		return array(
			array('config.add', array('track_forums_visits', false)),
		);
	}

	public function revert_data()
	{
		return array(
			array('config.remove', array('track_forums_visits')),
		);
	}

}

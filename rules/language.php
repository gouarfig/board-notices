<?php

namespace fq\boardnotices\rules;

use \fq\boardnotices\service\constants;

class language extends rule_base implements rule_interface
{
	/** @var \fq\boardnotices\repository\users_interface $data_layer */
	private $data_layer;

	public function __construct(
		\fq\boardnotices\service\serializer $serializer,
		\fq\boardnotices\service\phpbb\api_interface $api,
		\fq\boardnotices\repository\users_interface $data_layer)
	{
		$this->serializer = $serializer;
		$this->api = $api;
		$this->data_layer = $data_layer;
	}

	public function getDisplayName()
	{
		return $this->api->lang('RULE_LANGUAGE');
	}

	public function getType()
	{
		return constants::$RULE_TYPE_MULTIPLE_CHOICE;
	}

	public function getDefault()
	{
		return array();
	}

	public function getPossibleValues()
	{
		return $this->data_layer->getLanguages();
	}

	public function isTrue($conditions)
	{
		$valid = false;
		$languages = $this->validateArrayOfConditions($conditions);
		$languages = $this->cleanEmptyStringsFromArray($languages);
		if (!empty($languages))
		{
			foreach ($languages as $language_id)
			{
				$valid = ($this->api->getUserLanguage() == $language_id);
				if ($valid)
				{
					break;
				}
			}
		}
		return $valid;
	}

}

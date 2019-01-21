<?php

namespace fq\boardnotices;

class serializer
{
	/**
	 * @param mixed $data
	 * @return string
	 */
	public function encode($data)
	{
		return serialize($data);
	}

	/**
	 * @param string $mixed
	 * @return mixed
	 */
	public function decode($string)
	{
		if (empty($string))
		{
			return null;
		}
		return unserialize($string);
	}
}

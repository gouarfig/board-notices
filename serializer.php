<?php

namespace fq\boardnotices;

/**
 * Use this class to avoid the use of serialize and unserialize functions (which are unsafe)
 */
class serializer
{
	/**
	 * Encoding data into a JSON format (with 'json:' in front)
	 * @param mixed $data
	 * @return string
	 */
	public function encode($data)
	{
		return 'json:' . json_encode($data);
	}

	/**
	 * Data gets encoded in JSON for security reasons
	 * But this method also accepts serialized data for compatibility with boardnotices versions <0.4
	 * @param string $mixed
	 * @return mixed
	 */
	public function decode($string)
	{
		if (empty($string))
		{
			return null;
		}
		if (substr($string, 0, 5) === 'json:')
		{
			return json_decode(substr($string, 5));
		}
		/** @todo Remove compatibility before releasing version 1.0 */
		return $this->safeUnserialize($string);
	}

	private function safeUnserialize($string)
	{
		if (intval(substr(phpversion(), 0, 1)) >= 7)
		{
			return unserialize($string, ['allowed_classes' => false]);
		}
		return unserialize($string);
	}
}

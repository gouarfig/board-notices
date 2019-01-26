<?php

namespace fq\boardnotices\service;

/**
 * Use this class to avoid the use of serialize and unserialize functions (which are unsafe)
 */
class serializer
{
	private $lastError = false;

	/**
	 * Encoding data into a JSON format (with 'json:' in front)
	 * @param mixed $data
	 * @return string
	 */
	public function encode($data)
	{
		$encoded = 'json:' . json_encode($data);
		$this->lastError = json_last_error();
		return $encoded;
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
			$decoded = json_decode(substr($string, 5));
			$this->lastError = json_last_error();
			return $decoded;
		}
		/** @todo Remove compatibility before releasing version 1.0 */
		return $this->safeUnserialize($string);
	}

	/**
	 * Indicates if the last encoding or decoding resulted in an error (and returned NULL)
	 *
	 * @return bool
	 */
	public function errorDetected()
	{
		return $this->lastError != JSON_ERROR_NONE;
	}

	private function isSerialized($data)
	{
		// if it isn't a string, it isn't serialized
		if (!is_string($data))
		{
			return false;
		}
		$data = trim($data);
		if ('N;' == $data)
		{
			return true;
		}
		if (strlen($data) < 4)
		{
			return false;
		}
		if (':' !== $data[1])
		{
			return false;
		}
		if (!preg_match('/^([adObis]):/', $data, $badions))
		{
			return false;
		}
		switch ($badions[1])
		{
			case 'a' :
			case 'O' :
			case 's' :
				if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data))
				{
					return true;
				}
				break;
			case 'b' :
			case 'i' :
			case 'd' :
				if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data))
				{
					return true;
				}
				break;
		}
		return false;
	}

	private function safeUnserialize($string)
	{
		if (!$this->isSerialized($string))
		{
			return $string;
		}
		if (intval(substr(phpversion(), 0, 1)) >= 7)
		{
			return @unserialize($string, ['allowed_classes' => false]);
		}
		return @unserialize($string);
	}
}

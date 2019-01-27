<?php

namespace fq\boardnotices\service;

/**
 * Use this class to avoid the use of serialize and unserialize functions (which are unsafe)
 */
class serializer
{
	private $lastJsonError = false;
	private $lastError = false;

	/**
	 * Encoding data into a JSON format (with 'json:' in front)
	 * @param mixed $data
	 * @return string
	 */
	public function encode($data)
	{
		$encoded = 'json:' . json_encode($data);
		$this->lastJsonError = json_last_error();
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
		if (empty($string) || !is_string($string))
		{
			return $this->error();
		}
		if (substr($string, 0, 5) === 'json:')
		{
			$decoded = json_decode(substr($string, 5));
			$this->lastJsonError = json_last_error();
			return $decoded;
		}
		/** @todo Remove compatibility before releasing version 1.0 */
		$decoded = $this->safeUnserialize($string);
		if (($decoded === false) && ($string !== 'b:0;'))
		{
			return $this->error();
		}
		return $decoded;
	}

	/**
	 * Indicates if the last encoding or decoding resulted in an error (and returned NULL)
	 *
	 * @return bool
	 */
	public function errorDetected()
	{
		return ($this->lastJsonError != JSON_ERROR_NONE) || ($this->lastError);
	}

	/**
	 * Always return null on error
	 */
	private function error()
	{
		$this->lastError = true;
		return null;
	}

	private function safeUnserialize($string)
	{
		$this->lastError = false;
		if (intval(substr(phpversion(), 0, 1)) >= 7)
		{
			return @unserialize($string, ['allowed_classes' => false]);
		}
		return @unserialize($string);
	}
}

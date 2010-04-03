<?php

class Ftp_Client_Config
{
	const MODE_BINARY = FTP_BINARY;

	const MODE_ASCII = FTP_ASCII;

	protected $aParams = array();

	public function __construct(array $aParams = array())
	{
		if (empty($aParams['port'])) $aParams['port'] = 21;
		if (empty($aParams['timeout'])) $aParams['timeout'] = 90;
		if (empty($aParams['pasv'])) $aParams['pasv'] = false;
		if (empty($aParams['ssl'])) $aParams['ssl'] = false;
		if (empty($aParams['transfermode'])) $aParams['transfermode'] = self::MODE_BINARY;

		$this->aParams = $aParams;
	} // function

	public function __call($sMethodname, array $aParams = array())
	{
		if (substr($sMethodname, 0, 3) == 'get' && empty($aParams))
		{
			if (!empty($this->aParams[strtolower(substr($sMethodname, 3))]))
				return $this->aParams[strtolower(substr($sMethodname, 3))];
		} // if
	} // function

	public function __get($sPropertyname)
	{
		if (!empty($this->aParams[strtolower($sPropertyname)]))
			return $this->aParams[strtolower($sPropertyname)];
	} // function
} // class
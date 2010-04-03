<?php
/**
 * Config container class for Ftp_Client.
 * This container is read-only and can only be set via it's constructor.
 * @author Dominik Siebel <ftpclient@dsiebel.de>
 */
class Ftp_Client_Config
{
	/**
	 * const: Binary transfer mode
	 * @var int
	 */
	const MODE_BINARY = FTP_BINARY;

	/**
	 * cons: ASCII transfer mode
	 * @var int
	 */
	const MODE_ASCII = FTP_ASCII;

	/**
	 * Parameters
	 * @var array
	 */
	protected $aParams = array();

	/**
	 * Constructor
	 * @param array $aParams
	 */
	public function __construct(array $aParams = array())
	{
		if (empty($aParams['port'])) $aParams['port'] = 21;
		if (empty($aParams['timeout'])) $aParams['timeout'] = 90;
		if (empty($aParams['pasv'])) $aParams['pasv'] = false;
		if (empty($aParams['ssl'])) $aParams['ssl'] = false;
		if (empty($aParams['transfermode'])) $aParams['transfermode'] = self::MODE_BINARY;

		$this->aParams = $aParams;
	} // function

	/**
	 * Magic call override
	 * routes get-calls to class properties
	 * @param string $sMethodname
	 * @param array  $aParams
	 */
	public function __call($sMethodname, array $aParams = array())
	{
		if (substr($sMethodname, 0, 3) == 'get' && empty($aParams))
		{
			if (!empty($this->aParams[strtolower(substr($sMethodname, 3))]))
			{
				return $this->aParams[strtolower(substr($sMethodname, 3))];
			} // if
		} // if
	} // function

	/**
	 * Magic get override
	 * routes direct property access
	 * @param string $sPropertyname
	 */
	public function __get($sPropertyname)
	{
		if (!empty($this->aParams[strtolower($sPropertyname)]))
		{
			return $this->aParams[strtolower($sPropertyname)];
		} // if
	} // function
} // class
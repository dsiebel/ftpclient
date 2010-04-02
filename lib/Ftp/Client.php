<?php

require_once (dirname(__FILE__) . '/Client/Config.php');

class Ftp_Client
{
	protected $oConfig = null;

	protected $rConn = null;

	protected $bConnected = false;

	public function __construct(Ftp_Client_Config $oConfig = null)
	{
		if (null === $oConfig)
			$this->oConfig = new Ftp_Client_Config();
		else
			$this->oConfig = $oConfig;
	} // function

	public function open($sHost, $sUsername, $sPassword = '')
	{
		// @todo implement ssl-ftp connections
		$this->rConn = ftp_connect($sHost, $this->oConfig->port, $this->oConfig->timeout);

		if (false === $this->rConn)
			throw new Exception('Could not connect to host ' . $sHost);

		if (!ftp_login($this->rConn, $sUsername, $sPassword))
			throw new Exception('Could not login user ' . $sUsername);

		$this->bConnected = true;
	} // function

	public function pwd()
	{
		if (true === $this->bConnected)
		{
			return ftp_pwd($this->rConn);
		} // if
	} // function

	public function dir()
	{
		if (true === $this->bConnected)
		{
			return ftp_nlist($this->rConn, $this->pwd());
		} // if
	} // function
} // class
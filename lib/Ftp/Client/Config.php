<?php

class Ftp_Client_Config
{
	const MODE_BINARY = FTP_BINARY;

	const MODE_ASCII = FTP_ASCII;

	public $port;

	public $timeout;

	public $pasv;

	public $ssl;

	public $mode;

	public function __construct()
	{
		$this->port = 21;
		$this->timeout = 90;
		$this->pasv = false;
		$this->ssl = false;
		$this->mode = self::MODE_BINARY;
	}
} // class
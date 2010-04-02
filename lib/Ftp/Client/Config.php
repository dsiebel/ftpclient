<?php

class Ftp_Client_Config
{
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
		$this->mode = FTP_BINARY;
	}
} // class
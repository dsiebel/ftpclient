<?php

require_once (dirname(__FILE__) . '/Helper.php');

class Ftp_Proxy_WWW
{
	protected $oFtpClient = null;

	public function __construct(Ftp_Client $oFtpClient)
	{
		$this->oFtpClient = $oFtpClient;
	} // function

	public function get($sFilenameRemote)
	{
		header('Content-Type: ' . Ftp_Proxy_Helper::guessMimetype($sFilenameRemote));
		header('Content-Disposition: attachment; filename='.basename($sFilenameRemote));
		header('Content-Length: ' . $this->oFtpClient->size($sFilenameRemote));

		$this->oFtpClient->fget($sFilenameRemote, fopen('php://output', 'b+'));
	} // function
} // class
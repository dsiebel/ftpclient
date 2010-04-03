<?php
/**
 * WWW proxy class for Ftp_Client.
 * Provides functionaility for a web frontend
 * @author Dominik Siebel <ftpclient@dsiebel.de>
 */
class Ftp_Proxy_WWW
{
	/**
	 * Instance of Ftp_Client
	 * @var Ftp_Client
	 */
	protected $oFtpClient = null;

	/**
	 * Constructor
	 * @param Ftp_Client $oFtpClient
	 */
	public function __construct(Ftp_Client $oFtpClient)
	{
		$this->oFtpClient = $oFtpClient;
	} // function

	/**
	 * Download a remote file
	 * @param string $sFilenameRemote
	 */
	public function get($sFilenameRemote)
	{
		require_once (dirname(__FILE__) . '/../Client/FileHelper.php');
		header('Content-Type: ' . Ftp_Client_FileHelper::guessMimetype($sFilenameRemote));
		header('Content-Disposition: attachment; filename='.basename($sFilenameRemote));
		header('Content-Length: ' . $this->oFtpClient->size($sFilenameRemote));

		$this->oFtpClient->fget($sFilenameRemote, fopen('php://output', 'b+'));
	} // function

	/**
	 * Upload a local file to the remote host.
	 * Expects $_FILE- like array.
	 * @param array $aUploadFiles
	 */
	public function put(array $aUploadFiles)
	{
		foreach ($aUploadFiles as $aFileInfo)
		{
			if ($aFileInfo['tmp_name'] && $aFileInfo['name'] && $aFileInfo['size'] && $aFileInfo['type'])
			{
				$this->oFtpClient->put(basename($aFileInfo['name']), $aFileInfo['tmp_name']);
			} // if
		} // foreach
	} // function
} // class
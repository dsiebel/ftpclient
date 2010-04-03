<?php

require_once (dirname(__FILE__) . '/Client/Config.php');
require_once (dirname(__FILE__) . '/Client/File/Remote.php');
require_once (dirname(__FILE__) . '/Client/FileHelper.php');

class Ftp_Client
{
	protected $oConfig = null;

	protected $rConn = false;

	protected $bConnected = false;

	public function __construct(Ftp_Client_Config $oConfig = null)
	{
		if (null === $oConfig)
			$this->oConfig = new Ftp_Client_Config();
		else
			$this->oConfig = $oConfig;
	} // function

	public function open($sHost)
	{
		// @todo implement ssl-ftp connections
		$this->rConn = ftp_connect($sHost, $this->oConfig->port, $this->oConfig->timeout);

		if (false === $this->rConn)
			throw new Exception('Could not connect to host ' . $sHost);

		return $this;
	} // function

	public function login($sUsername = '', $sPassword = '')
	{
		if (!@ftp_login($this->rConn, $sUsername, $sPassword))
			throw new Exception('Could not login user ' . $sUsername);

		if (true === $this->oConfig->pasv)
		{
			$this->pasv(true);
		} // if

		return $this;
	} // function

	public function pasv($bEnable = true)
	{
		if (false !== $this->rConn)
		{
			ftp_pasv($this->rConn, $bEnable);
		} // if
		return $this;
	} // function

	public function pwd()
	{
		if (false !== $this->rConn)
		{
			return ftp_pwd($this->rConn);
		} // if
		return false;
	} // function

	public function dir($bExtend = true, $bCheckFormat = true)
	{
		if (false !== $this->rConn)
		{
			$aDirList = array();
			if (false === $bExtend)
			{
				$aDirList = ftp_nlist($this->rConn, $this->pwd());
			} // if
			else
			{
				foreach(ftp_rawlist($this->rConn, $this->pwd()) as $sRawFileInfo)
				{
					if ((true === $bCheckFormat ? true === Ftp_Client_FileHelper::checkRawFormat($sRawFileInfo) :true))
					{
						$oFile = new Ftp_Client_File_Remote();
						$aTmp = preg_split('([\s]+)', $sRawFileInfo, 9);

						$oFile->setIsDir((bool) $aTmp[0]{0} == 'd');
						$oFile->setChmodRaw($aTmp[0]);
						$oFile->setChmod(Ftp_Client_FileHelper::getChmod($aTmp[0]));
						$oFile->setOwner($aTmp[2]);
						$oFile->setGroup($aTmp[3]);
						$oFile->setSizeRaw($aTmp[4]);
						$oFile->setSize(Ftp_Client_FileHelper::getSize($aTmp[4]));
						$oFile->setDateRaw($aTmp[6] . ' ' . $aTmp[5] . ' ' . $aTmp[7]);
						$oFile->setTimestamp(Ftp_Client_FileHelper::getTimestamp($aTmp[6], $aTmp[5], $aTmp[7]));
						$oFile->setName($aTmp[8]);
						$oFile->setMime(Ftp_Client_FileHelper::guessMimeType($aTmp[8]));
						$oFile->setRaw($sRawFileInfo);

						$aDirList[] = $oFile;
					} // if
				} // foreach
			} // else
			return $aDirList;
		} // if
		return false;
	} // function

	public function chdir($sDirectory)
	{
		if (false !== $this->rConn)
		{
			ftp_chdir($this->rConn, $sDirectory);
		} // if
		return $this;
	} // function

	public function get($sFilenameRemote, $sFilenameLocal, $sTransferMode = Ftp_Client_Config::MODE_BINARY)
	{
		if (false !== $this->rConn)
		{
			ftp_get($this->rConn, $sFilenameLocal, $sFilenameRemote, $sTransferMode);
		} // if
		return $this;
	} // function

	public function fget($sFilenameRemote, $rStream, $sTransferMode = Ftp_Client_Config::MODE_BINARY)
	{
		if (false !== $this->rConn)
		{
			return ftp_fget($this->rConn, $rStream, $sFilenameRemote, $sTransferMode);
		} // if
		return false;
	} // function

	public function put($sFilenameRemote, $sFilenameLocal, $sTransferMode = Ftp_Client_Config::MODE_BINARY)
	{
		if (false !== $this->rConn)
		{
			return ftp_put($this->rConn, $sFilenameRemote, $sFilenameLocal, $sTransferMode);
		} // if
		return false;
	} // function

	public function mkdir($sDirectory)
	{
		if (false !== $this->rConn)
		{
			return ftp_mkdir($this->rConn, $sDirectory);
		} // if
		return false;
	} // function

	public function size($sFilenameRemote)
	{
		if (false !== $this->rConn)
		{
			return ftp_size($this->rConn, $sFilenameRemote);
		} // if
		return false;
	} // function

	public function close()
	{
		if (false !== $this->rConn)
		{
			ftp_close($this->rConn);
			$this->rConn = false;
			return true;
		} // if
		return false;
	} // function

	public function __destruct()
	{
		$this->close();
	} // function
} // class
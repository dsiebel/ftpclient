<?php
/**
 * Ftp_Client class
 * Delegate ftp-commands to the remote host
 * @author Dominik Siebel <ftpclient@dsiebel.de>
 */
require_once (dirname(__FILE__) . '/Client/Config.php');
require_once (dirname(__FILE__) . '/Client/File/Remote.php');
require_once (dirname(__FILE__) . '/Client/FileHelper.php');
/**
 * Ftp_Client class
 * Delegate ftp-commands to the remote host
 * @author Dominik Siebel <ftpclient@dsiebel.de>
 */
class Ftp_Client
{
	/**
	 * Client configuration
	 * @var Ftp_Client_Config
	 */
	protected $oConfig = null;

	/**
	 * Ftp connection resource
	 * @var resource
	 */
	protected $rConn = false;

	/**
	 * Constructor.
	 * Sets the Client's configuration
	 * @param mixed $mConfig
	 */
	public function __construct($mConfig = array())
	{
		if ($mConfig instanceof Ftp_Client_Config)
		{
			$this->oConfig = $mConfig;
		} // else
		elseif (is_array($mConfig))
		{
			$this->oConfig = new Ftp_Client_Config($mConfig);
		} // elseif
		else
		{
			throw new Exception('Parameter of Ftp_Client::__construct has to be either an array or an instance of Ftp_Client_Config');
		} // else
	} // function

	/**
	 * Open a connection to the specified remote host.
	 * Implements a fluent interface.
	 * @param  string $sHost
	 * @return Ftp_Client
	 */
	public function open($sHost)
	{
		if (true === $this->oConfig->ssl)
		{
			$this->rConn = ftp_ssl_connect($sHost, $this->oConfig->port, $this->oConfig->timeout);
		} // if
		else
		{
			$this->rConn = ftp_connect($sHost, $this->oConfig->port, $this->oConfig->timeout);
		} // else

		if (false === $this->rConn)
		{
			throw new Exception('Could not connect to host ' . $sHost);
		} // if
		return $this;
	} // function

	/**
	 * Login the specified user.
	 * Implements a fluent interface.
	 * @param  string $sUsername
	 * @param  string $sPassword
	 * @return Ftp_Client
	 */
	public function login($sUsername = '', $sPassword = '')
	{
		if (!@ftp_login($this->rConn, $sUsername, $sPassword))
		{
			throw new Exception('Could not login user ' . $sUsername);
		} // if

		if (true === $this->oConfig->pasv)
		{
			$this->pasv($this->oConfig->pasv);
		} // if
		return $this;
	} // function

	/**
	 * Enable or disable passive mode.
	 * Implements a fluent interface.
	 * @param bool $bEnable
	 * @return Ftp_Client
	 */
	public function pasv($bEnable = true)
	{
		if (false !== $this->rConn)
		{
			ftp_pasv($this->rConn, $bEnable);
		} // if
		return $this;
	} // function

	/**
	 * Return current working dir on the remote host
	 * @return mixed
	 */
	public function pwd()
	{
		if (false !== $this->rConn)
		{
			return ftp_pwd($this->rConn);
		} // if
		return false;
	} // function

	/**
	 * Return a filelisting of the curent remote directory.
	 * @param  bool $bExtend
	 * @param  bool $bCheckFormat
	 * @return array[Ftp_Client_File_Remote]
	 */
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
					if ((true === $bCheckFormat ? (true === Ftp_Client_FileHelper::checkRawFormat($sRawFileInfo)) : true))
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

	/**
	 * Change current directory.
	 * Implements a fluent interface.
	 * @param  string $sDirectory new directory
	 * @return Ftp_Client
	 */
	public function chdir($sDirectory)
	{
		if (false !== $this->rConn)
		{
			ftp_chdir($this->rConn, $sDirectory);
		} // if
		return $this;
	} // function

	/**
	 * Download a remote file.
	 * Implements a fluent interface.
	 * @param  string $sFilenameRemote
	 * @param  string $sFilenameLocal
	 * @param  int    $iTransferMode
	 * @return Ftp_Client
	 */
	public function get($sFilenameRemote, $sFilenameLocal, $iTransferMode = Ftp_Client_Config::MODE_BINARY)
	{
		if (false !== $this->rConn)
		{
			ftp_get($this->rConn, $sFilenameLocal, $sFilenameRemote, $iTransferMode);
		} // if
		return $this;
	} // function

	/**
	 * Get a remote file as file stream
	 * @param  string   $sFilenameRemote
	 * @param  resource $rStream
	 * @param  int      $iTransferMode
	 * @return bool
	 */
	public function fget($sFilenameRemote, $rStream, $iTransferMode = Ftp_Client_Config::MODE_BINARY)
	{
		if (false !== $this->rConn)
		{
			return ftp_fget($this->rConn, $rStream, $sFilenameRemote, $iTransferMode);
		} // if
		return false;
	} // function

	/**
	 * Upload a local file to the remote host
	 * @param  string $sFilenameRemote
	 * @param  string $sFilenameLocal
	 * @param  int    $iTransferMode
	 * @return bool
	 */
	public function put($sFilenameRemote, $sFilenameLocal, $iTransferMode = Ftp_Client_Config::MODE_BINARY)
	{
		if (false !== $this->rConn)
		{
			return ftp_put($this->rConn, $sFilenameRemote, $sFilenameLocal, $iTransferMode);
		} // if
		return false;
	} // function

	/**
	 * Create a new directory
	 * @param  string $sDirectory
	 * @return bool
	 */
	public function mkdir($sDirectory)
	{
		if (false !== $this->rConn)
		{
			return ftp_mkdir($this->rConn, $sDirectory);
		} // if
		return false;
	} // function

	/**
	 * Return the file size
	 * @param  string $sFilenameRemote
	 * @return mixed
	 */
	public function size($sFilenameRemote)
	{
		if (false !== $this->rConn)
		{
			return ftp_size($this->rConn, $sFilenameRemote);
		} // if
		return false;
	} // function

	/**
	 * Close current connection to the remote host.
	 * @return bool
	 */
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

	/**
	 * Desctructor.
	 * Close the current remote host connection on shutdown.
	 * @retrn void
	 */
	public function __destruct()
	{
		$this->close();
	} // function
} // class

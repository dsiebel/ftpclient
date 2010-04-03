<?php
/**
 * Container class for remote files
 * @author Dominik Siebel <ftpclient@dsiebel.de>
 */
class Ftp_Client_File_Remote
{
	/**
	 * Is file a directory
	 * @var boolean
	 */
	protected $bIsDir;

	/**
	 * Raw permissions string
	 * @var string
	 */
	protected $sChmodRaw;

	/**
	 * Permissions numeric
	 * @var int
	 */
	protected $iChmod;

	/**
	 * File owner
	 * @var string
	 */
	protected $sOwner;

	/**
	 * File group
	 * @var string
	 */
	protected $sGroup;

	/**
	 * Raw file size
	 * @var int
	 */
	protected $iSizeRaw;

	/**
	 * File size human readable
	 * @var string
	 */
	protected $sSize;

	/**
	 * Raw date string
	 * @var string
	 */
	protected $sDateRaw;

	/**
	 * File timestamp
	 * @var int
	 */
	protected $iTimestamp;

	/**
	 * File name
	 * @var string
	 */
	protected $sName;

	/**
	 * Mimetype of the file
	 * @var string
	 */
	protected $sMimeType;

	/**
	 * Raw file string
	 * @var string
	 */
	protected $sRaw;

	/**
	 * Constructor
	 */
	public function __construct()
	{
	} // function

	/**
	 * Getter for directory flag
	 * @return bool
	 */
	public function getIsDir()
	{
		return $this->bIsDir;
	} // function

	/**
	 * Setter for directory flag
	 * @param bool $bIsDir
	 */
	public function setIsDir($bIsDir)
	{
		$this->bIsDir = (bool) $bIsDir;
	} // function

	/**
	 * Getter for file permissions
	 * @param int $bRaw
	 * @return mixed
	 */
	public function getChmod($bRaw = false)
	{
		if (true === $bRaw)
			return $this->iChmod;
		else
			return $this->sChmodRaw;
	} // function

	/**
	 * Setter for file permissions
	 * @param int $iChmod
	 */
	public function setChmod($iChmod)
	{
		$this->iChmod = (int) $iChmod;
	} // function

	/**
	 * Setter for raw file permissions
	 * @param string $sChmodRaw
	 */
	public function setChmodRaw($sChmodRaw)
	{
		$this->sChmodRaw = $sChmodRaw;
	} // function

	/**
	 * Getter for file owner
	 * @return string
	 */
	public function getOwner()
	{
		return $this->sOwner;
	} // function

	/**
	 * Setter for file owner
	 * @param string $sOwner
	 */
	public function setOwner($sOwner)
	{
		$this->sOwner = $sOwner;
	} // function

	/**
	 * Getter for file group
	 * @return string
	 */
	public function getGroup()
	{
		return $this->sGroup;
	} // function

	/**
	 * Setter for file group
	 * @param string $sGroup
	 */
	public function setGroup($sGroup)
	{
		$this->sGroup = $sGroup;
	} // function

	/**
	 * Getter for file size
	 * @param bool $bRaw
	 * @return mixed
	 */
	public function getSize($bRaw = false)
	{
		if (true === $bRaw)
			return $this->sSize;
		else
			return $this->iSizeRaw;
	} // function

	/**
	 * Setter for raw filesize
	 * @param int $iSizeRaw
	 */
	public function setSizeRaw($iSizeRaw)
	{
		$this->iSizeRaw = (int) $iSizeRaw;
	} // function

	/**
	 * Setter for file size (human readable)
	 * @param string $sSize
	 */
	public function setSize($sSize)
	{
		$this->sSize = $sSize;
	} // function

	/**
	 * Get file date
	 * @param bool $bRaw
	 * @return mixed
	 */
	public function getDate($bRaw = false)
	{
		if (true === $bRaw)
			return $this->sDateRaw;
		else
			return $this->iTimestamp;
	} // function

	/**
	 * Setter for raw date string
	 * @param string $sDateRaw
	 */
	public function setDateRaw($sDateRaw)
	{
		$this->sDateRaw = $sDateRaw;
	} // function

	/**
	 * Setter for timestamp
	 * @param int $iTimestamp
	 */
	public function setTimestamp($iTimestamp)
	{
		$this->iTimestamp = (int) $iTimestamp;
	} // function

	/**
	 * Getter for file name
	 * @return string
	 */
	public function getName()
	{
		return $this->sName;
	} // function

	/**
	 * Setter for filename
	 * @param string $sName
	 */
	public function setName($sName)
	{
		$this->sName = $sName;
	} // function

	/**
	 * Getter for mimetype
	 * @return string
	 */
	public function getMime()
	{
		return $this->sMimeType;
	} // function

	/**
	 * Setter for Mimetype
	 * @param string $sMimetype
	 */
	public function setMime($sMimetype)
	{
		$this->sMimeType = $sMimetype;
	} // function

	/**
	 * Getter for raw file string
	 * @return string
	 */
	public function getRaw()
	{
		return $this->sRaw;
	} // function

	/**
	 * Setter for raw file string
	 * @param int $sRaw
	 */
	public function setRaw($sRaw)
	{
		$this->sRaw = $sRaw;
	} // function

	public function __toString()
	{
		return $this->sRaw;
	} // function
} // class
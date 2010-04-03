<?php

class Ftp_Client_File_Remote
{
	protected $bIsDir;

	protected $sChmodRaw;

	protected $iChmod;

	protected $sOwner;

	protected $sGroup;

	protected $iSizeRaw;

	protected $sSize;

	protected $sDateRaw;

	protected $iTimestamp;

	protected $sName;

	protected $sMimeType;

	protected $sRaw;

	public function __construct()
	{
	} // function

	public function setIsDir($bIsDir)
	{
		$this->bIsDir = (bool) $bIsDir;
	} // function

	public function setChmod($iChmod)
	{
		$this->iChmod = (int) $iChmod;
	} // function

	public function setChmodRaw($sChmodRaw)
	{
		$this->sChmodRaw = $sChmodRaw;
	} // function

	public function setOwner($sOwner)
	{
		$this->sOwner = $sOwner;
	} // function

	public function setGroup($sGroup)
	{
		$this->sGroup = $sGroup;
	} // function

	public function setSizeRaw($iSizeRaw)
	{
		$this->iSizeRaw = (int) $iSizeRaw;
	} // function

	public function setSize($sSize)
	{
		$this->sSize = $sSize;
	} // function

	public function setDateRaw($sDateRaw)
	{
		$this->sDateRaw = $sDateRaw;
	} // function

	public function setTimestamp($iTimestamp)
	{
		$this->iTimestamp = (int) $iTimestamp;
	} // function

	public function setName($sName)
	{
		$this->sName = $sName;
	} // function

	public function setMime($sMimetype)
	{
		$this->sMimeType = $sMimetype;
	} // function

	public function setRaw($sRaw)
	{
		$this->sRaw = $sRaw;
	} // function

	public function getIsDir()
	{
		return $this->bIsDir;
	} // function

	public function getChmod($bRaw = false)
	{
		if (true === $bRaw)
			return $this->iChmod;
		else
			return $this->sChmodRaw;
	} // function

	public function getOwner()
	{
		return $this->sOwner;
	} // function

	public function getGroup()
	{
		return $this->sGroup;
	} // function


	public function getSize($bRaw = false)
	{
		if (true === $bRaw)
			return $this->sSize;
		else
			return $this->iSizeRaw;
	} // function

	public function getDate($bRaw = false)
	{
		if (true === $bRaw)
			return $this->sDateRaw;
		else
			return $this->iTimestamp;
	} // function

	public function getName()
	{
		return $this->sName;
	} // function

	public function getMime()
	{
		return $this->sMimeType;
	} // function

	public function getRaw()
	{
		return $this->sRaw;

	} // function
	public function __toString()
	{
		return $this->sRaw;
	} // function
} // class
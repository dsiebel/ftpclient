<?php

/**
 * This file is part of the FtpClient library.
 *
 * (c) Dominik Siebel <code@dsiebel.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE file.
 */

namespace DSi\Ftp\Client\File;

/**
 * Container class for remote files.
 */
class FtpClientRemoteFile {
    /**
     * Is file a directory
     * @var boolean
     */
    protected $isDir;

    /**
     * Raw permissions string
     * @var string
     */
    protected $chmodRaw;

    /**
     * Permissions numeric
     * @var int
     */
    protected $chmod;

    /**
     * File owner
     * @var string
     */
    protected $owner;

    /**
     * File group
     * @var string
     */
    protected $group;

    /**
     * Raw file size
     * @var int
     */
    protected $sizeRaw;

    /**
     * File size human readable
     * @var string
     */
    protected $size;

    /**
     * Raw date string
     * @var string
     */
    protected $dateRaw;

    /**
     * File timestamp
     * @var int
     */
    protected $timestamp;

    /**
     * File name
     * @var string
     */
    protected $name;

    /**
     * Mimetype of the file
     * @var string
     */
    protected $mimeType;

    /**
     * Raw file string
     * @var string
     */
    protected $raw;

    /**
     * Getter for directory flag.
     *
     * @return bool
     */
    public function isDir() {
        return $this->isDir;
    }

    /**
     * Setter for directory flag
     * @param bool $isDir
     */
    public function setIsDir($isDir) {
        $this->isDir = (bool) $isDir;
    }

    /**
     * Getter for file permissions
     * @param bool $raw (default false)
     * @return mixed
     */
    public function getChmod($raw = false) {
        return true === $raw
            ? $this->chmod
            : $this->chmodRaw;
    }

    /**
     * Setter for file permissions.
     *
     * @param int $chmod
     */
    public function setChmod($chmod) {
        $this->chmod = (int) $chmod;
    }

    /**
     * Setter for raw file permissions.
     *
     * @param string $chmodRaw
     */
    public function setChmodRaw($chmodRaw) {
        $this->chmodRaw = $chmodRaw;
    }

    /**
     * Getter for file owner.
     *
     * @return string
     */
    public function getOwner() {
        return $this->owner;
    }

    /**
     * Setter for file owner.
     *
     * @param string $sOwner
     */
    public function setOwner($sOwner) {
        $this->owner = $sOwner;
    }

    /**
     * Getter for file group.
     *
     * @return string
     */
    public function getGroup() {
        return $this->group;
    }

    /**
     * Setter for file group.
     *
     * @param string $sGroup
     */
    public function setGroup($sGroup) {
        $this->group = $sGroup;
    }

    /**
     * Getter for file size.
     *
     * @param bool $raw
     * @return mixed
     */
    public function getSize($raw = false) {
        return true === $raw
            ? $this->size
            : $this->sizeRaw;
    }

    /**
     * Setter for raw filesize.
     *
     * @param int $sizeRaw
     */
    public function setSizeRaw($sizeRaw) {
        $this->sizeRaw = (int) $sizeRaw;
    }

    /**
     * Setter for file size (human readable).
     *
     * @param string $sSize
     */
    public function setSize($sSize) {
        $this->size = $sSize;
    }

    /**
     * Get file date.
     *
     * @param bool $bRaw
     * @return mixed
     */
    public function getDate($bRaw = false) {
        return true === $bRaw
            ? $this->dateRaw
            : $this->timestamp;
    }

    /**
     * Setter for raw date string.
     *
     * @param string $dateRaw
     */
    public function setDateRaw($dateRaw) {
        $this->dateRaw = $dateRaw;
    }

    /**
     * Setter for timestamp.
     *
     * @param int $timestamp
     */
    public function setTimestamp($timestamp) {
        $this->timestamp = (int) $timestamp;
    }

    /**
     * Getter for file name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Setter for filename.
     *
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Getter for mimetype.
     *
     * @return string
     */
    public function getMime() {
        return $this->mimeType;
    }

    /**
     * Setter for Mimetype.
     *
     * @param string $mimeType
     */
    public function setMime($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    /**
     * Getter for raw file string.
     *
     * @return string
     */
    public function getRaw() {
        return $this->raw;
    }

    /**
     * Setter for raw file string.
     *
     * @param int $raw
     */
    public function setRaw($raw) {
        $this->raw = $raw;
    }

    public function __toString() {
        return $this->raw;
    }
}
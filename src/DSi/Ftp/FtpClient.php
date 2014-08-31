<?php

/**
 * This file is part of the FtpClient library.
 *
 * (c) Dominik Siebel <code@dsiebel.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE file.
 */

namespace DSi\Ftp;

use DSi\Ftp\Client\File\FtpClientRemoteFile;
use DSi\Ftp\Client\FtpClientConfig;
use DSi\Ftp\Client\FtpClientFileHelper;
use DSi\Ftp\Exception\FtpClientConnectException;
use DSi\Ftp\Exception\FtpClientException;
use DSi\Ftp\Exception\FtpClientLoginException;

/**
 * Ftp_Client class
 * Delegate ftp-commands to the remote host
 */
class FtpClient {
    /**
     * Client configuration
     * @var FtpClientConfig
     */
    protected $config = null;

    /**
     * Ftp connection resource
     * @var resource
     */
    protected $connection = false;

    /**
     * Constructor.
     * Sets the Client's configuration.
     *
     * @param FtpClientConfig|array $config
     * @throws \Exception on wrong config format
     */
    public function __construct($config) {
        if ($config instanceof FtpClientConfig) {
            $this->config = $config;
        } elseif (is_array($config)) {
            $this->config = new FtpClientConfig($config);
        } else {
            throw new \InvalidArgumentException(
                'Argument 1 passed to FtpClient::__construct must be either array or instance of FtpClientConfig'
            );
        }
    }

    /**
     * Open a connection to the specified remote host.
     * Implements a fluent interface.
     *
     * @param  string $hostname
     * @throws FtpClientConnectException
     * @return FtpClient
     */
    public function open($hostname) {
        if (true === $this->config->useSSL) {
            $this->connection = ftp_ssl_connect($hostname, $this->config->port, $this->config->timeout);
        } else {
            $this->connection = ftp_connect($hostname, $this->config->port, $this->config->timeout);
        }

        if (false === $this->connection) {
            throw new FtpClientConnectException('Could not connect to host ' . $hostname);
        }
        return $this;
    }

    /**
     * Login the specified user.
     * Implements a fluent interface.
     *
     * @param string $user (default: '')
     * @param string $password (default: '')
     * @throws FtpClientLoginException if user could not be logged in
     * @return FtpClient
     */
    public function login($user = '', $password = '') {
        if (!@ftp_login($this->connection, $user, $password)) {
            throw new FtpClientLoginException('Could not login user ' . $user);
        }

        if (true === $this->config->usePassive) {
            $this->pasv($this->config->usePassive);
        }
        return $this;
    }

    /**
     * Enable or disable passive mode.
     * Implements a fluent interface.
     *
     * @param bool $enable
     * @return FtpClient
     */
    public function pasv($enable = true) {
        if (false !== $this->connection) {
            ftp_pasv($this->connection, $enable);
        }
        return $this;
    }

    /**
     * Return current working dir on the remote host
     * @return mixed
     */
    public function pwd() {
        if (false !== $this->connection) {
            return ftp_pwd($this->connection);
        }
        return false;
    }

    /**
     * Return a file listing of the current remote directory.
     *
     * @param  bool $extend
     * @param  bool $checkFormat
     * @throws \Exception if $checkFormat is true and format is invalid
     * @return array[Ftp_Client_File_Remote]
     */
    public function dir($extend = true, $checkFormat = true) {
        if (false === $this->connection) {
            return false;
        }

        $listing = [];
        if (false === $extend) {
            $listing = ftp_nlist($this->connection, $this->pwd());
        } else {
            foreach(ftp_rawlist($this->connection, $this->pwd()) as $rawFileInfo) {
                if (true === $checkFormat) {
                    try {
                        FtpClientFileHelper::checkRawFormat($rawFileInfo);
                    } catch(\InvalidArgumentException $e) {
                        throw new FtpClientException('Could not list directory contents with format check', 0, $e);
                    }
                }

                $file = new FtpClientRemoteFile();
                $seg = preg_split('([\s]+)', $rawFileInfo, 9);

                $file->setIsDir((bool) $seg[0]{0} == 'd');
                $file->setChmodRaw($seg[0]);
                $file->setChmod(FtpClientFileHelper::getChmod($seg[0]));
                $file->setOwner($seg[2]);
                $file->setGroup($seg[3]);
                $file->setSizeRaw($seg[4]);
                $file->setDateRaw($seg[6] . ' ' . $seg[5] . ' ' . $seg[7]);
                $file->setTimestamp(FtpClientFileHelper::getTimestamp($seg[7], $seg[5], $seg[6]));
                $file->setName($seg[8]);
                $file->setMime(FtpClientFileHelper::guessMimeType($seg[8]));
                $file->setRaw($rawFileInfo);

                if (!$file->isDir()) {
                    $file->setSize(FtpClientFileHelper::getSize($seg[4]));
                }
                $listing[] = $file;
            }
        }
        return $listing;
    }

    /**
     * Change current directory.
     * Implements a fluent interface.
     *
     * @param  string $directory new directory
     * @return bool
     */
    public function chdir($directory) {
        $result = false;
        if (false !== $this->connection) {
            $result |= ftp_chdir($this->connection, $directory);
        }
        return $result;
    }

    /**
     * Download a remote file.
     * Implements a fluent interface.
     *
     * @param  string $remoteFilename
     * @param  string $localFilename
     * @param  int    $transferMode
     * @return FtpClient
     */
    public function get($remoteFilename, $localFilename, $transferMode = FtpClientConfig::MODE_BINARY) {
        if (false !== $this->connection) {
            ftp_get($this->connection, $localFilename, $remoteFilename, $transferMode);
        }
        return $this;
    }

    /**
     * Get a remote file as file stream.
     *
     * @param  string   $remoteFilename
     * @param  resource $streamResource
     * @param  int      $transferMode
     * @return bool
     */
    public function fget($remoteFilename, $streamResource, $transferMode = FtpClientConfig::MODE_BINARY) {
        if (false !== $this->connection) {
            return ftp_fget($this->connection, $streamResource, $remoteFilename, $transferMode);
        }
        return false;
    }

    /**
     * Upload a local file to the remote host.
     *
     * @param  string $remoteFilename
     * @param  string $localFilename
     * @param  int    $transferMode
     * @return bool
     */
    public function put($remoteFilename, $localFilename, $transferMode = FtpClientConfig::MODE_BINARY) {
        if (false !== $this->connection) {
            return ftp_put($this->connection, $remoteFilename, $localFilename, $transferMode);
        }
        return false;
    }

    /**
     * Create a new directory.
     *
     * @param  string $directory
     * @return bool
     */
    public function mkdir($directory) {
        if (false !== $this->connection) {
            return ftp_mkdir($this->connection, $directory);
        }
        return false;
    }

    /**
     * Return the file size.
     *
     * @param  string $remoteFilename
     * @return mixed
     */
    public function size($remoteFilename) {
        if (false !== $this->connection) {
            return ftp_size($this->connection, $remoteFilename);
        }
        return false;
    }

    /**
     * Close current connection to the remote host.
     *
     * @return bool
     */
    public function close() {
        if (false !== $this->connection) {
            ftp_close($this->connection);
            $this->connection = false;
            return true;
        }
        return false;
    }

    /**
     * Desctructor.
     * Close the current remote host connection on shutdown.
     */
    public function __destruct() {
        $this->close();
    }
}
<?php

/**
 * This file is part of the FtpClient library.
 *
 * (c) Dominik Siebel <code@dsiebel.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE file.
 */

namespace DSi\Ftp\Client;

/**
 * Config container class for FtpClient.
 * This container is read-only and can only be set via it's constructor.
 */
class FtpClientConfig {
    /**
     * Binary transfer mode
     * @var int
     */
    const MODE_BINARY = FTP_BINARY;

    /**
     * ASCII transfer mode
     * @var int
     */
    const MODE_ASCII = FTP_ASCII;

    /**
     * @var int
     */
    public $port = 21;

    /**
     * @var int
     */
    public $timeout = 90;

    /**
     * @var bool
     */
    public $usePassive = false;

    /**
     * @var bool
     */
    public $useSSL = false;

    /**
     * @var int
     */
    public $transferMode = self::MODE_BINARY;


    /**
     * Constructor.
     *
     * @param array $params
     */
    public function __construct(array $params = []) {
        $this->init($params);
    }

    private function init(array $params) {
        if (!empty($params['port'])) $this->port = $params['port'];
        if (!empty($params['timeout'])) $this->timeout = $params['timeout'];
        if (!empty($params['pasv'])) $this->usePassive = $params['pasv'];
        if (!empty($params['ssl'])) $this->useSSL = $params['ssl'];
        if (!empty($params['transfermode'])) $this->transferMode = $params['transfermode'];
    }
}
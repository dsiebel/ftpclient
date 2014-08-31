<?php

/**
 * This file is part of the FtpClient library.
 *
 * (c) Dominik Siebel <code@dsiebel.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE file.
 */

namespace DSi\Ftp\Proxy;

use DSi\Ftp\Client\FtpClientFileHelper;
use DSi\Ftp\FtpClient;

/**
 * Http proxy class for Ftp_Client.
 * Provides functionality for a web frontend.
 */
class FtpProxyHttp {
    /**
     * Instance of FtpClient
     * @var FtpClient
     */
    protected $client = null;

    /**
     * Constructor.
     *
     * @param FtpClient $client
     */
    public function __construct(FtpClient $client) {
        $this->client = $client;
    }

    /**
     * Download a remote file.
     *
     * @param string $remoteFilename
     */
    public function get($remoteFilename) {
        header('Content-Type: ' . FtpClientFileHelper::guessMimetype($remoteFilename));
        header('Content-Disposition: attachment; filename='.basename($remoteFilename));
        header('Content-Length: ' . $this->client->size($remoteFilename));
        $this->client->fget($remoteFilename, fopen('php://output', 'b+'));
    }

    /**
     * Upload a local file to the remote host.
     * Expects $_FILE- like array.
     *
     * @param array $files
     */
    public function put(array $files) {
        foreach ($files as $file) {
            if ($file['tmp_name'] && $file['name'] && $file['size'] && $file['type']) {
                $this->client->put(basename($file['name']), $file['tmp_name']);
            }
        }
    }
}
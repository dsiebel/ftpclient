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
 * Static file helper class.
 * Provides various file related methods.
 */
class FtpClientFileHelper {
    /**
     * Check the raw file string for validity.
     *
     * @param  string $rawFileInfo
     * @throws \InvalidArgumentException if format invalid
     */
    public static function checkRawFormat($rawFileInfo) {
        // Matches -rw-rw-rw-   1 owner  group       1187 Aug 30  2014 filename.ext
        // Matches drwxr-xr-x   1 owner  group       1187 Aug 30  08:00 dirname

        $buffer = preg_split('([\s]+)', $rawFileInfo);

        // check permissions string
        if (!preg_match('(^[d\-]([r\-][w\-][x\-]){3}$)', $buffer[0])) {
            throw new \InvalidArgumentException('Permissions not found in ' . $buffer[0]);
        }

        // check file owner
        if (!preg_match('(^[A-Za-z0-9]+$)', $buffer[2])) {
            throw new \InvalidArgumentException('Owner not found in ' . $buffer[2]);
        }

        // check file group
        if (!preg_match('(^[A-Za-z0-9]+$)', $buffer[3])) {
            throw new \InvalidArgumentException('Group not found in ' . $buffer[3]);
        }

        // check file size
        if (!is_numeric($buffer[4])) {
            throw new \InvalidArgumentException('Size not found in ' . $buffer[4]);
        }

        // check file date
        if (false === self::getTimestamp($buffer[6], $buffer[5], $buffer[7])) {
            throw new \InvalidArgumentException('Could not generate timestamp from ' . $buffer[6] . ' ' . $buffer[5] . ' ' . $buffer[7]);
        }

        // check file name
        if (!strlen($buffer[8]) > 0) {
            throw new \InvalidArgumentException('Filename not found in ' . $buffer[8]);
        }
    }

    /**
     * Generates a timestamp from given input.
     * e.g. 11 Aug 2009 => 1249941600
     *
     * @param  string $year
     * @param  string $month
     * @param  string $day
     * @return int
     */
    public static function getTimestamp($year, $month, $day) {
        if (preg_match('(\d\d:\d\d)', $year)) {
            $year = date('Y');
        }
        return strtotime($day . ' ' . $month . ' ' . $year);
    }

    /**
     * Gets integer value from file permission string.
     * e.g. -rw-rw-rw- => 666
     *
     * @param  string $rawChmod
     * @return int
     */
    public static function getChmod($rawChmod) {
        $lookup = ['-' => '0', 'r' => '4', 'w' => '2', 'x' => '1'];
        $chmodStr = substr(strtr($rawChmod, $lookup), 1);
        $chmodArr = str_split($chmodStr, 3);
        return array_sum(str_split($chmodArr[0])) . array_sum(str_split($chmodArr[1])) . array_sum(str_split($chmodArr[2]));
    }

    /**
     * Parse file size in byte to human readable format.
     *
     * @param  string $rawSize
     * @return string
     */
    public static function getSize($rawSize) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $exp = (int) floor( log((int)$rawSize) / log(1024) );
        return sprintf( '%.2f ' . $units[ $exp ], ((int)$rawSize / pow(1024, floor($exp))) );
    }

    /**
     * Guess file's mimetype by it's extension.
     * Default is application/octet-stream.
     *
     * @param  string $filename
     * @return string
     */
    public static function guessMimetype($filename) {
        $extension = strrchr(basename($filename), '.');

        // skip switch if file has no extension
        if ($extension == '') {
            return 'application/octet-stream';
        }

        switch ($extension) {
            case '.zip': $mimeType = 'application/zip'; break;
            case '.ez':  $mimeType = 'application/andrew-inset'; break;
            case '.hqx': $mimeType = 'application/mac-binhex40'; break;
            case '.cpt': $mimeType = 'application/mac-compactpro'; break;
            case '.doc': $mimeType = 'application/msword'; break;
            case '.bin': $mimeType = 'application/octet-stream'; break;
            case '.dms': $mimeType = 'application/octet-stream'; break;
            case '.lha': $mimeType = 'application/octet-stream'; break;
            case '.lzh': $mimeType = 'application/octet-stream'; break;
            case '.exe': $mimeType = 'application/octet-stream'; break;
            case '.class': $mimeType = 'application/octet-stream'; break;
            case '.so':  $mimeType = 'application/octet-stream'; break;
            case '.dll': $mimeType = 'application/octet-stream'; break;
            case '.oda': $mimeType = 'application/oda'; break;
            case '.pdf': $mimeType = 'application/pdf'; break;
            case '.ai':  $mimeType = 'application/postscript'; break;
            case '.eps': $mimeType = 'application/postscript'; break;
            case '.ps':  $mimeType = 'application/postscript'; break;
            case '.smi': $mimeType = 'application/smil'; break;
            case '.smil': $mimeType = 'application/smil'; break;
            case '.xls': $mimeType = 'application/vnd.ms-excel'; break;
            case '.ppt': $mimeType = 'application/vnd.ms-powerpoint'; break;
            case '.wbxml': $mimeType = 'application/vnd.wap.wbxml'; break;
            case '.wmlc': $mimeType = 'application/vnd.wap.wmlc'; break;
            case '.wmlsc': $mimeType = 'application/vnd.wap.wmlscriptc'; break;
            case '.bcpio': $mimeType = 'application/x-bcpio'; break;
            case '.vcd': $mimeType = 'application/x-cdlink'; break;
            case '.pgn': $mimeType = 'application/x-chess-pgn'; break;
            case '.cpio': $mimeType = 'application/x-cpio'; break;
            case '.csh': $mimeType = 'application/x-csh'; break;
            case '.dcr': $mimeType = 'application/x-director'; break;
            case '.dir': $mimeType = 'application/x-director'; break;
            case '.dxr': $mimeType = 'application/x-director'; break;
            case '.dvi': $mimeType = 'application/x-dvi'; break;
            case '.spl': $mimeType = 'application/x-futuresplash'; break;
            case '.gtar': $mimeType = 'application/x-gtar'; break;
            case '.hdf': $mimeType = 'application/x-hdf'; break;
            case '.js':  $mimeType = 'application/x-javascript'; break;
            case '.skp': $mimeType = 'application/x-koan'; break;
            case '.skd': $mimeType = 'application/x-koan'; break;
            case '.skt': $mimeType = 'application/x-koan'; break;
            case '.skm': $mimeType = 'application/x-koan'; break;
            case '.latex': $mimeType = 'application/x-latex'; break;
            case '.nc':  $mimeType = 'application/x-netcdf'; break;
            case '.cdf': $mimeType = 'application/x-netcdf'; break;
            case '.sh':  $mimeType = 'application/x-sh'; break;
            case '.shar': $mimeType = 'application/x-shar'; break;
            case '.swf': $mimeType = 'application/x-shockwave-flash'; break;
            case '.sit': $mimeType = 'application/x-stuffit'; break;
            case '.sv4cpio': $mimeType = 'application/x-sv4cpio'; break;
            case '.sv4crc': $mimeType = 'application/x-sv4crc'; break;
            case '.tar': $mimeType = 'application/x-tar'; break;
            case '.tcl': $mimeType = 'application/x-tcl'; break;
            case '.tex': $mimeType = 'application/x-tex'; break;
            case '.texinfo': $mimeType = 'application/x-texinfo'; break;
            case '.texi': $mimeType = 'application/x-texinfo'; break;
            case '.t':$mimeType = 'application/x-troff'; break;
            case '.tr':  $mimeType = 'application/x-troff'; break;
            case '.roff': $mimeType = 'application/x-troff'; break;
            case '.man': $mimeType = 'application/x-troff-man'; break;
            case '.me':  $mimeType = 'application/x-troff-me'; break;
            case '.ms':  $mimeType = 'application/x-troff-ms'; break;
            case '.ustar': $mimeType = 'application/x-ustar'; break;
            case '.src': $mimeType = 'application/x-wais-source'; break;
            case '.xhtml': $mimeType = 'application/xhtml+xml'; break;
            case '.xht': $mimeType = 'application/xhtml+xml'; break;
            case '.au':  $mimeType = 'audio/basic'; break;
            case '.snd': $mimeType = 'audio/basic'; break;
            case '.mid': $mimeType = 'audio/midi'; break;
            case '.midi': $mimeType = 'audio/midi'; break;
            case '.kar': $mimeType = 'audio/midi'; break;
            case '.mpga': $mimeType = 'audio/mpeg'; break;
            case '.mp2': $mimeType = 'audio/mpeg'; break;
            case '.mp3': $mimeType = 'audio/mpeg'; break;
            case '.aif': $mimeType = 'audio/x-aiff'; break;
            case '.aiff': $mimeType = 'audio/x-aiff'; break;
            case '.aifc': $mimeType = 'audio/x-aiff'; break;
            case '.m3u': $mimeType = 'audio/x-mpegurl'; break;
            case '.ram': $mimeType = 'audio/x-pn-realaudio'; break;
            case '.rm':  $mimeType = 'audio/x-pn-realaudio'; break;
            case '.rpm': $mimeType = 'audio/x-pn-realaudio-plugin'; break;
            case '.ra':  $mimeType = 'audio/x-realaudio'; break;
            case '.wav': $mimeType = 'audio/x-wav'; break;
            case '.pdb': $mimeType = 'chemical/x-pdb'; break;
            case '.xyz': $mimeType = 'chemical/x-xyz'; break;
            case '.bmp': $mimeType = 'image/bmp'; break;
            case '.gif': $mimeType = 'image/gif'; break;
            case '.ief': $mimeType = 'image/ief'; break;
            case '.jpeg': $mimeType = 'image/jpeg'; break;
            case '.jpg': $mimeType = 'image/jpeg'; break;
            case '.jpe': $mimeType = 'image/jpeg'; break;
            case '.png': $mimeType = 'image/png'; break;
            case '.tiff': $mimeType = 'image/tiff'; break;
            case '.tif': $mimeType = 'image/tiff'; break;
            case '.djvu': $mimeType = 'image/vnd.djvu'; break;
            case '.djv': $mimeType = 'image/vnd.djvu'; break;
            case '.wbmp': $mimeType = 'image/vnd.wap.wbmp'; break;
            case '.ras': $mimeType = 'image/x-cmu-raster'; break;
            case '.pnm': $mimeType = 'image/x-portable-anymap'; break;
            case '.pbm': $mimeType = 'image/x-portable-bitmap'; break;
            case '.pgm': $mimeType = 'image/x-portable-graymap'; break;
            case '.ppm': $mimeType = 'image/x-portable-pixmap'; break;
            case '.rgb': $mimeType = 'image/x-rgb'; break;
            case '.xbm': $mimeType = 'image/x-xbitmap'; break;
            case '.xpm': $mimeType = 'image/x-xpixmap'; break;
            case '.xwd': $mimeType = 'image/x-xwindowdump'; break;
            case '.igs': $mimeType = 'model/iges'; break;
            case '.iges': $mimeType = 'model/iges'; break;
            case '.msh': $mimeType = 'model/mesh'; break;
            case '.mesh': $mimeType = 'model/mesh'; break;
            case '.silo': $mimeType = 'model/mesh'; break;
            case '.wrl': $mimeType = 'model/vrml'; break;
            case '.vrml': $mimeType = 'model/vrml'; break;
            case '.css': $mimeType = 'text/css'; break;
            case '.html': $mimeType = 'text/html'; break;
            case '.htm': $mimeType = 'text/html'; break;
            case '.asc': $mimeType = 'text/plain'; break;
            case '.txt': $mimeType = 'text/plain'; break;
            case '.rtx': $mimeType = 'text/richtext'; break;
            case '.rtf': $mimeType = 'text/rtf'; break;
            case '.sgml': $mimeType = 'text/sgml'; break;
            case '.sgm': $mimeType = 'text/sgml'; break;
            case '.tsv': $mimeType = 'text/tab-separated-values'; break;
            case '.wml': $mimeType = 'text/vnd.wap.wml'; break;
            case '.wmls': $mimeType = 'text/vnd.wap.wmlscript'; break;
            case '.etx': $mimeType = 'text/x-setext'; break;
            case '.xml': $mimeType = 'text/xml'; break;
            case '.xsl': $mimeType = 'text/xml'; break;
            case '.mpeg': $mimeType = 'video/mpeg'; break;
            case '.mpg': $mimeType = 'video/mpeg'; break;
            case '.mpe': $mimeType = 'video/mpeg'; break;
            case '.qt':  $mimeType = 'video/quicktime'; break;
            case '.mov': $mimeType = 'video/quicktime'; break;
            case '.mxu': $mimeType = 'video/vnd.mpegurl'; break;
            case '.avi': $mimeType = 'video/x-msvideo'; break;
            case '.movie': $mimeType = 'video/x-sgi-movie'; break;
            case '.asf': $mimeType = 'video/x-ms-asf'; break;
            case '.asx': $mimeType = 'video/x-ms-asf'; break;
            case '.wm':  $mimeType = 'video/x-ms-wm'; break;
            case '.wmv': $mimeType = 'video/x-ms-wmv'; break;
            case '.wvx': $mimeType = 'video/x-ms-wvx'; break;
            case '.ice': $mimeType = 'x-conference/x-cooltalk'; break;
            default: $mimeType = 'application/octet-stream'; break;
        }
        return $mimeType;
    }
}
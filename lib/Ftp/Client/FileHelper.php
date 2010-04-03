<?php
/**
 * Static file helper class.
 * Provides various file related methods.
 * @author Dominik Siebel <ftpclient@dsiebel.de>
 */
class Ftp_Client_FileHelper
{
	/**
	 * Check the raw file string for validity
	 * @param  string $sRawFileInfo
	 * @return bool
	 */
	public static function checkRawFormat($sRawFileInfo)
	{
		// Match for: -rw-rw-rw-   1 owner  group       1187 Jul 29  2009 filename.ext
		// Match for: drwxr-xr-x   1 owner  group       1187 Jul 29  08:00 dirname

		$aErr = array();
		$aTmp = preg_split('([\s]+)', $sRawFileInfo);

		// check permissions string
		if (!preg_match('(^[d\-]([r\-][w\-][x\-]){3}$)', $aTmp[0]))
			$aErr[] = 'Permissions not found in ' . $aTmp[0];
		// check file owner
		if (!preg_match('(^[A-Za-z0-9]+$)', $aTmp[2]))
			$aErr[] = 'Owner not found in ' . $aTmp[2];
		// check file group
		if (!preg_match('(^[A-Za-z0-9]+$)', $aTmp[3]))
			$aErr[] = 'Group not found in ' . $aTmp[3];
		// check file size
		if (!is_numeric($aTmp[4]))
			$aErr[] = 'Size not found in ' . $aTmp[4];
		// check file date
		if (false === self::getTimestamp($aTmp[6], $aTmp[5], $aTmp[7]))
			$aErr[] = 'Could not generate timestamp from ' . $aTmp[6] . ' ' . $aTmp[5] . ' ' . $aTmp[7];
		// check file name
		if (!strlen($aTmp[8]) > 0)
			$aErr[] = 'Filename not found in ' . $aTmp[8];

		if (count($aErr) > 0)
		{
			foreach($aErr as $sMsg)
			{
				error_log(__CLASS__ . '::' . __FUNCTION__ . ' - ' . $sMsg);
			} // foreach
			return false;
		} // if
		else
		{
			return true;
		} // else
	} // function

	/**
	 * Generates a timestamp from given input
	 * e.g. 11 Aug 2009 => 1249941600
	 * @param  string $sDay
	 * @param  string $sMonth
	 * @param  string $sYear
	 * @return int
	 */
	public static function getTimestamp($sDay, $sMonth, $sYear)
	{
		$sTime = '';
		if(preg_match('(\d\d:\d\d)', $sYear))
		{
			$sTime = $sYear;
			$sYear = date('Y');
		}
		return strtotime($sDay . ' ' . $sMonth . ' ' . $sYear);
	} // function

	/**
	 * Gets integer value from file permission string
	 * e.g. -rw-rw-rw- => 666
	 * @param  string $sRawChmod
	 * @return int
	 */
	public static function getChmod($sRawChmod)
	{
		$aTrans = array('-' => '0', 'r' => '4', 'w' => '2', 'x' => '1');
		$sChmod = substr(strtr($sRawChmod, $aTrans), 1);
		$aChmod = str_split($sChmod, 3);
		return array_sum(str_split($aChmod[0])) . array_sum(str_split($aChmod[1])) . array_sum(str_split($aChmod[2]));
	} // function

	/**
	 * Parse file size in byte to human readable format
	 * @param  string $sRawSize
	 * @return string
	 */
	public static function getSize($iRawSize)
	{
		$aSymbol = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$exp = floor( log((int)$iRawSize) / log(1024) );
		return sprintf( '%.2f ' . $aSymbol[ $exp ], ((int)$iRawSize / pow(1024, floor($exp))) );
	} // function

	/**
	 * Guess file's mimetype by it's extension.
	 * Default is application/octet-stream
	 * @param  string $sFilename
	 * @return string
	 */
	public static function guessMimetype($sFilename)
	{
		$sFileExtension = strrchr(basename($sFilename), '.');

		// don't switch if extension is empty
		if ($sFileExtension == '')
			return 'application/octet-stream';

		switch ($sFileExtension) {
			case '.zip': $sMimeType = 'application/zip'; break;
			case '.ez':  $sMimeType = 'application/andrew-inset'; break;
			case '.hqx': $sMimeType = 'application/mac-binhex40'; break;
			case '.cpt': $sMimeType = 'application/mac-compactpro'; break;
			case '.doc': $sMimeType = 'application/msword'; break;
			case '.bin': $sMimeType = 'application/octet-stream'; break;
			case '.dms': $sMimeType = 'application/octet-stream'; break;
			case '.lha': $sMimeType = 'application/octet-stream'; break;
			case '.lzh': $sMimeType = 'application/octet-stream'; break;
			case '.exe': $sMimeType = 'application/octet-stream'; break;
			case '.class': $sMimeType = 'application/octet-stream'; break;
			case '.so':  $sMimeType = 'application/octet-stream'; break;
			case '.dll': $sMimeType = 'application/octet-stream'; break;
			case '.oda': $sMimeType = 'application/oda'; break;
			case '.pdf': $sMimeType = 'application/pdf'; break;
			case '.ai':  $sMimeType = 'application/postscript'; break;
			case '.eps': $sMimeType = 'application/postscript'; break;
			case '.ps':  $sMimeType = 'application/postscript'; break;
			case '.smi': $sMimeType = 'application/smil'; break;
			case '.smil': $sMimeType = 'application/smil'; break;
			case '.xls': $sMimeType = 'application/vnd.ms-excel'; break;
			case '.ppt': $sMimeType = 'application/vnd.ms-powerpoint'; break;
			case '.wbxml': $sMimeType = 'application/vnd.wap.wbxml'; break;
			case '.wmlc': $sMimeType = 'application/vnd.wap.wmlc'; break;
			case '.wmlsc': $sMimeType = 'application/vnd.wap.wmlscriptc'; break;
			case '.bcpio': $sMimeType = 'application/x-bcpio'; break;
			case '.vcd': $sMimeType = 'application/x-cdlink'; break;
			case '.pgn': $sMimeType = 'application/x-chess-pgn'; break;
			case '.cpio': $sMimeType = 'application/x-cpio'; break;
			case '.csh': $sMimeType = 'application/x-csh'; break;
			case '.dcr': $sMimeType = 'application/x-director'; break;
			case '.dir': $sMimeType = 'application/x-director'; break;
			case '.dxr': $sMimeType = 'application/x-director'; break;
			case '.dvi': $sMimeType = 'application/x-dvi'; break;
			case '.spl': $sMimeType = 'application/x-futuresplash'; break;
			case '.gtar': $sMimeType = 'application/x-gtar'; break;
			case '.hdf': $sMimeType = 'application/x-hdf'; break;
			case '.js':  $sMimeType = 'application/x-javascript'; break;
			case '.skp': $sMimeType = 'application/x-koan'; break;
			case '.skd': $sMimeType = 'application/x-koan'; break;
			case '.skt': $sMimeType = 'application/x-koan'; break;
			case '.skm': $sMimeType = 'application/x-koan'; break;
			case '.latex': $sMimeType = 'application/x-latex'; break;
			case '.nc':  $sMimeType = 'application/x-netcdf'; break;
			case '.cdf': $sMimeType = 'application/x-netcdf'; break;
			case '.sh':  $sMimeType = 'application/x-sh'; break;
			case '.shar': $sMimeType = 'application/x-shar'; break;
			case '.swf': $sMimeType = 'application/x-shockwave-flash'; break;
			case '.sit': $sMimeType = 'application/x-stuffit'; break;
			case '.sv4cpio': $sMimeType = 'application/x-sv4cpio'; break;
			case '.sv4crc': $sMimeType = 'application/x-sv4crc'; break;
			case '.tar': $sMimeType = 'application/x-tar'; break;
			case '.tcl': $sMimeType = 'application/x-tcl'; break;
			case '.tex': $sMimeType = 'application/x-tex'; break;
			case '.texinfo': $sMimeType = 'application/x-texinfo'; break;
			case '.texi': $sMimeType = 'application/x-texinfo'; break;
			case '.t':$sMimeType = 'application/x-troff'; break;
			case '.tr':  $sMimeType = 'application/x-troff'; break;
			case '.roff': $sMimeType = 'application/x-troff'; break;
			case '.man': $sMimeType = 'application/x-troff-man'; break;
			case '.me':  $sMimeType = 'application/x-troff-me'; break;
			case '.ms':  $sMimeType = 'application/x-troff-ms'; break;
			case '.ustar': $sMimeType = 'application/x-ustar'; break;
			case '.src': $sMimeType = 'application/x-wais-source'; break;
			case '.xhtml': $sMimeType = 'application/xhtml+xml'; break;
			case '.xht': $sMimeType = 'application/xhtml+xml'; break;
			case '.zip': $sMimeType = 'application/zip'; break;
			case '.au':  $sMimeType = 'audio/basic'; break;
			case '.snd': $sMimeType = 'audio/basic'; break;
			case '.mid': $sMimeType = 'audio/midi'; break;
			case '.midi': $sMimeType = 'audio/midi'; break;
			case '.kar': $sMimeType = 'audio/midi'; break;
			case '.mpga': $sMimeType = 'audio/mpeg'; break;
			case '.mp2': $sMimeType = 'audio/mpeg'; break;
			case '.mp3': $sMimeType = 'audio/mpeg'; break;
			case '.aif': $sMimeType = 'audio/x-aiff'; break;
			case '.aiff': $sMimeType = 'audio/x-aiff'; break;
			case '.aifc': $sMimeType = 'audio/x-aiff'; break;
			case '.m3u': $sMimeType = 'audio/x-mpegurl'; break;
			case '.ram': $sMimeType = 'audio/x-pn-realaudio'; break;
			case '.rm':  $sMimeType = 'audio/x-pn-realaudio'; break;
			case '.rpm': $sMimeType = 'audio/x-pn-realaudio-plugin'; break;
			case '.ra':  $sMimeType = 'audio/x-realaudio'; break;
			case '.wav': $sMimeType = 'audio/x-wav'; break;
			case '.pdb': $sMimeType = 'chemical/x-pdb'; break;
			case '.xyz': $sMimeType = 'chemical/x-xyz'; break;
			case '.bmp': $sMimeType = 'image/bmp'; break;
			case '.gif': $sMimeType = 'image/gif'; break;
			case '.ief': $sMimeType = 'image/ief'; break;
			case '.jpeg': $sMimeType = 'image/jpeg'; break;
			case '.jpg': $sMimeType = 'image/jpeg'; break;
			case '.jpe': $sMimeType = 'image/jpeg'; break;
			case '.png': $sMimeType = 'image/png'; break;
			case '.tiff': $sMimeType = 'image/tiff'; break;
			case '.tif': $sMimeType = 'image/tiff'; break;
			case '.djvu': $sMimeType = 'image/vnd.djvu'; break;
			case '.djv': $sMimeType = 'image/vnd.djvu'; break;
			case '.wbmp': $sMimeType = 'image/vnd.wap.wbmp'; break;
			case '.ras': $sMimeType = 'image/x-cmu-raster'; break;
			case '.pnm': $sMimeType = 'image/x-portable-anymap'; break;
			case '.pbm': $sMimeType = 'image/x-portable-bitmap'; break;
			case '.pgm': $sMimeType = 'image/x-portable-graymap'; break;
			case '.ppm': $sMimeType = 'image/x-portable-pixmap'; break;
			case '.rgb': $sMimeType = 'image/x-rgb'; break;
			case '.xbm': $sMimeType = 'image/x-xbitmap'; break;
			case '.xpm': $sMimeType = 'image/x-xpixmap'; break;
			case '.xwd': $sMimeType = 'image/x-xwindowdump'; break;
			case '.igs': $sMimeType = 'model/iges'; break;
			case '.iges': $sMimeType = 'model/iges'; break;
			case '.msh': $sMimeType = 'model/mesh'; break;
			case '.mesh': $sMimeType = 'model/mesh'; break;
			case '.silo': $sMimeType = 'model/mesh'; break;
			case '.wrl': $sMimeType = 'model/vrml'; break;
			case '.vrml': $sMimeType = 'model/vrml'; break;
			case '.css': $sMimeType = 'text/css'; break;
			case '.html': $sMimeType = 'text/html'; break;
			case '.htm': $sMimeType = 'text/html'; break;
			case '.asc': $sMimeType = 'text/plain'; break;
			case '.txt': $sMimeType = 'text/plain'; break;
			case '.rtx': $sMimeType = 'text/richtext'; break;
			case '.rtf': $sMimeType = 'text/rtf'; break;
			case '.sgml': $sMimeType = 'text/sgml'; break;
			case '.sgm': $sMimeType = 'text/sgml'; break;
			case '.tsv': $sMimeType = 'text/tab-separated-values'; break;
			case '.wml': $sMimeType = 'text/vnd.wap.wml'; break;
			case '.wmls': $sMimeType = 'text/vnd.wap.wmlscript'; break;
			case '.etx': $sMimeType = 'text/x-setext'; break;
			case '.xml': $sMimeType = 'text/xml'; break;
			case '.xsl': $sMimeType = 'text/xml'; break;
			case '.mpeg': $sMimeType = 'video/mpeg'; break;
			case '.mpg': $sMimeType = 'video/mpeg'; break;
			case '.mpe': $sMimeType = 'video/mpeg'; break;
			case '.qt':  $sMimeType = 'video/quicktime'; break;
			case '.mov': $sMimeType = 'video/quicktime'; break;
			case '.mxu': $sMimeType = 'video/vnd.mpegurl'; break;
			case '.avi': $sMimeType = 'video/x-msvideo'; break;
			case '.movie': $sMimeType = 'video/x-sgi-movie'; break;
			case '.asf': $sMimeType = 'video/x-ms-asf'; break;
			case '.asx': $sMimeType = 'video/x-ms-asf'; break;
			case '.wm':  $sMimeType = 'video/x-ms-wm'; break;
			case '.wmv': $sMimeType = 'video/x-ms-wmv'; break;
			case '.wvx': $sMimeType = 'video/x-ms-wvx'; break;
			case '.ice': $sMimeType = 'x-conference/x-cooltalk'; break;
			default: $sMimeType = 'application/octet-stream'; break;
		} // switch
		return $sMimeType;
	} // function
} // class
<?php

define('PATH_LIB', realpath(dirname(__FILE__) . '/lib/') . '/', true);

require_once (PATH_LIB . 'Ftp/Client.php');
require_once (PATH_LIB . 'Ftp/Proxy/WWW.php');

$oFtpClient = new Ftp_Client();

$oFtpClient->open('dsiebel.de')->login('dsiebel', 'dome.S4DS')->chdir('subdomains/wiki/httpdocs/var');

$oProxy = new Ftp_Proxy_WWW($oFtpClient);

if (!empty($_GET['getFile'])):
	$oProxy->get($_GET['getFile']);
else: ?>
<html>
<body>
<h3>Directory Listing</h3>
<?php
	if ($_POST['submit'])
	{
		if (!empty($_FILES))
		{
			$oProxy->put($_FILES);
		} // if
		if (!empty($_POST['newFolder']))
		{
			$oFtpClient->mkdir($_POST['newFolder']);
		} // if
	} // if


	foreach ($oFtpClient->dir() as $oFile)
	{
//		echo '<a href="?getFile=' . basename($sFilename) . '">' . $sFilename . '</a><br />' . PHP_EOL;
		echo '<p>' . $oFile . '</p>';
	} // foreach

endif; // else

?>
<hr />
<h3>Upload</h3>
<form enctype="multipart/form-data" method="POST">
Upload: <input name="userfile" type="file" /><br />
New Folder: <input name="newFolder" type="text" /><br />
<input name="submit" type="submit" value="submit">
</form>

</body>
</html>
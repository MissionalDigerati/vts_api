<?php 
header('Cache-Control: no-cache, must-revalidate');
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
header('Content-type: application/json');
echo $this->fetch('content'); 
?>
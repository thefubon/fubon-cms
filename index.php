<?php

include 'boot.php';

$page	= @$_REQUEST['page'] ? : 'index';
$file	= 'pages/' . $page . '.json';
if(!is_file($file)){
	include 'notfound.php';
	exit;
}
$str	= file_get_contents($file);

if(empty($str)) die('No Content');

extract(json_decode($str, true));

?><!DOCTYPE html>
<html>
<head>
	<meta name="description" content="<?= $description ?>">
	<meta name="keywords" content="<?= $keywords ?>">
	<title><?= $title ?></title>
	<link rel="icon" type="image/x-icon" href="//thefubon.com/favicon.ico">
	<?= $head ?><style><?= $styles ?></style>
	<link rel="stylesheet" href="//cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
</head>
<body>
	<?= $content ?>
	<?= $footer ?>
	<script>
		simplemde.value("This text will appear in the editor");
	</script>
</body>
</html>
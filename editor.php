<?php
include 'boot.php';
if(!$_SESSION['login']){
	header('Location: login.php');
	exit;
}
if(!is_dir('pages'))
{
	if(!mkdir('pages'))
		die('Cannot create "pages" directory, make sure is writtable.');

	if(! touch('pages/index.json') ){
		die('Cannot write on "pages" directory, make sure is writtable.');
	}

}
$pages	= [];
foreach (scandir('pages') as $key => $value) {
	if($value == '.' || $value == '..')
		continue;
	$pages[]	= basename($value,'.json');
}

if(empty($pages)){
	if(! touch('pages/index.json') ){
		die('Cannot write on "pages" directory, make sure is writtable.');
	}
	$pages	= array('index');
}
if(isset($_GET['delete'])){
	$file	= 'pages/' . urlencode($_GET['delete']) . '.json';
	if(is_file($file) && !unlink($file))
	{
		die('Fail delete.');
	}
	header('Location: editor.php');
	exit;
}
if(@$_GET['new_page']){
	$new_page	= strtolower(strtr($_GET['new_page'], '	 *"+&#\'','________'));
	if(!is_file('pages/' . $new_page . '.json'))
	{
		if(! touch('pages/' . $new_page . '.json') ){
			die('Cannot write on "pages" directory, make sure is writtable.');
		}
	}
	header('Location: editor.php?page=' . $_GET['new_page']);
	exit;
}

if(! isset($_GET['page']) ){
	header('Location: editor.php?page=' . @$pages[0]);
	exit;
}

$current= $_GET['page'];
if(!in_array($current, $pages))
{
	die("Page not found");
}

$file	= 'pages/' . urlencode($current) . '.json';

if(!empty($_POST) ){
	$data	= $_POST;
	if(!file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT))){
		die("Make sure directory 'pages' is writable.\n Cannot save: $file.");
	}
}else{
	$data	= json_decode(file_get_contents($file), true);
}

$title	= "Fubon CMS | Editor ⚙️";
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?= $title ?></title>
	<link rel="icon" type="image/x-icon" href="//thefubon.com/favicon.ico">
	<link rel="stylesheet" href="//cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
	<link rel="stylesheet" href="/assets/css/editor.css" />
</head>
<body class="flex min-h-screen antialiased">

	<div class="flex flex-col justify-between p-4 border-r border-[#EFF2F6]">
		<div class="flex flex-col space-y-6">
			<a href="/" target="_blank"><img class="w-[29px]" src="/assets/img/fubon-one.svg" alt="Fubon CMS"></a>
			<a class="flex flex-col space-y-1" href="#!" onclick="return newPage()" title="Add New Page">
				<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-6 h-6" viewBox="0 0 16 16">
					<path d="m.5 3 .04.87a1.99 1.99 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14H9v-1H2.826a1 1 0 0 1-.995-.91l-.637-7A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09L14.54 8h1.005l.256-2.819A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2zm5.672-1a1 1 0 0 1 .707.293L7.586 3H2.19c-.24 0-.47.042-.683.12L1.5 2.98a1 1 0 0 1 1-.98h3.672z"/>
					<path d="M13.5 10a.5.5 0 0 1 .5.5V12h1.5a.5.5 0 1 1 0 1H14v1.5a.5.5 0 1 1-1 0V13h-1.5a.5.5 0 0 1 0-1H13v-1.5a.5.5 0 0 1 .5-.5z"/>
				</svg>
				<span class="text-xs">Add</span>
			</a>
		</div>
		<div class="flex flex-col space-y-6">
			<a href="https://github.com/thefubon/fubon-cms" target="_blank">
				<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-6 h-6" viewBox="0 0 16 16">
					<path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.012 8.012 0 0 0 16 8c0-4.42-3.58-8-8-8z"/>
				</svg>
			</a>
			<a href="https://thefubon.com" target="_blank">
				<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-6 h-6" viewBox="0 0 16 16">
					<path d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1h-3zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5zM.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5zM3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7zm2 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-7zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7z"/>
				</svg>
			</a>
			<a href="logout.php" title="Logout">
				<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-6 h-6" viewBox="0 0 16 16">
					<path d="M3 2a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v13h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V2zm1 13h8V2H4v13z"/>
					<path d="M9 9a1 1 0 1 0 2 0 1 1 0 0 0-2 0z"/>
				</svg>
			</a>
		</div>
	</div>

	<div class="md:flex-auto max-w-[240px] 2xl:max-w-[320px] space-y-0.5 flex flex-col p-4 border-r border-[#EFF2F6] overflow-auto h-screen">
		<?php foreach($pages as $key => $page):
				$cls	= '';
				if( ($current && $page == $current) || (!$page && $key === 0) ){
					$cls	= 'bg-[#F2F1FF] hover:bg-[#F2F1FF] text-[#6563FD]';
				}
			?>
				<a class="w-full p-2 text-sm rounded hover:bg-[#F0F2F7] <?= $cls ?>" href="editor.php?page=<?= $page ?>"><?= ucwords( strtr($page,'-_','  ') ) ?></a>
			<?php endforeach ?>
	</div>

	<div class="flex-1 overflow-auto h-screen relative">
		<form method="post">

			<div class="flex justify-between items-center w-full px-6 h-16 border-b border-[#EFF2F6] bg-white sticky top-0 z-20">

				<div class="flex items-center space-x-4">
					<h2 class="text-xl font-bold"><?= ucwords( strtr($current,'-_','  ') ) ?></h2>

					<?php if($current != 'index'): ?>
					<a class="text-red-500" href="editor.php?delete=<?= $current ?>" onclick="return confirm('Delete this page?')" class="btn red">
						<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
						</svg>
					</a>
				<?php endif ?>
				</div>
				<div class="flex space-x-4">
					<a class="flex items-center bg-[#6563FD] hover:bg-[#3437B3] text-white text-sm pt-1.5 pb-2 px-3 rounded" href="/?page=<?= $current ?>" target="_blank">
						<span class="hidden md:block mr-2">View</span>
						<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
						</svg>
					</a>

					<button type="submit" class="flex items-center bg-[#33C48D] hover:bg-[#0B5440] text-white text-sm pt-1.5 pb-2 px-3 rounded">
						<span class="hidden md:block mr-2">Save</span>
						<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
						</svg>
					</button>
				</div>
			</div>

			<div class="p-6 space-y-4">			
				<div class="pb-2">
					<h4 class="border border-[#DFE2EF] border-b-0 rounded-t p-4 text-xs uppercase">Meta</h4>

					<div class="p-4 border border-[#DFE2EF] rounded-b space-y-4">
						<div class="space-y-1">
							<legend class="w-36 text-xs text-[#818B95]">Title</legend>
							<input class="w-full text-sm rounded border border-[#DFE2EF] focus:border-[#DFE2EF] focus:ring-0 focus:bg-[#FAFAFC]" name="title" type="text" value="<?= @$data['title'] ?>">
						</div>

						<div class="space-y-1">
							<legend class="w-36 text-xs text-[#818B95]">Description</legend>
							<input class="w-full text-sm rounded border border-[#DFE2EF] focus:border-[#DFE2EF] focus:ring-0 focus:bg-[#FAFAFC]" name="description" type="text" value="<?= @$data['description'] ?>">
						</div>
						
						<div class="space-y-1">
							<legend class="w-36 text-xs text-[#818B95]">Keywords</legend>
							<input class="w-full text-sm rounded border border-[#DFE2EF] focus:border-[#DFE2EF] focus:ring-0 focus:bg-[#FAFAFC]" name="keywords" type="text" value="<?= @$data['keywords'] ?>">
						</div>
					</div>
				</div>

				<div>
					<h4 class="border border-[#DFE2EF] border-b-0 rounded-t p-4 text-xs uppercase">Head</h4>
					<textarea class="w-full text-sm rounded-b border border-[#DFE2EF] focus:border-[#DFE2EF] focus:ring-0 focus:bg-[#FAFAFC]" name="head"><?= @$data['head'] ?></textarea>
				</div>
					
					<div>
						<h4 class="border border-[#DFE2EF] border-b-0 rounded-t p-4 text-xs uppercase">Styles</h4>
						<textarea class="w-full text-sm rounded-b border border-[#DFE2EF] focus:ring-0 focus:border-[#DFE2EF] focus:bg-[#FAFAFC]" name="styles"><?= @$data['styles'] ?></textarea>
					</div>
			
				
				<div class="pb-2">
					<textarea id="MyID" name="content"><?= @$data['content'] ?></textarea>
				</div>

				<div>
					<h4 class="border border-[#DFE2EF] border-b-0 rounded-t p-4 text-xs uppercase">Footer</h4>
					<textarea class="w-full text-sm rounded-b border border-[#DFE2EF] focus:ring-0 focus:border-[#DFE2EF] focus:bg-[#FAFAFC]" name="footer"><?= @$data['footer'] ?></textarea>
				</div>

			</div>
		</form>

	</div>

<script src="//cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<script src='/assets/js/autosize.min.js'></script>
<script>
	// Created New Page
	function newPage(){
		var name	= prompt('Slug/File name');
		if(name){
			document.location = 'editor.php?new_page=' + name;
		}
	}
	// SimpleMDE Config
	var simplemde = new SimpleMDE({
		element: document.getElementById("MyID"),
		//autofocus: true,
		//lineWrapping: false,
		placeholder: "Type HTML here...",
		spellChecker: false,
		status: false,
		renderingConfig: {
			singleLineBreaks: false,
			codeSyntaxHighlighting: true,
		},
		toolbar: ["preview", "side-by-side", "fullscreen"],
	});

	// Textarea AutoSize - https://www.jacklmoore.com/autosize/
	autosize(document.querySelectorAll('textarea'));
 </script>

</body>
</html>
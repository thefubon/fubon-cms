<?php
include 'boot.php';
if (!$_SESSION['login']) {
    header('Location: login.php');
    exit();
}
if (!is_dir('pages')) {
    if (!mkdir('pages')) {
        die('Cannot create "pages" directory, make sure is writtable.');
    }

    if (!touch('pages/index.json')) {
        die('Cannot write on "pages" directory, make sure is writtable.');
    }
}
$pages = [];
foreach (scandir('pages') as $key => $value) {
    if ($value == '.' || $value == '..') {
        continue;
    }
    $pages[] = basename($value, '.json');
}

if (empty($pages)) {
    if (!touch('pages/index.json')) {
        die('Cannot write on "pages" directory, make sure is writtable.');
    }
    $pages = ['index'];
}
if (isset($_GET['delete'])) {
    $file = 'pages/' . urlencode($_GET['delete']) . '.json';
    if (is_file($file) && !unlink($file)) {
        die('Fail delete.');
    }
    header('Location: editor.php');
    exit();
}
if (@$_GET['new_page']) {
    $new_page = strtolower(strtr($_GET['new_page'], '	 *"+&#\'', '________'));
    if (!is_file('pages/' . $new_page . '.json')) {
        if (!touch('pages/' . $new_page . '.json')) {
            die('Cannot write on "pages" directory, make sure is writtable.');
        }
    }
    header('Location: editor.php?page=' . $_GET['new_page']);
    exit();
}

if (!isset($_GET['page'])) {
    header('Location: editor.php?page=' . @$pages[0]);
    exit();
}

$current = $_GET['page'];
if (!in_array($current, $pages)) {
    die('Page not found');
}

$file = 'pages/' . urlencode($current) . '.json';

if (!empty($_POST)) {
    $data = $_POST;
    if (!file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT))) {
        die("Make sure directory 'pages' is writable.\n Cannot save: $file.");
    }
} else {
    $data = json_decode(file_get_contents($file), true);
}

$title = 'Fubon CMS | Editor ⚙️';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?= $title ?></title>
	<link rel="icon" type="image/x-icon" href="//thefubon.com/favicon.ico">
	<link rel="stylesheet" href="/assets/css/editor.css" />
</head>
<body class="flex flex-col md:flex-row min-h-screen antialiased">
	
<div class="flex md:flex-col justify-between items-center p-4 border-b md:border-r border-[#EFF2F6]">
	<div class="flex md:flex-col items-center space-x-1 md:space-x-0">
		<button class="mobile-menu-button md:hidden mr-4">
			<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-6 h-6" viewBox="0 0 16 16">
				<path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
			</svg>
		</button>
		<a href="/" target="_blank"><img class="w-[18px] md:w-[28px]" src="/assets/img/fubon-one.svg" alt="Fubon CMS"></a>
		<a class="hidden md:mt-6 md:flex md:flex-col items-center space-x-1 md:space-x-0 md:space-y-1 group" href="#!" onclick="return newPage()" title="Add New Page">
			<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-6 h-6 group-hover:text-[#6563FD] duration-300" viewBox="0 0 16 16">
				<path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
				<path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
			</svg>
			<span class="text-xs">Add</span>
		</a>
	</div>
	<div class="flex md:flex-col space-x-4 md:space-x-0 md:space-y-6">
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

<div class="sidebar fixed left-0 transform -translate-x-full md:-translate-x-0 duration-300 md:relative bg-white z-40 w-full max-w-[240px] 2xl:max-w-[320px] space-y-0.5 flex flex-col p-4 border-r border-[#EFF2F6] overflow-auto h-screen">
	<a class="md:hidden mb-4 p-2 flex items-center space-x-2 group" href="#!" onclick="return newPage()" title="Add New Page">
			<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-6 h-6 group-hover:text-[#6563FD] duration-300" viewBox="0 0 16 16">
				<path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
				<path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
			</svg>
			<span class="text-xs">Add Page</span>
		</a>
		
	<?php foreach ($pages as $key => $page):

     $cls = 'hover:bg-[#F0F2F7]';
     if (($current && $page == $current) || (!$page && $key === 0)) {
         $cls = 'bg-[#F2F1FF] hover:bg-[#F2F1FF] text-[#6563FD]';
     }
     ?>
			<a class="w-full p-2 text-sm rounded duration-300 <?= $cls ?>" href="editor.php?page=<?= $page ?>"><?= ucwords(
    strtr($page, '-_', '  ')
) ?></a>
		<?php
 endforeach; ?>
</div>

<div class="flex-1 overflow-auto h-screen relative">
	<form method="post">

		<div class="flex justify-between items-center w-full px-6 h-16 border-b border-[#EFF2F6] bg-white sticky top-0 z-20">

			<div class="flex items-center space-x-4">
				<h2 class="text-xl font-bold"><?= ucwords(strtr($current, '-_', '  ')) ?></h2>

				<?php if ($current != 'index'): ?>
				<a class="text-red-500" href="editor.php?delete=<?= $current ?>" onclick="return confirm('Delete this page?')" class="btn red">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
					</svg>
				</a>
			<?php endif; ?>
			</div>
			<div class="flex space-x-4">
				<a class="flex items-center bg-[#6563FD] hover:bg-[#3437B3] duration-300 text-white text-sm pt-1.5 pb-2 px-3 rounded" href="/?page=<?= $current ?>" target="_blank">
					<span class="hidden md:block mr-2">View</span>
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
					</svg>
				</a>

				<button type="submit" class="flex items-center bg-[#33C48D] duration-300 hover:bg-[#0B5440] text-white text-sm pt-1.5 pb-2 px-3 rounded">
					<span class="hidden md:block mr-2">Save</span>
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
					</svg>
				</button>
			</div>
		</div>

		<div class="p-4 md:p-6 space-y-10">
			<div class="pb-2">
				<h4 class="text-[#818B95] mb-2 text-xs uppercase">Meta</h4>

				<div class="p-4 border border-[#DFE2EF] rounded space-y-4">
					<div class="space-y-1">
						<legend class="w-36 text-xs text-[#818B95]">Title</legend>
						<input class="w-full text-sm rounded border border-[#DFE2EF] focus:border-[#6563FD] focus:ring-0 focus:bg-[#FAFAFC]" name="title" type="text" value="<?= @$data[
          'title'
      ] ?>">
					</div>

					<div class="space-y-1">
						<legend class="w-36 text-xs text-[#818B95]">Description</legend>
						<input class="w-full text-sm rounded border border-[#DFE2EF] focus:border-[#6563FD] focus:ring-0 focus:bg-[#FAFAFC]" name="description" type="text" value="<?= @$data[
          'description'
      ] ?>">
					</div>
					
					<div class="space-y-1">
						<legend class="w-36 text-xs text-[#818B95]">Keywords</legend>
						<input class="w-full text-sm rounded border border-[#DFE2EF] focus:border-[#6563FD] focus:ring-0 focus:bg-[#FAFAFC]" name="keywords" type="text" value="<?= @$data[
          'keywords'
      ] ?>">
					</div>
				</div>
			</div>

			<div>
				<h4 class="text-[#818B95] mb-2 text-xs uppercase">Head</h4>
				<textarea class="w-full text-sm rounded border border-[#DFE2EF] focus:border-[#6563FD] focus:ring-0 focus:bg-[#FAFAFC]" name="head"><?= @$data[
        'head'
    ] ?></textarea>
			</div>
				
			<div>
				<h4 class="text-[#818B95] mb-2 text-xs uppercase">Styles</h4>
				<textarea class="w-full text-sm rounded border border-[#DFE2EF] focus:ring-0 focus:border-[#6563FD] focus:bg-[#FAFAFC]" name="styles"><?= @$data[
        'styles'
    ] ?></textarea>
			</div>
			
			<div>
				<h4 class="uppercase text-[#818B95] text-xs mb-2">Body</h4>
				<input class="w-full mb-2 rounded border text-sm placeholder-gray-400 border-[#DFE2EF] focus:border-[#6563FD] focus:ring-0 focus:bg-[#FAFAFC]" type="text" name="bodycss" placeholder="Example: class='class-name' data-name" value="<?= @$data[
        'bodycss'
    ] ?>">
				<textarea class="w-full min-h-[280px] text-sm rounded placeholder-gray-400 border border-[#DFE2EF] focus:ring-0 focus:bg-[#FAFAFC] focus:border-[#6563FD]" placeholder="Enter HTML Source Code" name="content"><?= @$data[
        'content'
    ] ?></textarea>
			</div>

			<div>
				<h4 class="text-[#818B95] mb-2 text-xs uppercase">Footer</h4>
				<textarea class="w-full text-sm rounded border border-[#DFE2EF] focus:ring-0 focus:border-[#6563FD] focus:bg-[#FAFAFC]" name="footer"><?= @$data[
        'footer'
    ] ?></textarea>
			</div>

		</div>
	</form>
</div>

<div class="overlay mobile-menu-overlay bg-gray-800 inset-0 fixed z-20 md:hidden opacity-75 transform translate-x-full md:-translate-x-0 duration-300 overflow-y-hidden"></div>

<script src='/assets/js/autosize.min.js'></script>
<script src='/assets/js/main.js'></script>
</body>
</html>
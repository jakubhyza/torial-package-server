<?php
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: *");
	header("Content-Type: text/plain");

	$id = strtr($_GET['id'], [
		'..' => '',
		'/' => '',
	]);

	$readmeFileNames = [
		'readme.md',
		'readme.MD',
		'README.md',
		'README.MD',
		'readme.txt',
		'readme.TXT',
		'README.txt',
		'README.TXT',
		'readme',
		'README'
	];

	$readme = null;

	foreach ($readmeFileNames as $rm)
	{
		if (file_exists(__DIR__.'/files/'.$id.'/'.$rm))
		{
			$readme = __DIR__.'/files/'.$id.'/'.$rm;
			break;
		}
	}

	if ($readme != null)
	{
		$file = fopen($readme, 'r');
		fpassthru($file);
		fclose($file);
	}
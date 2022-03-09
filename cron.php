<?php

	$repoInfo = json_decode(file_get_contents(__DIR__.'/index.json'));

	//Settings
	$repositoryUri = $repoInfo->repository_path;

	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	$start_time = microtime(true);

	?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>CRON JOB OUTPUT</title>

		<style>
			body {
				padding: 10px;
				margin: 0px;
				background: #202124;
				font-family: monospace;
				color: white;
			}
			code {
				color: #cccccc;
			}
			p {
				border-bottom: 1px solid #3c3d42;
				margin: 0px;
				padding: 7px 0px;
			}
			p:before {
				content: '> ';
				color: #9e9e9e;
			}
			.important {
				color: yellow;
			}
			.important:before {
				content: '> ';
				color: #03a9f4;
			}
		</style>
	</head>
	<body>
		<p class="important">Running cron job for Torial Repository</p>
	<?php

	/* Index repository */

	$dirs = glob(__DIR__.'/files/*',GLOB_ONLYDIR);
	
	$index = [
		'repository_uri'=>$repositoryUri,
		'document_type'=>'index',
		'timestamp'=>time(),
		'length'=>count($dirs),
		'modules'=>[]
	];

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

	$index['modules'] = array_map(function($module) use ($repositoryUri,$readmeFileNames){

		if (!file_exists($module.'/manifest.json'))
			return;

		$manifest = file_get_contents($module.'/manifest.json');
		$manifest = json_decode($manifest);


		$readme = null;
		foreach ($readmeFileNames as $rm)
		{
			if (file_exists($module.'/'.$rm))
			{
				$readme = $repositoryUri.'files/'.basename($module).'/'.$rm;
				break;
			}
		}
		
		$icon = null;
		if (file_exists($module.'/icon.png'))
			$icon = $repositoryUri.'files/'.basename($module).'/icon.png';
		
		echo '<p>Indexing module <code>'.htmlspecialchars(basename($module)).'</code></p>';

		return [
			'id'=>basename($module),

			'name'=>$manifest->name,
			'author'=>$manifest->author,
			'version'=>$manifest->version,

			'dependencies' => isset($manifest->dependencies) ? $manifest->dependencies : [],
			'readme' => $readme,
			'icon' => $icon,
			'download' => $repositoryUri.'download?id='.urlencode(basename($module))
		];
	},$dirs);


	file_put_contents(__DIR__.'/indices/main.json',json_encode($index));


	/* Clear temp */

	$time = 30; //in minutes, time until file deletion threshold
	foreach (glob(__DIR__."/tmp/*.zip") as $filename) {
		if (file_exists($filename)) {
			if(time() - filemtime($filename) > $time * 60) {
				unlink($filename);
				echo '<p>Removed <code>'.htmlspecialchars(basename($filename)).'</code> from download temp</p>';
			}
		}
	}  

?>
	<p class="important">Done in <code><?= round(microtime(true) - $start_time,3) ?></code> seconds</p>
</body>
</html>
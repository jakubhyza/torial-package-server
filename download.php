<?php

	// This file handle ziping and downloading files

	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	function zipFolder($sourceFolder,$destination,$wrap = 'module')
	{
		// Get real path for our folder
		$rootPath = realpath($sourceFolder);

		// Initialize archive object
		$zip = new ZipArchive();
		$zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE);

		// Create recursive directory iterator
		/** @var SplFileInfo[] $files */
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($rootPath),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($files as $name => $file)
		{
			// Skip directories (they would be added automatically)
			if (!$file->isDir())
			{
				// Get real and relative path for current file
				$filePath = $file->getRealPath();
				$relativePath = substr($filePath, strlen($rootPath) + 1);
				$relativePath = $wrap.'/'.$relativePath;

				// Add current file to archive
				$zip->addFile($filePath, $relativePath);
			}
		}

		// Zip archive will be created only after closing object
		$zip->close();
	}

	function RandomString()
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 6; $i++) {
            $randstring .= $characters[rand(0, strlen($characters))];
        }
        return $randstring;
    }

	$module = $_GET['id'];
	//TODO: Check input

	if (!file_exists(__DIR__.'/files/'.$module))
		die();

	$fileName = RandomString().'_'.time().'.zip';

	zipFolder(__DIR__.'/files/'.$module,__DIR__.'/tmp/'.$fileName,$module);

	header('Location: https://torial.jakubhyza.cz/repository/tmp/'.$fileName);

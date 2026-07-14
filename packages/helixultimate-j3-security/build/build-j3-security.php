#!/usr/bin/env php
<?php
/**
 * Build Helix Ultimate J3 security patch zip.
 *
 * Usage: php packages/helixultimate-j3-security/build/build-j3-security.php
 */

declare(strict_types=1);

$root = realpath(__DIR__ . '/../../..');
$packageDir = $root . '/packages/helixultimate-j3-security';
$filesDir = $packageDir . '/files';
$version = '1.0.0';
$outputZip = $packageDir . '/dist/helixultimate_j3_security_fixes_v' . $version . '.zip';

$patchedFiles = array(
	'plugins/system/helixultimate/helixultimate.php',
	'plugins/system/helixultimate/helixultimate.xml',
	'plugins/system/helixultimate/script.php',
	'plugins/system/helixultimate/src/Platform/Helper.php',
	'plugins/system/helixultimate/src/Platform/Platform.php',
	'plugins/system/helixultimate/src/Platform/Request.php',
	'plugins/system/helixultimate/src/Platform/Media.php',
	'plugins/system/helixultimate/src/Platform/Blog.php',
	'plugins/system/helixultimate/src/HttpResponse/Response.php',
	'plugins/system/helixultimate/src/Core/Classes/HelixultimateMenu.php',
	'plugins/system/helixultimate/overrides/layouts/joomla/content/blog/audio.php',
	'plugins/system/helixultimate/overrides/layouts/joomla/content/blog/gallery.php',
	'plugins/system/helixultimate/overrides/layouts/joomla/content/blog/video.php',
	'plugins/system/helixultimate/overrides/mod_menu/default.php',
	'templates/shaper_helixultimate/templateDetails.xml',
);

if (!is_dir($packageDir))
{
	fwrite(STDERR, "Package directory not found.\n");
	exit(1);
}

if (is_dir($filesDir))
{
	removeDirectory($filesDir);
}

mkdir($filesDir, 0755, true);

if (!is_dir(dirname($outputZip)))
{
	mkdir(dirname($outputZip), 0755, true);
}

$manifest = array(
	'packageVersion' => $version,
	'helixBaseline'  => '2.1.4-j3sec',
	'builtAt'        => gmdate('c'),
	'files'          => array(),
);

foreach ($patchedFiles as $relativePath)
{
	$source = $root . '/' . $relativePath;
	$target = $filesDir . '/' . $relativePath;

	if (!is_file($source))
	{
		fwrite(STDERR, "Missing source file: {$relativePath}\n");
		exit(1);
	}

	$targetDir = dirname($target);

	if (!is_dir($targetDir))
	{
		mkdir($targetDir, 0755, true);
	}

	if (!copy($source, $target))
	{
		fwrite(STDERR, "Failed to copy: {$relativePath}\n");
		exit(1);
	}

	$manifest['files'][$relativePath] = sha1_file($target);
}

file_put_contents(
	$packageDir . '/manifest.json',
	json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
);

$zip = new ZipArchive();

if ($zip->open($outputZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true)
{
	fwrite(STDERR, "Unable to create zip: {$outputZip}\n");
	exit(1);
}

$packageFiles = array(
	'helixultimatej3securityfixes.xml',
	'script.php',
);

foreach ($packageFiles as $file)
{
	$zip->addFile($packageDir . '/' . $file, $file);
}

addDirectoryToZip($zip, $filesDir, 'files');
$zip->close();

buildFullExtensionZips($root, $packageDir . '/dist', $version);

$checksumsFile = $packageDir . '/dist/SHA256SUMS';
$checksumLines = array();

foreach (glob($packageDir . '/dist/*.zip') as $zipFile)
{
	$checksumLines[] = hash_file('sha256', $zipFile) . '  ' . basename($zipFile);
}

file_put_contents($checksumsFile, implode("\n", $checksumLines) . "\n");

echo "Built {$outputZip}\n";
echo "Manifest: {$packageDir}/manifest.json\n";
echo "Checksum: {$checksumsFile}\n";

function buildFullExtensionZips(string $root, string $distDir, string $version): void
{
	$pluginVersion = $version;
	$pluginXmlFile = $root . '/plugins/system/helixultimate/helixultimate.xml';
	if (is_file($pluginXmlFile))
	{
		$xml = simplexml_load_file($pluginXmlFile);
		if ($xml && isset($xml->version))
		{
			$pluginVersion = (string) $xml->version;
		}
	}

	$templateVersion = $version;
	$templateXmlFile = $root . '/templates/shaper_helixultimate/templateDetails.xml';
	if (is_file($templateXmlFile))
	{
		$xml = simplexml_load_file($templateXmlFile);
		if ($xml && isset($xml->version))
		{
			$templateVersion = (string) $xml->version;
		}
	}

	$pluginZip = $distDir . '/plg_system_helixultimate_j3_v' . $pluginVersion . '.zip';
	$templateZip = $distDir . '/helixultimate_j3_template_v' . $templateVersion . '.zip';

	buildDirectoryZip(
		$root . '/plugins/system/helixultimate',
		$pluginZip,
		'plg_system_helixultimate',
		array('tests')
	);

	// Append missing language file for system plugin
	$pluginLangFile = $root . '/administrator/language/en-GB/en-GB.plg_system_helixultimate.ini';
	if (is_file($pluginLangFile))
	{
		$zip = new ZipArchive();
		if ($zip->open($pluginZip) === true)
		{
			$zip->addFile($pluginLangFile, 'plg_system_helixultimate/language/en-GB.plg_system_helixultimate.ini');
			$zip->close();
		}
	}
	else
	{
		fwrite(STDERR, "Warning: Plugin language file not found at {$pluginLangFile}\n");
	}

	buildDirectoryZip(
		$root . '/templates/shaper_helixultimate',
		$templateZip,
		'shaper_helixultimate',
		array('installer.script.php', 'installer.xml')
	);

	// Append missing language file for template
	$templateLangFile = $root . '/language/en-GB/en-GB.tpl_shaper_helixultimate.ini';
	if (is_file($templateLangFile))
	{
		$zip = new ZipArchive();
		if ($zip->open($templateZip) === true)
		{
			$zip->addFile($templateLangFile, 'shaper_helixultimate/en-GB.tpl_shaper_helixultimate.ini');
			$zip->close();
		}
	}
	else
	{
		fwrite(STDERR, "Warning: Template language file not found at {$templateLangFile}\n");
	}

	echo "Built {$pluginZip}\n";
	echo "Built {$templateZip}\n";
}

function buildDirectoryZip(string $sourceDir, string $zipPath, string $prefix, array $excludeDirs): void
{
	$zip = new ZipArchive();

	if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true)
	{
		throw new RuntimeException('Unable to create zip: ' . $zipPath);
	}

	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS)
	);

	foreach ($iterator as $fileInfo)
	{
		/** @var SplFileInfo $fileInfo */
		if (!$fileInfo->isFile())
		{
			continue;
		}

		$relative = substr($fileInfo->getPathname(), strlen($sourceDir) + 1);

		foreach ($excludeDirs as $excludeDir)
		{
			if (strpos($relative, $excludeDir . '/') === 0 || $relative === $excludeDir)
			{
				continue 2;
			}
		}

		$zip->addFile($fileInfo->getPathname(), $prefix . '/' . str_replace('\\', '/', $relative));
	}

	$zip->close();
}

function addDirectoryToZip(ZipArchive $zip, string $directory, string $localPrefix): void
{
	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
	);

	foreach ($iterator as $fileInfo)
	{
		/** @var SplFileInfo $fileInfo */
		if (!$fileInfo->isFile())
		{
			continue;
		}

		$localPath = $localPrefix . '/' . substr($fileInfo->getPathname(), strlen($directory) + 1);
		$zip->addFile($fileInfo->getPathname(), str_replace('\\', '/', $localPath));
	}
}

function removeDirectory(string $directory): void
{
	if (!is_dir($directory))
	{
		return;
	}

	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
		RecursiveIteratorIterator::CHILD_FIRST
	);

	foreach ($iterator as $fileInfo)
	{
		if ($fileInfo->isDir())
		{
			rmdir($fileInfo->getPathname());
		}
		else
		{
			unlink($fileInfo->getPathname());
		}
	}

	rmdir($directory);
}

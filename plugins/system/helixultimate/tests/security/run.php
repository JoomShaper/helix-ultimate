<?php
/**
 * Helix Ultimate security test runner.
 *
 * Usage: php plugins/system/helixultimate/tests/security/run.php
 *
 * @package Helix_Ultimate_Framework
 */

declare(strict_types=1);

$tests = [
	'Phase01OpenRedirectTest.php',
	'Phase02CsrfAclTest.php',
];

$allFailures = [];

foreach ($tests as $file)
{
	$path = __DIR__ . '/' . $file;

	if (!is_file($path))
	{
		continue;
	}

	require_once $path;

	$class = pathinfo($file, PATHINFO_FILENAME);

	if (!class_exists($class) || !method_exists($class, 'run'))
	{
		$allFailures[] = $class . ' is missing a run() method.';
		continue;
	}

	$failures = $class::run();

	foreach ($failures as $failure)
	{
		$allFailures[] = $class . ': ' . $failure;
	}
}

if ($allFailures !== [])
{
	fwrite(STDERR, "Security tests failed:\n");

	foreach ($allFailures as $failure)
	{
		fwrite(STDERR, "  - {$failure}\n");
	}

	exit(1);
}

echo "All security tests passed (" . count($tests) . " suites).\n";

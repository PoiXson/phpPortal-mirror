<?php declare(strict_types=1);

// class loader
$loader = null;
if (\file_exists(__DIR__.'/../vendor/autoload.php')) {
	$loader = require(__DIR__.'/../vendor/autoload.php');
} else
if (\file_exists(__DIR__.'/../website.phar')) {
	$loader = require(__DIR__.'/../website.phar/vendor/autoload.php');
}
if ($loader == null) { echo "Failed to detect autoload.php\n"; exit(1); }

// website class
$website_class = null;
if (\file_exists(__DIR__.'/.website')) {
	$website_class = \file_get_contents(__DIR__.'/.website');
} else
if (\file_exists(__DIR__.'/../.website')) {
	$website_class = \file_get_contents(__DIR__.'/../.website');
}
if (empty($website_class)) { echo "Failed to detect website class\n"; exit(1); }
$website_class = \explode("\n", $website_class, 2);
$website_class = \trim(\reset($website_class));
if (\str_ends_with($website_class, '\\'))
	$website_class = \mb_substr($website_class, 0, -1);
if (empty($website_class)) { echo "Invalid website class\n"; exit(1); }
if (!\str_ends_with($website_class, '\\Website'))
	$website_class .= '\\Website';
if (!\class_exists($website_class)) { echo "Website class not found: $website_class\n"; exit(1); }

// load website
$website = new $website_class($loader);
$website->run();

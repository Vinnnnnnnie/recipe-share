<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config())
	->setRiskyAllowed(true)
	->setRules([
		'@PSR12' => true,
		'braces_position' => [
			'control_structures_opening_brace' => 'same_line',
			'functions_opening_brace' => 'same_line',
			'classes_opening_brace' => 'same_line',
			'anonymous_functions_opening_brace' => 'same_line',
			'anonymous_classes_opening_brace' => 'same_line',
		],
		'indentation_type' => true,
	])
	->setIndent("\t")
	->setLineEnding("\n")
	// 💡 by default, Fixer looks for `*.php` files excluding `./vendor/` - here, you can groom this config
	->setFinder(
		(new Finder())
			// 💡 root folder to check
			->in(__DIR__)
	// 💡 additional files, eg bin entry file
	// ->append([__DIR__.'/bin-entry-file'])
	// 💡 folders to exclude, if any
	// ->exclude([/* ... */])
	// 💡 path patterns to exclude, if any
	// ->notPath([/* ... */])
	// 💡 extra configs
	// ->ignoreDotFiles(false) // true by default in v3, false in v4 or future mode
	// ->ignoreVCS(true) // true by default
	);

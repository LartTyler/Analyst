<?php
	namespace DaybreakStudios\Analyst\Samples;

	use DaybreakStudios\Analyst\Experiment;

	require __DIR__ . '/bootstrap.php';

	ob_start();

	$fullName = 'Tyler Lartonoix';
	$name = Experiment::create()
		->setThrowOnMismatch(true) // Throw an exception if our control and candidate disagree on a result, instead of
		->setContext([			   // hiding the mismatch
			'name' => $fullName,
		])
		->control(function($name) {
			$name = trim($name);

			return [
				substr($name, 0, strpos($name, ' ')),
				substr($name, strpos($name, ' ') + 1) ?: null,
			];
		})
		->candidate(function($name) {
			return [
				strtok(trim($name), ' '),
				strtok('') ?: null,
			];
		})
		->run();

	printf(":: Results\nFull name: %s\nFirst name: %s\nLast name: %s", $fullName, $name[0], $name[1]);

	echo ob_get_clean() . "\n";

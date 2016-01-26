<?php
	namespace DaybreakStudios\Analyst\Samples;

	use DaybreakStudios\Analyst\Experiment;
	use DaybreakStudios\Analyst\ResultInterface;

	require __DIR__ . '/bootstrap.php';

	class MyExperiment extends Experiment {
		public function publish(ResultInterface $result) {
			printf(":: Name: %s\n", parent::getName());
			printf("Observations made: %d\n", sizeof($result->getObservations()));
			printf("Mismatches? %d\n", sizeof($result->getMismatches()));

			printf(":: Observation Results\n");

			foreach ($result->getObservations() as $ob)
				printf("\t%s: %s\n", $ob->getName(), $ob->hasException() ? 'Exception thrown: ' .
					get_class($ob->getException()) : getVarDumpValue($ob->getResult()));
		}
	}

	$a = MyExperiment::create('experiment-a')
		->control(function() { return 1; })
		->candidate(function() { return 1; })
		->run();

	printf("Result: %s\n\n", getVarDumpValue($a));

	$b = MyExperiment::create('experiment-b')
		->control(function() { return 1; })
		->candidate(function() { return '1'; })
		->run();

	printf("Result %s\n", getVarDumpValue($b));

	function getVarDumpValue($val) {
		ob_start();
		var_dump($val);

		return trim(ob_get_clean());
	}
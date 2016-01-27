<?php
	namespace DaybreakStudios\Analyst\Samples;

	use DaybreakStudios\Analyst\Experiment;
	use DaybreakStudios\Analyst\ResultInterface;
	use PDO;

	require __DIR__ . '/bootstrap.php';

	define('DB_HOST', '127.0.0.1');
	define('DB_NAME', 'analyst_demo');
	define('DB_USER', 'root');
	define('DB_PASS', '');

	class MyExperiment extends Experiment {
		private $db;
		private $stmt;

		public function __construct($name, PDO $db) {
			parent::__construct($name);

			$this->db = $db;
			$this->stmt = $db->prepare('insert into experiment_results (experiment, timestamp, behavior, is_control,
				execution_time, result) values (:experiment, UTC_TIMESTAMP, :behavior, :control, :duration, :result)');

			$this->stmt->bindValue(':experiment', $name);
		}

		public function publish(ResultInterface $result) {
			$behaviors = $result->getCandidates();

			array_unshift($behaviors, $result->getControl());

			$this->db->beginTransaction();

			foreach ($behaviors as $behavior) {
				$this->stmt->bindValue(':behavior', $behavior->getName());
				$this->stmt->bindValue(':control', $behavior === $result->getControl(), PDO::PARAM_BOOL);
				$this->stmt->bindValue(':duration', $behavior->getDuration());
				$this->stmt->bindValue(':result', serialize($behavior->getResult()));

				$this->stmt->execute();
			}

			$this->db->commit();
		}

		public static function create($name = 'experiment', PDO $db) {
			return new MyExperiment($name, $db);
		}
	}

	$a = MyExperiment
		::create('my-experiment', new PDO(sprintf('mysql:dbname=%s;host=%s', DB_NAME, DB_HOST), DB_USER, DB_PASS))
		->control(function() { return 1; })
		->candidate(function() { return '1'; })
		->run();

	printf("Result: %s\n\n", getVarDumpValue($a));

	function getVarDumpValue($val) {
		ob_start();
		var_dump($val);

		return trim(ob_get_clean());
	}
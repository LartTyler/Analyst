<?php
	namespace DaybreakStudios\Analyst;

	class Observation implements ObservationInterface {
		private $name;
		private $duration;

		private $result = null;
		private $exception = null;

		public function __construct($name, callable $behavior) {
			$this->name = $name;

			$now = microtime(true);
		}
	}
<?php
	namespace DaybreakStudios\Analyst;

	use DaybreakStudios\Analyst\Exception\BehaviorMissingException;
	use DaybreakStudios\Analyst\Exception\BehaviorNotUniqueException;
	use DaybreakStudios\Analyst\Exception\MismatchException;

	class Experiment implements ExperimentInterface {
		private $name;

		private $behaviors = [];
		private $context = null;
		private $comparator = null;
		private $throwOnMismatch = false;

		/**
		 * Experiment constructor.
		 *
		 * @param string $name
		 */
		public function __construct($name = 'experiment') {
			$this->name = $name;
		}

		public function getName() {
			return $this->name;
		}

		public function setContext(array $context) {
			$this->context = $context;

			return $this;
		}

		public function getContext() {
			return $this->context;
		}

		public function setComparator(callable $comparator) {
			$this->comparator = $comparator;

			return $this;
		}

		public function getComparator() {
			return $this->comparator;
		}

		public function setThrowOnMismatch($throwOnMismatch) {
			$this->throwOnMismatch = $throwOnMismatch;

			return $this;
		}

		public function getThrowOnMismatch() {
			return $this->throwOnMismatch;
		}

		public function getBehaviors() {
			return $this->behaviors;
		}

		public function control(callable $control) {
			return $this->candidate($control, 'control');
		}

		public function candidate(callable $candidate, $name = 'candidate') {
			if (isset($this->behaviors[$name]))
				throw new BehaviorNotUniqueException($name);

			$this->behaviors[$name] = $candidate;

			return $this;
		}

		public function compare(ObservationInterface $control, ObservationInterface $candidate) {
			return $control->compare($candidate, $this->comparator);
		}

		public function isEnabled() {
			return true;
		}

		public function publish(ResultInterface $result) {
			// noop
		}

		public function run($name = null) {
			$name = $name ?: 'control';

			if (!isset($this->behaviors[$name]))
				throw new BehaviorMissingException($name);

			$control = $this->behaviors[$name];

			if (sizeof($this->behaviors) === 1 || !$this->isEnabled())
				return call_user_func_array($control, $this->context ?: []);

			/** @var ObservationInterface[] $observations */
			$observations = [];

			foreach (ArrayUtil::shuffle($this->behaviors) as $n => $behavior) {
				$observations[] = new Observation($n, $behavior, $this->context);

				if ($n === $name)
					$control = $observations[sizeof($observations) - 1];
			}

			$result = new Result($this, $control, $observations);

			$this->publish($result);

			if ($this->getThrowOnMismatch() && sizeof($result->getMismatches()))
				throw new MismatchException($this->getName());

			if ($control->hasException())
				throw $control->getException();

			return $control->getResult();
		}

		public static function create($name = 'experiment') {
			return new static($name);
		}
	}
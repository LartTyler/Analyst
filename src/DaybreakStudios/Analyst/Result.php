<?php
	namespace DaybreakStudios\Analyst;

	class Result implements ResultInterface {
		/**
		 * @var ObservationInterface
		 */
		private $control;

		/**
		 * @var ObservationInterface[]
		 */
		private $candidates;

		/**
		 * @var ExperimentInterface
		 */
		private $experiment;

		/**
		 * @var ObservationInterface[]
		 */
		private $mismatched;

		/**
		 * @var ObservationInterface[]
		 */
		private $observations;

		public function __construct(
			ExperimentInterface $experiment, ObservationInterface $control, array $observations = null
		) {
			$this->experiment = $experiment;
			$this->observations = $observations;
			$this->control = $control;

			$this->candidates = [];

			foreach ($observations as $ob)
				if ($control !== $ob)
					$this->candidates[] = $ob;

			$this->evaluate();
		}

		public function getCandidates() {
			return $this->candidates;
		}

		public function getControl() {
			return $this->control;
		}

		public function getObservations() {
			return $this->observations;
		}

		public function getExperiment() {
			return $this->experiment;
		}

		public function getMismatches() {
			return $this->mismatched;
		}

		protected function evaluate() {
			foreach ($this->candidates as $candidate)
				if ($this->control->getResult() !== $candidate->getResult())
					$this->mismatched[] = $candidate;
		}
	}
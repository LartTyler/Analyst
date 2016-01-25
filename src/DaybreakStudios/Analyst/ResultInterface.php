<?php
	namespace DaybreakStudios\Analyst;

	interface ResultInterface {
		/**
		 * Gets all observations, excluding the control
		 *
		 * @return ObservationInterface[]
		 */
		public function getCandidates();

		/**
		 * @return ObservationInterface
		 */
		public function getControl();

		/**
		 * @return ObservationInterface[]
		 */
		public function getObservations();

		/**
		 * @return ExperimentInterface
		 */
		public function getExperiment();

		/**
		 * Gets all observations whose result did not match the control
		 *
		 * @return ObservationInterface[]
		 */
		public function getMismatches();
	}
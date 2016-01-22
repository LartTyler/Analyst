<?php
	namespace DaybreakStudios\Analyst;

	interface AnalystInterface {
		public function isEnabled();
		public function control(callable $control);
		public function candidate(callable $candidate, $name = 'candidate');
		public function publish();
		public function run();
	}
<?php
	namespace DaybreakStudios\Analyst\Exception;

	class BehaviorNotUniqueException extends \Exception {
		public function __construct($name) {
			parent::__construct(sprintf('Behavior names must be unique; %s already exists', $name));
		}
	}
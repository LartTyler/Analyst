<?php
	namespace DaybreakStudios\Analyst;

	interface ObservationInterface {
		public function hasException();
		public function getException();
		public function getDuration();
		public function getResult();
		public function getName();
	}
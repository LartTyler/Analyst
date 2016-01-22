<?php
	namespace DaybreakStudios\Analyst;

	class ArrayUtil {
		public static function shuffle($array) {
			$keys = array_keys($array);
			$res = [];

			shuffle($keys);

			foreach ($keys as $key)
				$res[$key] = $array[$key];

			return $res;
		}
	}
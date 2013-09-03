<?
/**
 * Static class for arrays
 *
 * @author Willfred di Vampo <divampo@gmail.com>
 * @date 2012-04-02
 * @package MediaCore
 * @category Helpers
 * @namespace MediaCore\Lib\Helpers
 * @copyright Copyright (c), 2012
 */
namespace MediaCore\Lib\Helpers;

class HArray {

	/**
	 * Get multilevel array from string, ignore nulls
	 *
	 * Example:
	 * expression
	 * <? print_r(HArray::createFromString('1,2,3,4,5|6,7,8,9,0,,,', array('|', ','))) ?>
	 * will return
	 * Array
	 * (
	 *      [0] => Array
	 *          (
	 *              [0] => 1
	 *              [1] => 2
	 *              [2] => 3
	 *              [3] => 4
	 *              [4] => 5
	 *          )
	 * 	    [1] => Array
	 *          (
	 *              [0] => 6
	 *              [1] => 7
	 *              [2] => 8
	 *              [3] => 9
	 *              [4] => 0
	 *          )
	 * )
	 *
	 * @static
	 * @access public
	 * @param string $string input string
	 * @param array/string $separators array/string of separator(s)
	 *
	 * @return array output array
	 */
	public static function createFromString($string, $separators = array(',')) {
		if (!is_string($string)) {
			return array();
		}

		$separators = (array)$separators;
		$separator = array_shift($separators);

		$array = array();
		foreach (array_map('trim', explode($separator, $string)) as $item) {
			if ($item !== '') {
				if (count($separators)) {
					$array[] = self::createFromString($item, $separators);
				} else {
					$array[] = $item;
				}
			}
		}

		return $array;
    }

	/**
	 * Create array with necessary structure
	 *
	 * Very useful to create multilevel array structures from database table data
	 *
	 * @static
	 * @access public
	 * @param string|array $key array/string of serial array keys
	 * @param array $array input data
	 * @param string|null $value array key of output array values (if needed)
	 *
	 * @return array output structure
	 */
	public static function createByKey($key, array $array, $value = null) {
		$result = array();

		if ($key === null) {
			$key = array(null);
		}
		$key = (array)$key;

		foreach ($array as $item) {
			if ($value === null) $tmp = $item;
			else $tmp = $item[$value];

			$counter_path = '';
			foreach ($key as $n => $k) {
				if ($k !== null) {
					$counter_path .= $item[$k];
				} else {
					$counter_path .= $n;
				}
			}

			foreach (array_reverse($key) as $k) {
				if ($k !== null) {
					$tmp = array($item[$k] => $tmp);
				} else {
					if (!isset($counter[$counter_path])) {
						$counter[$counter_path] = 0;
					}
					$tmp = array($counter[$counter_path]++ => $tmp);
				}
			}
			$result = self::mergeReplace($result, $tmp);
		}

		return $result;
	}

	/**
	 * Recursive merge and replace values in arrays
	 *
	 * @static
	 * @access public
	 * @param array $data main array
	 * @param array $data1 [, $data2, ... ] replacment array(s)
	 *
	 * @return merged array
	 */
	public static function mergeReplace() {
		$args = func_get_args();

		$array = $args[0];
		for ($i = 1; $i < count($args); $i++) {
			foreach ($args[$i] as $key => $value) {
				if (is_array($value) && isset($array[$key])) {
					$array[$key] = self::mergeReplace($array[$key], $value);
				} else {
					$array[$key] = $value;
				}
			}
		}

		return $array;
	}

	/**
	 * Recursive call to each array value
	 *
	 * @static
	 * @access public
	 * @param callback $callback callback
	 * @param array $element input data
	 *
	 * @return array output array
	 */
	public static function mapRecursive($callback, array $data) {

		if (is_array($data)) {
			$array = array();
			foreach ($data as $k => $e) {
				$array[$k] = self::mapRecursive($callback, $e);
			}
			return $array;
		}

		return $callback($data);
	}

	/**
	 * Escape array values with addslashes
	 *
	 * @static
	 * @access public
	 * @param array &$variable input array
	 *
	 * @return void
	 */
	public static function addSlashes(array &$variable) {
		foreach ($variable as $key => $value) {
			if (is_array($variable[$key])) {
				self::addSlashes($variable[$key]);
			} else {
				$variable[$key] = addslashes($variable[$key]);
			}
		}
	}

	/**
	 * Unescape array values with stripslashes
	 *
	 * @static
	 * @access public
	 * @param array &$variable input array
	 *
	 * @return void
	 */
	public static function stripSlashes(array &$variable) {
		foreach ($variable as $key => $value) {
			if (is_array($variable[$key])) {
				self::stripSlashes($variable[$key]);
			} else {
				$variable[$key] = stripslashes($variable[$key]);
			}
		}
	}
}

<?
/**
 * Static class for http client
 *
 * @author Willfred di Vampo <divampo@gmail.com>
 * @date 2012-04-02
 * @package MediaCore
 * @category Helpers
 * @namespace MediaCore\Lib\Helpers
 * @copyright Copyright (c), 2012
 */
namespace MediaCore\Lib\Helpers;

class HClient {
	/**
	 * Get real user IP-address
	 *
	 * @static
	 * @access public
	 *
	 * @return string IP-address
	 */
	public static function getRealIP() {
		// check for shared internet/ISP IP
		if (!empty($_SERVER['HTTP_CLIENT_IP']) && self::validateIP($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		}

		// check for IPs passing through proxies
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			// check if multiple ips exist in var
			foreach (explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']) as $ip) {
				$ip = trim($ip);
				if (self::validateIP($ip)) {
					return $ip;
				}
			}
		}
		if (!empty($_SERVER['HTTP_X_FORWARDED']) && self::validateIP($_SERVER['HTTP_X_FORWARDED'])) {
			return $_SERVER['HTTP_X_FORWARDED'];
		}
		if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && self::validateIP($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
			return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
		}
		if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && self::validateIP($_SERVER['HTTP_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_FORWARDED_FOR'];
		}
		if (!empty($_SERVER['HTTP_FORWARDED']) && self::validateIP($_SERVER['HTTP_FORWARDED'])) {
			return $_SERVER['HTTP_FORWARDED'];
		}
		if (!empty($_SERVER['REMOTE_ADDR']) && self::validateIP($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		}

		// return unreliable ip since all else failed
		return null;
	}

	/**
	 * IP validation
	 *
	 * @static
	 * @access public
	 * $param string $ip IP-address to validate
	 *
	 * @return bool validation result
	 */
	public static function validateIP($ip) {
		if (strtolower($ip) === 'unknown') {
			return false;
		}

		// generate ipv4 network address
		$ip = ip2long($ip);

		// if the ip is set and not equivalent to 255.255.255.255
		if ($ip !== false && $ip !== -1) {
			// make sure to get unsigned long representation of ip
			// due to discrepancies between 32 and 64 bit OSes and
			// signed numbers (ints default to signed in PHP)
			$ip = sprintf('%u', $ip);
			// do private network range checking
			if ($ip >= 0 && $ip <= 50331647) return false; // 0.0.0.0	2.255.255.255
			if ($ip >= 167772160 && $ip <= 184549375) return false; // 24-bit Block (/8 prefix, 1 x A) 	10.0.0.0 	10.255.255.255 	16,777,216
			if ($ip >= 2130706432 && $ip <= 2147483647) return false; // 127.0.0.0	127.255.255.255
			if ($ip >= 2851995648 && $ip <= 2852061183) return false; // 169.254.0.0	169.254.255.255
			if ($ip >= 2886729728 && $ip <= 2887778303) return false; // 20-bit Block (/12 prefix, 16 x B) 	172.16.0.0 	172.31.255.255 	1,048,576
			if ($ip >= 3221225984 && $ip <= 3221226239) return false; // 192.0.2.0	192.0.2.255
			if ($ip >= 3232235520 && $ip <= 3232301055) return false; // 16-bit Block (/16 prefix, 256 x C) 	192.168.0.0 	192.168.255.255 	65,536
			//if ($ip >= 3758096384 && $ip <= 4026531840) return false; // 224.0.0.0 	240.0.0.0
			if ($ip >= 4294967040) return false; // 255.255.255.0	255.255.255.255
		}

		return true;
	}

	/**
	 * Check user agent for compliance
	 *
	 * @static
	 * @access public
	 * @param string $os check string (for example)
	 * @param string|null $user_agent HTTP_USER_AGENT string or null if method must check current
	 *
	 * @return bool check result
	 */
	public static function checkUserAgent($os, $user_agent = null) {
		if ($user_agent === null && isset($_SERVER['HTTP_USER_AGENT'])) {
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
		}
		if (!$user_agent) {
			return false;
		}

		return preg_match('/'.preg_quote($os, '/').'/i', $user_agent);
	}

	/**
	 * Get user browser name
	 *
	 * This method use browsercap.ini data.
	 * browsercap.ini must be in the same directory with HClient
	 * Visit http://tempdownloads.browserscap.com/ for more information
	 *
	 * @static
	 * @access public
	 * @param string|null $user_agent HTTP_USER_AGENT string or null if method must check current
	 * @param string|null $attr specific attribute name or null to get all information
	 *
	 * @return bool check result
	 */
	public static function getBrowser($user_agent = null, $attr = null) {
		if ($user_agent === null && isset($_SERVER['HTTP_USER_AGENT'])) {
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
		}
		if (!$user_agent) {
			return false;
		}

		$browscap = self::getBrowscapData();

		$browser = array();
		foreach ($browscap['patterns'] as $key => $pattern) {
			if (preg_match($pattern, $user_agent)) {
				$browser = array(
					$user_agent, // Original useragent
					trim(strtolower($pattern), '/'),
					$browscap['userAgents'][$key]
				);

				$browser = $value = $browser + $browscap['browsers'][$key];
				while (array_key_exists(3, $value) && $value[3]) {
					$value      =   $browscap['browsers'][$value[3]];
					$browser    +=  $value;
				}

				if (!empty($browser[3])) {
					$browser[3] = $browscap['userAgents'][$browser[3]];
				}

				break;
			}
		}

		$data = array();
		foreach ($browser as $key => $value) {
			$data[$browscap['properties'][$key]] = $value;
		}

		if ($attr !== null && isset($data[$attr])) {
			return $data[$attr];
		}

		return $data;
	}

	/**
	 * @ignore
	 * Get data from browsercap.ini
	 */
	private static function getBrowscapData() {
		$data = array();

		$browscap_path = __DIR__.'/php_browscap.ini';
		$cache_file = \MediaCore\DIR_CACHE.'/'.md5($browscap_path.filemtime($browscap_path)).'.browscap.ini';

		if (file_exists($cache_file)) {
			return unserialize(file_get_contents($cache_file));
		} else {
			$browsers = parse_ini_file($browscap_path, true, INI_SCANNER_RAW);
			array_shift($browsers);

			$_properties = array_keys($browsers['DefaultProperties']);
			array_unshift(
				$_properties,
				'browser_name',
				'browser_name_regex',
				'browser_name_pattern',
				'Parent'
			);

			$_userAgents = array_keys($browsers);
			usort($_userAgents, function($a, $b) {
				$a = strlen($a);
				$b = strlen($b);
				return $a == $b ? 0 : ($a < $b ? 1 : -1);
			});

			$user_agents_keys 	= array_flip($_userAgents);
			$properties_keys	= array_flip($_properties);
			$_browsers = $_patterns = array();
			foreach ($_userAgents as $user_agent) {
				$_patterns[] 	= '/^'.str_replace(array('\*', '\?'), array('.*', '.'), preg_quote($user_agent, '/')).'$/i';

				if (!empty($browsers[$user_agent]['Parent'])) {
					$parent = $browsers[$user_agent]['Parent'];
					$browsers[$user_agent]['Parent'] = $user_agents_keys[$parent];
				}

				foreach ($browsers[$user_agent] as $key => $value) {
					$key = $properties_keys[$key];
					$browser[$key] = $value;
				}

				$_browsers[] = $browser;
				unset($browser);
			}
			unset($user_agents_keys, $properties_keys, $browsers);

			$data = array(
				'properties'	=> $_properties,
				'patterns'		=> $_patterns,
				'userAgents'	=> $_userAgents,
				'browsers'		=> $_browsers,
			);

			file_put_contents($cache_file, serialize($data));
		}

		ret

}

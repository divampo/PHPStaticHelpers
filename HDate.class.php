<?
/**
 * Static class for dates
 *
 * @author Willfred di Vampo <divampo@gmail.com>
 * @date 2012-04-02
 * @package MediaCore
 * @category Helpers
 * @namespace MediaCore\Lib\Helpers
 * @copyright Copyright (c), 2012
 */
namespace MediaCore\Lib\Helpers;

class HDate {
	/**
	 * Get Russian weekday name
	 *
	 * @static
	 * @access public
	 * @param int $weekday weekday number [0 = Sunday, 1 = Monday, ..., 7 = Sunday]
	 * @param int $type return case (1 or 2) or short (3)
	 *
	 * @return string weekday name
	 */
	public static function getRusWeekday($weekday, $type = 1) {
		if ($type == 1) {
			$weekdays[0] = 'Воскресенье';
			$weekdays[1] = 'Понедельник';
			$weekdays[2] = 'Вторник';
			$weekdays[3] = 'Среда';
			$weekdays[4] = 'Четверг';
			$weekdays[5] = 'Пятница';
			$weekdays[6] = 'Суббота';
			$weekdays[7] = 'Воскресенье';
		}
		if ($type == 2) {
			$weekdays[0] = 'Воскресенье';
			$weekdays[1] = 'Понедельник';
			$weekdays[2] = 'Вторник';
			$weekdays[3] = 'Среду';
			$weekdays[4] = 'Четверг';
			$weekdays[5] = 'Пятницу';
			$weekdays[6] = 'Субботу';
			$weekdays[7] = 'Воскресенье';
		}
		if ($type == 3) {
			$weekdays[0] = 'Вс';
			$weekdays[1] = 'Пн';
			$weekdays[2] = 'Вт';
			$weekdays[3] = 'Ср';
			$weekdays[4] = 'Чт';
			$weekdays[5] = 'Пт';
			$weekdays[6] = 'Сб';
			$weekdays[7] = 'Вс';
		}

		return $weekdays[(int)$weekday];
	}

	/**
	 * Get Russian month name
	 *
	 * @static
	 * @access public
	 * @param int $month month number
	 * @param int $type return case (1 or 2) or short (3)
	 *
	 * @return string month name
	 */
	public static function getRusMonth($month, $type = 1) {
		if (is_array($month)) {
			$months = array();
			foreach ($month as $n) {
				$months[$n] = self::getRusMonth($n, $type);
			}
			return $months;
		}

		if ($type == 1) {
			$months[1]  = 'Январь';
			$months[2]  = 'Февраль';
			$months[3]  = 'Март';
			$months[4]  = 'Апрель';
			$months[5]  = 'Май';
			$months[6]  = 'Июнь';
			$months[7]  = 'Июль';
			$months[8]  = 'Август';
			$months[9]  = 'Сентябрь';
			$months[10] = 'Октябрь';
			$months[11] = 'Ноябрь';
			$months[12] = 'Декабрь';
		}

		if ($type == 2) {
			$months[1]  = 'Января';
			$months[2]  = 'Февраля';
			$months[3]  = 'Марта';
			$months[4]  = 'Апреля';
			$months[5]  = 'Мая';
			$months[6]  = 'Июня';
			$months[7]  = 'Июля';
			$months[8]  = 'Августа';
			$months[9]  = 'Сентября';
			$months[10] = 'Октября';
			$months[11] = 'Ноября';
			$months[12] = 'Декабря';
		}

		if ($type == 3) {
			$months[1]  = 'Янв';
			$months[2]  = 'Фев';
			$months[3]  = 'Мар';
			$months[4]  = 'Апр';
			$months[5]  = 'Май';
			$months[6]  = 'Июн';
			$months[7]  = 'Июл';
			$months[8]  = 'Авг';
			$months[9]  = 'Сен';
			$months[10] = 'Окт';
			$months[11] = 'Ноя';
			$months[12] = 'Дек';
		}

		return $months[(int)$month];
	}

	/**
	 * Convert date to Russian text format (Завтра, HH:mm) or to alternative format if unable
	 *
	 * @static
	 * @access public
	 * @param string $format_from input date format {@link http://php.net/manual/en/function.date.php }
	 * @param string $format_to alternative output format {@link http://php.net/manual/en/function.date.php } extended with Месяца, Месяц, Мес, Деньнедели, Деньнед, месяца, месяц, мес, деньнедели, деньнед
	 * @param string $str input date
	 *
	 * @return string converted date
	 */
	public static function convertToText($format_from, $format_to, $str) {
		if ($format_from !== 'U') $str = self::convert($format_from, 'U', $str);
		$year = date('Y', $str);
		$day  = date('z', $str);

		if ($year == date('Y')) {
			if ($day == (date('z') + 1)) return 'Завтра, '.date('H:i', $str);
			if ($day == (date('z') - 0)) return 'Сегодня, '.date('H:i', $str);
			if ($day == (date('z') - 1)) return 'Вчера, '.date('H:i', $str);
		}

		return self::convert('U', $format_to, $str);
	}

	/**
	 * Convert date to certain format
	 *
	 * @static
	 * @access public
	 * @param string $format_from input date format {@link http://php.net/manual/en/function.date.php }
	 * @param string $format_to alternative output format {@link http://php.net/manual/en/function.date.php } extended with Месяца, Месяц, Мес, Деньнедели, Деньнед, месяца, месяц, мес, деньнедели, деньнед
	 * @param string $str input date
	 *
	 * @return string converted date
	 */
	public static function convert($format_from, $format_to, $str) {
		$date = self::parseDate($format_from, $str);
		return self::createDate($format_to, $date);
	}

	/**
	 * Get time difference between dates (Russian)
	 *
	 * @static
	 * @access public
	 * @param string $date1 date 1
	 * @param string $date2 date 2
	 * @param string $format input dates format (must be the same) {@link http://php.net/manual/en/function.date.php }
	 * @param array $except excepted time values
	 * @param bool $array output in array format
	 *
	 * @return string time difference
	 */
	public static function getDateDiff($date1, $date2, $format = 'U', array $except = array(), $array = false) {
		if ($format !== 'U') $date1 = self::convert($format, 'U', $date1);
		if ($format !== 'U') $date2 = self::convert($format, 'U', $date2);

		$diff_secs = abs($date1 - $date2);
		$base_year = min(date('Y', $date1), date('Y', $date2));

		$diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);

		$to_remain = array();
		if (!in_array('y', $except) && date('Y', $diff) - $base_year > 0) $to_remain['y'] = date('Y', $diff) - $base_year.' '.HString::pluralForm(date('Y', $diff) - $base_year, 'год', 'года', 'лет');
		if (!in_array('m', $except) && date('n', $diff) - 1 > 0) $to_remain['m'] = (date('n', $diff) - 1).' '.HString::pluralForm(date('n', $diff) - 1, 'месяц', 'месяца', 'месяцев');
		if (!in_array('d', $except) && date('j', $diff) - 1 > 0) $to_remain['d'] = (date('j', $diff) - 1).' '.HString::pluralForm(date('j', $diff) - 1, 'день', 'дня', 'дней');
		if (!in_array('h', $except) && date('G', $diff) > 0) $to_remain['h'] = date('G', $diff).' '.HString::pluralForm(date('G', $diff), 'час', 'часа', 'часов');
		if (!in_array('i', $except) && date('i', $diff) > 0) $to_remain['i'] = (int) date('i', $diff).' '.HString::pluralForm((int) date('i', $diff), 'минуту', 'минуты', 'минут');
		if (!in_array('s', $except) && date('s', $diff) > 0) $to_remain['s'] = (int) date('s', $diff).' '.HString::pluralForm((int) date('s', $diff), 'секунду', 'секунды', 'секунд');

		if ($array === true) {
			return $to_remain;
		}

		return implode(' ', $to_remain);
	}

	/**
	 * Parse date to array
	 *
	 * @static
	 * @access public
	 * @param string $format input date format {@link http://php.net/manual/en/function.date.php }
	 * @param string $str input date
	 *
	 * @return string output dates array as array(year, month, day, hour, minute, second)
	 */
	public static function parseDate($format, $str) {
		$patterns['Y'] = array('(\d{4})',   'year');
		$patterns['y'] = array('(\d{2})');
		$patterns['m'] = array('(\d{2})',   'month');
		$patterns['n'] = array('(\d{1,2})', 'month');
		$patterns['d'] = array('(\d{2})',   'day');
		$patterns['j'] = array('(\d{1,2})', 'day');
		$patterns['H'] = array('(\d{2})',   'hour');
		$patterns['h'] = array('(\d{1,2})', 'hour');
		$patterns['i'] = array('(\d{1,2})', 'minute');
		$patterns['M'] = array('(\d{2})',   'minute');
		$patterns['s'] = array('(\d{1,2})', 'second');
		$patterns['U'] = array('(\d+)');

		$parameters = array();
		$out_types  = array();
		for ($i = 0; $i < strlen($format); $i++) {
			if (isset($patterns[$format[$i]])) {
				$out_types[]  = $format[$i];
				$parameters[] = $patterns[$format[$i]][0];
			} else {
				$parameters[] = $format[$i];
			}
		}

		if (!preg_match('/^'.implode($parameters).'$/', $str, $out)) throw new \Exception('Шаблон даты не соответствует значению!');

		$date['year']   = 1970;
		$date['month']  = 1;
		$date['day']    = 1;
		$date['hour']   = 0;
		$date['minute'] = 0;
		$date['second'] = 0;

		for ($i = 0; $i < count($out_types); $i++) {
			$pattern = $patterns[$out_types[$i]];
			if (isset($pattern[1])) {
				$date[$pattern[1]] = (int)$out[$i + 1];
			} else {
				if ($out_types[$i] === 'U') {
					$standard_date  = getdate($out[$i + 1]);
					$date['year']   = $standard_date['year'];
					$date['month']  = $standard_date['mon'];
					$date['day']    = $standard_date['mday'];
					$date['hour']   = $standard_date['hours'];
					$date['minute'] = $standard_date['minutes'];
					$date['second'] = $standard_date['seconds'];
				}
				if ($out_types[$i] === 'y') {
					$date['year'] = $out[$i + 1] + ($out[$i + 1] < 30) ? 2000 : 1900;
				}
			}
		}
		return $date;
	}

	/**
	 * Create array of days for input month
	 *
	 * @static
	 * @access public
	 * @param string|null $year year (current as default)
	 * @param string|null $month month (current as default)
	 *
	 * @return array output array
	 */
	public static function getCalendar($year = null, $month = null) {
        if (empty($year)) {
			$year  = date('Y');
		}
        if (empty($month)) {
			$month = date('m');
		}

        $rows = explode(',', date('t,w', mktime(0, 0, 0, $month, 1, $year)));

        if (!$rows[1]--) {
			$rows[1] = 6;
		}

        $calendar = array();

        // first string
        for ($i = 0; $i < $rows[1]; $i++) {
			$calendar[0][$i] = '';
		}

        $week = 0;
        for ($day = 1; $day <= $rows[0]; $day++) {
			$calendar[$week][$rows[1]] = $day;
			$rows[1]++;
			if ($rows[1] > 6) {
				$rows[1] = 0;
				$week++;
			}
        }

        // last string
        if ($rows[1] != 0) {
			while ($rows[1] <= 6) $calendar[$week][$rows[1]++] = '';
        }

		return $calendar;
	}

	/**
	 * @ignore
	 * Get internal date format from input
	 */
	private static function createDate($format, $date) {
		$patterns['Y'] = str_pad($date['year'], 4, '0', STR_PAD_LEFT);
		$patterns['y'] = str_pad($date['year'] - (int)($date['year'] / 100) * 100, 2, '0', STR_PAD_LEFT);
		$patterns['m'] = str_pad($date['month'], 2, '0', STR_PAD_LEFT);
		$patterns['n'] = $date['month'];
		$patterns['d'] = str_pad($date['day'], 2, '0', STR_PAD_LEFT);
		$patterns['j'] = $date['day'];
		$patterns['H'] = str_pad($date['hour'], 2, '0', STR_PAD_LEFT);
		$patterns['h'] = $date['hour'];
		$patterns['G'] = str_pad($date['hour'], 2, '0', STR_PAD_LEFT);
		$patterns['g'] = $date['hour'];
		$patterns['i'] = str_pad($date['minute'], 2, '0', STR_PAD_LEFT);
		$patterns['M'] = str_pad($date['minute'], 2, '0', STR_PAD_LEFT);
		$patterns['s'] = str_pad($date['second'], 2, '0', STR_PAD_LEFT);
		$patterns['U'] = 0;
		if (($date['year'] >= 1970 && $date['year'] <= 2068) || ($date['year'] >= 0 && $date['year'] < 100)) {
			$patterns['U'] = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
		}

		$parameters = array();
		for ($i = 0; $i < strlen($format); $i++) {
			if (isset($patterns[$format{$i}])) {
				$parameters[] = $patterns[$format{$i}];
			} else {
				$parameters[] = $format{$i};
			}
		}

		$text = implode($parameters);

		$functions['Месяца'] = self::getRusMonth($date['month'], 2);
		$functions['Месяц']  = self::getRusMonth($date['month']);
		$functions['Мес']  = self::getRusMonth($date['month'], 3);
		$functions['месяца'] = mb_strtolower(self::getRusMonth($date['month'], 2), 'utf-8');
		$functions['месяц']  = mb_strtolower(self::getRusMonth($date['month']), 'utf-8');
		$functions['мес']  = mb_strtolower(self::getRusMonth($date['month'], 3), 'utf-8');

		$functions['Деньнедели']  = self::getRusWeekday(date('w', $patterns['U']));
		$functions['Деньнед']  = self::getRusWeekday(date('w', $patterns['U']), true);
		$functions['деньнедели']  = mb_strtolower(self::getRusWeekday(date('w', $patterns['U'])), 'utf-8');
		$functions['деньнед']  = mb_strtolower(self::getRusWeekday(date('w', $patterns['U']), true), 'utf-8');

		return str_replace(array_keys($functions), $functions, $text);
	}
}
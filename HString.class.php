<?
/**
 * Static class for strings
 *
 * @author Willfred di Vampo <divampo@gmail.com>
 * @date 2012-04-02
 * @package MediaCore
 * @category Helpers
 * @namespace MediaCore\Lib\Helpers
 * @copyright Copyright (c), 2012
 */
namespace MediaCore\Lib\Helpers;

class HString {
	/**
	 * Escape stings for output
	 *
	 * @static
	 * @access public
	 * @param string $str input string
	 * @param string $type escape type (html, js, text)
	 *
	 * @return string escaped string
	 */
	public static function escape($str, $type = 'html') {
		$type = mb_strtolower($type, 'utf-8');

		if ($type == 'html') {
			return htmlspecialchars($str, ENT_QUOTES, \MediaCore\USE_CHARSET, false);
		}
		if ($type == 'js')   {
			return addslashes(htmlspecialchars($str, ENT_QUOTES, \MediaCore\USE_CHARSET, false));
		}
		if ($type == 'text') {
			return nl2br(htmlspecialchars($str, ENT_QUOTES, \MediaCore\USE_CHARSET, false));
		}

		return $str;
	}

	/**
	 * Calculate filesize to the smallest visualisation
	 *
	 * @static
	 * @access public
	 * @param int $bytes filesize in bytes
	 * @param int $n precision
	 * @param array $types size names abbreviations
	 *
	 * @return string calculated string
	 */
	public static function bytes2Text($bytes, $n = 2, array $types = array('b', 'Kb', 'Mb', 'Gb', 'Tb')) {
		$current = 0;
		while ($bytes > 1024) {
			$current++;
			$bytes /= 1024;
		}

		return round($bytes, $n).' '.$types[$current];
	}

	/**
	 * Get result of evaluated string (The eval() language construct is very dangerous!!!)
	 *
	 * @static
	 * @access public
	 * @param string $string input string (code)
	 *
	 * @return mixed evaluated result
	 */
	public static function getEvalResult($string) {
		eval('\$value = $string;');
		return $value;
	}

	/**
	 * Get plural name of sth from input number
	 *
	 * @static
	 * @access public
	 * @param int $n input number
	 * @param string $form1 output if $n = 1
	 * @param string $form2 output if $n = 2
	 * @param string $form5 output if $n = 5
	 *
	 * @return string correct plural form
	 */
	public static function pluralForm($n, $form1, $form2, $form5) {
		$n = abs($n) % 100;
		$n1 = $n % 10;

		if ($n > 10 && $n < 20) {
			return $form5;
		}
		if ($n1 > 1 && $n1 < 5) {
			return $form2;
		}
		if ($n1 == 1) {
			return $form1;
		}
		return $form5;
	}

	/**
	 * Cut the string to sertain length
	 *
	 * @static
	 * @access public
	 * @param str $string input string
	 * @param int $length output length
	 * @param string $final output cutted string finalisation (if cutted)
	 * @param string $type cut type (
	 *      length	    - cut with specific length
	 *      word	    - cut with specific length to the end of the word
	 *      sentence    - cut with specific length by the end of the sentence
	 * )
	 *
	 * @return string cutted string
	 */
	public static function crop($string, $length, $final = '...', $type = 'word') {
		$result = '';

		if ($length <= 0) {
			return $result;
		}

		if (mb_strlen($string, 'UTF-8') <= $length) {
			return $string;
		}

		if ($type == 'length') {
			$result = mb_substr($string, 0, $length, 'UTF-8');
		}

		if ($type == 'word') {
			preg_match('/.{'.(int)$length.'}[^ ]*/siu', $string, $matches);
			$result = $matches[0];
		}

		if ($type == 'sentence') {
			preg_match('/.{'.(int)$length.'}[^.;!?]*[.;!?]/siu', $string, $matches);
			$result = $matches[0];
		}

		return $result.$final;
	}

	/**
	 * Shorten the string
	 *
	 * @static
	 * @access public
	 * @param str $string input string
	 * @param int $length output length (without middle part)
	 * @param string $middle placeholder for middle (cutted) part
	 *
	 * @return string sort string
	 */
	public static function shorten($string, $length, $middle = '...') {
		$result = '';

		if ($length <= 1) {
			return $result;
		}

		$strlen = mb_strlen($string, 'UTF-8');
		$midlen = mb_strlen($middle, 'UTF-8');
		$start = ceil($length / 2);

		if ($strlen <= $length || $strlen - $midlen <= $length) {
			return $string;
		}

		$result = mb_substr($string, 0, $start, 'UTF-8').$middle;
		$result .= mb_substr($string, -($length - $start), $length - $start, 'UTF-8');

		return $result;
	}

	/**
	 * Transliteration from Russian to English
	 *
	 * @static
	 * @access public
	 * @param str $string input string
	 * @param array $extra extra transliteration symbols
	 *
	 * @return string transliterated string
	 */
	public static function translit($string, array $extra = array()) {
		$converter = array(
			'а' => 'a',   'б' => 'b',   'в' => 'v',
			'г' => 'g',   'д' => 'd',   'е' => 'e',
			'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
			'и' => 'i',   'й' => 'i',   'к' => 'k',
			'л' => 'l',   'м' => 'm',   'н' => 'n',
			'о' => 'o',   'п' => 'p',   'р' => 'r',
			'с' => 's',   'т' => 't',   'у' => 'u',
			'ф' => 'f',   'х' => 'h',   'ц' => 'c',
			'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
			'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
			'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

			'А' => 'A',   'Б' => 'B',   'В' => 'V',
			'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
			'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
			'И' => 'I',   'Й' => 'I',   'К' => 'K',
			'Л' => 'L',   'М' => 'M',   'Н' => 'N',
			'О' => 'O',   'П' => 'P',   'Р' => 'R',
			'С' => 'S',   'Т' => 'T',   'У' => 'U',
			'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
			'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
			'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
			'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
		);
		$converter = array_merge($converter, $extra);

		return strtr($string, $converter);
	}

	/**
	 * Convert between Russian & English letters
	 *
	 * @static
	 * @access public
	 * @param str $string input string
	 * @param string $to convertation direction (rus/eng)
	 * @param array $extra extra symbols
	 *
	 * @return string converted string
	 */
	public static function transletter($string, $to = 'rus', $extra = array()) {
		$converter = array(
			'q' => 'й',
			'w' => 'ц',
			'e' => 'у',
			'r' => 'к',
			't' => 'е',
			'y' => 'н',
			'u' => 'г',
			'i' => 'ш',
			'o' => 'щ',
			'p' => 'з',
			'[' => 'х',
			']' => 'ъ',
			'a' => 'ф',
			's' => 'ы',
			'd' => 'в',
			'f' => 'а',
			'g' => 'п',
			'h' => 'р',
			'j' => 'о',
			'k' => 'л',
			'l' => 'д',
			';' => 'ж',
			'\'' => 'э',
			'z' => 'я',
			'x' => 'ч',
			'c' => 'с',
			'v' => 'м',
			'b' => 'и',
			'n' => 'т',
			'm' => 'ь',
			',' => 'б',
			'.' => 'ю',
		);
		if ($to == 'eng') {
			$converter = array_flip($converter);
		}
		$converter = array_merge($converter, $extra);

		return strtr($string, $converter);
	}

	/**
	 * Convert rating from one scale to another
	 *
	 * @static
	 * @access public
	 * @param int $n input rank
	 * @param int $min_val minimum value of new scale
	 * @param int $max_val maximum value of new scale
	 * @param int $min_base minimum value of base scale
	 * @param int $max_base maximum value of base scale
	 *
	 * @return int converted value
	 */
	public static function ranking($n, $min_val, $max_val, $min_base = 0, $max_base = 10) {
		if ($max_val - $min_val > 0) {
			return (int)floor( ($n - $min_val) / ($max_val - $min_val) * ($max_base - $min_base) );
		}
		return 0;
	}
}

<?
/**
 * Static class for working with filesystem
 *
 * @author Willfred di Vampo <divampo@gmail.com>
 * @date 2012-04-02
 * @package MediaCore
 * @category Helpers
 * @namespace MediaCore\Lib\Helpers
 * @copyright Copyright (c), 2012
 */
namespace MediaCore\Lib\Helpers;

class HFilesystem {
	/**
	 * Recursive directory delete
	 * WARNING be careful using this method!
	 *
	 * @static
	 * @access public
	 * @param string $filepath delete path
	 *
	 * @return bool operation state
	 */
	public static function removeRecursive($filepath) {
		if (is_dir($filepath) && !is_link($filepath)) {
			if ($dh = opendir($filepath)) {
				while (($sf = readdir($dh)) !== false) {
					if ($sf == '.' || $sf == '..') {
						continue;
					}
					if (!self::removeRecursive($filepath.'/'.$sf)) {
						return false;
					}
				}
				closedir($dh);
			}
			return rmdir($filepath);
		}
		return unlink($filepath);
	}

	/**
	 * Convert image path/url to certain format
	 * Work only with formats like name_WIDTHxHEIGHT.ext
	 *
	 * @static
	 * @access public
	 * @param string $image image path or url
	 * @param string $format output image format
	 *
	 * @return string converted path/url
	 */
	public static function getImagePreview($image, $format) {
		if (empty($image)) {
			return false;
		}

		$pathinfo = pathinfo($image);
		$pathinfo['filename'] = preg_replace('/_[0-9]+x[0-9]+$/i', '', $pathinfo['filename']);

		if ($pathinfo['dirname'] == '.') {
	        return $pathinfo['filename'].'_'.$format.'.'.$pathinfo['extension'];
		}

        return $pathinfo['dirname'].'/'.$pathinfo['filename'].'_'.$format.'.'.$pathinfo['extension'];
    }

}

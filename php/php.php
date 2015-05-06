<?php
/**
 * @file
 * PHP snippets.
 */

/**
 * Check is value serialized.
 *
 * @param string $str
 *   Input string.
 *
 * @return bool
 *   If value is serialized - TRUE, FALSE otherwise.
 */
function is_serialized($str) {
  $data = @unserialize($str);
  return ($str === 'b:0;' || $data !== FALSE);
}

/**
 * Helper function for changing array specific key.
 *
 * @param array $array
 *   Initial array.
 *
 * @param string|int $old_key
 *   Old key value.
 *
 * @param string|int $new_key
 *   New key value.
 *
 * @return array
 *   Altered array.
 */
function change_array_key(array $array, $old_key, $new_key) {
  if (!array_key_exists($old_key, $array)) {
    return $array;
  }
  $keys = array_keys($array);
  $keys[array_search($old_key, $keys, TRUE)] = $new_key;
  return array_combine($keys, $array);
}

/**
 * Get diffs between two dates.
 *
 * @param string $date1
 *   First date argument.
 *
 * @param string $date2
 *   Second date argument.
 *
 * @param string $gran
 *   Granularity param.
 *     Available values: 'all', 'd', 'm', 'y', 'h', 'i', 's'.
 *     Defaults to 'all'.
 *
 * @return object|int
 *   Diffs object or granularity-based diff value.
 */
function get_dates_diff($date1, $date2, $gran = 'all') {
  $date1 = new DateTime($date1);
  $date2 = new DateTime($date2);
  $return = $date1->diff($date2);
  return ($gran != 'all') ? $return->$gran : $return;
}

/**
 * Get unique array.
 *
 * @param array $array
 *   The array to be filtered.
 *
 * @param int $total
 *   The maximum number of items to return.
 *
 * @param bool $unique
 *   Whether or not to remove duplicates before getting a random list.
 *   Defaults to TRUE.
 *
 * @return array
 *   Unique array.
 */
function get_unique_array($array = array(), $total, $unique = TRUE) {
  $new_array = array();
  if ((bool) $unique) {
    $array = array_unique($array);
  }
  shuffle($array);
  $length = count($array);
  for ($i = 0; $i < $total; $i++) {
    if ($i < $length) {
      $new_array[] = $array[$i];
    }
  }
  return $new_array;
}

/**
 * Check is array contain a duplicates.
 *
 * @param array $array
 *   Array to check.
 *
 * @return bool
 *   TRUE - if array has duplicated rows, FALSE otherwise.
 */
function array_has_duplicates($array) {
  if (!is_array($array)) {
    return NULL;
  }
  return count($array) !== count(array_unique($array));
}

/**
 * Get file size.
 *
 * @param string $url
 *   URL path to the file.
 *
 * @param bool $convert
 *   Make convert or not.
 *   Defaults to TRUE.
 *
 * @return string
 *   File size value.
 */
function get_file_size($url, $convert = TRUE) {
  if (!is_string($url)) {
    return NULL;
  }
  $size = filesize($url);
  if (!$convert) {
    return $size;
  }
  if ($size >= 1073741824) {
    $file_size = round($size / 1024 / 1024 / 1024, 1) . 'GB';
  }
  elseif ($size >= 1048576) {
    $file_size = round($size / 1024 / 1024, 1) . 'MB';
  }
  elseif ($size >= 1024) {
    $file_size = round($size / 1024, 1) . 'KB';
  }
  else {
    $file_size = $size . ' bytes';
  }
  return $file_size;
}

/**
 * Check the password strength.
 *
 * @param string $string
 *   Password to check.
 *
 * @return int|null
 *   Password strength value(from 0 to 100).
 */
function password_strength($string) {
  if (!is_string($string)) {
    return NULL;
  }
  $h = 0; $size = strlen($string);
  foreach (count_chars($string, 1) as $v) {
    $p = $v / $size;
    $h -= $p * log($p) / log(2);
  }
  $strength = round(($h / 4) * 100);
  if ($strength > 100) {
    $strength = 100;
  }
  return $strength;
}

/**
 * Generate a random password.
 *
 * @param int $length
 *   Password length.
 *
 * @param string $chars
 *   Available chars.
 *
 * @param string $additional
 *   Additional chars.
 *
 * @return string
 *   Generated password.
 */
function random_password_generate($length = 8, $chars = NULL, $additional = NULL) {
  if (!is_int($length)) {
    return NULL;
  }
  // If chars isn't set - set default alphanumeric mask.
  if (empty($chars)) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  }
  // Add additional chars to the beginning of mask.
  if (!empty($chars) && !empty($additional)) {
    $additional .= $chars;
    $chars = $additional;
  }
  return substr(str_shuffle($chars), 0, $length);
}

/**
 * Browser detection.
 *
 * @param bool $name
 *   Optional param to return only browser name.
 *
 * @return array|string
 *   Browser detection data array or HTTP_USER_AGENT string.
 */
function detect_browser($name = FALSE) {
  if (file_exists(__DIR__ . '/browser/BrowserDetector.php')) {
    require_once __DIR__ . '/browser/BrowserDetector.php';
    $detector = new BrowserDetector();
    if (is_object($detector)) {
      return ($name === TRUE)
        ? $detector->getBrowser()
        : array(
          'name'     => (string) $detector->getBrowser(),
          'version'  => (string) $detector->getVersion(),
          'platform' => (string) $detector->getPlatform(),
          'mobile'   => (bool) $detector->isMobile(),
          'tablet'   => (bool) $detector->isTablet(),
          'robot'    => (bool) $detector->isRobot(),
          'facebook' => (bool) $detector->isFacebook(),
          'aol'      => ($detector->isAol()) ? $detector->getAolVersion() : FALSE,
        );
    }
  }
  return $_SERVER['HTTP_USER_AGENT'];
}

/**
 * Helper function for checking is a value between the max and min ranges.
 *
 * @param int $val
 *   Value to check.
 *
 * @param int $min
 *   Min range.
 *
 * @param int $max
 *   Max range.
 *
 * @return bool
 *   Checking result.
 */
function number_is_between($val, $min, $max) {
  return ($val >= $min && $val <= $max);
}

/**
 * Email validation.
 *
 * @param string $email
 *   Email string.
 *
 * @return bool
 *   TRUE - email is valid, FALSE otherwise.
 */
function email_is_valid($email) {
  if (empty($email) || !is_string($email)) {
    return FALSE;
  }
  $regex = '^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$^';
  return (bool) preg_match($regex, $email);
}

/**
 * Get the latitude and longitude values by address string.
 *
 * @param string $address
 *   Address string.
 *
 * @return array
 *   Coordinates array.
 */
function get_googlemaps_coordinates($address) {
  if (empty($address) || !is_string($address)) {
    return NULL;
  }
  $result = FALSE; $coordinates = array();
  $url = sprintf('http://maps.google.com/maps?output=js&q=%s', rawurlencode($address));
  if ($result = file_get_contents($url)) {
    if (strpos($result, 'errortips') > 1 || strpos($result, 'Did you mean:') !== FALSE) {
      return FALSE;
    }
    $regex = '!center:\s*{lat:\s*(-?\d+\.\d+),lng:\s*(-?\d+\.\d+)}!U';
    preg_match($regex, $result, $match, 0, 2);
    // Remove first element of array and re-index the array.
    array_splice($match, 0, 1);
    list($coordinates['lat'], $coordinates['long']) = $match;
    $coordinates['address'] = $address;
    ksort($coordinates);
  }
  return $coordinates;
}


<?php
/**
 * @file
 * BrowserDetector class definition file.
 *
 * The class has been forked from https://github.com/cbschuld/Browser.php.
 */

/**
 * Typical Usage.
 *
 * @code
 *   $browser = new BrowserDetector();
 *   if ($browser->getBrowser() == BrowserDetector::BROWSER_FIREFOX
 *   && $browser->getVersion() >= 2) {
 *    echo 'You have FireFox version 2 or greater';
 *   }
 */

/**
 * Class BrowserDetector.
 */
class BrowserDetector {
  // Private variables.
  private $httpAgent        = '';
  private $webBrowserName   = '';
  private $browserVersion   = '';
  private $browserPlatform  = '';
  private $operationSystem  = '';
  private $isAol            = FALSE;
  private $isMobile         = FALSE;
  private $isTablet         = FALSE;
  private $isRobot          = FALSE;
  private $isFacebook       = FALSE;
  private $aolVersion       = '';
  // Constants.
  // Browsers.
  const BROWSER_UNKNOWN             = 'unknown';
  const VERSION_UNKNOWN             = 'unknown';
  const BROWSER_OPERA               = 'Opera';
  const BROWSER_OPERA_MINI          = 'Opera Mini';
  const BROWSER_WEBTV               = 'WebTV';
  const BROWSER_IE                  = 'Internet Explorer';
  const BROWSER_POCKET_IE           = 'Pocket Internet Explorer';
  const BROWSER_KONQUEROR           = 'Konqueror';
  const BROWSER_ICAB                = 'iCab';
  const BROWSER_OMNIWEB             = 'OmniWeb';
  const BROWSER_FIREBIRD            = 'Firebird';
  const BROWSER_FIREFOX             = 'Firefox';
  const BROWSER_ICEWEASEL           = 'Iceweasel';
  const BROWSER_SHIRETOKO           = 'Shiretoko';
  const BROWSER_MOZILLA             = 'Mozilla';
  const BROWSER_AMAYA               = 'Amaya';
  const BROWSER_LYNX                = 'Lynx';
  const BROWSER_SAFARI              = 'Safari';
  const BROWSER_IPHONE              = 'iPhone';
  const BROWSER_IPOD                = 'iPod';
  const BROWSER_IPAD                = 'iPad';
  const BROWSER_CHROME              = 'Chrome';
  const BROWSER_ANDROID             = 'Android';
  const BROWSER_GOOGLEBOT           = 'GoogleBot';
  const BROWSER_SLURP               = 'Yahoo! Slurp';
  const BROWSER_W3CVALIDATOR        = 'W3C Validator';
  const BROWSER_BLACKBERRY          = 'BlackBerry';
  const BROWSER_ICECAT              = 'IceCat';
  const BROWSER_NOKIA_S60           = 'Nokia S60 OSS Browser';
  const BROWSER_NOKIA               = 'Nokia Browser';
  const BROWSER_MSN                 = 'MSN Browser';
  const BROWSER_MSNBOT              = 'MSN Bot';
  const BROWSER_BINGBOT             = 'Bing Bot';
  const BROWSER_NETSCAPE_NAVIGATOR  = 'Netscape Navigator';
  const BROWSER_GALEON              = 'Galeon';
  const BROWSER_NETPOSITIVE         = 'NetPositive';
  const BROWSER_PHOENIX             = 'Phoenix';
  // Platforms.
  const PLATFORM_UNKNOWN            = 'unknown';
  const PLATFORM_WINDOWS            = 'Windows';
  const PLATFORM_WINDOWS_CE         = 'Windows CE';
  const PLATFORM_APPLE              = 'Apple';
  const PLATFORM_LINUX              = 'Linux';
  const PLATFORM_OS2                = 'OS/2';
  const PLATFORM_BEOS               = 'BeOS';
  const PLATFORM_IPHONE             = 'iPhone';
  const PLATFORM_IPOD               = 'iPod';
  const PLATFORM_IPAD               = 'iPad';
  const PLATFORM_BLACKBERRY         = 'BlackBerry';
  const PLATFORM_NOKIA              = 'Nokia';
  const PLATFORM_FREEBSD            = 'FreeBSD';
  const PLATFORM_OPENBSD            = 'OpenBSD';
  const PLATFORM_NETBSD             = 'NetBSD';
  const PLATFORM_SUNOS              = 'SunOS';
  const PLATFORM_OPENSOLARIS        = 'OpenSolaris';
  const PLATFORM_ANDROID            = 'Android';
  const OPERATING_SYSTEM_UNKNOWN    = 'unknown';

  /**
   * Browser constructor.
   *
   * @param string $user_agent
   *   User agent($_SERVER['HTTP_USER_AGENT']).
   */
  public function __construct($user_agent = "") {
    $this->reset();
    if ($user_agent != "") {
      $this->setUserAgent($user_agent);
    }
    else {
      $this->determine();
    }
  }

  /**
   * Reset all properties.
   */
  public function reset() {
    $this->httpAgent        = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
    $this->webBrowserName   = self::BROWSER_UNKNOWN;
    $this->browserVersion   = self::VERSION_UNKNOWN;
    $this->browserPlatform  = self::PLATFORM_UNKNOWN;
    $this->operationSystem  = self::OPERATING_SYSTEM_UNKNOWN;
    $this->isAol            = FALSE;
    $this->isMobile         = FALSE;
    $this->isTablet         = FALSE;
    $this->isRobot          = FALSE;
    $this->isFacebook       = FALSE;
    $this->aolVersion       = self::VERSION_UNKNOWN;
  }

  /**
   * Check to see if the specific browser is valid.
   *
   * @param string $browser_name
   *   Browser name.
   *
   * @return bool
   *   True if the browser is the specified browser.
   */
  public function isBrowser($browser_name) {
    return (0 == strcasecmp($this->webBrowserName, trim($browser_name)));
  }
  /**
   * The name of the browser.
   *
   * All return types are from the class constants.
   *
   * @return string
   *   Name of the browser.
   */
  public function getBrowser() {
    return $this->webBrowserName;
  }
  /**
   * Set the name of the browser.
   *
   * @param string $browser
   *   The name of the Browser.
   */
  public function setBrowser($browser) {
    $this->webBrowserName = $browser;
  }
  /**
   * The name of the platform.
   *
   * All return types are from the class constants.
   *
   * @return string
   *   Name of the browser.
   */
  public function getPlatform() {
    return $this->browserPlatform;
  }
  /**
   * Set the name of the platform.
   *
   * @param string $platform
   *   The name of the Platform.
   */
  public function setPlatform($platform) {
    $this->browserPlatform = $platform;
  }
  /**
   * The version of the browser.
   *
   * @return string
   *   Version of the browser
   *   (will only contain alpha-numeric characters and a period).
   */
  public function getVersion() {
    return $this->browserVersion;
  }
  /**
   * Set the version of the browser.
   *
   * @param string $version
   *   The version of the Browser.
   */
  public function setVersion($version) {
    $this->browserVersion = preg_replace('/[^0-9,.,a-z,A-Z-]/', '', $version);
  }
  /**
   * The version of AOL.
   *
   * @return string
   *   Version of AOL
   *   (will only contain alpha-numeric characters and a period).
   */
  public function getAolVersion() {
    return $this->aolVersion;
  }
  /**
   * Set the version of AOL.
   *
   * @param string $version
   *   The version of AOL.
   */
  public function setAolVersion($version) {
    $this->aolVersion = preg_replace('/[^0-9,.,a-z,A-Z]/', '', $version);
  }
  /**
   * Is the browser from AOL?.
   *
   * @return bool
   *   True if the browser is from AOL otherwise false.
   */
  public function isAol() {
    return $this->isAol;
  }
  /**
   * Is the browser from a mobile device?.
   *
   * @return bool
   *   True if the browser is from a mobile device otherwise false.
   */
  public function isMobile() {
    return $this->isMobile;
  }
  /**
   * Is the browser from a tablet device?.
   *
   * @return bool
   *   True if the browser is from a tablet device otherwise false.
   */
  public function isTablet() {
    return $this->isTablet;
  }
  /**
   * Is the browser from a robot (ex Slurp,GoogleBot)?.
   *
   * @return bool
   *   True if the browser is from a robot otherwise false.
   */
  public function isRobot() {
    return $this->isRobot;
  }
  /**
   * Is the browser from facebook?.
   *
   * @return bool
   *   True if the browser is from facebook otherwise false.
   */
  public function isFacebook() {
    return $this->isFacebook;
  }
  /**
   * Set the browser to be from AOL.
   *
   * @param bool $is_aol
   *   Is AOL value.
   */
  public function setAol($is_aol) {
    $this->isAol = $is_aol;
  }
  /**
   * Set the Browser to be mobile.
   *
   * @param bool $value
   *   Value param for isMobile.
   */
  protected function setMobile($value = TRUE) {
    $this->isMobile = $value;
  }
  /**
   * Set the Browser to be tablet.
   *
   * @param bool $value
   *   Value param for isTablet.
   */
  protected function setTablet($value = TRUE) {
    $this->isTablet = $value;
  }
  /**
   * Set the Browser to be a robot.
   *
   * @param bool $value
   *   Value param for isRobot.
   */
  protected function setRobot($value = TRUE) {
    $this->isRobot = $value;
  }
  /**
   * Set the Browser to be a Facebook request.
   *
   * @param bool $value
   *   Value param for isFacebook.
   */
  protected function setFacebook($value = TRUE) {
    $this->isFacebook = $value;
  }
  /**
   * Get the user agent value in use to determine the browser.
   *
   * @return string
   *   The user agent from the HTTP header.
   */
  public function getUserAgent() {
    return $this->httpAgent;
  }
  /**
   * Set the user agent value.
   *
   * The construction will use the HTTP header value - this will overwrite it.
   *
   * @param string $agent_string
   *   The value for the User Agent.
   */
  public function setUserAgent($agent_string) {
    $this->reset();
    $this->httpAgent = $agent_string;
    $this->determine();
  }
  /**
   * Used to determine if the browser is actually "chromeframe".
   *
   * @since 1.7
   *
   * @return bool
   *   True if the browser is using chromeframe.
   */
  public function isChromeFrame() {
    return (strpos($this->httpAgent, "chromeframe") !== FALSE);
  }
  /**
   * Returns a formatted string with a summary of the details of the browser.
   *
   * @return string
   *   Formatted string with a summary of the browser.
   */
  public function __toString() {
    $str  = "<strong>Browser Name:</strong> {$this->getBrowser()}<br/>\n";
    $str .= "<strong>Browser Version:</strong> {$this->getVersion()}<br/>\n";
    $str .= "<strong>Browser User Agent String:</strong> {$this->getUserAgent()}<br/>\n";
    $str .= "<strong>Platform:</strong> {$this->getPlatform()}<br/>";
    return $str;
  }
  /**
   * Protected routine to calculate and determine what the browser is in use.
   *
   * Including platform.
   */
  protected function determine() {
    $this->checkPlatform();
    $this->checkBrowsers();
    $this->checkForAol();
  }
  /**
   * Protected routine to determine the browser type.
   *
   * @return bool
   *   True if the browser was detected otherwise false.
   */
  protected function checkBrowsers() {
    return (
      $this->checkBrowserWebTv() ||
      $this->checkBrowserInternetExplorer() ||
      $this->checkBrowserOpera() ||
      $this->checkBrowserGaleon() ||
      $this->checkBrowserNetscapeNavigator9Plus() ||
      $this->checkBrowserFirefox() ||
      $this->checkBrowserChrome() ||
      $this->checkBrowserOmniWeb() ||
      $this->checkBrowserAndroid() ||
      $this->checkBrowseriPad() ||
      $this->checkBrowseriPod() ||
      $this->checkBrowseriPhone() ||
      $this->checkBrowserBlackBerry() ||
      $this->checkBrowserNokia() ||
      $this->checkBrowserGoogleBot() ||
      $this->checkBrowserMsnBot() ||
      $this->checkBrowserBingBot() ||
      $this->checkBrowserSlurp() ||
      $this->checkFacebookExternalHit() ||
      $this->checkBrowserSafari() ||
      $this->checkBrowserNetPositive() ||
      $this->checkBrowserFirebird() ||
      $this->checkBrowserKonqueror() ||
      $this->checkBrowserIcab() ||
      $this->checkBrowserPhoenix() ||
      $this->checkBrowserAmaya() ||
      $this->checkBrowserLynx() ||
      $this->checkBrowserShiretoko() ||
      $this->checkBrowserIceCat() ||
      $this->checkBrowserIceweasel() ||
      $this->checkBrowserWorldWideWebConsortiumValidator() ||
      $this->checkBrowserMozilla()
    );
  }
  /**
   * Determine if the user is using a BlackBerry (last updated 1.7).
   *
   * @return bool
   *   True if the browser is the BlackBerry browser otherwise false.
   */
  protected function checkBrowserBlackBerry() {
    if ($this->isHttpAgentContains('blackberry')) {
      $result = $this->getHttpAgentExplosion("BlackBerry");
      if (isset($result[1])) {
        $version = explode(' ', $result[1]);
        $this->setVersion($version[0]);
        $this->webBrowserName = self::BROWSER_BLACKBERRY;
        $this->setMobile(TRUE);
        return TRUE;
      }
    }
    return FALSE;
  }
  /**
   * Determine if the user is using an AOL User Agent (last updated 1.7).
   *
   * @return bool
   *   True if the browser is from AOL otherwise false.
   */
  protected function checkForAol() {
    $this->setAol(FALSE);
    $this->setAolVersion(self::VERSION_UNKNOWN);
    if ($this->isHttpAgentContains('aol')) {
      $version = $this->isHttpAgentContains('AOL');
      if (isset($version[1])) {
        $this->setAol(TRUE);
        $this->setAolVersion(preg_replace('/[^0-9\.a-z]/i', '', $version[1]));
        return TRUE;
      }
    }
    return FALSE;
  }
  /**
   * Check if the http agent string contains the input name.
   *
   * @param string $name
   *   String name to check.
   *
   * @return bool
   *   Checking result.
   */
  protected function isHttpAgentContains($name) {
    return (stripos($this->httpAgent, $name) !== FALSE);
  }
  /**
   * Explode the string.
   *
   * @param string $name
   *   String name.
   *
   * @param string $delimiter
   *   Delimiter string.
   *
   * @return array
   *   Exploded array.
   */
  protected function getHttpAgentExplosion($name, $delimiter = '/') {
    return explode($delimiter, stristr($this->httpAgent, $name));
  }
  /**
   * Determine if a browser is the Bot.
   *
   * @param string $bot_name
   *   Bot name.
   *
   * @param string $browser_name
   *   Browser name.
   *
   * @return bool
   *   Checking result.
   */
  protected function checkBrowserBot($bot_name, $browser_name) {
    if ($this->isHttpAgentContains($bot_name)) {
      $result = $this->getHttpAgentExplosion($bot_name);
      if (isset($result[1])) {
        $version = explode(' ', $result[1]);
        $this->setVersion(str_replace(';', '', $version[0]));
        $this->webBrowserName = $browser_name;
        $this->setRobot(TRUE);
        return TRUE;
      }
    }
    return FALSE;
  }
  /**
   * Determine if the browser is the GoogleBot or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is the GoogletBot otherwise false.
   */
  protected function checkBrowserGoogleBot() {
    return $this->checkBrowserBot('googlebot', self::BROWSER_GOOGLEBOT);
  }
  /**
   * Determine if the browser is the MSNBot or not (last updated 1.9).
   *
   * @return bool
   *   True if the browser is the MSNBot otherwise false.
   */
  protected function checkBrowserMsnBot() {
    return $this->checkBrowserBot('msnbot', self::BROWSER_MSNBOT);
  }
  /**
   * Determine if the browser is the BingBot or not (last updated 1.9).
   *
   * @return bool
   *   True if the browser is the BingBot otherwise false.
   */
  protected function checkBrowserBingBot() {
    return $this->checkBrowserBot('bingbot', self::BROWSER_BINGBOT);
  }
  /**
   * Determine if the browser is the W3C Validator or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is the W3C Validator otherwise false.
   */
  protected function checkBrowserWorldWideWebConsortiumValidator() {
    if ($this->isHttpAgentContains('W3C-checklink')) {
      $result = $this->getHttpAgentExplosion('W3C-checklink');
      if (isset($result[1])) {
        $version = explode(' ', $result[1]);
        $this->setVersion($version[0]);
        $this->webBrowserName = self::BROWSER_W3CVALIDATOR;
        return TRUE;
      }
    }
    elseif ($this->isHttpAgentContains('W3C_Validator')) {
      // Some of the Validator versions do not delineate w/
      // a slash - add it back in.
      $ua = str_replace("W3C_Validator ", "W3C_Validator/", $this->httpAgent);
      $result = explode('/', stristr($ua, 'W3C_Validator'));
      if (isset($result[1])) {
        $version = explode(' ', $result[1]);
        $this->setVersion($version[0]);
        $this->webBrowserName = self::BROWSER_W3CVALIDATOR;
        return TRUE;
      }
    }
    elseif ($this->isHttpAgentContains('W3C-mobileOK')) {
      $this->webBrowserName = self::BROWSER_W3CVALIDATOR;
      $this->setMobile(TRUE);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Determine if the browser is the Yahoo! Slurp Robot or not.
   *
   * @return bool
   *   True if the browser is the Yahoo! Slurp Robot otherwise false.
   */
  protected function checkBrowserSlurp() {
    if ($this->isHttpAgentContains('slurp')) {
      $result = $this->getHttpAgentExplosion('Slurp');
      if (isset($result[1])) {
        $version = explode(' ', $result[1]);
        $this->setVersion($version[0]);
        $this->webBrowserName = self::BROWSER_SLURP;
        $this->setRobot(TRUE);
        $this->setMobile(FALSE);
        return TRUE;
      }
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Internet Explorer or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is Internet Explorer otherwise false.
   */
  protected function checkBrowserInternetExplorer() {
    if ($this->isHttpAgentContains('Trident/7.0; rv:11.0')) {
      $this->setBrowser(self::BROWSER_IE);
      $this->setVersion('11.0');
      return TRUE;
    }
    elseif ($this->isHttpAgentContains('microsoft internet explorer')) {
      $this->setBrowser(self::BROWSER_IE);
      $this->setVersion('1.0');
      $result = stristr($this->httpAgent, '/');
      if (preg_match('/308|425|426|474|0b1/i', $result)) {
        $this->setVersion('1.5');
      }
      return TRUE;
    }
    elseif ($this->isHttpAgentContains('msie') && !$this->isHttpAgentContains('opera')) {
      // See if the browser is the odd MSN Explorer.
      if (stripos($this->httpAgent, 'msnb') !== FALSE) {
        $result = explode(' ', stristr(str_replace(';', '; ', $this->httpAgent), 'MSN'));
        if (isset($result[1])) {
          $this->setBrowser(self::BROWSER_MSN);
          $this->setVersion(str_replace(array('(', ')', ';'), '', $result[1]));
          return TRUE;
        }
      }
      $result = explode(' ', stristr(str_replace(';', '; ', $this->httpAgent), 'msie'));
      if (isset($result[1])) {
        $this->setBrowser(self::BROWSER_IE);
        $this->setVersion(str_replace(array('(', ')', ';'), '', $result[1]));
        if (stripos($this->httpAgent, 'IEMobile') !== FALSE) {
          $this->setBrowser(self::BROWSER_POCKET_IE);
          $this->setMobile(TRUE);
        }
        return TRUE;
      }
    }
    elseif ($this->isHttpAgentContains('trident')) {
      $this->setBrowser(self::BROWSER_IE);
      $result = explode('rv:', $this->httpAgent);
      if (isset($result[1])) {
        $this->setVersion(preg_replace('/[^0-9.]+/', '', $result[1]));
        $this->httpAgent = str_replace(array("Mozilla", "Gecko"), "MSIE", $this->httpAgent);
      }
    }
    elseif ($this->isHttpAgentContains('mspie') || $this->isHttpAgentContains('pocket')) {
      $result = $this->getHttpAgentExplosion('mspie', ' ');
      if (isset($result[1])) {
        $this->setPlatform(self::PLATFORM_WINDOWS_CE);
        $this->setBrowser(self::BROWSER_POCKET_IE);
        $this->setMobile(TRUE);
        if ($this->isHttpAgentContains('mspie')) {
          $this->setVersion($result[1]);
        }
        else {
          $version = explode('/', $this->httpAgent);
          if (isset($version[1])) {
            $this->setVersion($version[1]);
          }
        }
        return TRUE;
      }
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Opera or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is Opera otherwise false.
   */
  protected function checkBrowserOpera() {
    if ($this->isHttpAgentContains('opera mini')) {
      $resultant = stristr($this->httpAgent, 'opera mini');
      if (preg_match('/\//', $resultant)) {
        $result = explode('/', $resultant);
        if (isset($result[1])) {
          $version = explode(' ', $result[1]);
          $this->setVersion($version[0]);
        }
      }
      else {
        $version = explode(' ', stristr($resultant, 'opera mini'));
        if (isset($version[1])) {
          $this->setVersion($version[1]);
        }
      }
      $this->webBrowserName = self::BROWSER_OPERA_MINI;
      $this->setMobile(TRUE);
      return TRUE;
    }
    elseif ($this->isHttpAgentContains('opera')) {
      $resultant = stristr($this->httpAgent, 'opera');
      if (preg_match('/Version\/(1*.*)$/', $resultant, $matches)) {
        $this->setVersion($matches[1]);
      }
      elseif (preg_match('/\//', $resultant)) {
        $result = explode('/', str_replace("(", " ", $resultant));
        if (isset($result[1])) {
          $version = explode(' ', $result[1]);
          $this->setVersion($version[0]);
        }
      }
      else {
        $version = explode(' ', stristr($resultant, 'opera'));
        $this->setVersion(isset($version[1]) ? $version[1] : "");
      }
      if ($this->isHttpAgentContains('Opera Mobi')) {
        $this->setMobile(TRUE);
      }
      $this->webBrowserName = self::BROWSER_OPERA;
      return TRUE;
    }
    elseif ($this->isHttpAgentContains('OPR')) {
      $resultant = stristr($this->httpAgent, 'OPR');
      if (preg_match('/\//', $resultant)) {
        $result = explode('/', str_replace("(", " ", $resultant));
        if (isset($result[1])) {
          $version = explode(' ', $result[1]);
          $this->setVersion($version[0]);
        }
      }
      if ($this->isHttpAgentContains('Mobile')) {
        $this->setMobile(TRUE);
      }
      $this->webBrowserName = self::BROWSER_OPERA;
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Chrome or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is Chrome otherwise false.
   */
  protected function checkBrowserChrome() {
    if ($this->isHttpAgentContains('Chrome')) {
      $result = $this->getHttpAgentExplosion('Chrome');
      if (isset($result[1])) {
        $version = explode(' ', $result[1]);
        $this->setVersion($version[0]);
        $this->setBrowser(self::BROWSER_CHROME);
        // Chrome on Android.
        if ($this->isHttpAgentContains('Android')) {
          if ($this->isHttpAgentContains('Mobile')) {
            $this->setMobile(TRUE);
          }
          else {
            $this->setTablet(TRUE);
          }
        }
        return TRUE;
      }
    }
    return FALSE;
  }
  /**
   * Determine if the browser is WebTv or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is WebTv otherwise false.
   */
  protected function checkBrowserWebTv() {
    if ($this->isHttpAgentContains('webtv')) {
      $result = $this->getHttpAgentExplosion('webtv');
      if (isset($result[1])) {
        $version = explode(' ', $result[1]);
        $this->setVersion($version[0]);
        $this->setBrowser(self::BROWSER_WEBTV);
        return TRUE;
      }
    }
    return FALSE;
  }
  /**
   * Determine if the browser is NetPositive or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is NetPositive otherwise false.
   */
  protected function checkBrowserNetPositive() {
    if ($this->isHttpAgentContains('NetPositive')) {
      $result = $this->getHttpAgentExplosion('NetPositive');
      if (isset($result[1])) {
        $version = explode(' ', $result[1]);
        $this->setVersion(str_replace(array('(', ')', ';'), '', $version[0]));
        $this->setBrowser(self::BROWSER_NETPOSITIVE);
        return TRUE;
      }
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Galeon or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is Galeon otherwise false.
   */
  protected function checkBrowserGaleon() {
    if ($this->isHttpAgentContains('galeon')) {
      $result = $this->getHttpAgentExplosion('galeon', ' ');
      $version = explode('/', $result[0]);
      if (isset($version[1])) {
        $this->setVersion($version[1]);
        $this->setBrowser(self::BROWSER_GALEON);
        return TRUE;
      }
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Konqueror or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is Konqueror otherwise false.
   */
  protected function checkBrowserKonqueror() {
    if ($this->isHttpAgentContains('Konqueror')) {
      $result = $this->getHttpAgentExplosion('Konqueror', ' ');
      $version = explode('/', $result[0]);
      if (isset($version[1])) {
        $this->setVersion($version[1]);
        $this->setBrowser(self::BROWSER_KONQUEROR);
        return TRUE;
      }
    }
    return FALSE;
  }
  /**
   * Determine if the browser is iCab or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is iCab otherwise false.
   */
  protected function checkBrowserIcab() {
    if ($this->isHttpAgentContains('icab')) {
      $version = explode(' ', stristr(str_replace('/', ' ', $this->httpAgent), 'icab'));
      if (isset($version[1])) {
        $this->setVersion($version[1]);
        $this->setBrowser(self::BROWSER_ICAB);
        return TRUE;
      }
    }
    return FALSE;
  }
  /**
   * Determine if the browser is OmniWeb or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is OmniWeb otherwise false.
   */
  protected function checkBrowserOmniWeb() {
    if ($this->isHttpAgentContains('omniweb')) {
      $result = $this->getHttpAgentExplosion('omniweb');
      $version = explode(' ', isset($result[1]) ? $result[1] : "");
      $this->setVersion($version[0]);
      $this->setBrowser(self::BROWSER_OMNIWEB);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Phoenix or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is Phoenix otherwise false.
   */
  protected function checkBrowserPhoenix() {
    if ($this->isHttpAgentContains('Phoenix')) {
      $version = $this->getHttpAgentExplosion('Phoenix');
      if (isset($version[1])) {
        $this->setVersion($version[1]);
        $this->setBrowser(self::BROWSER_PHOENIX);
        return TRUE;
      }
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Firebird or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is Firebird otherwise false.
   */
  protected function checkBrowserFirebird() {
    if ($this->isHttpAgentContains('Firebird')) {
      $version = $this->getHttpAgentExplosion('Firebird');
      if (isset($version[1])) {
        $this->setVersion($version[1]);
        $this->setBrowser(self::BROWSER_FIREBIRD);
        return TRUE;
      }
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Netscape Navigator 9+ or not.
   *
   * NOTE: http://browser.netscape.com/
   * Official support ended on March 1st, 2008).
   *
   * @return bool
   *   True if the browser is Netscape Navigator 9+ otherwise false.
   */
  protected function checkBrowserNetscapeNavigator9Plus() {
    if ($this->isHttpAgentContains('Firefox') && preg_match('/Navigator\/([^ ]*)/i', $this->httpAgent, $matches)) {
      $this->setVersion($matches[1]);
      $this->setBrowser(self::BROWSER_NETSCAPE_NAVIGATOR);
      return TRUE;
    }
    elseif (!$this->isHttpAgentContains('Firefox') && preg_match('/Netscape6?\/([^ ]*)/i', $this->httpAgent, $matches)) {
      $this->setVersion($matches[1]);
      $this->setBrowser(self::BROWSER_NETSCAPE_NAVIGATOR);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Shiretoko or not.
   *
   * See: https://wiki.mozilla.org/Projects/shiretoko.
   *
   * @return bool
   *   True if the browser is Shiretoko otherwise false.
   */
  protected function checkBrowserShiretoko() {
    if ($this->isHttpAgentContains('Mozilla') && preg_match('/Shiretoko\/([^ ]*)/i', $this->httpAgent, $matches)) {
      $this->setVersion($matches[1]);
      $this->setBrowser(self::BROWSER_SHIRETOKO);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Ice Cat or not.
   *
   * See: http://en.wikipedia.org/wiki/GNU_IceCat.
   *
   * @return bool
   *   True if the browser is Ice Cat otherwise false.
   */
  protected function checkBrowserIceCat() {
    if ($this->isHttpAgentContains('Mozilla') && preg_match('/IceCat\/([^ ]*)/i', $this->httpAgent, $matches)) {
      $this->setVersion($matches[1]);
      $this->setBrowser(self::BROWSER_ICECAT);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Nokia or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is Nokia otherwise false.
   */
  protected function checkBrowserNokia() {
    if (preg_match("/Nokia([^\/]+)\/([^ SP]+)/i", $this->httpAgent, $matches)) {
      $this->setVersion($matches[2]);
      if ($this->isHttpAgentContains('Series60') || $this->isHttpAgentContains('S60')) {
        $this->setBrowser(self::BROWSER_NOKIA_S60);
      }
      else {
        $this->setBrowser(self::BROWSER_NOKIA);
      }
      $this->setMobile(TRUE);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Firefox or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is Firefox otherwise false.
   */
  protected function checkBrowserFirefox() {
    if (!$this->isHttpAgentContains('safari')) {
      if (preg_match("/Firefox[\/ \(]([^ ;\)]+)/i", $this->httpAgent, $matches)) {
        $this->setVersion($matches[1]);
        $this->setBrowser(self::BROWSER_FIREFOX);
        // Firefox on Android.
        if ($this->isHttpAgentContains('Android')) {
          if ($this->isHttpAgentContains('Mobile')) {
            $this->setMobile(TRUE);
          }
          else {
            $this->setTablet(TRUE);
          }
        }
        return TRUE;
      }
      elseif (preg_match("/Firefox$/i", $this->httpAgent, $matches)) {
        $this->setVersion("");
        $this->setBrowser(self::BROWSER_FIREFOX);
        return TRUE;
      }
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Firefox or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is Firefox otherwise false.
   */
  protected function checkBrowserIceweasel() {
    if ($this->isHttpAgentContains('Iceweasel')) {
      $result = $this->getHttpAgentExplosion('Iceweasel');
      if (isset($result[1])) {
        $version = explode(' ', $result[1]);
        $this->setVersion($version[0]);
        $this->setBrowser(self::BROWSER_ICEWEASEL);
        return TRUE;
      }
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Mozilla or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is Mozilla otherwise false.
   */
  protected function checkBrowserMozilla() {
    if ($this->isHttpAgentContains('mozilla')
      && preg_match('/rv:[0-9].[0-9][a-b]?/i', $this->httpAgent)
      && !$this->isHttpAgentContains('netscape'))
    {
      $version = $this->getHttpAgentExplosion('rv:', ' ');
      preg_match('/rv:[0-9].[0-9][a-b]?/i', $this->httpAgent, $version);
      $this->setVersion(str_replace('rv:', '', $version[0]));
      $this->setBrowser(self::BROWSER_MOZILLA);
      return TRUE;
    }
    elseif ($this->isHttpAgentContains('mozilla')
      && preg_match('/rv:[0-9]\.[0-9]/i', $this->httpAgent)
      && !$this->isHttpAgentContains('netscape'))
    {
      $version = $this->getHttpAgentExplosion('rv:', '');
      $this->setVersion(str_replace('rv:', '', $version[0]));
      $this->setBrowser(self::BROWSER_MOZILLA);
      return TRUE;
    }
    elseif ($this->isHttpAgentContains('mozilla')
      && preg_match('/mozilla\/([^ ]*)/i', $this->httpAgent, $matches)
      && !$this->isHttpAgentContains('netscape'))
    {
      $this->setVersion($matches[1]);
      $this->setBrowser(self::BROWSER_MOZILLA);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Lynx or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is Lynx otherwise false.
   */
  protected function checkBrowserLynx() {
    if ($this->isHttpAgentContains('lynx')) {
      $result = $this->getHttpAgentExplosion('Lynx');
      $version = explode(' ', (isset($result[1]) ? $result[1] : ""));
      $this->setVersion($version[0]);
      $this->setBrowser(self::BROWSER_LYNX);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Amaya or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is Amaya otherwise false.
   */
  protected function checkBrowserAmaya() {
    if ($this->isHttpAgentContains('amaya')) {
      $result = $this->getHttpAgentExplosion('Amaya');
      if (isset($result[1])) {
        $version = explode(' ', $result[1]);
        $this->setVersion($version[0]);
        $this->setBrowser(self::BROWSER_AMAYA);
        return TRUE;
      }
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Safari or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is Safari otherwise false.
   */
  protected function checkBrowserSafari() {
    if ($this->isHttpAgentContains('Safari')
      && !$this->isHttpAgentContains('iPhone')
      && !$this->isHttpAgentContains('iPod'))
    {
      $result = $this->getHttpAgentExplosion('Version');
      if (isset($result[1])) {
        $version = explode(' ', $result[1]);
        $this->setVersion($version[0]);
      }
      else {
        $this->setVersion(self::VERSION_UNKNOWN);
      }
      $this->setBrowser(self::BROWSER_SAFARI);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Detect if URL is loaded from FacebookExternalHit.
   *
   * @return bool
   *   True if it detects FacebookExternalHit otherwise false.
   */
  protected function checkFacebookExternalHit() {
    if ($this->isHttpAgentContains('FacebookExternalHit')) {
      $this->setRobot(TRUE);
      $this->setFacebook(TRUE);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Detect if URL is being loaded from internal Facebook browser.
   *
   * @return bool
   *   True if it detects internal Facebook browser otherwise false.
   */
  protected function checkForFacebookIos() {
    if ($this->isHttpAgentContains('FBIOS')) {
      $this->setFacebook(TRUE);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Detect Version for the Safari browser on iOS devices.
   *
   * @return bool
   *   True if it detects the version correctly otherwise false.
   */
  protected function getSafariVersionOnIos() {
    $result = $this->getHttpAgentExplosion('Version');
    if (isset($result[1])) {
      $version = explode(' ', $result[1]);
      $this->setVersion($version[0]);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Detect Version for the Chrome browser on iOS devices.
   *
   * @return bool
   *   True if it detects the version correctly otherwise false.
   */
  protected function getChromeVersionOnIos() {
    $result = $this->getHttpAgentExplosion('CriOS');
    if (isset($result[1])) {
      $version = explode(' ', $result[1]);
      $this->setVersion($version[0]);
      $this->setBrowser(self::BROWSER_CHROME);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Determine if the browser is iPhone or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is iPhone otherwise false.
   */
  protected function checkBrowseriPhone() {
    if ($this->isHttpAgentContains('iPhone')) {
      $this->setVersion(self::VERSION_UNKNOWN);
      $this->setBrowser(self::BROWSER_IPHONE);
      $this->getSafariVersionOnIos();
      $this->getChromeVersionOnIos();
      $this->checkForFacebookIos();
      $this->setMobile(TRUE);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Determine if the browser is iPad or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is iPad otherwise false.
   */
  protected function checkBrowseriPad() {
    if ($this->isHttpAgentContains('iPad')) {
      $this->setVersion(self::VERSION_UNKNOWN);
      $this->setBrowser(self::BROWSER_IPAD);
      $this->getSafariVersionOnIos();
      $this->getChromeVersionOnIos();
      $this->checkForFacebookIos();
      $this->setTablet(TRUE);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Determine if the browser is iPod or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is iPod otherwise false.
   */
  protected function checkBrowseriPod() {
    if ($this->isHttpAgentContains('iPod')) {
      $this->setVersion(self::VERSION_UNKNOWN);
      $this->setBrowser(self::BROWSER_IPOD);
      $this->getSafariVersionOnIos();
      $this->getChromeVersionOnIos();
      $this->checkForFacebookIos();
      $this->setMobile(TRUE);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Determine if the browser is Android or not (last updated 1.7).
   *
   * @return bool
   *   True if the browser is Android otherwise false.
   */
  protected function checkBrowserAndroid() {
    if ($this->isHttpAgentContains('Android')) {
      $result = $this->getHttpAgentExplosion('Android', ' ');
      if (isset($result[1])) {
        $version = explode(' ', $result[1]);
        $this->setVersion($version[0]);
      }
      else {
        $this->setVersion(self::VERSION_UNKNOWN);
      }
      if ($this->isHttpAgentContains('Mobile')) {
        $this->setMobile(TRUE);
      }
      else {
        $this->setTablet(TRUE);
      }
      $this->setBrowser(self::BROWSER_ANDROID);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Determine the user's platform (last updated 1.7).
   */
  protected function checkPlatform() {
    // Prepare the platforms array.
    $platforms = array(
      'windows'     => self::PLATFORM_WINDOWS,
      'iPad'        => self::PLATFORM_IPAD,
      'iPod'        => self::PLATFORM_IPOD,
      'iPhone'      => self::PLATFORM_IPHONE,
      'mac'         => self::PLATFORM_APPLE,
      'android'     => self::PLATFORM_ANDROID,
      'linux'       => self::PLATFORM_LINUX,
      'Nokia'       => self::PLATFORM_NOKIA,
      'BlackBerry'  => self::PLATFORM_BLACKBERRY,
      'FreeBSD'     => self::PLATFORM_FREEBSD,
      'OpenBSD'     => self::PLATFORM_OPENBSD,
      'NetBSD'      => self::PLATFORM_NETBSD,
      'OpenSolaris' => self::PLATFORM_OPENSOLARIS,
      'SunOS'       => self::PLATFORM_SUNOS,
      'OS\/2'       => self::PLATFORM_OS2,
      'BeOS'        => self::PLATFORM_BEOS,
      'win'         => self::PLATFORM_WINDOWS,
    );
    foreach ($platforms as $agent => $platform) {
      if ($this->isHttpAgentContains($agent)) {
        $this->browserPlatform = $platform;
        break;
      }
    }
  }

}

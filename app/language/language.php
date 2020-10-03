<?php
declare(strict_types=1);

namespace app\language;

use mysql_xdevapi\Exception;

class language
{
    private static $inst;
    private $languages;
    private array $refSession;
    private array $refCookie;
    private bool $isInit = false;

    public static function init()
    {
        if (!language::$inst instanceof language) {
            language::$inst = new self();
        }/* elseif (!language::$inst->isInit) {
            throw new \RuntimeException("Data source not initialized");
        }*/
        return language::$inst;
    }

    private function __clone()
    {
    }

    public function __call($name, $arguments)
    {
    }

    public static function __callStatic($name, $arguments)
    {
    }

    private function __construct()
    {
        $this->isInit = false;
    }

    private function setDefaultLanguage(string $httpAcceptLanguage)
    {
        if (!isset($this->refSession['default lang'])) {
            $default = substr($httpAcceptLanguage, 0, 2);
            if (!isset($this->languages[$default])) {
                $this->refSession['default lang'] = DEFAULT_LANG;
            } else {
                $this->refSession['default lang'] = $default;
            }
        }

        if (isset($this->refCookie['lang'])) {
            $this->refSession['lang'] =  $this->refCookie['lang'];
        }

        if (!isset($this->refSession['lang'])) {
            $this->refSession['lang'] = $this->refSession['default lang'];
        }

        if (!isset($this->languages[$this->refSession['lang']])) {
            $this->refSession['lang'] = DEFAULT_LANG;
        }
    }

    public function getLibrary(string $page):array
    {
        if ($this->isInit) {
            $lang_dic = require DIR_LANGUAGES . $this->refSession['lang'] . '/load.php';
            return $lang_dic;
        } else {
            throw new \RuntimeException("Data source not initialized");
        }
    }

    public function setLanguage(string $languages)
    {
        if ($this->isInit) {
            if (isset($this->languages[$languages])) {
                $this->refSession['lang'] = $languages;
                $y2k = mktime(0, 0, 0, 1, 1, date("Y") + 1);
                if (!defined('TEST')) {
                    setcookie('lang', $languages, $y2k);
                } else {
                    $this->refCookie['lang']=$languages;
                }
            }
        } else {
            throw new \RuntimeException("Data source not initialized");
        }
    }

    public function initStore(array &$refSession, array &$refCookie, string $httpAcceptLanguage)
    {
        if (!$this->isInit) {
            $this->isInit = true;
            $this->refSession = &$refSession;
            $this->refCookie = &$refCookie;
            $this->languages = LANGUAGES;
            $this->setDefaultLanguage($httpAcceptLanguage);
        }
    }

    /**
     * TODO For testing.
     */
    public function __destruct()
    {
        language::$inst = null;
    }
}

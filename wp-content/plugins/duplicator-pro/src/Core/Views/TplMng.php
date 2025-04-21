<?php

/**
 * Template view manager
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace Duplicator\Core\Views;

use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Libs\Snap\SnapJson;

final class TplMng
{
    /** @var ?self */
    private static $instance;
    /** @var string */
    private $mainFolder = '';
    /** @var bool */
    private static $stripSpaces = false;
    /** @var mixed[] */
    private $globalData = [];
    /** @var ?mixed[] */
    private $renderData;

    /**
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Class constructor
     */
    private function __construct()
    {
        $this->mainFolder = DUPLICATOR____PATH . '/template/';
    }

    /**
     * If strip spaces is true in render method spaced between tag are removed
     *
     * @param bool $strip if true strip spaces
     *
     * @return void
     */
    public static function setStripSpaces($strip)
    {
        self::$stripSpaces = (bool) $strip;
    }

    /**
     * Set template global value in template data
     *
     * @param string $key global value key
     * @param mixed  $val value
     *
     * @return void
     */
    public function setGlobalValue($key, $val)
    {
        $this->globalData[$key] = $val;
    }

    /**
     * Remove global value if exist
     *
     * @param string $key gloval value key
     *
     * @return void
     */
    public function unsetGlobalValue($key)
    {
        if (isset($this->globalData[$key])) {
            unset($this->globalData[$key]);
        }
    }

    /**
     * Return true if global values exists
     *
     * @param string $key gloval value key
     *
     * @return bool
     */
    public function hasGlobalValue($key)
    {
        return isset($this->globalData[$key]);
    }

    /**
     * Multiple global data set
     *
     * @param array<string, mixed> $data data tu set in global data
     *
     * @return void
     */
    public function updateGlobalData(array $data = [])
    {
        $this->globalData = array_merge($this->globalData, (array) $data);
    }

    /**
     * Return global data
     *
     * @return array<string, mixed>
     */
    public function getGlobalData()
    {
        return $this->globalData;
    }

    /**
     * Return global value
     *
     * @param string $key     global value key
     * @param mixed  $default default value if global value not exists
     *
     * @return mixed
     */
    public function getGlobalValue($key, $default = null)
    {
        return $this->globalData[$key] ?? $default;
    }

    /**
     * Render template
     *
     * @param string               $slugTpl template file is a relative path from root template folder
     * @param array<string, mixed> $args    array key / val where key is the var name in template
     * @param bool                 $echo    if false return template in string
     *
     * @return string
     */
    public function render($slugTpl, $args = [], $echo = true)
    {
        ob_start();
        if (($renderFile = $this->getFileTemplate($slugTpl)) !== false) {
            $origRenderData = $this->renderData;
            if (is_null($this->renderData)) {
                $this->renderData = array_merge($this->globalData, $args);
            } else {
                $this->renderData = array_merge($this->renderData, $args);
            }
            $this->renderData = apply_filters(self::getDataHook($slugTpl), $this->renderData);
            $tplData          = $this->renderData;
            // controller manager helper
            $ctrlMng = ControllersManager::getInstance();
            $tplMng  = $this;
            require($renderFile);
            $this->renderData = $origRenderData;
        } else {
            echo '<p>FILE TPL NOT FOUND: ' . $slugTpl . '</p>';
        }
        $renderResult = apply_filters(self::getRenderHook($slugTpl), ob_get_clean());

        if (self::$stripSpaces) {
            $renderResult = preg_replace('~>[\n\s]+<~', '><', $renderResult);
        }
        if ($echo) {
            echo $renderResult;
            return '';
        } else {
            return $renderResult;
        }
    }

    /**
     * Render data exists
     *
     * @param string $key render data key
     *
     * @return bool
     */
    public function dataValueExists(string $key): bool
    {
        return isset($this->renderData[$key]);
    }

    /**
     * Get render data int value
     *
     * @param string $key     render data key
     * @param int    $default default value if key not exists
     *
     * @return int
     */
    public function getDataValueInt(string $key, int $default = 0): int
    {
        return isset($this->renderData[$key]) ? (int) $this->renderData[$key] : $default;
    }

    /**
     * Get render data string value
     *
     * @param string $key     render data key
     * @param string $default default value if key not exists
     *
     * @return string
     */
    public function getDataValueString(string $key, string $default = ''): string
    {
        return isset($this->renderData[$key]) ? (string) $this->renderData[$key] : $default;
    }

    /**
     * Get render data bool value
     *
     * @param string $key     render data key
     * @param bool   $default default value if key not exists
     *
     * @return bool
     */
    public function getDataValueBool(string $key, bool $default = false): bool
    {
        return isset($this->renderData[$key]) ? (bool) $this->renderData[$key] : $default;
    }

    /**
     * Get render data array value
     *
     * @param string             $key     render data key
     * @param array<mixed,mixed> $default default value if key not exists
     *
     * @return array<mixed,mixed>
     */
    public function getDataValueArray(string $key, array $default = []): array
    {
        return isset($this->renderData[$key]) ? (array) $this->renderData[$key] : $default;
    }

    /**
     * Get render data object class
     *
     * @template T of object
     *
     * @param string          $key     render data key
     * @param class-string<T> $class   class name
     * @param ?T              $default default value if key not exists
     *
     * @return ?T
     */
    public function getDataValueObject(string $key, $class, $default = null): ?object
    {
        return ((isset($this->renderData[$key]) && $this->renderData[$key] instanceof $class) ? $this->renderData[$key] : $default);
    }

    /**
     * Render template in json string
     *
     * @param string              $slugTpl template file is a relative path from root template folder
     * @param array<string,mixed> $args    array key / val where key is the var name in template
     * @param bool                $echo    if false return template in string
     *
     * @return string
     */
    public function renderJson($slugTpl, $args = [], $echo = true)
    {
        $renderResult = SnapJson::jsonEncode($this->render($slugTpl, $args, false));
        if ($echo) {
            echo $renderResult;
            return '';
        } else {
            return $renderResult;
        }
    }

    /**
     * Render template apply esc attr
     *
     * @param string               $slugTpl template file is a relative path from root template folder
     * @param array<string, mixed> $args    array key / val where key is the var name in template
     * @param bool                 $echo    if false return template in string
     *
     * @return string
     */
    public function renderEscAttr($slugTpl, $args = [], $echo = true)
    {
        $renderResult = esc_attr($this->render($slugTpl, $args, false));
        if ($echo) {
            echo $renderResult;
            return '';
        } else {
            return $renderResult;
        }
    }

    /**
     * Get hook unique from template slug
     *
     * @param string $slugTpl template slug
     *
     * @return string
     */
    public static function tplFileToHookSlug($slugTpl)
    {
        return str_replace(['\\', '/', '.'], '_', $slugTpl);
    }

    /**
     * Return data hook from template slug
     *
     * @param string $slugTpl template slug
     *
     * @return string
     */
    public static function getDataHook($slugTpl)
    {
        return 'duplicator_template_data_' . self::tplFileToHookSlug($slugTpl);
    }

    /**
     * Return render hook from template slug
     *
     * @param string $slugTpl template slug
     *
     * @return string
     */
    public static function getRenderHook($slugTpl)
    {
        return 'duplicator_template_render_' . self::tplFileToHookSlug($slugTpl);
    }

    /**
     * Acctept html of php extensions. if the file have unknown extension automatic add the php extension
     *
     * @param string $slugTpl template slug
     *
     * @return boolean|string return false if don\'t find the template file
     */
    protected function getFileTemplate($slugTpl)
    {
        $fullPath = apply_filters('duplicator_template_file', $this->mainFolder . $slugTpl . '.php', $slugTpl);

        if (file_exists($fullPath)) {
            return $fullPath;
        } else {
            return false;
        }
    }

    /**
     * Get input name
     *
     * @param string $field    field nam
     * @param string $subInxed sub index
     *
     * @return string
     */
    public static function getInputName($field, $subInxed = '')
    {
        return 'dup_input_' . $field . (strlen($subInxed) ? '_' . $subInxed : '');
    }

    /**
     * Get input id
     *
     * @param string $field    field nam
     * @param string $subInxed sub index
     *
     * @return string
     */
    public static function getInputId($field, $subInxed = '')
    {
        return self::getInputName($field, $subInxed);
    }
}

<?php

namespace Delz\Config;

/**
 * Yaconf扩展配置类
 *
 * 使用本类必须安装Yaconf扩展，具体安装请参考https://github.com/laruence/yaconf
 *
 * 本类须php版本须php7+
 *
 * @package Delz\Config\Adapter
 */
class Yaconf implements IConfig
{
    /**
     * Yaconf的配置文件名，如
     *
     * 配置文件名 app.ini，那么namespace可以设置为app
     *
     * @var string
     */
    private $namespace;

    /**
     * @param string $namespace
     */
    public function __construct($namespace)
    {
        if(!extension_loaded("yaconf")) {
            throw new \RuntimeException("Please install yaconf extension.");
        }
        $this->namespace = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return \Yaconf::get($this->namespace);
        }
        return \Yaconf::get($this->getKey($key), $default);
    }

    /**
     * 提供将数组转化为Yaconf需要的ini字符串
     *
     * @param array $data
     * @param array $parent
     * @return string
     */
    public static function arr2ini(array $data, array $parent = [])
    {
        $output = '';
        foreach ($data as $k => $v) {
            $index = str_replace(' ', '-', $k);
            if (is_array($v)) {
                $sec = array_merge((array)$parent, (array)$index);
                //$output .= PHP_EOL . '[' . join('.', $sec) . ']' . PHP_EOL;
                $output .= self::arr2ini($v, $sec);
            } else {
                $key = join('.', $parent);
                $output .= $key . ($key ? '.' : '') . "$index=";
                if (is_numeric($v) || is_float($v)) {
                    $output .= "$v";
                } elseif (is_bool($v)) {
                    $output .= ($v === true) ? 1 : 0;
                } elseif (is_string($v)) {
                    $output .= "'" . addcslashes($v, "'") . "'";
                } else {
                    $output .= "$v";
                }
                $output .= PHP_EOL;
            }
        }
        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return \Yaconf::has($this->getKey($key));
    }

    /**
     * 获取namespace获取Yaconf能读取的key值
     *
     * @param string $key
     * @return string
     */
    private function getKey($key)
    {
        return $this->namespace . '.' . $key;
    }
}
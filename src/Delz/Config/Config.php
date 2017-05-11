<?php

namespace Delz\Config;

/**
 * 标准配置类
 *
 * 可以从内存以及不同文件格式(ini\php\yaml\xml)获取配置
 *
 * @package Delz\Config
 */
class Config implements IConfig
{
    /**
     * 配置参数表
     *
     * @var array
     */
    private $map = [];

    /**
     * @param array $map
     */
    public function __construct($map = [])
    {
        $this->map = $map;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $this->map;
        }
        //引用可减少内存使用
        $pos =& $this->map;
        $parts = explode('.', $key);
        foreach ($parts as $part) {
            if (!isset($pos[$part])) {
                return $default;
            }
            $pos =& $pos[$part];
        }
        return $pos;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $pos =& $this->map;
        $parts = explode('.', $key);
        foreach ($parts as $part) {
            if (!isset($pos[$part])) {
                return false;
            }
            $pos =& $pos[$part];
        }
        return true;
    }

    /**
     * 从ini文件中载入配置
     *
     * @param string $iniFile ini文件地址
     * @throws Exception
     */
    public function loadIni($iniFile)
    {
        $iniFile = realpath($iniFile);
        $iniMap = parse_ini_file($iniFile, true, INI_SCANNER_RAW);
        if ($iniMap === false) {
            throw new Exception("Configuration file " . $iniFile . " can't be loaded");
        }
        $this->load($iniMap);
    }

    /**
     * 从php文件中载入配置
     *
     * php文件须return一个数组
     *
     * @param string $phpFile php文件地址
     */
    public function loadPhp($phpFile)
    {
        $phpFile = realpath($phpFile);
        $phpMap = include($phpFile);
        $this->load($phpMap);
    }

    /**
     * 从yaml文件中载入配置
     *
     * @param string $yamlFile yaml文件地址
     * @throws Exception
     */
    public function loadYaml($yamlFile)
    {
        if (!extension_loaded('yaml')) {
            throw new Exception('Yaml extension not loaded');
        }
        $yamlFile = realpath($yamlFile);
        $yamlMap = yaml_parse_file($yamlFile);
        if ($yamlMap === false) {
            throw new Exception('Configuration file ' . $yamlFile . ' can\'t be loaded');
        }
        $this->load($yamlMap);
    }

    /**
     * 从xml文件中载入配置
     *
     * @param string $xmlFile xml文件地址
     * @throws Exception
     */
    public function loadXml($xmlFile)
    {
        $xmlFile = realpath($xmlFile);
        $xml = simplexml_load_file($xmlFile);
        if ($xml === false) {
            throw new Exception('Configuration file ' . $xmlFile . ' can\'t be loaded');
        }
        $this->load(json_decode(json_encode($xml), true));
    }

    /**
     * 载入配置参数
     *
     * 将新配置参数$map合并到$this->map
     *
     * 此方法跟array_merge以及array_merge_recursive不同
     *
     * <code>
     * //数组1
     * $arr1 = [
     *      'name' => 'tom',
     *      'db' => [
     *          'name' => '123',
     *          'host' => '127.0.0.1'
     *      ]
     * ]
     * //数组2
     * $arr2 = [
     *      'title' => 'GM',
     *      'db' => [
     *          'user' => 'root',
     *          'password' => 'root'
     *      ]
     * ]
     *
     * 合并后的数组为
     * [
     *      'name' => 'tom',
     *      'db' => [
     *          'name' => '123',
     *          'host' => '127.0.0.1',
     *          'user' => 'root'.
     *          'password' => 'root'
     *     ]
     *     'title' => 'GM'
     * ]
     *
     * </code>
     *
     * @param array $map 要合并的参数
     */
    public function load($map = [])
    {
        foreach ($map as $k => $v) {
            if (is_array($v)) {
                $newMap = [];
                foreach ($v as $k1 => $v1) {
                    $newMap[$k . '.' . $k1] = $v1;
                }
                $this->load($newMap);
            } else {
                $parts = explode('.', $k);
                $max = count($parts) - 1;
                $pos =& $this->map;
                for ($i = 0; $i <= $max; $i++) {
                    $part = $parts[$i];
                    if ($i < $max) {
                        if (!isset($pos[$part])) {
                            $pos[$part] = [];
                        }
                        $pos =& $pos[$part];
                    } else {
                        $pos[$part] = $v;
                    }
                }
            }
        }
    }


}
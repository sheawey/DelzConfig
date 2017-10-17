<?php

namespace Delz\Config;

/**
 * Yaconf配置文件生成器
 *
 * @package Delz\Config
 */
class YaconfGenerator
{
    /**
     * @param array $data 要生成的数据
     * @param string $file 要生成的文件，不要写后缀名，后缀名默认是.ini
     */
    public static function dump(array $data, $file)
    {
        file_put_contents($file.'.ini', self::arr2ini($data));
    }

    /**
     * @param array $data
     * @param array $parent
     * @return string
     */
    protected static function arr2ini(array $data, array $parent = [])
    {
        $output = '';
        foreach ($data as $k => $v) {
            $index = str_replace(' ', '-', $k);
            if (is_array($v)) {
                $sec = array_merge((array)$parent, (array)$index);
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
}
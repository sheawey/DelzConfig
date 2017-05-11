<?php

namespace Delz\Config;

/**
 * 配置参数类接口
 *
 * 说明：
 *
 * 如果配置参数是：
 * <code>
 * [
 *      'name' => 'tom',
 *      'db' => [
 *          'name' => 'demo',
 *          'host' => '127.0.0.1',
 *          'user' => 'root',
 *          'password' => 'root'
 *      ]
 * ]
 *
 * //如果要获取name，则是
 * IConfig::get('name')
 * //如果要获取数组的二级元素，每个元素用.符号分割，如
 * IConfig::get('db.name')
 * IConfig::get('db.host')
 * </code>
 *
 * @package Delz\Config
 */
interface IConfig
{
    /**
     * 根据键值$key获取配置参数值
     *
     * 如果$key不存在，返回默认$default值
     *
     * 如果$key设置为null，返回所有配置参数
     *
     * @param string|null $key 键值
     * @param null|mixed $default 如果$key不存在，返回的默认$default值
     * @return mixed 返回参数值
     */
    public function get($key = null, $default = null);

    /**
     * 判断键值$key是否存在
     *
     * @param string $key 键值
     * @return bool 存在返回true, 否则返回false
     */
    public function has($key);

}
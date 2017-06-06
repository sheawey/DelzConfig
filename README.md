# 配置组件

配置组件包含两个类：

Delz\Config\Config

Delz\Config\Yaconf

其中Delz\Config\Yaconf需要安装yaconf第三方插件才可以使用，具体安装请参考https://github.com/laruence/yaconf

## 两者共有方法：

根据键值$key获取配置参数值
如果$key不存在，返回默认$default值
如果$key设置为null，返回所有配置参数

get($key = null, $default = null)


判断键值$key是否存在

has($key)

## Delz\Config\Config

可以将类型的配置项集中在一起，包括：

(1) 构造函数注入

    $map = [
        'key1' => 'val1',
        'key2' => 'val2'
    ];
    new Config($map);
    
(2) ini文件

    $iniFile = '/path/demo.ini';
    $config = new Config();
    $config->loadIni($iniFile);
    
(3) yaml文件
    
    $ymlFile = '/path/demo.yml';
    $config = new Config();
    $config->loadYaml($ymlFile);
    
(4) php文件 
php文件的结构如下：

    return [
        'key1' => 'val1',
        'key2' => 'val2'
    ];
    
装载php文件

    $phpFile = '/path/demo.php';
    $config = new Config();
    $config->loadPhp($phpFile);
    
(5) xml文件
    
    $xmlFile = '/path/demo.xml';
    $config = new Config();
    $config->loadXml($xmlFile);
    
(6) 数组文件

    $map = [
        'key1' => 'val1',
        'key2' => 'val2'
    ];
    $config = new Config();
    $config->load($map);
    
(7) 装载文件
    
不清楚文件类型，根据后缀，使用不同的方法，支持php、ini、xml、yml
    
说明：装载不同类型的会合并配置文件，如下：

    //数组1
    $arr1 = [
         'name' => 'tom',
         'db' => [
              'name' => '123',
              'host' => '127.0.0.1'
        ]
    ]
    //数组2
    $arr2 = [
         'title' => 'GM',
         'db' => [
               'user' => 'root',
               'password' => 'root'
         ]
    ]
   
     //合并后的数组为
    [
           'name' => 'tom',
           'db' => [
               'name' => '123',
               'host' => '127.0.0.1',
               'user' => 'root'.
               'password' => 'root'
          ]
          'title' => 'GM'
    ]
    
## 应用场景
    
如果像swoole这种框架，直接用Config性能上没有损失

如果是普通的php场景，每次都要初始化环境，读取不同配置文件会影响性能，可以如下解决

（1）用Config类读取不同配置文件后，缓存到一个文件，每次只读一个文件
（2）用Config类读取配置后，生成Yaconf需要的ini文件，用Yaconf类高性能读取，如下：

    //读取config目录配置文件
    $configPath = '/path/config';
    $config = new Config();
    $iterators = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($configPath), \RecursiveIteratorIterator::SELF_FIRST);
    foreach($iterators as $name => $file) {
        if(!$file->isDir()) {
            $config->loadFile($name);
        }
    }
    
    $configMap = $config->get();
    //生成yaconf的ini文件
    $ini = Yaconf::arr2ini($configMap);
    //将ini保存到Yaconf制定的$namespace.ini的文件，就可以用Yaconf读取配置参数
    


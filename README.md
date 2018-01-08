# ThinkPHP-项目初入门

## 项目运行环境

操作系统:`macOS High Sierra 10.13.2 (17C88)`

运行环境:`PHP7` + `Nginx`

> PHP 7.1.12 (cli) (built: Dec  2 2017 12:15:25) ( NTS )

> nginx version: nginx/1.12.2


## 相关软件

### HomeBrew

`HomeBrew` 

常用命令
`brew services list`

```
php71 started liuhao /Users/liuhao/Library/LaunchAgents/homebrew.mxcl.php71.plist
nginx started liuhao /Users/liuhao/Library/LaunchAgents/homebrew.mxcl.nginx.plist
```

使用`HomeBrew`安装的软件服务,可以用 `brew services list`查询其是否正在运行或者状态:

- `Php-fpm`的已运行
- `nginx`的已运行

### Php-fpm

`Php-fpm` 的配置文件目录 

```
/etc/php-fpm.d/www.conf

```

### Nginx

Nginx的配置文件目录

```
 /usr/local/etc/nginx/nginx.conf
```

打开此文件并编辑
在最后一行 添加一行:

```
include conf.d/*.conf;

```
这一句就是让`Nginx`在运行的时候 **自动查找我们的自定义目录下的配置文件并载入**

操作方式:

在与`nginx.conf`的同一级目录下(也就是`/usr/local/etc/nginx/`),创建目录`conf.d`:

```
mkdir conf.d

cd conf.d
```
进入后创建你的某个应用配置文件:

```
touch mySamplesApp.conf

```

修改Host文件

```
subl /etc/hosts

```

在文件中新增一行

```
127.0.0.1 	myApp.lc
```
### 我的应用和配置

#### 创建我的应用 

在任意位置创建一个新的目录,并记住目录的**绝对路径**

```
/Users/liuhao/Documents/PHPProjectSamples

```
此时你的php应用工程还是空的

#### 记住Hosts文件中你为php应用绑定的域名

```
myApp.lc
```


#### 我的项目配置

在刚才的创建的配置文件`mySamplesApp.conf`中,添加如下配置:

```
    server {
        listen       8080;  //访问你php应用的端口
        
        server_name  myApp.lc; //本机host文件中,你为php应用所绑定的域名
        
        root /Users/liuhao/Documents/PHPProjectSamples;//php应用的本机绝对路径
        
        index index.html index.htm index.php;

        charset utf-8;

        location / {
                if (!-e $request_filename) {
                        rewrite  ^(.*)$  /index.php?s=$1  last;
                        #break;
                }
        }

        location ~ \.php$ {
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
            include        fastcgi_params;
        }
    }
    
```


#### Nginx重新加载配置

`nginx -s reload`

#### 创建第一个php文件
在你的php应用的目录中(目前还是空的)`/Users/liuhao/Documents/PHPProjectSamples`:


` touch index.php`

```
<?php
	phpinfo();
?>
```

#### 浏览器访问 应用

在浏览器打开 `http://myapp.lc:8080/`

看到以下页面

就说明你的本地应用运行配置成功了

![](https://wx4.sinaimg.cn/mw690/6de36fdcgy1fn95wzrc6ij217s0uc113.jpg)



#### TODO 如何运行 ThinkPHP 项目
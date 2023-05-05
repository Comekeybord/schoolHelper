ThinkPHP 6.0
===============

> 运行环境要求PHP7.2+，兼容PHP8.1

## 1. 基本配置
#### 1.1 安装框架
> `composer create-project topthink/think tp` 

#### 1.2 多应用开发模式
> `composer require topthink/think-multi-app`

#### 1.3 跨域问题解决
1. 在代码处，增加header函数，在中间件增加跨域类
   > `header("Access-Control-Allow-Origin:*");`
2. 在中间件增加跨域类
    > 在app/middleware中进行配置，添加跨域类
   >

#### 1.4 对.example.env文件进行配置
> 把.example.env重命名为.env文件文件即可生效。具体配置见文件夹

#### 1.5 配置扩展文件
> 创建extend/yk/Ticket.php文件，该文件是token生成文件，具体配置见文件夹

#### 1.6 配置url重写
1. httpd.conf配置文件中加载了mod_rewrite.so模块
2. AllowOverride None 将None改为 All
3. 把下面的内容保存为.htaccess文件放到应用入口文件的同级目录下
```text
<IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On

  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php?/$1 [QSA,PT,L]
</IfModule>
```



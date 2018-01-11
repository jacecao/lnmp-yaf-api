## yaf框架开发PHP-API

##

#### 1 部署和运行
可以按照以下步骤来部署和运行程序:
1.请确已经安装了Yaf框架, 并且已经加载入PHP;
2.需要在php.ini里面启用如下配置，生产的代码才能正确运行：
	yaf.environ="product"
3.重启Webserver;
4.访问http://yourhost/six/,出现Hellow Word!, 表示运行成功,否则请查看php错误日志;

主机访问地址：localhost:8080

#### 2 邮件服务

如何部署邮件服务
> 1. packagist.org -> phpmailer 了解phpmailer
> 2. 安装composer到项目根目录 
>   php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    执行 'php composer-setup.php'    
	[参考官方安装流程](https://getcomposer.org/download/)
	
> 3. ./composer.phar require phpmailer/phpmailer 拉取插件	

	[composer中文网和国内镜像](http://www.phpcomposer.com), 在运行上面的命令后会在根目录下创建composer.json文件，这就跟NPM大同小异了
	
	最后我们选用的是nette/mail这个插件，可以支持第三方邮件服务 

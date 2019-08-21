# EasyApiDoc

A Library For Creating PHP API Document

Inspired by [PhalApi doc]( http://demo.phalapi.net/docs.php)

## Installation

#### Use [Composer](https://getcomposer.org/) to install the library.

- 在项目中的 `composer.json` 文件中添加：  
```
    "anychange/easyapidoc":"dev-master"
```

## Usage

### Rules：
接口标题(注释的第一行)、@dese(接口说明)、@param(接口入参 类型 参数名 是否必须 描述)、@return(接口出参 类型 参数名 是否必返 描述)
```php
    /**
     * API Title
     * @Author: zjm
     * @Date  : 2019-08-06 10:16
     * @desc API Instruction
     *
     * @param string mobile true  手机号
     *
     * @return string sex true 性别
     */
```
```php
$doc =new \AnyChange\EasyApiDoc\EasyApiDoc();
//advanced option
//Set The Name Of Your Project
$this->setProjectName( 'myproject');
//Set The Basic Access Url For API Testing 
$this->setProjectApiBaseUrl('http://192.168.1.209/v1');
//Set The ClassList That Need Exclude
$this->setProjectExcludeClassList(array ('App\Controllers\BaseController'));
//Set The FunctionList That Need Exclude
$this->setProjectExcludeFuncList(array ('App\Controllers\V1\About\index'));
//Set The MenuGroup's Self Reflection
$this->setSelfMenuGroup(array ('my'=> '我的'));
//Set The Self Menu List Which Will Put In The Start Of The List
$doc->setSelfMenuStartList ('introduction','API Description','The Document Is For The Developer');
//Set The Self Menu List Which Will Put In The End Of The List
$doc->setSelfMenuStopList ('the end','API Description','Just The End Of The Document');
//basic useage
//Set The Namespace Of The Project
$doc->setProjectNamespace('App\Controllers\V1');
//Set The Source Path Of The API
$doc->setProjectApiPath(APPPATH . 'Controllers/V1');
//Show The Document
$doc->onlineShow ();
```







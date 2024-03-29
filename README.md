# EasyApiDoc

A Library For Creating PHP API Document

Inspired by [PhalApi doc]( http://demo.phalapi.net/docs.php)

## Installation

#### Use [Composer](https://getcomposer.org/) to install the library.

- Add the dependency to your project:
```
bash:

composer require anychange/easyapidoc:dev-master

Or:

{
    "require": {
        "anychange/easyapidoc":"dev-master"
    }
}
```
## Usage
If You Want To Exclude One Class Or Fuction,Use '@ignore'.
### Rules：
```php
    /**
     * API Title
     * @Author: zjm
     * @Date  : 2019-08-06 10:16
     * @desc API Instruction
     *
     * @param string mobile true  The User Mobile
     *
     * @return string sex true The User Sex
     */
```
```php
$doc =new \AnyChange\EasyApiDoc\EasyApiDoc();
//advanced option

//Set The Name Of Your Project
$this->setProjectName( 'myproject');

//Set The Basic Access Url For API Testing 
$this->setApiBaseUrl('http://192.168.1.209/v1');

//Set The MenuGroup's Self Reflection
$this->setSelfMenuGroup(array ('my'=> 'My Account'));

//Set The Self Menu List Which Will Put In The Start Of The List As Default
$doc->setSelfMenuList ('introduction','API Introduction','The Document Is For The Developer<br>The Document Is For The Developer\nThe Document Is For The Developer');

//Set The Self Menu List Which Will Put In The End Of The List
$doc->setSelfMenuList ('the end','API Description','Just The End Of The Document','code instruction',array(array('Error Code','Error Message'),array('404','Not Fount')),'stop');
$doc->setSelfMenuStopList ('the end','API Description','Just The End Of The Document','',array(),'stop');

//basic useage
//Set The Namespace Of The Project
$doc->setNamespace('App\Controllers\V1');

//Set The Source Path Of The API
$doc->setApiPath(APPPATH . 'Controllers/V1');

//Show The Document
$doc->onlineShow ();
```







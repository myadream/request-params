# 动态查询

## 安装

```terminal
composer require little-superman/laravel-request-params
```

## Laravel

### laravel >= 5.5
**ServiceProvider** 会自动加载 

### 其他版本
在 `config/app.php` 中添加 `LittleSuperman\RequestParams\Providers\ConsoleServiceProvider::class`, 
`LittleSuperman\RequestParams\Providers\ConfigServiceProvider::class`  

```php
'providers' => [
    ...
    LittleSuperman\RequestParams\Providers\ConfigServiceProvider::class
    LittleSuperman\RequestParams\Providers\ConsoleServiceProvider::class,
],
```

## 命令行

### 创建模型
```terminal
php artisan customize:mold 模型名称
```

### 创建控制器
```termianl
php artisan customize:controller 控制器名称
```

### 创建请求资源
```termianl
php artisan customize:request 资源名称
```

## 字段说明

### 格式
```json
{
  "where": {
    "userId|name[!]":1,
    "|userId[~]":[1,3,4],
    "userId":1,"userId[!]":1
  },
  "order":{
    "userId":"desc"
  },
  "page":1,
  "limit":10,
  }
```

### 字段说明
```text
where 用于数据过滤
order 用于排序
page 分页
limit 条数
```

### where 条件字段限制
```php
'whereSymbols' => [
    //key => '对应前端传递值'
    'and' => '&',
    'or' => '|',
    'equal' => '=',
    'notEqual' => '!',
    'greater' => '>',
    'less' => '<',
    'in' => '-',
    'notIn' => '!-',
    'betwwen' => '<>',
    'notBetwwen' => '><',
    'like' => '~',
    'notlike' => '!=',
],
```

### order 字段限制
```php
'orderSymbols' => [
    //key => '对应前端传递值'
    'asc' => 'asc',
    'desc' => 'desc'
],
```

配置文件位置: `resources/config/dynamicQuery.php`


##使用

### 配置Model

1. 在模型中引入 `LittleSuperman\RequestParams\DynamicQuery\Query`

2. 模型中添加 `$searchField`, key 对应前端使用的字段 value 对应数据库使用字段
```text
$searchField = [
   'userId' => 'id',
];
```

#### 配置样式
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LittleSuperman\RequestParams\DynamicQuery\Query;

class User extends Model
{
    //引入改特征
    use Query;

    /**
     * 搜索字段
     *
     * @var array
     */
    protected $searchField = [ //允许使用的字段
        //前端使用的字段 => 数据库字段
        'userId' => 'id',
    ];
}
```

### 开启动态查询

在模型后面调用`queryable`方法来开启动态查询

```php
User::queryable()->paginate();
```

加载其他模型搜索字段
```php
User::queryable(User::class, ...)->get(); 
```

[!['Build Status'](https://travis-ci.org/shopwwi/webman-scout.svg?branch=main)](https://github.com/shopwwi/webman-scout) [!['Latest Stable Version'](https://poser.pugx.org/shopwwi/webman-scout/v/stable.svg)](https://packagist.org/packages/shopwwi/webman-scout) [!['Total Downloads'](https://poser.pugx.org/shopwwi/webman-scout/d/total.svg)](https://packagist.org/packages/shopwwi/webman-scout) [!['License'](https://poser.pugx.org/shopwwi/webman-scout/license.svg)](https://packagist.org/packages/shopwwi/webman-scout)

# 安装

```
composer require shopwwi/webman-scout
```

- 如果觉得方便了你 不要吝啬你的小星星哦 tycoonSong<8988354@qq.com>
- 不适用于非 laravel orm
- 此插件源于laravel/scout 感谢伟大的开源付出

## 使用方法

### 设置
当使用 `xunsearch` 时，需提前在config/plugin/shopwwi/scout/ini下创建「indexName」.ini文件，亦可指定ini文件夹路径
```
// 路径config/plugin/shopwwi/scout

    'driver' => 'meilisearch', // 支持"algolia", "meilisearch","elasticsearch", "database", "collection", "null","xunsearch"
    'queue' => false, // 队列默认关闭 开启队列请先安装composer require webman/redis-queue
    'xunsearch' => [
        'path' => config_path().'/plugin/shopwwi/scout/ini/'
    ]
```

### 搜索驱动安装

- algolia

```
composer require algolia/algoliasearch-client-php
```

- xunsearch

```
composer require hightman/xunsearch
```

- meilisearch

```
composer require meilisearch/meilisearch-php
```

- elasticsearch

```
composer require elasticsearch/elasticsearch
```

### 命令行

- 创建index 一般无需操作 elasticsearch需要

```
php webman scout:index 'goods'
```

- 删除index 如非model设定 一般为表名

```
php webman scout:delete-index 'goods'
```

- 初始数据model导入 --chunk导入批次数量 不宜设置过大哦

```
php webman scout:import 'app/model/Goods' --chunk=200
```

- 重置清空模型数据 

```
php webman scout:flush 'app/model/Goods'
```

### 配置模型索引
每个 Eloquent 模型都与给定的搜索 「索引」同步，该索引包含该模型的所有可搜索记录。 换句话说，你可以将每个索引视为一个 MySQL 表。 默认情况下，每个模型都将持久化到与模型的典型 「表」名称匹配的索引。 通常，是模型名称的复数形式； 但你可以通过重写模型上的 `searchableAs` 方法来自由地自定义模型的索引：

```php
<?php
 
namespace app\model;
 
use Illuminate\Database\Eloquent\Model;
use Shopwwi\WebmanScout\Searchable;
 
class Goods extends Model
{
    use Searchable;
    
     /**
     * 与模型关联的表名 后缀不带s的必须设置 默认是带s的
     *
     * @var string
     */
    protected $table = 'goods';
    
    /**
     * 获取与模型关联的索引的名称。
     * 如果不存在该类 则默认为表名
     * @return string
     */
    public function searchableAs()
    {
        return 'posts_index';
    }
}
```
### 配置可搜索数据
默认情况下，模型以完整的 `toArray` 格式持久化到搜索索引。如果要自定义同步到搜索索引的数据，可以覆盖模型上的 `toSearchableArray` 方法：

```php

<?php
 
namespace app\model;
 
use Illuminate\Database\Eloquent\Model;
use Shopwwi\WebmanScout\Searchable;
 
class Goods extends Model
{
    use Searchable;
    
    /**
     * 获取模型的可索引的数据。
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
 
        // 自定义数据数组...
 
        return $array;
    }
}

```

### 配置模型 ID
默认情况下，Scout 将使用模型的主键作为搜索索引中存储的唯一 ID /key。 可以通过模型上的 `getScoutKey` 和 `getScoutKeyName` 方法自定义：

```php

<?php
 
namespace app\model;
 
use Illuminate\Database\Eloquent\Model;
use Shopwwi\WebmanScout\Searchable;
 
class Goods extends Model
{
    use Searchable;
    
    /**
     * 获取用于索引模型的值
     *
     * @return mixed
     */
    public function getScoutKey()
    {
        return $this->email;
    }
 
    /**
     * 获取用于索引模型的键名
     *
     * @return mixed
     */
    public function getScoutKeyName()
    {
        return 'email';
    }
}

```

### 自定义数据库搜索策略
默认情况下，数据库引擎将对你已配置为可搜索 的每个模型属性执行「where like」查询。但是，在某些情况下，这可能会导致性能不佳。因此，你可以通过配置数据库引擎的搜索策略，使某些指定的列使用全文搜索查询或仅使「where like」约束来搜索字符串的前缀（example%），而不是在整个字符串中搜索（%example%)。

要定义此行为，你可以将 PHP 属性分配给模型的 `toSearchableArray` 方法。任何未分配额外搜索策略行为的列将继续使用默认的「where like」策略：

```php

<?php
 
namespace app\model;
 
use Illuminate\Database\Eloquent\Model;
use Shopwwi\WebmanScout\Searchable;
 
class Goods extends Model
{
    use Searchable;
    
    use Shopwwi\WebmanScout\Attributes\SearchUsingFullText;
    use Shopwwi\WebmanScout\Attributes\SearchUsingPrefix;

    /**
     * 获取模型的可索引数据数组。
     *
     * @return array
     */
    #[SearchUsingPrefix(['id', 'email'])]
    #[SearchUsingFullText(['bio'])]
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'bio' => $this->bio,
        ];
    }
}

```

### 修改导入查询
如果要修改用于检索所有模型以进行批量导入的查询，可以在模型上定义 `makeAllSearchableUsing` 方法。这是一个很好的地方，可以在导入模型之前添加任何可能需要的即时关系加载：

```php

<?php
 
namespace app\model;
 
use Illuminate\Database\Eloquent\Model;
use Shopwwi\WebmanScout\Searchable;
 
class Goods extends Model
{
    use Searchable;
    
    /**
     * 在使所有模型都可搜索时，修改用于检索模型的查询。
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function makeAllSearchableUsing($query)
    {
        return $query->with('author');
    }
}

```

### 添加记录
一旦将 `Shopwwi\WebmanScout\Searchable` trait 添加到模型中，你只需 save 或 create 模型实例，它就会自动添加到搜索索引中。如果已将 Scout 配置为 使用队列，此操作将由队列 worker 进程在后台执行：


```php

use app\model\Goods;

$goods = new Goods;

// ...

$goods->save();

```

### 通过查询添加
如果你希望通过 Eloquent 查询将模型集合添加到搜索索引中，你也可以在 Eloquent 查询构造器上链式调用 `searchable` 方法。`searchable` 会把构造器的查询 结果分块 并将记录添加到搜索索引中。同样，如果你已将 Scout 配置为使用队列，则队列 worker 将在后台导入所有块：

```php

use app\model\Goods;

Goods::where('price', '>', 100)->searchable();

```

你还可以在 Eloquent 关联实例上调用 `searchable` 方法：

```php

$goods->images()->searchable();

```

或者，如果内存中已经有一组 Eloquent 模型，可以调用集合实例上的 `searchable` 方法，将模型实例添加到相应的索引中：

```php

$goods->searchable();

```

### 更新记录
要更新可搜索的模型，只需要更新模型实例的属性并将模型 `save` 到数据库。Scout 会自动将更新同步到你的搜索索引中：

```php

use app\model\Goods;

$goods = Goods::find(1);

// 更新商品...

$goods->save();

```
你也可以在 Eloquent 查询语句上使用 `searchable` 方法来更新一个模型的集合。如果这个模型不存在你检索的索引里，就会被创建：

```php
Order::where('price', '>', 100)->searchable();
```

如果要更新关系中所有模型的搜索索引记录，可以在关系实例上调用 `searchable` ：

```php
$user->orders()->searchable();
```

或者，如果内存中已经有 Eloquent 模型集合，则可以调用集合实例上的 `searchable` 方法来更新相应索引中的模型实例：

```php
$orders->searchable();
```
### 移除记录
要从索引中删除记录，只需从数据库中 `delete` 模型即可。即使你正在使用 软删除 模型，也可以这样做：

```php

use app\model\Goods;

$goods = Goods::find(1);

// 更新商品...

$goods->delete();

```

如果你不希望记录在删除之前被检索到，可以在 Eloquent 查询实例或集合上使用 `unsearchable` 方法：

```php
Order::where('price', '>', 100)->unsearchable();
```

如果要删除关系中所有模型的搜索索引记录，可以在关系实例上调用 `unsearchable` ：

```php
$user->orders()->unsearchable();
```

或者，如果内存中已经有 Eloquent 模型集合，则可以调用集合实例上的 `unsearchable` 方法，从相应的索引中删除模型实例：

```php
$orders->unsearchable();
```

### 暂停索引
你可能需要在执行一批 Eloquent 操作的时候，不同步模型数据到搜索索引。此时你可以使用 `withoutSyncingToSearch` 方法来执行此操作。这个方法接受一个立即执行的回调。该回调中所有的操作都不会同步到模型的索引：
```php

    use app\model\Goods;
    
    Goods::::withoutSyncingToSearch(function () {
        // 执行模型操作...
    });

```

### 有条件的搜索模型实例
有时你可能只需要在某些条件下使模型可搜索。例如，假设你有 `app\model\Goods` 模型可能是两种状态之一：「仓库中」和「线上」。你可能只允许搜索 「线上」的商品。为了实现这一点，你需要在模型中定义一个 `shouldBeSearchable` 方法：
```php

    /**
     * 确定模型是否可搜索
     *
     * @return bool
     */
    public function shouldBeSearchable()
    {
        return $this->isOnline();
    }

```

### 搜索

你可以使用 `search` 方法来搜索模型。`search` 方法接受一个用于搜索模型的字符串。你还需要在搜索查询上链式调用 `get` 方法，才能用给定的搜索语句查询与之匹配的 Eloquent 模型：
```php
use app\model\Goods;

$goods = Goods::search('webman')->get();
```

由于 Scout 搜索返回 Eloquent 模型的集合，你甚至可以直接从路由或控制器返回结果，结果将自动转换为 JSON ：

```php
use app\model\Goods;
use support\Request;

Route::get('/search', function (Request $request) {
return Goods::search($request->search)->get();
});
```

如果你想在它们转换成 Eloquent 模型前得到原始结果，你应该使用 raw 方法：

```php
$goods = Goods::search('webman')->raw();
```

### 自定义索引

搜索查询通常会在模型的 `searchableAs` 方法指定的索引上执行。但是，你可以使用 `within` 方法指定应搜索的自定义索引：

```php
    $goods = Goods::search('webman')
    ->within('goods_desc')
    ->get();
```

### Where 子句
Scout 允许你在搜索查询中添加简单的「where」子句。目前，这些子句仅支持基本的数值相等性检查，主要用于按所有者 ID 确定搜索查询的范围。

```php
    use app\model\Goods;
    
    $goods = Goods::search('webman')->where('status', 1)->get();
    //你可以使用 whereIn 方法将结果限制在给定的一组值上：
   
    $goods = Goods::search('webman')->whereIn(
    'status', ['paid', 'open']
    )->get();
```

由于搜索索引不是关系数据库，因此目前不支持更高级的「where」子句。

### 分页
除了检索模型的集合，你也可以使用 paginate 方法对搜索结果进行分页。默认传参为「page」「limit」
```php
    use app\model\Goods;
    
    $goods = Goods::search('webman')->paginate();
```

通过将数量作为第一个参数传递给 paginate 方法，可以指定每页要检索多少个模型：

```php
    $goods = Goods::search('webman')->paginate(15);
    // 亦可指定参数
    $goods = Goods::search('webman')->paginate(request()->input('page',1),'page',request()->input('limit',10));
```

### 软删除
如果你索引的模型是 软删除，并且你需要搜索已删除的模型，请将 config 配置文件中的 soft_delete 选项设置为 true：
```php
    'soft_delete' => true,
```
当此配置选项为 true 时，Scout 不会从搜索索引中删除软删除的模型。相反，它将在索引记录上设置一个隐藏的__soft_deleted 属性。然后，你可以在搜索时使用 withTrashed 或 onlyTrashed 方法检索软删除记录：
```php
    use app\model\Goods;
    
    // 检索结果包括已删除记录
    $goods = Goods::search('webman')->withTrashed()->get();
    
    // 仅检索已删除记录...
    $goods = Goods::search('webman')->onlyTrashed()->get();

```
<?php
/**
 *-------------------------------------------------------------------------p*
 * 全局配置文件
 *-------------------------------------------------------------------------h*
 * @copyright  Copyright (c) 2015-2022 Shopwwi Inc. (http://www.shopwwi.com)
 *-------------------------------------------------------------------------c*
 * @license    http://www.shopwwi.com        s h o p w w i . c o m
 *-------------------------------------------------------------------------e*
 * @link       http://www.shopwwi.com by 象讯科技 phcent.com
 *-------------------------------------------------------------------------n*
 * @since      shopwwi象讯·PHP商城系统Pro
 *-------------------------------------------------------------------------t*
 */


use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use MeiliSearch\Client as MeiliSearch;
use Elastic\Elasticsearch\Client as ElasticSearch;
use Shopwwi\WebmanScout\EngineManager;


if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param  string|null  $abstract
     * @param  array  $parameters
     * @return mixed|\Illuminate\Contracts\Foundation\Application
     */
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }
        return Container::getInstance()->make($abstract, $parameters);
    }
}
if (! function_exists('event')) {
    /**
     * Dispatch an event and call the listeners.
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @param  bool  $halt
     * @return array|null
     */
    function event(...$args)
    {

        return app(Dispatcher::class)->dispatch(...$args);
    }
}

if (class_exists(MeiliSearch::class)) {
    app()->singleton(MeiliSearch::class, function ($app) {
        $config = config('plugin.shopwwi.scout.app.meilisearch');
        return new MeiliSearch($config['host'], $config['key']);
    });
}
if (class_exists(ElasticSearch::class)) {
    app()->singleton(ElasticSearch::class, function ($app) {
        $config = config('plugin.shopwwi.scout.app.elasticsearch');
        return new ElasticSearch($config['host'], $config['key']);
    });
}
app()->singleton(Dispatcher::class, function ($app) {
    return  new Dispatcher($app);
});
app()->singleton(EngineManager::class, function ($app) {
    return  new EngineManager($app);
});
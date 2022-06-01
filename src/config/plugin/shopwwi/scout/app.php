<?php
/**
 *-------------------------------------------------------------------------p*
 * 配置文件
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

return [
    'enable' => true,
    /*
    |--------------------------------------------------------------------------
    | Default Search Engine
    |--------------------------------------------------------------------------
    |
    | This option controls the default search connection that gets used while
    | using Laravel Scout. This connection is used when syncing all models
    | to the search service. You should adjust this based on your needs.
    |
    | Supported: "algolia", "meilisearch","elasticsearch", "database", "collection", "null"
    |
    */

    'driver' => 'meilisearch', //SCOUT_DRIVER

    /*
    |--------------------------------------------------------------------------
    | Index Prefix
    |--------------------------------------------------------------------------
    |
    | Here you may specify a prefix that will be applied to all search index
    | names used by Scout. This prefix may be useful if you have multiple
    | "tenants" or applications sharing the same search infrastructure.
    |
    */

    'prefix' => '', //SCOUT_PREFIX

    /*
    |--------------------------------------------------------------------------
    | Queue Data Syncing
    |--------------------------------------------------------------------------
    |
    | This option allows you to control if the operations that sync your data
    | with your search engines are queued. When this is set to "true" then
    | all automatic data syncing will get queued for better performance.
    |
    */

    'queue' => true, //SCOUT_QUEUE

    /*
    |--------------------------------------------------------------------------
    | Database Transactions
    |--------------------------------------------------------------------------
    |
    | This configuration option determines if your data will only be synced
    | with your search indexes after every open database transaction has
    | been committed, thus preventing any discarded data from syncing.
    |
    */

    'after_commit' => false,

    /*
    |--------------------------------------------------------------------------
    | Chunk Sizes
    |--------------------------------------------------------------------------
    |
    | These options allow you to control the maximum chunk size when you are
    | mass importing data into the search engine. This allows you to fine
    | tune each of these chunk sizes based on the power of the servers.
    |
    */

    'chunk' => [
        'searchable' => 500,
        'unsearchable' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Soft Deletes
    |--------------------------------------------------------------------------
    |
    | This option allows to control whether to keep soft deleted records in
    | the search indexes. Maintaining soft deleted records can be useful
    | if your application still needs to search for the records later.
    |
    */

    'soft_delete' => false,

    /*
    |--------------------------------------------------------------------------
    | Identify User
    |--------------------------------------------------------------------------
    |
    | This option allows you to control whether to notify the search engine
    | of the user performing the search. This is sometimes useful if the
    | engine supports any analytics based on this application's users.
    |
    | Supported engines: "algolia"
    |
    */

    'identify' => false, //SCOUT_IDENTIFY

    /*
    |--------------------------------------------------------------------------
    | Algolia Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Algolia settings. Algolia is a cloud hosted
    | search engine which works great with Scout out of the box. Just plug
    | in your application ID and admin API key to get started searching.
    |
    */

    'algolia' => [
        'id' => '', // ALGOLIA_APP_ID
        'secret' => '', //ALGOLIA_SECRET
    ],

    /*
    |--------------------------------------------------------------------------
    | MeiliSearch Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your MeiliSearch settings. MeiliSearch is an open
    | source search engine with minimal configuration. Below, you can state
    | the host and key information for your own MeiliSearch installation.
    |
    | See: https://docs.meilisearch.com/guides/advanced_guides/configuration.html
    |
    */

    'meilisearch' => [
        'host' => 'http://meili.shopwwi.com',
        'key' =>  null,
    ],

    /*
    |--------------------------------------------------------------------------
    | ElasticSearch Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your ElasticSearch settings. ElasticSearch is an open
    | source search engine with minimal configuration. Below, you can state
    | the host and key information for your own ElasticSearch installation.
    |
    | See: https://www.elastic.co/guide/en/elasticsearch/reference/current/index.html
    |
    */

    'elasticsearch' => [
        'hosts' => [
            'http://115.159.153.41:9200',
            // [
            //     'host'   => env('ELASTICSEARCH_HOST', 'localhost'),
            //     'port'   => env('ELASTICSEARCH_PORT', '9200'),
            //     'scheme' => env('ELASTICSEARCH_SCHEME', 'https'),
            //     'path'   => env('ELASTICSEARCH_PATH', '/elastic'),
            //     'user'   => env('ELASTICSEARCH_USER', 'username'),
            //     'pass'   => env('ELASTICSEARCH_PASS', 'password'),
            // ]
        ],
//        'index' => [
//            'setting' => [],
//            'aliases' => [],
//            'mappings' => [],
//        ]
    ],
];
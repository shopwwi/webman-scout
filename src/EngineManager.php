<?php
/**
 *-------------------------------------------------------------------------s*
 *
 *-------------------------------------------------------------------------h*
 * @copyright  Copyright (c) 2015-2022 Shopwwi Inc. (http://www.shopwwi.com)
 *-------------------------------------------------------------------------o*
 * @license    http://www.shopwwi.com        s h o p w w i . c o m
 *-------------------------------------------------------------------------p*
 * @link       http://www.shopwwi.com by 象讯科技 phcent.com
 *-------------------------------------------------------------------------w*
 * @since      shopwwi象讯·PHP商城系统Pro
 *-------------------------------------------------------------------------w*
 * @author      TycoonSong 8988354@qq.com
 *-------------------------------------------------------------------------i*
 */
namespace Shopwwi\WebmanScout;

use Algolia\AlgoliaSearch\Config\SearchConfig;
use Algolia\AlgoliaSearch\SearchClient as Algolia;
use Algolia\AlgoliaSearch\Support\UserAgent;
use Exception;
use Shopwwi\WebmanScout\Engines\AlgoliaEngine;
use Shopwwi\WebmanScout\Engines\CollectionEngine;
use Shopwwi\WebmanScout\Engines\DatabaseEngine;
use Shopwwi\WebmanScout\Engines\ElasticSearchEngine;
use Shopwwi\WebmanScout\Engines\MeiliSearchEngine;
use Shopwwi\WebmanScout\Engines\NullEngine;
use MeiliSearch\Client as MeiliSearch;
use Elastic\Elasticsearch\Client as ElasticSearch;
use Elastic\Elasticsearch\ClientBuilder;
use Shopwwi\WebmanScout\Engines\XunSearchEngine;

class EngineManager extends Manager
{
    /**
     * Get a driver instance.
     *
     * @param  string|null  $name
     * @return \Shopwwi\WebmanScout\Engines\Engine
     */
    public function engine($name = null)
    {
        return $this->driver($name);
    }

    /**
     * Create an Algolia engine instance.
     *
     * @return \Shopwwi\WebmanScout\Engines\AlgoliaEngine
     */
    public function createAlgoliaDriver()
    {
        $this->ensureAlgoliaClientIsInstalled();

        UserAgent::addCustomUserAgent('Laravel Scout', '9.4.9');

        $config = SearchConfig::create(
            config('plugin.shopwwi.scout.app.algolia.id'),
            config('plugin.shopwwi.scout.app.algolia.secret')
        )->setDefaultHeaders(
            $this->defaultAlgoliaHeaders()
        );

        if (is_int($connectTimeout = config('plugin.shopwwi.scout.app.algolia.connect_timeout'))) {
            $config->setConnectTimeout($connectTimeout);
        }

        if (is_int($readTimeout = config('plugin.shopwwi.scout.app.algolia.read_timeout'))) {
            $config->setReadTimeout($readTimeout);
        }

        if (is_int($writeTimeout = config('plugin.shopwwi.scout.app.algolia.write_timeout'))) {
            $config->setWriteTimeout($writeTimeout);
        }

        return new AlgoliaEngine(Algolia::createWithConfig($config), config('plugin.shopwwi.scout.app.soft_delete'));
    }

    /**
     * Ensure the Algolia API client is installed.
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function ensureAlgoliaClientIsInstalled()
    {
        if (class_exists(Algolia::class)) {
            return;
        }

        if (class_exists('AlgoliaSearch\Client')) {
            throw new Exception('Please upgrade your Algolia client to version: ^2.2.');
        }

        throw new Exception('Please install the Algolia client: algolia/algoliasearch-client-php.');
    }

    /**
     * Set the default Algolia configuration headers.
     *
     * @return array
     */
    protected function defaultAlgoliaHeaders()
    {
        if (! config('plugin.shopwwi.scout.app.identify')) {
            return [];
        }

        $headers = [];

        if (! config('app.debug') &&
            filter_var($ip = request()->ip(), FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
        ) {
            $headers['X-Forwarded-For'] = $ip;
        }

        if (($user = request()->user()) && method_exists($user, 'getKey')) {
            $headers['X-Algolia-UserToken'] = $user->getKey();
        }

        return $headers;
    }

    /**
     * Create an MeiliSearch engine instance.
     *
     * @return \Shopwwi\WebmanScout\Engines\MeiliSearchEngine
     */
    public function createMeilisearchDriver()
    {

        $this->ensureMeiliSearchClientIsInstalled();
        $client = $this->container->make(MeiliSearch::class);
        return new MeiliSearchEngine(
            $client,
            config('plugin.shopwwi.scout.app.soft_delete', false)
        );
    }

    /**
     * Ensure the MeiliSearch client is installed.
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function ensureMeiliSearchClientIsInstalled()
    {
        if (class_exists(MeiliSearch::class)) {
            return;
        }

        throw new Exception('Please install the MeiliSearch client: meilisearch/meilisearch-php.');
    }

    /**
     * Create an ElasticSearch engine instance.
     *
     * @return \Shopwwi\WebmanScout\Engines\ElasticSearchEngine
     */
    public function createElasticsearchDriver()
    {

        $this->ensureElasticSearchClientIsInstalled();
        $client = ClientBuilder::create()
            ->setHosts(config('plugin.shopwwi.scout.app.elasticsearch.hosts'))
            ->build();
        return new ElasticSearchEngine(
            $client,
            config('plugin.shopwwi.scout.app.soft_delete', false)
        );
    }

    /**
     * Ensure the MeiliSearch client is installed.
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function ensureElasticSearchClientIsInstalled()
    {
        if (class_exists(ElasticSearch::class)) {
            return;
        }

        throw new Exception('Please install the ElasticSearch client: elasticsearch/elasticsearch.');
    }

    /**
     * Create an ElasticSearch engine instance.
     *
     * @return \Shopwwi\WebmanScout\Engines\XunSearchEngine
     * @throws \Elastic\Elasticsearch\Exception\AuthenticationException
     */
    public function createXunsearchDriver()
    {

        $this->ensureXunSearchClientIsInstalled();
        return new XunSearchEngine(
            new XunSearchClient(),
            config('plugin.shopwwi.scout.app.soft_delete', false)
        );
    }

    /**
     * Ensure the MeiliSearch client is installed.
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function ensureXunSearchClientIsInstalled()
    {
        if (class_exists(\XS::class)) {
            return;
        }

        throw new Exception('Please install the ElasticSearch client: elasticsearch/elasticsearch.');
    }
    /**
     * Create a database engine instance.
     *
     * @return \Shopwwi\WebmanScout\Engines\DatabaseEngine
     */
    public function createDatabaseDriver()
    {
        return new DatabaseEngine;
    }

    /**
     * Create a collection engine instance.
     *
     * @return \Shopwwi\WebmanScout\Engines\CollectionEngine
     */
    public function createCollectionDriver()
    {
        return new CollectionEngine;
    }

    /**
     * Create a null engine instance.
     *
     * @return \Shopwwi\WebmanScout\Engines\NullEngine
     */
    public function createNullDriver()
    {
        return new NullEngine;
    }

    /**
     * Forget all of the resolved engine instances.
     *
     * @return $this
     */
    public function forgetEngines()
    {
        $this->drivers = [];

        return $this;
    }

    /**
     * Get the default Scout driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        if (is_null($driver = config('plugin.shopwwi.scout.app.driver'))) {
            return 'null';
        }

        return $driver;
    }
}

<?php
/**
 *-------------------------------------------------------------------------s*
 *
 *-------------------------------------------------------------------------h*
 * @copyright  Copyright (c) 2015-2022 Shopwwi Inc. (http://www.shopwwi.com)
 *-------------------------------------------------------------------------o*
 * @license    http://www.shopwwi.com        s h o p w w i . c o m
 *-------------------------------------------------------------------------p*
 * @link       http://www.shopwwi.com by 无锡豚豹科技
 *-------------------------------------------------------------------------w*
 * @since      shopwwi豚豹·PHP商城系统
 *-------------------------------------------------------------------------w*
 * @author      TycoonSong 8988354@qq.com
 *-------------------------------------------------------------------------i*
 */
namespace Shopwwi\WebmanScout\Engines;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\LazyCollection;
use Shopwwi\WebmanScout\Builder;
use Shopwwi\WebmanScout\XunSearchClient;

class XunSearchEngine extends Engine
{
    /**
     * XunSearch client.
     *
     */
    protected $xunsearch;

    /**
     * Determines if soft deletes for Scout are enabled or not.
     *
     * @var bool
     */
    protected $softDelete;

    /**
     * Create a new engine instance.
     *
     * @return void
     */
    public function __construct(XunSearchClient $xs, $softDelete = false)
    {
        $this->xunsearch = $xs;
        $this->softDelete = $softDelete;
    }

    /**
     * Create a search index.
     *
     * @param  string  $name
     * @param  array  $options
     * @return mixed
     */
    public function createIndex($name, array $options = [])
    {
        return $this->xunsearch->createIndex($name, $options);
    }


    public function deleteIndex($name)
    {
        $this->xunsearch->getIndex()->clean();
    }

    /**
     * Update the given model in the index.
     *
     * @param  Collection  $models
     * @return void
     */
    public function update($models)
    {
        if ($models->isEmpty()) {
            return;
        }
        if ($this->usesSoftDelete($models->first()) && $this->softDelete) {
            $models->each->pushSoftDeleteMetadata();
        }
        $index = $this->xunsearch->task($models->first()->searchableAs())->getIndex();
        $models->map(function ($model) use ($index) {
            if (empty($searchableData = $model->toSearchableArray())) {
                return;
            }
            $doc = new \XSDocument;
            $doc->setFields($searchableData);
            $index->update($doc);
        });
        $index->flushIndex();
    }

    /**
     * Remove the given model from the index.
     *
     * @param  Collection  $models
     * @return void
     */
    public function delete($models)
    {
        $index = $this->xunsearch->task($models->first()->searchableAs())->getIndex();

        $models->map(function ($model) use ($index) {
            $index->del($model->getKey());
        });

        $index->flushIndex();
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  Builder  $builder
     * @return mixed
     */
    public function search(Builder $builder)
    {
        return $this->performSearch($builder, array_filter([
            'hitsPerPage' => $builder->limit,
        ]));
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  Builder  $builder
     * @param  int  $perPage
     * @param  int  $page
     * @return mixed
     */
    public function paginate(Builder $builder, $perPage, $page)
    {
        $result = $this->performSearch($builder, [
            'hitsPerPage' =>(int)  $perPage,
            'page' =>$page ?? 0 - 1,
        ]);
        $result['pageCount'] = $this->xunsearch->task($builder->index ?: $builder->model->searchableAs())->getSearch()->getLastCount();
        return $result;
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  Builder  $builder
     * @param  array  $options
     * @return mixed
     */
    protected function performSearch(Builder $builder, array $options = [])
    {
        $search = $this->xunsearch->refresh($builder->index ?: $builder->model->searchableAs())->getSearch();

        if ($builder->callback) {
            return call_user_func(
                $builder->callback,
                $search,
                $builder->query,
                $options
            );
        }

        $search->setFuzzy()->setQuery($builder->query);

        collect($builder->wheres)->map(function ($value, $key) use ($search) {
            $search->addRange($key, $value, $value);
        });

        $offset = 0;
        $perPage = $options['hitsPerPage'];
        if (!empty($options['page'])) {
            $offset = $perPage * $options['page'];
        }
        return $search->setLimit($perPage, $offset)->search();
    }

    /**
     * Get the filter array for the query.
     *
     * @param  Builder  $builder
     * @return array
     */
    protected function filters(Builder $builder)
    {
        return collect($builder->wheres)->map(function ($value, $key) {
            if (is_array($value)) {
                return ['terms' => [$key => $value]];
            }
            return ['match_phrase' => [$key => $value]];
        })->values()->all();
    }

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param  mixed  $results
     * @return \Illuminate\Support\Collection
     */
    public function mapIds($results)
    {
        $hits = collect($results);
        $key = key($hits->first());
        return $hits->pluck($key)->values();
    }

    /**
     * Map the given results to instances of the given model.
     *
     * @param  mixed  $results
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return Collection
     */
    public function map(Builder $builder, $results, $model)
    {
        if (count($results) === 0) {
            return $model->newCollection();
        }
        $objectIds = collect($results)->pluck($model->getKeyName())->whereNotNull()->values()->all();
        $objectIdPositions = array_flip($objectIds);
        $fff = $model->getScoutModelsByIds(
            $builder, $objectIds
        )->filter(function ($model) use ($objectIds) {
            return in_array($model->getScoutKey(), $objectIds);
        })->sortBy(function ($model) use ($objectIdPositions) {
            return $objectIdPositions[$model->getScoutKey()];
        })->values();
        return $fff;
    }

    public function lazyMap(Builder $builder, $results, $model)
    {
        if (count($results) === 0) {
            return LazyCollection::make($model->newCollection());
        }

        $objectIds = collect($results)->pluck($model->getKeyName())->values()->all();
        $objectIdPositions = array_flip($objectIds);

        return $model->queryScoutModelsByIds(
            $builder, $objectIds
        )->cursor()->filter(function ($model) use ($objectIds) {
            return in_array($model->getScoutKey(), $objectIds);
        })->sortBy(function ($model) use ($objectIdPositions) {
            return $objectIdPositions[$model->getScoutKey()];
        })->values();
    }

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param  mixed  $results
     * @return int
     */
    public function getTotalCount($results)
    {
        //$this->xunsearch->search->getLastCount();
        return $results['pageCount'];

    }

    /**
     * Flush all of the model's records from the engine.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function flush($model)
    {
        $model->newQuery()
            ->orderBy($model->getKeyName())
            ->unsearchable();
    }

    /**
     * Generates the sort if theres any.
     *
     * @param  Builder $builder
     * @return array|null
     */
    protected function sort($builder)
    {
        if (count($builder->orders) == 0) {
            return null;
        }

        return collect($builder->orders)->map(function ($order) {
            return [$order['column'] => $order['direction']];
        })->toArray();
    }
    /**
     * Determine if the given model uses soft deletes.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return bool
     */
    protected function usesSoftDelete($model)
    {
        return in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($model));
    }
    /**
     * Dynamically call the MeiliSearch client instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->xunsearch->$method(...$parameters);
    }
}

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
namespace Shopwwi\WebmanScout;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Scope;
use Psr\EventDispatcher\EventDispatcherInterface;
use Shopwwi\WebmanScout\Events\ModelsFlushed;
use Shopwwi\WebmanScout\Events\ModelsImported;

class SearchableScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(EloquentBuilder $builder, Model $model)
    {
        //
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(EloquentBuilder $builder)
    {
        $builder->macro('searchable', function (EloquentBuilder $builder, $chunk = null) {
            $builder->chunkById($chunk ?: config('plugin.shopwwi.scout.app.chunk.searchable', 500), function ($models) {
                $models->filter->shouldBeSearchable()->searchable();
                $mode = new ModelsImported($models);
                event($mode);

            });
        });

        $builder->macro('unsearchable', function (EloquentBuilder $builder, $chunk = null) {
            $builder->chunkById($chunk ?: config('plugin.shopwwi.scout.app.chunk.unsearchable', 500), function ($models) {
                $models->unsearchable();
                event(new ModelsFlushed($models));
            });
        });

        HasManyThrough::macro('searchable', function ($chunk = null) {
            $this->chunkById($chunk ?: config('plugin.shopwwi.scout.app.chunk.searchable', 500), function ($models) {
                $models->filter->shouldBeSearchable()->searchable();
                event(new ModelsImported($models));
            });
        });

        HasManyThrough::macro('unsearchable', function ($chunk = null) {
            $this->chunkById($chunk ?: config('plugin.shopwwi.scout.app.chunk.searchable', 500), function ($models) {
                $models->unsearchable();
                event(new ModelsFlushed($models));
            });
        });
    }
}

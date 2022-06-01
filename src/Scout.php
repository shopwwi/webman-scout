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

use Shopwwi\WebmanScout\Jobs\MakeSearchable;
use Shopwwi\WebmanScout\Jobs\RemoveFromSearch;

class Scout
{
    /**
     * The job class that should make models searchable.
     *
     * @var string
     */
    public static $makeSearchableJob = MakeSearchable::class;

    /**
     * The job that should remove models from the search index.
     *
     * @var string
     */
    public static $removeFromSearchJob = RemoveFromSearch::class;

    /**
     * Specify the job class that should make models searchable.
     *
     * @param  string  $class
     * @return void
     */
    public static function makeSearchableUsing(string $class)
    {
        static::$makeSearchableJob = $class;
    }

    /**
     * Specify the job class that should remove models from the search index.
     *
     * @param  string  $class
     * @return void
     */
    public static function removeFromSearchUsing(string $class)
    {
        static::$removeFromSearchJob = $class;
    }
}

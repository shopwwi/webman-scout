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
namespace Shopwwi\WebmanScout\Attributes;

use Attribute;
use Illuminate\Support\Arr;

#[Attribute]
class SearchUsingPrefix
{
    /**
     * The prefix search columns.
     *
     * @var array
     */
    public $columns = [];

    /**
     * Create a new attribute instance.
     *
     * @param  array|string  $columns
     * @return void
     */
    public function __construct($columns)
    {
        $this->columns = Arr::wrap($columns);
    }
}

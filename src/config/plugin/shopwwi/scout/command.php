<?php
/**
 *-------------------------------------------------------------------------p*
 *
 *-------------------------------------------------------------------------h*
 * @copyright  Copyright (c) 2015-2022 Shopwwi Inc. (http://www.shopwwi.com)
 *-------------------------------------------------------------------------c*
 * @license    http://www.shopwwi.com        s h o p w w i . c o m
 *-------------------------------------------------------------------------e*
 * @link       http://www.shopwwi.com by 无锡豚豹科技
 *-------------------------------------------------------------------------n*
 * @since      shopwwi豚豹·PHP商城系统
 *-------------------------------------------------------------------------t*
 */

use Shopwwi\WebmanScout\Command\DeleteIndexCommand;
use Shopwwi\WebmanScout\Command\FlushCommand;
use Shopwwi\WebmanScout\Command\ImportCommand;
use Shopwwi\WebmanScout\Command\IndexCommand;

return [
    IndexCommand::class,
    ImportCommand::class,
    FlushCommand::class,
    DeleteIndexCommand::class
];

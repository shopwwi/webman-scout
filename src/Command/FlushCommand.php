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
namespace Shopwwi\WebmanScout\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class FlushCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected static $defaultName = 'scout:flush';
    protected static $defaultDescription = "Flush all of the model's records from the index";
    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('model', InputArgument::OPTIONAL, '模型');
    }
    /**
     * Execute the console command.
     *
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $class = $input->getArgument('model');

        $model = new $class;

        $model::removeAllFromSearch();

        $output->writeln('All ['.$class.'] records have been flushed.');
        return 1;
    }
}

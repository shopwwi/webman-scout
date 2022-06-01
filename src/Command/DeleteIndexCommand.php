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
use Shopwwi\WebmanScout\EngineManager;
use support\Container;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteIndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected static $defaultName = 'scout:delete-index';
    protected static $defaultDescription = 'Delete an index';

    protected function configure()
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'The name of the index');
    }

    /**
     * Execute the console command.
     *
     * @param  \Shopwwi\WebmanScout\EngineManager  $manager
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $name = $input->getArgument('name');
            app(EngineManager::class)->engine()->deleteIndex($name);
            $output->writeln('Index "'.$name.'" deleted.');
        } catch (\Exception $exception) {
            $output->writeln($exception->getMessage());
        }
        return 1;
    }
}

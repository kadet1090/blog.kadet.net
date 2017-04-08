<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

$console = new Application('Kadet\'s blog', 'n/a');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);
$console
    ->register('index')
    ->setDefinition(array(
        // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
    ))
    ->setDescription('Does things')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app, $console) {
        $indexer = new \Kadet\Blog\PostIndexer([
            'logger' => new Monolog\Logger('indexer', [ new Monolog\Handler\StreamHandler('php://output') ]),
        ]);
        $indexer->index(__DIR__.'/../posts/');
        $indexer->save();
    });

$console
    ->register('generate')
    ->setDefinition(array(
        // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
    ))
    ->setDescription('Does even more things')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app, $console) {
        $provider = new \Kadet\Blog\PostProvider(__DIR__.'/../var/cache.yml');

        $generators = [
            new \Kadet\Blog\PaginationGenerator($provider->getAll(), function(array $posts, \Kadet\Blog\PaginationGenerator $generator) use($app) {
                return $app['twig']->render('index.html.twig', [ 'posts' => $posts ]);
            }),
        ];

        /** @var \Kadet\Blog\PageGenerator $generator */
        foreach ($generators as $generator) {
            $generator->generate(__DIR__.'/../web');
        }
    });

return $console;

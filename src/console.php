<?php

use Kadet\Blog\PaginationGenerator;
use Kadet\Blog\PostPagination;
use Kadet\Blog\PostRepository;
use Kadet\Blog\PostsGenerator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Yaml\Yaml;


function renderer($view, array $context = []) {
    return function (Twig_Environment $twig, array $posts, PaginationGenerator $generator) use ($view, $context) {
        return $twig->render($view, array_merge([
            'posts' => $posts, 'pagination' => $generator->getPagination()
        ], $context));
    };
}

$console = new Application('Kadet\'s blog', 'n/a');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED,
    'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);
$console
    ->register('index')
    ->setDefinition([
        // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
    ])
    ->setDescription('Does things')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app, $console) {
        $indexer = new \Kadet\Blog\PostIndexer([
            'logger' => new Monolog\Logger('indexer', [new Monolog\Handler\StreamHandler('php://output')]),
        ]);
        $indexer->index($app['post.dir']);
        $indexer->save();
    });

$console
    ->register('generate')
    ->setDefinition([
        // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
    ])
    ->setDescription('Does even more things')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app, $console) {
        $provider = new PostRepository(Yaml::parse(file_get_contents(__DIR__ . '/../var/cache.yml')));

        $generators = array_merge(
            [
                new PaginationGenerator(
                    new PostPagination($provider->getAll()),
                    renderer('index/index.html.twig')
                ),
                new PostsGenerator($provider->getAll()),
            ],
//
            // categories
            array_map(function ($posts, $category) use ($app) {
                return new PaginationGenerator(
                    new PostPagination($posts, ['prefix' => "category/$category/"]),
                    renderer('index/category.html.twig', ['category' => $category])
                );
            }, $provider->getCategories(), array_keys($provider->getCategories())),

            // tags
            array_map(function ($posts, $tag) use ($app) {
                return new PaginationGenerator(
                    new PostPagination($posts, ['prefix' => "tag/$tag/"]),
                    renderer('index/tag.html.twig', ['tag' => $tag])
                );
            }, $provider->getTags(), array_keys($provider->getTags()))
        );

        /** @var \Kadet\Blog\PageGenerator $generator */
        foreach ($generators as $generator) {
            $generator->generate(__DIR__ . '/../web', $app['twig']);
        }
    });

return $console;

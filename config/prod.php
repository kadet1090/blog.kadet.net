<?php

// configure your app for the production environment

$app['twig.path']    = [__DIR__.'/../templates'];
$app['twig.options'] = ['cache' => __DIR__.'/../var/cache/twig'];

$app['post.cache'] = __DIR__.'/../var/cache/posts';
$app['post.dir']   = __DIR__.'/../posts';
<?php
/** @var \Silex\Application $app */
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->get('/', function () use ($app) {
    /** @var \Kadet\Blog\PostRepository $repository */
    $repository = $app['post.repository'];

    $pagination = new \Kadet\Blog\PostPagination($repository->getAll());

    return $app['twig']->render('index/index.html.twig', [
        'posts' => $pagination->get(),
        'pagination' => $pagination
    ]);
})->bind('homepage');

$app->get('/page-{page}.html', function ($page) use ($app) {
    /** @var \Kadet\Blog\PostRepository $repository */
    $repository = $app['post.repository'];

    $pagination = new \Kadet\Blog\PostPagination($repository->getAll());
    $pagination->set($page);

    return $app['twig']->render('index/index.html.twig', [
        'posts' => $pagination->get(),
        'pagination' => $pagination
    ]);
})->bind('page');

$app->get('/{year}/{post}.html', function ($year, $post) use ($app) {
    return $app['twig']->render('post.html.twig', [
        'post' => \Kadet\Blog\Models\Post::fromMarkdownFile($app['post.dir'].'/'.$post.'.md')
    ]);
})->bind('post');

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});

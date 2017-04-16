<?php


namespace Kadet\Blog;


use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PostServiceProvider implements ServiceProviderInterface
{
    private $_indexer;
    private $_repository;

    /**
     * PostServiceProvider constructor.
     *
     * @param array $indexer
     * @param array $repository
     */
    public function __construct(array $indexer = [], array $repository = [])
    {
        $this->_indexer = $indexer;
        $this->_repository = $repository;
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app A container instance
     */
    public function register(Container $app)
    {
        $app['post.indexer'] = function() use($app) {
            $indexer = new PostIndexer($this->_indexer);
            $indexer->index($app['post.dir']);

            return $indexer;
        };

        $app['post.repository'] = function() use ($app) {
            return new PostRepository($app['post.indexer']->all());
        };
    }
}
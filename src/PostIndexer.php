<?php


namespace Kadet\Blog;


use Kadet\Blog\Models\Post;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Yaml\Yaml;

class PostIndexer
{
    private $_config;
    private $_cached;


    /**
     * PostIndexer constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->_config = array_merge([
            'cache'  => __DIR__ . '/../var',
            'logger' => new NullLogger(),
        ], $config);

        $this->_cached     = $this->load('cache');
    }

    public function index($dir)
    {
        $directory = new \DirectoryIterator($dir);
        foreach ($directory as $file) {
            if($file->getExtension() != 'md') {
                continue;
            }

            $this->register($file);
        }
    }

    public function save()
    {
        uasort($this->_cached, function($a, $b) {
            return $a['date'] < $b['date'];
        });

        file_put_contents($this->_config['cache'] . '/cache.yml', Yaml::dump($this->_cached));
    }

    private function register(\SplFileInfo $file)
    {
        if ($this->cached($file)) {
            return;
        }

        $post = Post::fromMarkdownFile($file->getRealPath());
        if(!$post) {
            return;
        }

        $this->cache($file);
    }

    private function cached(\SplFileInfo $file)
    {
        $hash = md5($file->getRealPath());

        if (!isset($this->_cached[ $hash ])) {
            return false;
        }

        if ($this->_cached[ $hash ]['modified'] !== $file->getMTime()) {
            $this->logger()->info(sprintf('Found cached %s, but modification time differs, updating.',
                $file->getBasename()));

            return false;
        }

        $this->logger()->info(sprintf('Found cached %s, omitting.', $file->getBasename()));

        return $this->_cached[ $hash ];
    }

    private function cache(\SplFileInfo $file)
    {
        $post = Post::fromMarkdownFile($file->getPathname());

        $cache = array_merge([
            'slug' => $file->getBasename('.md')
        ], $post->getMetadata(), [
            'modified' => $file->getMTime(),
            'path'     => $file->getRealPath(),
            'title'    => $post->getTitle(),
        ]);

        $this->logger()->info(sprintf('Caching %s', $file->getBasename()), $cache);

        $this->_cached[md5($file->getRealPath())] = $cache;
    }

    private function load($thing)
    {
        $file = $this->_config['cache'] . '/' . $thing . '.yml';
        if (!file_exists($file)) {
            return [];
        }

        return Yaml::parse(file_get_contents($file));
    }

    /**
     * @return LoggerInterface
     */
    private function logger()
    {
        return $this->_config['logger'];
    }
}
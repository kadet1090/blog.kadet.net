<?php


namespace Kadet\Blog;


use Kadet\Blog\Models\Post;
use Symfony\Component\Yaml\Yaml;

class PostRepository
{
    private $_categories = [];
    private $_tags = [];
    private $_all = [];

    public function __construct(array $meta)
    {
        $this->process($meta);
    }

    private function process($posts)
    {
        foreach($posts as $metadata)
        {
            $post = Post::fromMarkdownFile($metadata['path']);

            $this->merge($this->_categories, $metadata['categories'], $post);
            $this->merge($this->_tags, $metadata['tags'], $post);
            $this->_all[] = $post;
        }
    }

    /**
     * @return Post[][]
     */
    public function getCategories(): array
    {
        return $this->_categories;
    }

    /**
     * @return Post[][]
     */
    public function getTags(): array
    {
        return $this->_tags;
    }

    /**
     * @return Post[]
     */
    public function getAll(): array
    {
        return $this->_all;
    }

    private function merge(array &$array, array $keys, $value) {
        foreach($keys as $key) {
            $array[$key][] = $value;
        }
    }
}
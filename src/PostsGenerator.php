<?php


namespace Kadet\Blog;


class PostsGenerator implements PageGenerator
{
    /** @var array|Models\Post[] */
    private $_posts;

    /**
     * PostsGenerator constructor.
     *
     * @param array|\Kadet\Blog\Models\Post[] $posts
     */
    public function __construct($posts)
    {
        $this->_posts = $posts;
    }

    public function generate(string $directory, $twig)
    {
        foreach ($this->_posts as $post) {
            $path = $directory.DIRECTORY_SEPARATOR.$post->getUri();
            if(!file_exists(dirname($path))) {
                mkdir(dirname($path), 0644, true);
            }

            file_put_contents($path, $twig->render('post.html.twig', [ 'post' => $post ]));
        }
    }
}
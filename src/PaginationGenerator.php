<?php


namespace Kadet\Blog;


class PaginationGenerator implements PageGenerator
{
    private $_config = [
        'perPage' => 5,
        'prefix'  => null,
        'format'  => ['index.html', 'page-%d.html'],
    ];

    private $_posts    = [];
    /** @var callable|null */
    private $_renderer = null;

    private $_current = null;

    /**
     * PaginationGenerator constructor.
     *
     * @param array    $posts
     * @param array    $config
     * @param callable $renderer
     *
     * @internal param int $perPage
     */
    public function __construct(array $posts, callable $renderer, array $config = [])
    {
        $this->_config   = array_merge($this->_config, $config);
        $this->_posts    = $posts;
        $this->_renderer = $renderer;
    }


    public function generate(string $directory)
    {
        // We assume that provided posts are in valid order
        for($page = 0; $page * $this->_config['perPage'] < count($this->_posts); $page++) {
            $path = $directory.'/'.$this->path($page);

            if(!file_exists(dirname($path))) {
                mkdir(dirname($path), 0644, true);
            }

            file_put_contents(
                $directory.'/'.$this->path($page),
                $this->render($page)
            );
        }
    }

    public function getPerPage()
    {
        return $this->_config['perPage'];
    }

    public function getPage()
    {
        return $this->_current;
    }

    public function getPreviousPageLink()
    {
        if($this->_current == 1) {
            return false;
        } elseif($this->_current == 2) {
            return $this->_config['prefix'];
        }

        return $this->path($this->_current - 1);
    }

    public function getNextPageLink()
    {
        if(($this->_current+1)*$this->_config['perPage'] > count($this->_posts)) {
            return false;
        }

        return $this->path($this->_current + 1);
    }

    private function path($page)
    {
        return $this->_config['prefix'].sprintf($this->_config['format'][$page !== 0], $page + 1);
    }

    private function render($page)
    {
        $renderer = $this->_renderer;
        return $renderer(
            array_slice($this->_posts, $page * $this->getPerPage(), $this->getPerPage()),
            $this
        );
    }
}
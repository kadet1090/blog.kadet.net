<?php


namespace Kadet\Blog;


use Kadet\Blog\Helpers\Pagination;

class PostPagination implements Pagination
{
    private $_current = null;

    protected $_posts  = null;
    protected $_config = [
        'perPage' => 5,
        'prefix'  => null,
        'format'  => ['index.html', 'page-%d.html'],
    ];

    /**
     * PostPagination constructor.
     *
     * @param array $posts
     * @param array $config
     */
    public function __construct($posts, array $config = [])
    {
        $this->_posts  = $posts;
        $this->_config = array_merge($this->_config, $config);
    }


    public function set($page)
    {
        $this->_current = $page - 1;
        return $this;
    }

    public function get()
    {
        return array_slice(
            $this->_posts,
            $this->_current*$this->getPerPage(),
            $this->getPerPage()
        );
    }

    public function getPerPage()
    {
        return $this->_config['perPage'];
    }

    public function getPage()
    {
        return $this->_current + 1;
    }

    public function getPreviousPage()
    {
        if(!$this->_current) {
            return false;
        }

        return $this->path($this->getPage() - 1);
    }

    public function getNextPage()
    {
        if($this->getPage()*$this->_config['perPage'] > count($this->_posts)) {
            return false;
        }

        return $this->path($this->getPage() + 1);
    }

    private function path($page)
    {
        return $this->_config['prefix'].sprintf($this->_config['format'][$page !== 1], $page);
    }

    public function getUri()
    {
        return $this->path($this->getPage());
    }

    public function getPageCount()
    {
        return ceil(count($this->_posts) / $this->getPerPage());
    }
}
<?php


namespace Kadet\Blog;


use Kadet\Blog\Helpers\Pagination;

class PaginationGenerator implements PageGenerator
{
    /** @var callable|null */
    private $_renderer   = null;
    private $_pagination = null;

    /**
     * PaginationGenerator constructor.
     *
     * @param Pagination $pagination
     * @param callable   $renderer
     */
    public function __construct(Pagination $pagination, callable $renderer)
    {
        $this->_pagination = $pagination;
        $this->_renderer   = $renderer;
    }

    public function generate(string $directory, $twig)
    {
        // We assume that provided posts are in valid order
        for($page = 0, $count = $this->_pagination->getPageCount(); $page < $count; $page++) {
            $this->_pagination->set($page + 1);

            $path = $directory.'/'.$this->_pagination->getUri();
            if(!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            file_put_contents($path, $this->render($twig));
        }
    }

    public function getPagination()
    {
        return $this->_pagination;
    }

    private function render($twig)
    {
        return ($this->_renderer)($twig, $this->_pagination->get(), $this);
    }
}
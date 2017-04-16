<?php


namespace Kadet\Blog\Helpers;


interface Pagination
{
    public function getPerPage();
    public function getPageCount();

    public function getPage();
    public function set($page);

    public function get();

    public function getUri();
    public function getNextPage();
    public function getPreviousPage();
}
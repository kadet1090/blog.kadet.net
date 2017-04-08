<?php


namespace Kadet\Blog;


interface PageGenerator
{
    public function generate(string $directory);
}
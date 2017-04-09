<?php


namespace Kadet\Blog\Models;

use DateTime;
use Kadet\Blog\Helpers\ParsedownExtra;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Post
{
    /**
     * @var string
     */
    private $_title;

    /**
     * @var DateTime
     */
    private $_created;

    /** @var array */
    private $_metadata;

    /** @var string */
    private $_content;

    /** @var string */
    private $_slug;

    public function meta($key)
    {
        return $this->_metadata[$key] ?? false;
    }

    public function html(): string
    {
        return $this->_content;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->_title;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->_created;
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->_metadata['categories'] ?? [];
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->_metadata['tags'] ?? [];
    }

    public function getSlug()
    {
        return $this->_slug;
    }

    public function getUri()
    {
        return sprintf("%d/%s.html", $this->getCreated()->format('Y'), $this->getSlug());
    }

    /**
     * @param $file
     *
     * @return false|Post
     */
    public static function fromMarkdownFile($file)
    {
        $content = file_get_contents($file);
        if(!preg_match('/^-{3,}\R(?P<metadata>.*?)\R-{3,}\R/sm', $content, $matches)) {
            return false;
        }

        try {
            if(!self::validateMetadata($metadata = Yaml::parse($matches['metadata']))) {
                throw new ParseException('No date in metadata section.');
            }

            $markdown = substr($content, strlen($matches[0]));
            list($title, $markdown) = preg_split('/\R{2,}/m', $markdown, 2);

            $post = new Post();
            $post->_metadata = $metadata;
            $post->_title = substr($title, 2);
            $post->_created = DateTime::createFromFormat('U', $metadata['date']);
            $post->_content = ParsedownExtra::instance()->parse($markdown);
            $post->_slug    = $metadata['slug'] ?? basename($file, '.md');

            return $post;
        } catch (ParseException $exception) {
            return false;
        }
    }

    private static function saveInCache($path, Post $post)
    {
        $path = md5($path);

    }

    private static function validateMetadata($metadata)
    {
        return isset($metadata['date']) && is_int($metadata['date']);
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->_metadata;
    }
}
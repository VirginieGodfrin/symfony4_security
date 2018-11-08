<?php

namespace App\Service;

use Michelf\MarkdownInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Security\Core\Security;

class MarkdownHelper
{
    private $cache;
    private $markdown;
    private $logger;
    private $isDebug;
    private $security;

    public function __construct(AdapterInterface $cache, MarkdownInterface $markdown, LoggerInterface $markdownLogger, bool $isDebug, security $security)
    {
        $this->cache = $cache;
        $this->markdown = $markdown;
        $this->logger = $markdownLogger;
        $this->isDebug = $isDebug;
        $this->security = $security;
    }
    // how can we access the current User object from inside a service?
    // This code parses the article content through markdown and caches it. 
    // But also, if it sees the word "bacon" in the content ... 
    // which every article has in our fixtures, it logs this message.
    // The answer is... of course - by using another service (security - getUser() and isGranted())
    public function parse(string $source): string
    {
        if (stripos($source, 'bacon') !== false) {
            // Unrelated to security, every method on the logger, like info() , debug() or alert() , has two arguments. 
            // The first is the message string. The second is an optional array called a "context". 
            // This is just an array of any extra info that you want to include with the log message. 
            // I invented a user key and set it to the User object.
            $this->logger->info('They are talking about bacon again!', [
                'user' => $this->security->getUser()
            ]);
        }

        // skip caching entirely in debug
        if ($this->isDebug) {
            return $this->markdown->transform($source);
        }

        $item = $this->cache->getItem('markdown_'.md5($source));
        if (!$item->isHit()) {
            $item->set($this->markdown->transform($source));
            $this->cache->save($item);
        }

        return $item->get();
    }
}

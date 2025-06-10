<?php declare(strict_types=1);
/**
*
* @package Ultimate phpBB SEO Friendly URL
* @copyright (c) 2017 www.phpBB-SEO.ir
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbseo\usu\Service;

use phpbb\cache\driver\driver_interface;

class SeoCache
{
    private const CACHE_PREFIX = 'phpbbseo_';
    private const DEFAULT_TTL = 3600;

    public function __construct(
        private readonly driver_interface $cache,
        private readonly int $defaultTtl = self::DEFAULT_TTL
    ) {}

    public function getForumUrl(int $forumId): ?string
    {
        $data = $this->cache->get(self::CACHE_PREFIX . "forum_url_{$forumId}");
        return $data === false ? null : $data;
    }

    public function setForumUrl(int $forumId, string $url): bool
    {
        return $this->cache->put(
            self::CACHE_PREFIX . "forum_url_{$forumId}", 
            $url, 
            $this->defaultTtl
        );
    }

    public function getTopicUrl(int $topicId): ?string
    {
        $data = $this->cache->get(self::CACHE_PREFIX . "topic_url_{$topicId}");
        return $data === false ? null : $data;
    }

    public function setTopicUrl(int $topicId, string $url): bool
    {
        return $this->cache->put(
            self::CACHE_PREFIX . "topic_url_{$topicId}", 
            $url, 
            $this->defaultTtl
        );
    }

    public function getUserUrl(int $userId): ?string
    {
        $data = $this->cache->get(self::CACHE_PREFIX . "user_url_{$userId}");
        return $data === false ? null : $data;
    }

    public function setUserUrl(int $userId, string $url): bool
    {
        return $this->cache->put(
            self::CACHE_PREFIX . "user_url_{$userId}", 
            $url, 
            $this->defaultTtl
        );
    }

    public function clearAll(): bool
    {
        return $this->cache->destroy(self::CACHE_PREFIX, true);
    }

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        $fullKey = self::CACHE_PREFIX . $key;
        $data = $this->cache->get($fullKey);
        
        if ($data === false) {
            $data = $callback();
            $this->cache->put($fullKey, $data, $ttl);
        }
        
        return $data;
    }
}

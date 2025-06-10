<?php declare(strict_types=1);
/**
*
* @package Ultimate phpBB SEO Friendly URL
* @copyright (c) 2017 www.phpBB-SEO.ir
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbseo\usu\Service;

use phpbb\request\request;
use phpbbseo\usu\Config\SeoConfig;
use phpbbseo\usu\Exception\InvalidUrlException;

class UrlGenerator
{
    private array $urlCache = [
        'forum' => [],
        'topic' => [],
        'user' => [],
        'group' => []
    ];

    public function __construct(
        private readonly SeoConfig $config,
        private readonly SeoCache $cache,
        private readonly UrlValidator $validator,
        private readonly request $request
    ) {}

    public function generateForumUrl(int $forumId, string $title = ''): string
    {
        if ($forumId <= 0) {
            throw new InvalidUrlException('Invalid forum ID');
        }

        if (isset($this->urlCache['forum'][$forumId])) {
            return $this->urlCache['forum'][$forumId];
        }

        $cachedUrl = $this->cache->getForumUrl($forumId);
        if ($cachedUrl !== null) {
            return $this->urlCache['forum'][$forumId] = $cachedUrl;
        }

        if (empty($title)) {
            $url = $this->config->static->forum . $forumId;
        } else {
            $formattedTitle = $this->validator->formatUrl($title, 'forum');
            
            if ($this->config->modrType >= 2) {
                $url = $formattedTitle . ($this->config->remIds ? '' : $this->config->delimiters->forum . $forumId);
            } else {
                $url = $this->config->static->forum . $forumId;
            }
        }

        $this->cache->setForumUrl($forumId, $url);
        return $this->urlCache['forum'][$forumId] = $url;
    }

    public function generateTopicUrl(int $topicId, string $title = '', int $forumId = 0): string
    {
        if ($topicId <= 0) {
            throw new InvalidUrlException('Invalid topic ID');
        }

        if (isset($this->urlCache['topic'][$topicId])) {
            return $this->urlCache['topic'][$topicId];
        }

        $cachedUrl = $this->cache->getTopicUrl($topicId);
        if ($cachedUrl !== null) {
            return $this->urlCache['topic'][$topicId] = $cachedUrl;
        }

        $parentForum = '';
        if ($this->config->virtualFolder && $forumId > 0) {
            $parentForum = $this->generateForumUrl($forumId) . '/';
        }

        if (empty($title) || $this->config->modrType <= 2) {
            $url = $parentForum . $this->config->static->topic . $topicId;
        } else {
            $formattedTitle = $this->validator->formatUrl($title, 'topic');
            $url = $parentForum . $formattedTitle . $this->config->delimiters->topic . $topicId;
        }

        $this->cache->setTopicUrl($topicId, $url);
        return $this->urlCache['topic'][$topicId] = $url;
    }

    public function generateUserUrl(int $userId, string $username = ''): string
    {
        if ($userId <= 0) {
            throw new InvalidUrlException('Invalid user ID');
        }

        if (isset($this->urlCache['user'][$userId])) {
            return $this->urlCache['user'][$userId];
        }

        $cachedUrl = $this->cache->getUserUrl($userId);
        if ($cachedUrl !== null) {
            return $this->urlCache['user'][$userId] = $cachedUrl;
        }

        if (!$this->config->profileInj || empty($username)) {
            $url = $this->config->static->user . $userId;
        } else {
            if ($this->config->profileNoids) {
                $url = $this->config->static->user . '/' . $this->validator->formatUrl($username, 'user');
            } else {
                $formattedName = $this->validator->formatUrl($username, 'user');
                $url = $formattedName . $this->config->delimiters->user . $userId;
            }
        }

        $this->cache->setUserUrl($userId, $url);
        return $this->urlCache['user'][$userId] = $url;
    }

    public function rewriteUrl(string $url, array $params = [], bool $isAmp = true): ?string
    {
        if (!$this->config->urlRewrite) {
            return null;
        }

        // Parse URL components
        $parsedUrl = parse_url($url);
        if (!$parsedUrl) {
            return null;
        }

        $path = $parsedUrl['path'] ?? '';
        $queryString = $parsedUrl['query'] ?? '';
        
        // Parse existing query parameters
        $queryParams = [];
        if ($queryString) {
            parse_str($queryString, $queryParams);
        }
        
        // Merge with additional parameters
        $queryParams = array_merge($queryParams, $params);

        // Handle different page types
        $filename = basename($path, '.php');
        
        return match ($filename) {
            'viewtopic' => $this->rewriteTopicUrl($queryParams),
            'viewforum' => $this->rewriteForumUrl($queryParams),
            'memberlist' => $this->rewriteMemberUrl($queryParams),
            default => null
        };
    }

    private function rewriteTopicUrl(array $params): ?string
    {
        $topicId = (int) ($params['t'] ?? 0);
        $forumId = (int) ($params['f'] ?? 0);
        $postId = (int) ($params['p'] ?? 0);
        $start = (int) ($params['start'] ?? 0);

        if ($postId > 0) {
            return $this->config->static->post . $postId . $this->config->extensions->post;
        }

        if ($topicId > 0) {
            $url = $this->generateTopicUrl($topicId, '', $forumId);
            
            if ($start > 0) {
                $url .= $this->config->delimiters->start . $start;
            }
            
            return $url . $this->config->extensions->topic;
        }

        return null;
    }

    private function rewriteForumUrl(array $params): ?string
    {
        $forumId = (int) ($params['f'] ?? 0);
        $start = (int) ($params['start'] ?? 0);

        if ($forumId > 0) {
            $url = $this->generateForumUrl($forumId);
            
            if ($start > 0) {
                $url .= $this->config->delimiters->start . $start;
            }
            
            return $url . $this->config->extensions->forum;
        }

        return null;
    }

    private function rewriteMemberUrl(array $params): ?string
    {
        $mode = $params['mode'] ?? '';
        $userId = (int) ($params['u'] ?? 0);

        if ($mode === 'viewprofile' && $userId > 0) {
            return $this->generateUserUrl($userId) . $this->config->extensions->user;
        }

        return null;
    }
}

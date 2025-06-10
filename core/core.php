<?php declare(strict_types=1);
/**
*
* @package Ultimate phpBB SEO Friendly URL
* @copyright (c) 2017 www.phpBB-SEO.ir
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbseo\usu\core;

use phpbb\request\request;
use phpbb\user;
use phpbb\auth\auth;
use phpbbseo\usu\Config\SeoConfig;
use phpbbseo\usu\Service\UrlGenerator;
use phpbbseo\usu\Service\RedirectHandler;

/**
* Core SEO class - legacy wrapper for backward compatibility
*/
class core
{
    public int $modrtype;
    public array $seo_path = [];
    public array $seo_url = [];

    public function __construct(
        private readonly SeoConfig $config,
        private readonly UrlGenerator $urlGenerator,
        private readonly RedirectHandler $redirectHandler,
        private readonly request $request,
        private readonly user $user,
        private readonly auth $auth,
        private readonly string $phpbb_root_path,
        private readonly string $php_ext
    ) {
        $this->modrtype = $this->config->modrType;
        $this->initializePaths();
    }

    private function initializePaths(): void
    {
        $ssl = $this->request->is_secure();
        $serverProtocol = $ssl ? 'https://' : 'http://';
        $serverName = $this->request->server('HTTP_HOST') ?: 'localhost';
        $scriptPath = trim($this->request->server('SCRIPT_NAME', ''), '/');
        $scriptPath = dirname($scriptPath);
        $scriptPath = ($scriptPath === '.' || $scriptPath === '/') ? '' : $scriptPath . '/';

        $this->seo_path = [
            'root_url' => $serverProtocol . $serverName . '/',
            'phpbb_url' => $serverProtocol . $serverName . '/' . $scriptPath,
            'phpbb_script' => $scriptPath,
            'canonical' => ''
        ];
    }

    // Legacy methods for backward compatibility
    public function url_rewrite(string $url, mixed $params = false, bool $isAmp = true, mixed $sessionId = false): string
    {
        $rewritten = $this->urlGenerator->rewriteUrl($url, is_array($params) ? $params : [], $isAmp);
        return $rewritten ?? $url;
    }

    public function format_url(string $title, string $type = 'topic'): string
    {
        return $this->urlGenerator->formatUrl($title, $type);
    }

    public function prepare_forum_url(array &$forumData): string
    {
        $forumId = (int) $forumData['forum_id'];
        $forumName = $forumData['forum_name'] ?? '';
        
        $url = $this->urlGenerator->generateForumUrl($forumId, $forumName);
        $this->seo_url['forum'][$forumId] = $url;
        
        return $url;
    }

    public function prepare_topic_url(array &$topicData, int $forumId = 0): string
    {
        $topicId = (int) $topicData['topic_id'];
        $topicTitle = $topicData['topic_title'] ?? '';
        $forumId = $forumId ?: (int) ($topicData['forum_id'] ?? 0);
        
        $url = $this->urlGenerator->generateTopicUrl($topicId, $topicTitle, $forumId);
        $this->seo_url['topic'][$topicId] = $url;
        
        return $url;
    }

    public function set_user_url(string $username, int $userId): string
    {
        $url = $this->urlGenerator->generateUserUrl($userId, $username);
        $this->seo_url['user'][$userId] = $url;
        
        return $url;
    }

    public function zero_dupe(string $url = '', string $uri = '', string $path = ''): bool
    {
        if (!$this->config->urlRewrite) {
            return false;
        }

        $currentUrl = $uri ?: $this->request->server('REQUEST_URI', '');
        $canonicalUrl = $url ?: $this->buildCanonicalUrl();

        if ($this->redirectHandler->needsRedirect($currentUrl, $canonicalUrl)) {
            $this->redirectHandler->redirect($canonicalUrl);
        }

        return false;
    }

    private function buildCanonicalUrl(): string
    {
        $requestUri = $this->request->server('REQUEST_URI', '');
        return $this->seo_path['phpbb_url'] . ltrim($requestUri, '/');
    }

    // Placeholder methods for compatibility
    public function check_config(): void {}
    public function read_config(): bool { return true; }
    public function seo_redirect(string $url, int $code = 301): never
    {
        $this->redirectHandler->redirect($url, $code);
    }
}

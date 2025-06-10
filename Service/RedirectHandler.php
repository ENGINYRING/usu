<?php declare(strict_types=1);
/**
*
* @package Ultimate phpBB SEO Friendly URL
* @copyright (c) 2017 www.phpBB-SEO.ir
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbseo\usu\Service;

use phpbbseo\usu\Config\SeoConfig;

class RedirectHandler
{
    public function __construct(
        private readonly SeoConfig $config
    ) {}

    public function redirect(string $url, int $statusCode = 301): never
    {
        if (!in_array($statusCode, [301, 302, 307, 308], true)) {
            $statusCode = 301;
        }

        if (headers_sent()) {
            echo '<script>window.location.href="' . htmlspecialchars($url, ENT_QUOTES) . '";</script>';
            exit;
        }

        // Validate URL to prevent header injection
        if (!$this->isValidRedirectUrl($url)) {
            send_status_line(400, 'Bad Request');
            exit;
        }

        $statusMessages = [
            301 => 'Moved Permanently',
            302 => 'Found',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect'
        ];

        send_status_line($statusCode, $statusMessages[$statusCode]);
        header('Location: ' . $url);
        exit;
    }

    public function needsRedirect(string $currentUrl, string $canonicalUrl): bool
    {
        if (!$this->config->urlRewrite) {
            return false;
        }

        // Normalize URLs for comparison
        $currentUrl = $this->normalizeUrl($currentUrl);
        $canonicalUrl = $this->normalizeUrl($canonicalUrl);

        return $currentUrl !== $canonicalUrl;
    }

    private function isValidRedirectUrl(string $url): bool
    {
        // Check for header injection attempts
        if (strpos($url, "\n") !== false || strpos($url, "\r") !== false) {
            return false;
        }

        // Validate URL format
        $parsedUrl = parse_url($url);
        if (!$parsedUrl) {
            return false;
        }

        // Only allow http/https protocols
        if (isset($parsedUrl['scheme']) && !in_array($parsedUrl['scheme'], ['http', 'https'], true)) {
            return false;
        }

        return true;
    }

    private function normalizeUrl(string $url): string
    {
        // Remove trailing slashes and convert to lowercase
        $url = rtrim(strtolower($url), '/');
        
        // Remove common query parameters that don't affect content
        $url = preg_replace('/[?&](sid|hilit)=[^&]*/', '', $url);
        
        // Clean up any remaining ? or & at the end
        $url = rtrim($url, '?&');

        return $url;
    }
}

<?php declare(strict_types=1);
/**
*
* @package Ultimate phpBB SEO Friendly URL
* @copyright (c) 2017 www.phpBB-SEO.ir
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbseo\usu\Service;

use phpbbseo\usu\Exception\InvalidUrlException;

class UrlValidator
{
    private const MAX_URL_LENGTH = 255;
    private const ALLOWED_CHARS = '/^[a-zA-Z0-9\-_\/\.]+$/';
    private const RESERVED_WORDS = [
        'admin', 'api', 'www', 'ftp', 'mail', 'email', 'blog', 'forum',
        'user', 'users', 'member', 'members', 'group', 'groups',
        'topic', 'topics', 'post', 'posts', 'download', 'downloads'
    ];

    public function validateUrl(string $url): string
    {
        $url = trim($url, '/ ');
        
        if (empty($url)) {
            throw new InvalidUrlException('URL cannot be empty');
        }

        if (strlen($url) > self::MAX_URL_LENGTH) {
            throw new InvalidUrlException('URL too long (max ' . self::MAX_URL_LENGTH . ' characters)');
        }

        if (!preg_match(self::ALLOWED_CHARS, $url)) {
            throw new InvalidUrlException('URL contains invalid characters');
        }

        if (in_array(strtolower($url), self::RESERVED_WORDS, true)) {
            throw new InvalidUrlException('URL uses reserved word');
        }

        return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    }

    public function formatUrl(string $title, string $type = 'topic'): string
    {
        // Remove BBCode tags
        $url = preg_replace('/\[.*?\]/', '', $title);
        
        // Convert HTML entities
        $url = html_entity_decode($url, ENT_QUOTES, 'UTF-8');
        
        // Remove special characters and convert to lowercase
        $url = strtolower(trim($url));
        $url = preg_replace('/[^a-z0-9\s\-_]/', '', $url);
        
        // Replace spaces and multiple hyphens with single hyphen
        $url = preg_replace('/[\s\-_]+/', '-', $url);
        
        // Trim hyphens from start and end
        $url = trim($url, '-');
        
        return empty($url) ? $type : $url;
    }

    public function isValidPath(string $path): bool
    {
        // Check for directory traversal attempts
        if (strpos($path, '..') !== false) {
            return false;
        }

        // Check for null bytes
        if (strpos($path, "\0") !== false) {
            return false;
        }

        return true;
    }
}

<?php declare(strict_types=1);
/**
*
* @package Ultimate phpBB SEO Friendly URL
* @copyright (c) 2017 www.phpBB-SEO.ir
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbseo\usu\Config;

use phpbb\config\config;

readonly class SeoConfig
{
    public function __construct(
        public bool $urlRewrite = false,
        public int $modrType = 2,
        public bool $sqlRewrite = false,
        public bool $profileInj = false,
        public bool $profileVfolder = false,
        public bool $profileNoids = false,
        public bool $rewriteUsermsg = false,
        public bool $remSid = false,
        public bool $remHilit = true,
        public bool $remSmallWords = false,
        public bool $virtualFolder = false,
        public bool $virtualRoot = false,
        public bool $cacheLayer = true,
        public bool $remIds = false,
        public bool $redirect404Forum = false,
        public bool $redirect404Topic = false,
        public SeoDelimiters $delimiters = new SeoDelimiters(),
        public SeoExtensions $extensions = new SeoExtensions(),
        public SeoStatic $static = new SeoStatic()
    ) {}

    public static function fromPhpbbConfig(config $config): self
    {
        return new self(
            urlRewrite: (bool) ($config['seo_url_rewrite'] ?? false),
            modrType: (int) ($config['seo_modr_type'] ?? 2),
            sqlRewrite: (bool) ($config['seo_sql_rewrite'] ?? false),
            profileInj: (bool) ($config['seo_profile_inj'] ?? false),
            profileVfolder: (bool) ($config['seo_profile_vfolder'] ?? false),
            profileNoids: (bool) ($config['seo_profile_noids'] ?? false),
            rewriteUsermsg: (bool) ($config['seo_rewrite_usermsg'] ?? false),
            remSid: (bool) ($config['seo_rem_sid'] ?? false),
            remHilit: (bool) ($config['seo_rem_hilit'] ?? true),
            remSmallWords: (bool) ($config['seo_rem_small_words'] ?? false),
            virtualFolder: (bool) ($config['seo_virtual_folder'] ?? false),
            virtualRoot: (bool) ($config['seo_virtual_root'] ?? false),
            cacheLayer: (bool) ($config['seo_cache_layer'] ?? true),
            remIds: (bool) ($config['seo_rem_ids'] ?? false),
            redirect404Forum: (bool) ($config['seo_redirect_404_forum'] ?? false),
            redirect404Topic: (bool) ($config['seo_redirect_404_topic'] ?? false)
        );
    }
}

readonly class SeoDelimiters
{
    public function __construct(
        public string $forum = '-f',
        public string $topic = '-t',
        public string $user = '-u',
        public string $group = '-g',
        public string $start = '-',
        public string $sr = '-',
        public string $file = '/'
    ) {}
}

readonly class SeoExtensions
{
    public function __construct(
        public string $forum = '.html',
        public string $topic = '.html',
        public string $post = '.html',
        public string $user = '.html',
        public string $group = '.html',
        public string $index = '',
        public string $globalAnnounce = '/',
        public string $leaders = '.html',
        public string $atopic = '.html',
        public string $utopic = '.html',
        public string $npost = '.html',
        public string $urpost = '.html',
        public string $pagination = '.html'
    ) {}
}

readonly class SeoStatic
{
    public function __construct(
        public string $forum = 'forum',
        public string $topic = 'topic',
        public string $post = 'post',
        public string $user = 'member',
        public string $group = 'group',
        public string $index = '',
        public string $globalAnnounce = 'announces',
        public string $leaders = 'the-team',
        public string $atopic = 'active-topics',
        public string $utopic = 'unanswered',
        public string $npost = 'newposts',
        public string $urpost = 'unreadposts',
        public string $pagination = 'page'
    ) {}
}

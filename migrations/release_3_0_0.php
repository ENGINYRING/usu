<?php declare(strict_types=1);
/**
*
* @package Ultimate phpBB SEO Friendly URL
* @copyright (c) 2017 www.phpBB-SEO.ir
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbseo\usu\migrations;

use phpbb\db\migration\migration;

class release_3_0_0 extends migration
{
    public function effectively_installed(): bool
    {
        return version_compare($this->config['seo_usu_version'] ?? '0.0.0', '3.0.0', '>=');
    }

    static public function depends_on(): array
    {
        return ['\phpbbseo\usu\migrations\release_2_0_0_b2'];
    }

    public function update_data(): array
    {
        return [
            ['config.update', ['seo_usu_version', '3.0.0']],
            
            // Add new configuration options
            ['config.add', ['seo_url_rewrite', 0]],
            ['config.add', ['seo_modr_type', 2]],
            ['config.add', ['seo_sql_rewrite', 0]],
            ['config.add', ['seo_profile_inj', 0]],
            ['config.add', ['seo_profile_vfolder', 0]],
            ['config.add', ['seo_profile_noids', 0]],
            ['config.add', ['seo_rewrite_usermsg', 0]],
            ['config.add', ['seo_rem_sid', 0]],
            ['config.add', ['seo_rem_hilit', 1]],
            ['config.add', ['seo_rem_small_words', 0]],
            ['config.add', ['seo_virtual_folder', 0]],
            ['config.add', ['seo_virtual_root', 0]],
            ['config.add', ['seo_cache_layer', 1]],
            ['config.add', ['seo_rem_ids', 0]],
            ['config.add', ['seo_redirect_404_forum', 0]],
            ['config.add', ['seo_redirect_404_topic', 0]],
        ];
    }
}

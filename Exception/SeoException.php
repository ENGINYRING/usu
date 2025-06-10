<?php declare(strict_types=1);
/**
*
* @package Ultimate phpBB SEO Friendly URL
* @copyright (c) 2017 www.phpBB-SEO.ir
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbseo\usu\Exception;

class SeoException extends \Exception {}

class InvalidUrlException extends SeoException {}

class CacheException extends SeoException {}

class ConfigurationException extends SeoException {}

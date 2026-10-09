<?php

/**
 * Various Static Utility methods for working with the installer
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\Bootstrap;
use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Utils\InstDescMng;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapJson;

class DUPX_U
{
    const MAINTENANCE_INDEX_MARKER = '<!-- DUPLICATOR INSTALLER MAINTENANCE -->';

    /**
     * Adds a slash to the end of a file or directory path
     *
     * @param string $path A path
     *
     * @return string The original $path with a with '/' added to the end.
     */
    public static function addSlash($path)
    {
        $last_char = substr($path, strlen($path) - 1, 1);
        if ($last_char != '/') {
            $path .= '/';
        }
        return $path;
    }

    /**
     * Does one string contain other
     *
     * @param string $haystack The full string to search
     * @param string $needle   The substring to search on
     *
     * @return bool Returns true if the $needle was found in the $haystack
     */
    public static function contains($haystack, $needle): bool
    {
        $pos = strpos($haystack, $needle);
        return ($pos !== false);
    }

    /**
     * move all folder content up to parent
     *
     * @param string  $subFolderName   full path
     * @param boolean $deleteSubFolder if true delete subFolder after moved all
     *
     * @return boolean
     */
    public static function moveUpfromSubFolder($subFolderName, $deleteSubFolder = false)
    {
        if (!is_dir($subFolderName)) {
            return false;
        }

        $parentFolder = dirname($subFolderName);
        if (!is_writable($parentFolder)) {
            return false;
        }

        $success = true;
        if (($subList = glob(rtrim($subFolderName, '/') . '/*', GLOB_NOSORT)) === false) {
            Log::info("Problem glob folder " . $subFolderName);
            return false;
        } else {
            foreach ($subList as $cName) {
                $destination = $parentFolder . '/' . basename($cName);
                if (file_exists($destination)) {
                    $success = SnapIO::rrmdir($destination);
                }

                if ($success) {
                    $success = rename($cName, $destination);
                } else {
                    break;
                }
            }

            if ($success && $deleteSubFolder) {
                $success = SnapIO::rrmdir($subFolderName);
            }
        }

        if (!$success) {
            Log::info("Problem om moveUpfromSubFolder subFolder:" . $subFolderName);
        }

        return $success;
    }

    /**
     * @param string $archive_filepath full path of zip archive
     * @param string $password         archive password
     *
     * @return boolean|string  path of dup-installer folder of false if not found
     */
    public static function findDupInstallerFolder($archive_filepath, $password)
    {
        if (!DUPX_Conf_Utils::isPhpZipAvailable()) {
            return '';
        }
        $zipArchive = new ZipArchive();
        $result     = false;
        $descFolder = InstDescMng::getInstance()->getDescriptorsFolder();

        if ($zipArchive->open($archive_filepath) === true) {
            if (strlen($password)) {
                $zipArchive->setPassword($password);
            }
            for ($i = 0; $i < $zipArchive->numFiles; $i++) {
                $stat     = $zipArchive->statIndex($i);
                $safePath = rtrim(self::setSafePath($stat['name']), '/');
                if (substr_count($safePath, '/') > 2) {
                    continue;
                }
                $exploded = explode('/', $safePath);
                if (
                    ($dup_index = array_search($descFolder, $exploded)) !== false &&
                    $exploded[$dup_index - 1] === 'dup-installer'
                ) {
                    $result = implode('/', array_slice($exploded, 0, $dup_index - 1));
                    break;
                }
            }
            if ($zipArchive->close() !== true) {
                Log::info("Can't close ziparchive:" . $archive_filepath);
                return false;
            }
        } else {
            Log::info("Can't open zip archive:" . $archive_filepath);
            return false;
        }

        return $result;
    }

    /**
     * Dumps a variable for debugging
     *
     * @param mixed $var    The variable to view
     * @param bool  $pretty Pretty print the var
     *
     * @return void
     */
    public static function dump($var, $pretty = false): void
    {
        if ($pretty) {
            echo '<pre>';
            print_r($var);
            echo '</pre>';
        } else {
            print_r($var);
        }
    }

    /**
     * Return a string with the elapsed time
     *
     * @see getMicrotime()
     *
     * @param int|float $end   The final time in the sequence to measure
     * @param int|float $start The start time in the sequence to measure
     *
     * @return string The time elapsed from $start to $end
     */
    public static function elapsedTime($end, $start): string
    {
        return sprintf("%.4f sec.", abs($end - $start));
    }

    /**
     *  Echo 256 spaces
     *
     *  PHP_SAPI for fcgi requires a data flush of at least 256
     *  bytes every 40 seconds or else it forces a script halt
     *
     * @return void
     */
    public static function fcgiFlush(): void
    {
        echo (str_repeat(' ', 256));
        flush();
    }

    /**
     * Get current microtime as a float.  Method is used for simple profiling
     *
     * @see elapsedTime
     *
     * @return float A float in the form "msec sec", where sec is the number of seconds since the Unix epoch
     */
    public static function getMicrotime(): float
    {
        return microtime(true);
    }

    /**
     * Is the string JSON
     *
     * @param string $string Any string blob
     *
     * @return bool Returns true if the string is JSON encoded
     */
    public static function isJSON($string): bool
    {

        return is_string($string) && is_array(json_decode($string, true));
    }

    /**
     * Display human readable byte sizes
     *
     * @param int $size The size in bytes
     *
     * @return string Human readable bytes such as 50MB, 1GB
     */
    public static function readableByteSize($size): string
    {
        try {
            $units = [
                'B',
                'KB',
                'MB',
                'GB',
                'TB',
            ];
            for ($i = 0; $size >= 1024 && $i < 4; $i++) {
                $size /= 1024;
            }
            return round($size, 2) . $units[$i];
        } catch (Exception $e) {
            return "n/a";
        }
    }

    /**
     *  Makes path safe for any OS for PHP
     *
     *  Paths should ALWAYS READ be "/"
     *      uni:  /home/path/file.txt
     *      win:  D:/home/path/file.txt
     *
     * @param string $path The path to make safe
     *
     * @return string The original $path with a with all slashes facing '/'.
     */
    public static function setSafePath($path): string
    {
        return str_replace("\\", "/", $path);
    }


    /**
     *  Check PHP version
     *
     *  @param string $version PHP version we looking for
     *
     *  @return boolean Returns true if version is same or above.
     */
    public static function isVersion($version): bool
    {
        return (version_compare(PHP_VERSION, $version) >= 0);
    }

    /**
     * The domain part of the given URL
     *                     www.myurl.co.uk     => myurl.co.uk
     *                     www.google.com      => google.com
     *                     my.test.myurl.co.uk => myurl.co.uk
     *                     www.myurl.localweb  => myurl.localweb
     *
     * @param string $url string The URL whichs domain you want to get
     *
     * @return string
     */
    public static function getDomain($url)
    {
        $pieces = parse_url($url);
        $domain = $pieces['host'] ?? '';
        if (strpos($domain, ".") !== false) {
            if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
                return $regs['domain'];
            } else {
                $exDomain = explode('.', $domain);
                return implode('.', array_slice($exDomain, -2, 2));
            }
        } else {
            return $domain;
        }
    }

    /**
     *
     * @param string $oldSubUrl  The old sub url
     * @param string $oldMainUrl The old main url
     *
     * @return string
     */
    public static function getDefaultURL($oldSubUrl, $oldMainUrl): string
    {
        $paramsManager    = PrmMng::getInstance();
        $newMainUrl       = $paramsManager->getValue(PrmMng::PARAM_URL_NEW);
        $parsedNewMainUrl = parse_url($newMainUrl);
        $parsedOldMainUrl = parse_url($oldMainUrl);
        $parsedOldSubUrl  = parse_url($oldSubUrl);
        $oldMainDomain    = $parsedOldMainUrl['host'];
        $oldSubDomain     = $parsedOldSubUrl['host'];
        $newMainDomain    = $parsedNewMainUrl['host'];
        // PARSE SCHEME
        $resultScheme = $parsedNewMainUrl['scheme'] ?? 'http';
        // PARSE HOST
        if ($oldMainDomain === $oldSubDomain) {
            $resultDomain = $newMainDomain;
        } else {
            $oldNoWwwMainDomain = (strpos($oldMainDomain, 'www.') === 0) ? substr($oldMainDomain, 4) : $oldMainDomain;
            $newNoWwwMainDomain = (strpos($newMainDomain, 'www.') === 0) ? substr($newMainDomain, 4) : $newMainDomain;
            if (($pos                = strrpos($oldSubDomain, $oldNoWwwMainDomain)) === strlen($oldSubDomain) - strlen($oldNoWwwMainDomain)) {
                $subDif       = substr($oldSubDomain, 0, $pos);
                $resultDomain = $subDif . $newNoWwwMainDomain;
            } else {
                // If I can't find a match it is a non-manageable url so I take the value of the old url.
                $resultDomain = $oldSubDomain;
            }
        }

        // PARSE PATH
        $oldMainPath = $parsedOldMainUrl['path'] ?? '';
        $oldSubPath  = $parsedOldSubUrl['path'] ?? '';
        $newMainPath = $parsedNewMainUrl['path'] ?? '';
        if ($oldMainPath === $oldSubPath) {
            $resultPath = $newMainPath;
        } else {
            if (strpos($oldSubPath, $oldMainPath) === 0) {
                $subDif     = substr($oldSubPath, strlen($oldMainPath));
                $resultPath = $newMainPath . '/' . trim($subDif, '/');
            } else {
                // If I can't find a match it is a non-manageable path so I take the value of the old path.
                $resultPath = $oldSubPath;
            }
        }

        if (empty($resultPath) || $resultPath === '/') {
            $resultPath = '';
        }

        return $resultScheme . '://' . $resultDomain . '/' . trim($resultPath, '/');
    }

    /**
     * Get default chunk size in byte
     *
     * @param int $min_chunk_size Min minimum chunk size in bytes
     *
     * @return int An integer chunk size  byte value.
     */
    public static function get_default_chunk_size_in_byte($min_chunk_size = 0): int
    {

        if ($min_chunk_size == 0) {
            $min_chunk_size = 2 * MB_IN_BYTES;
        }
        $post_max_size_in_bytes                  = self::get_bytes_from_shorthand(ini_get('post_max_size'));
        $considered_post_max_size_in_bytes       = $post_max_size_in_bytes - KB_IN_BYTES;
        $upload_max_filesize_in_bytes            = self::get_bytes_from_shorthand(ini_get('upload_max_filesize'));
        $considered_upload_max_filesize_in_bytes = $upload_max_filesize_in_bytes - KB_IN_BYTES;
        $memory_limit_in_bytes                   = self::get_bytes_from_shorthand(ini_get('memory_limit'));
        $considered_memory_limit_in_bytes        = $memory_limit_in_bytes - KB_IN_BYTES;
        return min(
            $considered_post_max_size_in_bytes,
            $considered_upload_max_filesize_in_bytes,
            $considered_memory_limit_in_bytes, // In extraction process, 2 MB is improving speed, so we are using 5MB instead of 10 MB
            $min_chunk_size
        );
    }

    /**
     * Converts a shorthand byte value to an integer byte value.
     *
     * @param string $value A (PHP ini) byte value, either shorthand or ordinary.
     *
     * @return int An integer byte value.
     */
    private static function get_bytes_from_shorthand($value): int
    {
        $value = strtolower(trim($value));
        $bytes = (int) $value;
        if (false !== strpos($value, 'g')) {
            $bytes *= GB_IN_BYTES;
        } elseif (false !== strpos($value, 'm')) {
            $bytes *= MB_IN_BYTES;
        } elseif (false !== strpos($value, 'k')) {
            $bytes *= KB_IN_BYTES;
        }

        // For windows 32 bit int max limit
        if ($bytes < 0) {
            return PHP_INT_MAX;
        }

        return min($bytes, PHP_INT_MAX);
        // Deal with large (float) values which run into the maximum integer size.
    }

    /**
     * Escaping for HTML blocks.
     *
     * @param string $text The text to be escaped.
     *
     * @return string
     */
    public static function esc_html($text)
    {
        $safe_text = SnapJson::checkInvalidUTF8($text);
        /**
         * Filters a string cleaned and escaped for output in HTML.
         *
         * Text passed to esc_html() is stripped of invalid or special characters
         * before output.
         *
         * @param string $safe_text The text after it has been escaped.
         * @param string $text      The text prior to being escaped.
         */
        return self::_wp_specialchars($safe_text, ENT_QUOTES);
    }

    /**
     * Escape single quotes, htmlspecialchar " < > &, and fix line endings.
     *
     * Escapes text strings for echoing in JS. It is intended to be used for inline JS
     * (in a tag attribute, for example onclick="..."). Note that the strings have to
     * be in single quotes. The {@see 'js_escape'} filter is also applied here.
     *
     * @param string $text The text to be escaped.
     *
     * @return string Escaped text.
     */
    public static function esc_js($text): string
    {
        $safe_text = SnapJson::checkInvalidUTF8($text);
        $safe_text = self::_wp_specialchars($safe_text, ENT_COMPAT);
        $safe_text = preg_replace('/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes($safe_text));
        $safe_text = str_replace("\r", '', $safe_text);
        /**
         * Filters a string cleaned and escaped for output in JavaScript.
         *
         * Text passed to esc_js() is stripped of invalid or special characters,
         * and properly slashed for output.
         *
         * @param string $safe_text The text after it has been escaped.
         * @param string $text      The text prior to being escaped.
         */
        return str_replace("\n", '\\n', addslashes($safe_text));
    }

    /**
     * Escaping for HTML attributes.
     *
     * @param string $text The text to be escaped.
     *
     * @return string
     */
    public static function esc_attr($text)
    {
        $safe_text = SnapJson::checkInvalidUTF8($text);
        /**
         * Filters a string cleaned and escaped for output in an HTML attribute.
         *
         * Text passed to esc_attr() is stripped of invalid or special characters
         * before output.
         *
         * @param string $safe_text The text after it has been escaped.
         * @param string $text      The text prior to being escaped.
         */
        return self::_wp_specialchars($safe_text, ENT_QUOTES);
    }

    /**
     * Escaping for textarea values.
     *
     * @param string $text The text to be escaped.
     *
     * @return string
     */
    public static function esc_textarea($text): string
    {
        /**
         * Filters a string cleaned and escaped for output in a textarea element.
         *
         * @param string $safe_text The text after it has been escaped.
         * @param string $text      The text prior to being escaped.
         */
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Escape an HTML tag name.
     *
     * @param string $tag_name The tag name to be escaped.
     *
     * @return string
     */
    public function tag_escape($tag_name): string
    {
        /**
         * Filters a string cleaned and escaped for output as an HTML tag.
         *
         * @param string $safe_tag The tag name after it has been escaped.
         * @param string $tag_name The text before it was escaped.
         */
        return strtolower(preg_replace('/[^a-zA-Z0-9_:]/', '', $tag_name));
    }

    /**
     * Converts a number of special characters into their HTML entities.
     *
     * Specifically deals with: &, <, >, ", and '.
     *
     * $quote_style can be set to ENT_COMPAT to encode " to
     * &quot;, or ENT_QUOTES to do both. Default is ENT_NOQUOTES where no quotes are encoded.
     *
     * @param string      $string        The text which is to be encoded.
     * @param int|string  $quote_style   Optional. Converts double quotes if set to ENT_COMPAT,
     *                                   both single and double if set to ENT_QUOTES or none if
     *                                   set to ENT_NOQUOTES. Also compatible with old values;
     *                                   converting single quotes if set to 'single', double if
     *                                   set to 'double' or both if otherwise set. Default is
     *                                   ENT_NOQUOTES.
     * @param bool|string $charset       Optional. The character encoding of the string. Default is false.
     * @param bool        $double_encode Optional. Whether to encode existing html entities. Default is false.
     *
     * @return string The encoded text with HTML entities.
     */
    public static function _wp_specialchars($string, $quote_style = ENT_NOQUOTES, $charset = false, $double_encode = false) // phpcs:ignore
    {
        $string = (string) $string;
        if (0 === strlen($string)) {
            return '';
        }

        // Don't bother if there are no specialchars - saves some processing
        if (!preg_match('/[&<>"\']/', $string)) {
            return $string;
        }

        // Account for the previous behaviour of the function when the $quote_style is not an accepted value
        if (empty($quote_style)) {
            $quote_style = ENT_NOQUOTES;
        } elseif (!in_array($quote_style, [0, 2, 3, 'single', 'double'], true)) {
            $quote_style = ENT_QUOTES;
        }

        // Store the site charset as a static to avoid multiple calls to wp_load_alloptions()
        if (!$charset) {
            static $_charset = null;
            if (!isset($_charset)) {
                $_charset = '';
            }
            $charset = $_charset;
        }

        if (in_array($charset, ['utf8', 'utf-8', 'UTF8'])) {
            $charset = 'UTF-8';
        }

        $_quote_style = $quote_style;
        if ($quote_style === 'double') {
            $quote_style  = ENT_COMPAT;
            $_quote_style = ENT_COMPAT;
        } elseif ($quote_style === 'single') {
            $quote_style = ENT_NOQUOTES;
        }

        if (!$double_encode) {
            // Guarantee every &entity; is valid, convert &garbage; into &amp;garbage;
            // This is required for PHP < 5.4.0 because ENT_HTML401 flag is unavailable.
            $string = self::wp_kses_normalize_entities($string);
        }

        $string = @htmlspecialchars($string, $quote_style, $charset, $double_encode);
        // Back-compat.
        if ('single' === $_quote_style) {
            $string = str_replace("'", '&#039;', $string);
        }

        return $string;
    }

    /**
     * Perform a deep string replace operation to ensure the values in $search are no longer present
     *
     * Repeats the replacement operation until it no longer replaces anything so as to remove "nested" values
     * e.g. $subject = '%0%0%0DDD', $search ='%0D', $result ='' rather than the '%0%0DD' that
     * str_replace would return
     *
     * @access private
     *
     * @param string[] $search  The value being searched for, otherwise known as the needle.
     *                          An array may be used to designate multiple needles.
     * @param string   $subject The string being searched and replaced on, otherwise known as the haystack.
     *
     * @return string The string with the replaced svalues.
     */
    private static function _deep_replace(array $search, string $subject) // phpcs:ignore
    {
        $subject = (string) $subject;
        $count   = 1;
        while ($count) {
            $subject = str_replace($search, '', $subject, $count);
        }

        return $subject;
    }

    /**
     * Converts and fixes HTML entities.
     *
     * This function normalizes HTML entities. It will convert `AT&T` to the correct
     * `AT&amp;T`, `&#00058;` to `&#58;`, `&#XYZZY;` to `&amp;#XYZZY;` and so on.
     *
     * @param string $string Content to normalize entities
     *
     * @return string Content with normalized entities
     */
    public static function wp_kses_normalize_entities($string): string
    {
        // Disarm all entities by converting & to &amp;
        $string = str_replace('&', '&amp;', $string);
        // Change back the allowed entities in our entity whitelist
        $string = preg_replace_callback('/&amp;([A-Za-z]{2,8}[0-9]{0,2});/', [self::class, 'wp_kses_named_entities'], $string);
        $string = preg_replace_callback('/&amp;#(0*[0-9]{1,7});/', [self::class, 'wp_kses_normalize_entities2'], $string);
        return (string) preg_replace_callback('/&amp;#[Xx](0*[0-9A-Fa-f]{1,6});/', [self::class, 'wp_kses_normalize_entities3'], $string);
    }

    /**
     * Callback for wp_kses_normalize_entities() regular expression.
     *
     * This function only accepts valid named entity references, which are finite,
     * case-sensitive, and highly scrutinized by HTML and XML validators.
     *
     * @param string[] $matches preg_replace_callback() matches array
     *
     * @return string Correctly encoded entity
     */
    public static function wp_kses_named_entities($matches): string
    {
        if (empty($matches[1])) {
            return '';
        }

        $allowedentitynames = [
            'nbsp',
            'iexcl',
            'cent',
            'pound',
            'curren',
            'yen',
            'brvbar',
            'sect',
            'uml',
            'copy',
            'ordf',
            'laquo',
            'not',
            'shy',
            'reg',
            'macr',
            'deg',
            'plusmn',
            'acute',
            'micro',
            'para',
            'middot',
            'cedil',
            'ordm',
            'raquo',
            'iquest',
            'Agrave',
            'Aacute',
            'Acirc',
            'Atilde',
            'Auml',
            'Aring',
            'AElig',
            'Ccedil',
            'Egrave',
            'Eacute',
            'Ecirc',
            'Euml',
            'Igrave',
            'Iacute',
            'Icirc',
            'Iuml',
            'ETH',
            'Ntilde',
            'Ograve',
            'Oacute',
            'Ocirc',
            'Otilde',
            'Ouml',
            'times',
            'Oslash',
            'Ugrave',
            'Uacute',
            'Ucirc',
            'Uuml',
            'Yacute',
            'THORN',
            'szlig',
            'agrave',
            'aacute',
            'acirc',
            'atilde',
            'auml',
            'aring',
            'aelig',
            'ccedil',
            'egrave',
            'eacute',
            'ecirc',
            'euml',
            'igrave',
            'iacute',
            'icirc',
            'iuml',
            'eth',
            'ntilde',
            'ograve',
            'oacute',
            'ocirc',
            'otilde',
            'ouml',
            'divide',
            'oslash',
            'ugrave',
            'uacute',
            'ucirc',
            'uuml',
            'yacute',
            'thorn',
            'yuml',
            'quot',
            'amp',
            'lt',
            'gt',
            'apos',
            'OElig',
            'oelig',
            'Scaron',
            'scaron',
            'Yuml',
            'circ',
            'tilde',
            'ensp',
            'emsp',
            'thinsp',
            'zwnj',
            'zwj',
            'lrm',
            'rlm',
            'ndash',
            'mdash',
            'lsquo',
            'rsquo',
            'sbquo',
            'ldquo',
            'rdquo',
            'bdquo',
            'dagger',
            'Dagger',
            'permil',
            'lsaquo',
            'rsaquo',
            'euro',
            'fnof',
            'Alpha',
            'Beta',
            'Gamma',
            'Delta',
            'Epsilon',
            'Zeta',
            'Eta',
            'Theta',
            'Iota',
            'Kappa',
            'Lambda',
            'Mu',
            'Nu',
            'Xi',
            'Omicron',
            'Pi',
            'Rho',
            'Sigma',
            'Tau',
            'Upsilon',
            'Phi',
            'Chi',
            'Psi',
            'Omega',
            'alpha',
            'beta',
            'gamma',
            'delta',
            'epsilon',
            'zeta',
            'eta',
            'theta',
            'iota',
            'kappa',
            'lambda',
            'mu',
            'nu',
            'xi',
            'omicron',
            'pi',
            'rho',
            'sigmaf',
            'sigma',
            'tau',
            'upsilon',
            'phi',
            'chi',
            'psi',
            'omega',
            'thetasym',
            'upsih',
            'piv',
            'bull',
            'hellip',
            'prime',
            'Prime',
            'oline',
            'frasl',
            'weierp',
            'image',
            'real',
            'trade',
            'alefsym',
            'larr',
            'uarr',
            'rarr',
            'darr',
            'harr',
            'crarr',
            'lArr',
            'uArr',
            'rArr',
            'dArr',
            'hArr',
            'forall',
            'part',
            'exist',
            'empty',
            'nabla',
            'isin',
            'notin',
            'ni',
            'prod',
            'sum',
            'minus',
            'lowast',
            'radic',
            'prop',
            'infin',
            'ang',
            'and',
            'or',
            'cap',
            'cup',
            'int',
            'sim',
            'cong',
            'asymp',
            'ne',
            'equiv',
            'le',
            'ge',
            'sub',
            'sup',
            'nsub',
            'sube',
            'supe',
            'oplus',
            'otimes',
            'perp',
            'sdot',
            'lceil',
            'rceil',
            'lfloor',
            'rfloor',
            'lang',
            'rang',
            'loz',
            'spades',
            'clubs',
            'hearts',
            'diams',
            'sup1',
            'sup2',
            'sup3',
            'frac14',
            'frac12',
            'frac34',
            'there4',
        ];
        $i                  = $matches[1];
        return (!in_array($i, $allowedentitynames)) ? "&amp;$i;" : "&$i;";
    }

    /**
     * Helper function to determine if a Unicode value is valid.
     *
     * @param int $i Unicode value
     *
     * @return bool True if the value was a valid Unicode number
     */
    public static function wp_valid_unicode($i): bool
    {
        return ($i == 0x9 || $i == 0xa || $i == 0xd ||
            ($i >= 0x20 && $i <= 0xd7ff) ||
            ($i >= 0xe000 && $i <= 0xfffd) ||
            ($i >= 0x10000 && $i <= 0x10ffff));
    }

    /**
     * Callback for wp_kses_normalize_entities() regular expression.
     *
     * This function helps wp_kses_normalize_entities() to only accept 16-bit
     * values and nothing more for `&#number;` entities.
     *
     * @access private
     *
     * @param string[] $matches preg_replace_callback() matches array
     *
     * @return string Correctly encoded entity
     */
    public static function wp_kses_normalize_entities2($matches): string
    {
        if (empty($matches[1])) {
            return '';
        }

        $i = $matches[1];
        if (self::wp_valid_unicode($i)) { // @phpstan-ignore-line
            $i = str_pad(ltrim($i, '0'), 3, '0', STR_PAD_LEFT);
            $i = "&#$i;";
        } else {
            $i = "&amp;#$i;";
        }

        return $i;
    }

    /**
     * Callback for wp_kses_normalize_entities() for regular expression.
     *
     * This function helps wp_kses_normalize_entities() to only accept valid Unicode
     * numeric entities in hex form.
     *
     * @access private
     *
     * @param string[] $matches preg_replace_callback() matches array
     *
     * @return string Correctly encoded entity
     */
    public static function wp_kses_normalize_entities3($matches): string
    {
        if (empty($matches[1])) {
            return '';
        }

        $hexchars = $matches[1];
        return (!self::wp_valid_unicode(hexdec($hexchars))) ? "&amp;#x$hexchars;" : '&#x' . ltrim($hexchars, '0') . ';';
    }

    /**
     * Retrieve a list of protocols to allow in HTML attributes.
     *
     * @since 3.3.0
     * @since 4.3.0 Added 'webcal' to the protocols array.
     * @since 4.7.0 Added 'urn' to the protocols array.
     *
     * @see wp_kses()
     * @see esc_url()
     *
     * @return string[] Array of allowed protocols. Defaults to an array containing 'http', 'https',
     *               'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet',
     *               'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp', 'webcal', and 'urn'.
     */
    public static function wp_allowed_protocols()
    {
        static $protocols = [];
        if (empty($protocols)) {
            $protocols = [
                'http',
                'https',
                'ftp',
                'ftps',
                'mailto',
                'news',
                'irc',
                'gopher',
                'nntp',
                'feed',
                'telnet',
                'mms',
                'rtsp',
                'svn',
                'tel',
                'fax',
                'xmpp',
                'webcal',
                'urn',
            ];
        }

        return $protocols;
    }

    /**
     * Checks and cleans a URL.
     *
     * A number of characters are removed from the URL. If the URL is for displaying
     * (the default behaviour) ampersands are also replaced. The {@see 'clean_url'} filter
     * is applied to the returned cleaned URL.
     *
     * @since 2.8.0
     *
     * @param string   $url       The URL to be cleaned.
     * @param string[] $protocols Optional. An array of acceptable protocols.
     *                            Defaults to return value of
     *                            wp_allowed_protocols()
     * @param string   $_context  Private. Use esc_url_raw() for database usage.
     *
     * @return string The cleaned $url after the {@see 'clean_url'} filter is applied.
     */
    public static function esc_url($url, $protocols = null, $_context = 'display')
    {
        $original_url = $url;
        if ('' == $url) {
            return $url;
        }

        $url = str_replace(' ', '%20', $url);
        $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\[\]\\x80-\\xff]|i', '', $url);
        if ('' === $url) {
            return $url;
        }

        if (0 !== stripos($url, 'mailto:')) {
            $strip = [
                '%0d',
                '%0a',
                '%0D',
                '%0A',
            ];
            $url   = self::_deep_replace($strip, $url);
        }

        $url = str_replace(';//', '://', $url);
        /* If the URL doesn't appear to contain a scheme, we
         * presume it needs http:// prepended (unless a relative
         * link starting with /, # or ? or a php file).
         */
        if (
            strpos($url, ':') === false && !in_array($url[0], ['/', '#', '?']) &&
            !preg_match('/^[a-z0-9-]+?\.php/i', $url)
        ) {
            $url = 'http://' . $url;
        }
        // Replace ampersands and single quotes only when displaying.
        if ('display' == $_context) {
            $url = self::wp_kses_normalize_entities($url);
            $url = str_replace('&amp;', '&#038;', $url);
            $url = str_replace("'", '&#039;', $url);
        }

        if ((false !== strpos($url, '[')) || (false !== strpos($url, ']'))) {
            $parsed = parse_url($url);
            $front  = '';
            if (isset($parsed['scheme'])) {
                $front .= $parsed['scheme'] . '://';
            } elseif ('/' === $url[0]) {
                $front .= '//';
            }

            if (isset($parsed['user'])) {
                $front .= $parsed['user'];
            }

            if (isset($parsed['pass'])) {
                $front .= ':' . $parsed['pass'];
            }

            if (isset($parsed['user']) || isset($parsed['pass'])) {
                $front .= '@';
            }

            if (isset($parsed['host'])) {
                $front .= $parsed['host'];
            }

            if (isset($parsed['port'])) {
                $front .= ':' . $parsed['port'];
            }

            $end_dirty = str_replace($front, '', $url);
            $end_clean = str_replace(['[', ']'], ['%5B', '%5D'], $end_dirty);
            $url       = str_replace($end_dirty, $end_clean, $url);
        }

        if ('/' === $url[0]) {
            $good_protocol_url = $url;
        } else {
            if (!is_array($protocols)) {
                $protocols = self::wp_allowed_protocols();
            }
            $good_protocol_url = self::wp_kses_bad_protocol($url, $protocols);
            if (strtolower($good_protocol_url) != strtolower($url)) {
                return '';
            }
        }

        /**
         * Filters a string cleaned and escaped for output as a URL.
         *
         * @since 2.3.0
         *
         * @param string $good_protocol_url The cleaned URL to be returned.
         * @param string $original_url      The URL prior to cleaning.
         * @param string $_context          If 'display', replace ampersands and single quotes only.
         */
        return $good_protocol_url;
    }

    /**
     * Removes any invalid control characters in $string.
     *
     * Also removes any instance of the '\0' string.
     *
     * @param string              $string  The text which is to be purified.
     * @param array<string,mixed> $options Set 'slash_zero' => 'keep' when '\0' is allowed. Default is 'remove'.
     *
     * @return string
     */
    public static function wp_kses_no_null($string, $options = null)
    {
        if (!isset($options['slash_zero'])) {
            $options = ['slash_zero' => 'remove'];
        }

        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $string);
        if ('remove' == $options['slash_zero']) {
            $string = preg_replace('/\\\\+0+/', '', $string);
        }

        return $string;
    }

    /**
     * Sanitize string from bad protocols.
     *
     * This function removes all non-allowed protocols from the beginning of
     * $string. It ignores whitespace and the case of the letters, and it does
     * understand HTML entities. It does its work in a while loop, so it won't be
     * fooled by a string like "javascript:javascript:alert(57)".
     *
     * @param string   $string            Content to filter bad protocols from
     * @param string[] $allowed_protocols Allowed protocols to keep
     *
     * @return string Filtered content
     */
    public static function wp_kses_bad_protocol($string, $allowed_protocols): string
    {
        $string     = self::wp_kses_no_null($string);
        $iterations = 0;
        do {
            $original_string = $string;
            $string          = self::wp_kses_bad_protocol_once($string, $allowed_protocols);
        } while ($original_string != $string && ++$iterations < 6);
        if ($original_string != $string) {
            return '';
        }

        return $string;
    }

    /**
     * Sanitizes content from bad protocols and other characters.
     *
     * This function searches for URL protocols at the beginning of $string, while
     * handling whitespace and HTML entities.
     *
     * @param string   $string            Content to check for bad protocols
     * @param string[] $allowed_protocols Allowed protocols
     * @param int      $count             Optional. Number of times the function
     *
     * @return string Sanitized content
     */
    public static function wp_kses_bad_protocol_once($string, $allowed_protocols, $count = 1)
    {
        $string2 = preg_split('/:|&#0*58;|&#x0*3a;/i', $string, 2);
        if (isset($string2[1]) && !preg_match('%/\?%', $string2[0])) {
            $string   = trim($string2[1]);
            $protocol = self::wp_kses_bad_protocol_once2($string2[0], $allowed_protocols);
            if ('feed:' == $protocol) {
                if ($count > 2) {
                    return '';
                }
                $string = wp_kses_bad_protocol_once($string, $allowed_protocols, ++$count);
                if (empty($string)) {
                    return $string;
                }
            }
            $string = $protocol . $string;
        }

        return $string;
    }

    /**
     * Convert all entities to their character counterparts.
     *
     * This function decodes numeric HTML entities (`&#65;` and `&#x41;`).
     * It doesn't do anything with other entities like &auml;, but we don't
     * need them in the URL protocol whitelisting system anyway.
     *
     * @param string $string Content to change entities
     *
     * @return string Content after decoded entities
     */
    public static function wp_kses_decode_entities($string): ?string
    {
        $string = preg_replace_callback('/&#([0-9]+);/', [self::class, '_wp_kses_decode_entities_chr'], $string);
        return preg_replace_callback('/&#[Xx]([0-9A-Fa-f]+);/', [self::class, '_wp_kses_decode_entities_chr_hexdec'], $string);
    }

    /**
     * Regex callback for wp_kses_decode_entities()
     *
     * @param string[] $match preg match
     *
     * @return string
     */
    public static function _wp_kses_decode_entities_chr($match): string // phpcs:ignore
    {
        return chr($match[1]); // @phpstan-ignore-line
    }

    /**
     * Regex callback for wp_kses_decode_entities()
     *
     * @param string[] $match preg match
     *
     * @return string
     */
    public static function _wp_kses_decode_entities_chr_hexdec($match): string // phpcs:ignore
    {
        return chr(hexdec($match[1]));
    }

    /**
     * Callback for wp_kses_bad_protocol_once() regular expression.
     *
     * This function processes URL protocols, checks to see if they're in the
     * white-list or not, and returns different data depending on the answer.
     *
     * @access private
     *
     * @param string   $string            URI scheme to check against the whitelist
     * @param string[] $allowed_protocols Allowed protocols
     *
     * @return string Sanitized content
     */
    public static function wp_kses_bad_protocol_once2($string, $allowed_protocols): string
    {
        $string2 = self::wp_kses_decode_entities($string);
        $string2 = preg_replace('/\s/', '', $string2);
        $string2 = self::wp_kses_no_null($string2);
        $string2 = strtolower($string2);
        $allowed = false;
        foreach ((array) $allowed_protocols as $one_protocol) {
            if (strtolower($one_protocol) == $string2) {
                $allowed = true;
                break;
            }
        }

        if ($allowed) {
            return "$string2:";
        } else {
            return '';
        }
    }

    /**
     * Toggle maintenance mode for the site.
     *
     * Creates/deletes the maintenance file to enable/disable maintenance mode.
     *
     * @param bool $enable True to enable maintenance mode, false to disable.
     *
     * @return void
     */
    public static function maintenanceMode($enable = false): void
    {
        $homePath = SnapIO::trailingslashit(PrmMng::getInstance()->getValue(PrmMng::PARAM_PATH_WP_CORE_NEW));
        if (!is_writable($homePath)) {
            Log::info('CAN\'T ' . ($enable ? 'SET' : 'REMOVE') . ' MAINTENANCE MODE, ROOT FOLDER NOT WRITABLE');
            return;
        }

        $maintenanceFile = $homePath . '.maintenance';
        $indexFile       = $homePath . 'index.html';

        if (file_exists($indexFile)) {
            $indexContent = file_get_contents($indexFile);
            $manageIndex  = (strpos($indexContent, self::MAINTENANCE_INDEX_MARKER) !== false);
        } else {
            $manageIndex = true;
        }

        if ($enable) {
            Log::info('MAINTENANCE MODE ENABLE');
            if (file_put_contents($maintenanceFile, '<?php $upgrading = ' . time() . '; ?>') == false) {
                Log::info('CAN\'T SET MAINTENANCE MODE FILE \"' . $maintenanceFile . "\"");
            }
            if ($manageIndex && SnapIo::copy(DUPX_INIT . '/assets/maintenance.html', $indexFile) == false) {
                Log::info('CAN\'T SET MAINTENANCE INDEX FILE \"' . $indexFile . "\"");
            }
        } else {
            Log::info('MAINTENANCE MODE DISABLE');
            if (file_exists($maintenanceFile) && unlink($maintenanceFile) == false) {
                Log::info('CAN\'T REMOVE MAINTENANCE MODE FILE \"' . $maintenanceFile . "\"");
            }
            if ($manageIndex && file_exists($indexFile) && unlink($indexFile) == false) {
                Log::info('CAN\'T REMOVE MAINTENANCE INDEX FILE \"' . $indexFile . "\"");
            }
        }
    }

    /**
     * Check if string is base64 encoded
     *
     * @param string $str input string
     *
     * @return false|string return false if isn't base64 string or decoded string
     */
    public static function is_base64($str)
    {
        // Check if there are valid base64 characters
        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $str)) {
            return false;
        }

        // Decode the string in strict mode and check the results
        $decoded = base64_decode($str, true);
        if (false === $decoded) {
            return false;
        }

        // Encode the string again
        if (base64_encode($decoded) != $str) {
            return false;
        }

        return $decoded;
    }

    /**
     *
     * @param string[] $matches regex match
     *
     * @return string
     */
    public static function encodeUtf8CharFromRegexMatch($matches)
    {
        if (empty($matches) || !is_array($matches)) {
            return '';
        } else {
            return json_decode('"' . $matches[0] . '"');
        }
    }

    /**
     * this function escape generic string to prevent security issue.
     * Used to replace string in wp transformer
     *
     * for example
     * abc'" become "abc'\""
     *
     * @param string $str      input string
     * @param bool   $addQuote if true add " before and after string
     *
     * @return string
     */
    public static function getEscapedGenericString($str, $addQuote = true)
    {
        $result = SnapJson::jsonEncode(trim($str));
        $result = str_replace(['\/', '$'], ['/', '\\$'], $result);
        $result = preg_replace_callback(
            '/\\\\u[a-fA-F0-9]{4}/m',
            [
                self::class,
                'encodeUtf8CharFromRegexMatch',
            ],
            $result
        );
        if (!$addQuote) {
            $result = substr($result, 1, strlen($result) - 2);
        }
        return $result;
    }
}

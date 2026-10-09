<?php

namespace Duplicator\Libs\DupArchive\Headers;

use Duplicator\Libs\DupArchive\DupArchive;
use Duplicator\Libs\Snap\SnapIO;
use Exception;

/**
 * Dup archive header
 *
 * Format: #A#{version:5}#{isCompressed}!
 */
class DupArchiveHeader extends AbstractDupArchiveHeader
{
    /**
     * Format of the stored password hash: algorithm, rounds, salt and hash
     */
    private const HASH_PASSWORD_REGEX = '/^\$(\d)\$rounds=(\d+)\$(.+)\$(.+)$/';

    /** @var string */
    protected $version = '';
    /** @var int<0,max> */
    protected int $flags;
    /** @var string */
    protected $password = '';
    /** @var string  */
    protected $hashPassword = '';

    /**
     * Class Contructor
     *
     * @param int    $flags   archive flags
     * @param string $verison if empry get default version
     */
    public function __construct($flags = 0, $verison = '')
    {
        $this->version = (strlen($verison) ? $verison : DupArchive::DUPARCHIVE_VERSION);
        $this->flags   = (int) $flags;
    }

    /**
     * Return dup archive version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Return archive flags
     *
     * @return int<0,max>
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * Set the value of flags
     *
     * @param int<0,max> $flags archive flags
     *
     * @return void
     */
    public function setFlags($flags): void
    {
        $this->flags = (int) $flags;
    }

    /**
     * Enalbe globa encryption and set password
     *
     * @param string $pwd if password is empty, disavle encrytion
     *
     * @return void
     */
    public function setPassword($pwd): void
    {
        if (strlen($pwd) == 0) {
            $this->flags       &= ~DupArchive::FLAG_CRYPT;
            $this->password     = '';
            $this->hashPassword = '';
        } else {
            $hashPassword = self::pwdToHash($pwd, DupArchive::generateSalt(16));
            if (!self::isValidHashPassword($hashPassword)) {
                // Every read rejects this hash: fail before writing an archive nobody can open
                throw new Exception(
                    'The server crypt() output is not a valid archive password hash.',
                    DupArchive::EXCEPTION_CODE_ENCRYPTION_UNAVAILABLE
                );
            }
            $this->flags       |= DupArchive::FLAG_CRYPT;
            $this->password     = $pwd;
            $this->hashPassword = $hashPassword;
        }
    }

    /**
     * Check whether a stored password hash has the format readFromArchive() accepts
     *
     * @param string $hashPassword Stored password hash
     *
     * @return bool
     */
    private static function isValidHashPassword(string $hashPassword): bool
    {
        return preg_match(self::HASH_PASSWORD_REGEX, $hashPassword) === 1;
    }

    /**
     * Return archvie password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Write header to archive
     *
     * @param resource $archiveHandle archive resource
     *
     * @return int bytes written
     */
    public function writeToArchive($archiveHandle)
    {
        $content  = '<A>';
        $content .= '<V>' . $this->version . '</V>';
        $content .= '<X>' . pack('v', $this->flags) . '</X>';
        $content .= '<P>' . $this->hashPassword . '</P>';
        $content .= '</A>';
        return SnapIO::fwrite($archiveHandle, $content);
    }

    /**
     * Return true if archvie is compressed
     *
     * @return bool
     */
    public function isCompressed(): bool
    {
        return (bool) ($this->flags & DupArchive::FLAG_COMPRESS);
    }

    /**
     * Tru if default of archive is the encryption
     *
     * @return bool
     */
    public function isCrypt(): bool
    {
        return (bool) ($this->flags & DupArchive::FLAG_CRYPT);
    }

    /**
     * Get header from archive
     *
     * @param resource $archiveHandle archive resource
     * @param string   $password      password archvie to check, if empry archive must be unecrypted
     *
     * @return static
     */
    public function readFromArchive($archiveHandle, $password = '')
    {
        $startElement = fgets($archiveHandle, 4);
        if ($startElement != '<A>') {
            throw new Exception("Invalid archive header marker found {$startElement}", DupArchive::EXCEPTION_CODE_INVALID_MARKER);
        }

        $version      = self::getHeaderField($archiveHandle, 'V');
        $flags        = 0;
        $hashPassword = '';
        if (version_compare($version, '5.0.0', '<')) {
            if (filter_var(self::getHeaderField($archiveHandle, 'C'), FILTER_VALIDATE_BOOLEAN)) {
                $flags = DupArchive::FLAG_COMPRESS;
            }
            $password = '';
        } else {
            $falgs        = self::getHeaderField($archiveHandle, 'X');
            $flags        = unpack('vflags', $falgs)['flags'];
            $hashPassword = self::getHeaderField($archiveHandle, 'P');
            // ---
        }

        if (strlen($hashPassword)) {
            if (preg_match(self::HASH_PASSWORD_REGEX, $hashPassword, $matches) !== 1) {
                throw new Exception("Invalid archive stored password", DupArchive::EXCEPTION_CODE_EXTRACT_ERROR);
            }
            $algo   = $matches[1];
            $rounds = $matches[2];
            $salt   = $matches[3];
            $hash   = $matches[4];

            if (!hash_equals($hashPassword, self::pwdToHash($password, $salt))) {
                throw new Exception("Invalid archive password", DupArchive::EXCEPTION_CODE_INVALID_PASSWORD);
            }
        }

        $this->flags        = $flags;
        $this->version      = $version;
        $this->password     = $password;
        $this->hashPassword = $hashPassword;

        // Skip the </A>
        fgets($archiveHandle, 5);
        return $this;
    }

    /**
     * Return hash password
     *
     * @param string $password password
     * @param string $salt     salt string
     *
     * @return string
     */
    protected function pwdToHash($password, $salt): string
    {
        return crypt($password, DupArchive::PWD_ALGO . $salt . '$');
    }
}

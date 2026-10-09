<?php

declare(strict_types=1);

namespace Duplicator\Package\Failure;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Models\Fix;

/**
 * Adds a possible LiteSpeed remedy to compatible worker interruptions.
 */
final class LiteSpeedWorkerGuidance implements FailureGuidanceProviderInterface
{
    private const DOCUMENTATION_PATH = 'how-to-handle-server-timeout-issues/';

    private const CONFIGURATION = <<<'HTACCESS'
<IfModule Litespeed>
RewriteEngine On
RewriteRule .* - [E=noabort:1, E=noconntimeout:1]
</IfModule>
HTACCESS;

    /** @var int[] */
    private const FAILURE_CODES = [
        DupliException::CODE_SCAN_INDEX_INVALID,
        DupliException::CODE_INDEX_FILE_EMPTY,
        DupliException::CODE_INDEX_FILE_MISSING,
        DupliException::CODE_DB_RETRY_EXHAUSTED,
        DupliException::CODE_DB_PHP_DUMP_INTERRUPTED,
        DupliException::CODE_MYSQLDUMP_INTERRUPTED,
        DupliException::CODE_ZIP_RETRY_EXHAUSTED,
        DupliException::CODE_SHELL_ZIP_RETRY_EXHAUSTED,
        DupliException::CODE_DUP_ARCHIVE_RETRY_EXHAUSTED,
        DupliException::CODE_STUCK,
    ];

    /**
     * @param int $failureCode DupliException::CODE_* value
     *
     * @return bool
     */
    public static function supports(int $failureCode): bool
    {
        return in_array($failureCode, self::FAILURE_CODES, true) &&
            SnapServer::getWebServer() === SnapServer::WEBSERVER_LITESPEED;
    }

    /**
     * @param Fix $fix         Generated failure fix
     * @param int $failureCode DupliException::CODE_* value
     *
     * @return Fix
     */
    public static function apply(Fix $fix, int $failureCode): Fix
    {
        if (!self::supports($failureCode)) {
            return $fix;
        }

        $description = __(
            'LiteSpeed was detected. This failure may have been caused by LiteSpeed terminating a background worker
            after its AJAX connection closed. The configuration below is only a potential solution. It is not guaranteed
            to resolve the failure because other server conditions can produce the same symptom.',
            'duplicator'
        );
        if ($fix->getDescription() !== '') {
            $description = $fix->getDescription() . ' ' . $description;
        }

        $troubleshooting   = $fix->getTroubleshooting();
        $troubleshooting[] = sprintf(
            /* translators: %s: Absolute path to the root .htaccess file */
            __(
                'Add the suggested configuration to the root <code>.htaccess</code> file at <code>%s</code>, or ask
                your hosting provider to apply an equivalent server or virtual-host configuration. Duplicator does
                not modify this file automatically.',
                'duplicator'
            ),
            esc_html(wp_normalize_path(SnapWP::getHomePath() . '.htaccess'))
        );
        $troubleshooting[] = __(
            'Duplicator cannot verify an equivalent configuration defined outside the local
            <code>.htaccess</code> file. After the configuration has been reviewed, run the Backup again. If the
            failure continues, check the Backup log because the interruption may have another cause.',
            'duplicator'
        );

        $fix->setDescription($description)
            ->setTroubleshooting($troubleshooting)
            ->setCodeSnippet(self::CONFIGURATION);

        return $fix->setDocReference(
            DUPLICATOR_DUPLICATOR_DOCS_URL . self::DOCUMENTATION_PATH,
            __('Troubleshooting Server Time Out Issues', 'duplicator')
        );
    }
}

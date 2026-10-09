<?php

/**
 * Staging sites validation template
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\Params\PrmMng;

/**
 * Variables
 *
 * @var int $testResult DUPX_Validation_abstract_item::[LV_FAIL|LV_HARD_WARNING|...]
 * @var string[] $stagingPrefixes Array of staging table prefixes (e.g., ['dstg1_', 'dstg2_'])
 * @var string[] $stagingPaths Array of staging folder paths
 */

$statusClass  = ($testResult > DUPX_Validation_abstract_item::LV_SOFT_WARNING ? 'green' : 'red');
$hasPrefixes  = count($stagingPrefixes) > 0;
$hasPaths     = count($stagingPaths) > 0;
$stagingCount = max(count($stagingPrefixes), count($stagingPaths));
?>
<div class="sub-title">STATUS</div>
<p class="<?php echo $statusClass; ?>">
    <?php if ($stagingCount === 0) { ?>
        <i class="fas fa-check-circle green"></i>
        No staging sites detected in the database or filesystem.
    <?php } else { ?>
        <i class="fas fa-exclamation-triangle maroon"></i>
        <b class="maroon">
            Detected <?php echo $stagingCount; ?> staging site<?php echo $stagingCount > 1 ? 's' : ''; ?>
            managed by Duplicator. If you proceed with the installation, staging site data will be lost.
        </b>
    <?php } ?>
</p>

<div class="sub-title">DETAILS</div>
<p>
    <b>What are staging sites?</b><br>
    Staging sites are temporary WordPress installations created by Duplicator for testing changes
    before deploying to production. They share the same database server but use separate table prefixes
    (starting with <code>dstg</code>) and reside in subfolders under
    <code>wp-content/duplicator-backups/dup_staging/</code>.
</p>
<p>
    <b class="maroon">Recovery mode cannot restore staging sites</b>, even if the recovery option is enabled.
    Staging site data will be permanently lost during the installation process.
</p>

<?php if ($hasPrefixes) { ?>
    <p>
        <b>Staging Database Tables Detected:</b>
    </p>
    <ul>
        <?php foreach ($stagingPrefixes as $prefix) { ?>
            <li>
                Table prefix: <code><b><?php echo DUPX_U::esc_html($prefix); ?></b></code>
            </li>
        <?php } ?>
    </ul>
<?php } ?>

<?php if ($hasPaths) { ?>
    <p>
        <b>Staging Site Folders Detected:</b>
    </p>
    <ul>
        <?php foreach ($stagingPaths as $path) { ?>
            <li>
                <code><b><?php echo DUPX_U::esc_html($path); ?></b></code>
            </li>
        <?php } ?>
    </ul>
<?php } ?>

<div class="sub-title">RECOMMENDATIONS</div>
<p>
    <b>You have two options:</b>
</p>
<ol>
    <li>
        <b>Preserve staging sites:</b> Create backups of staging sites and delete them from the Duplicator
        admin panel before proceeding with the installation
    </li>
    <li>
        <b>Proceed anyway:</b> Continue with the installation knowing that staging site data will be lost
    </li>
</ol>

<?php if ($stagingCount > 0) { ?>
    <div class="sub-title">TROUBLESHOOT</div>
    <ul>
        <li>
            <b>Why am I seeing this warning?</b><br>
            This site has active staging sites. Restoring a backup will delete these temporary installations.
        </li>
        <li>
            <b>How do I delete staging sites?</b><br>
            Go to Duplicator &gt; Staging in your WordPress admin panel (before running the installer)
            to delete staging sites cleanly.
        </li>
    </ul>
<?php } ?>

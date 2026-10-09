<?php



defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * Variables
 *
 * @var int      $testResult           validation rest result enum
 * @var string[] $failMessages         fail message
 * @var bool     $noImportableSubsites true when the backend import is blocked because no subsite can be imported
 */

$statusClass = ($testResult > DUPX_Validation_abstract_item::LV_SOFT_WARNING ? 'green' : 'maroon' );
?>
<div class="sub-title">STATUS</div>
<p class="<?php echo $statusClass; ?>">
    <?php if ($testResult > DUPX_Validation_abstract_item::LV_SOFT_WARNING) { ?>
        The package has all the elements to be imported.
    <?php } elseif ($noImportableSubsites) { ?>
        This Backup <b>cannot be imported</b>.<br>
        It does not contain any site that can be imported into the current network.
    <?php } else { ?>
        You are importing a <b>partial package</b>.<br>
        A Backup with filtered elements could cause a malfunction of the current site.
    <?php } ?>
</p>

<?php if (count($failMessages) > 0) { ?>
    <div class="sub-title">DETAILS</div>
    <ul>
        <?php foreach ($failMessages as $failMessage) { ?>
            <li><?php echo $failMessage; ?></li>
        <?php } ?>
    </ul>
<?php } ?>

<div class="sub-title">TROUBLESHOOT</div>
<ul>
    <?php if ($noImportableSubsites) { ?>
        <li>
            Create a new Backup without database table filters and import it again.
        </li>
        <li>
            Alternatively, install this Backup with the classic installer by uploading the archive and installer file to the server manually.
        </li>
    <?php } else { ?>
        <li>
            The package can be installed, only the files in the package will be overwritten, make sure they are compatible with the current website.
        </li>
    <?php } ?>
</ul>

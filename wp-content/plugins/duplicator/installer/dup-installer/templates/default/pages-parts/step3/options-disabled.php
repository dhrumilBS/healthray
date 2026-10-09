<?php



defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<p>
    Search and replace options have been disabled.
    <?php if (DUPX_ArchiveConfig::getInstance()->isDBExcluded()) : ?>
        The database was excluded during the build of the package.
    <?php endif; ?>
</p>

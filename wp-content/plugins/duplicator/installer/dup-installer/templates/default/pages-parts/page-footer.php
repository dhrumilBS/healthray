<?php



defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\Params\PrmMng;

?>
</div>
<?php
do_action('duplicator_installer_page_footer');
PrmMng::getInstance()->getParamsHtmlInfo();
?>
</body>
</html>


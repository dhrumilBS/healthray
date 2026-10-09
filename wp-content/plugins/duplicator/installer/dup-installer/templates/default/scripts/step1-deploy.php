<?php



defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\InstState;
use Duplicator\Installer\Core\Security;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Utils\SecureCsrf;
use Duplicator\Libs\Snap\SnapJson;

if (!InstState::dbDoNothing()) {
    $nextStepParams = [
        PrmMng::PARAM_CTRL_ACTION => 'ctrl-step2',
        Security::CTRL_TOKEN      => SecureCsrf::generate('ctrl-step2'),
    ];
} else {
    $nextStepParams = [
        PrmMng::PARAM_CTRL_ACTION => 'ctrl-step4',
        Security::CTRL_TOKEN      => SecureCsrf::generate('ctrl-step4'),
    ];
}
?>
<script>
    DUPX.deployStep1 = function () {
        <?php if (!InstState::dbDoNothing()) { ?>
        DUPX.multipleStepDeploy($('#s1-input-form'), <?php echo SnapJson::jsonEncode($nextStepParams); ?>);
        <?php } else { ?>
        DUPX.oneStepDeploy($('#s1-input-form'), <?php echo SnapJson::jsonEncode($nextStepParams); ?>);
        <?php } ?>
    };
</script>

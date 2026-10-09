<?php

use Duplicator\Utils\Help\Article;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

/** @var Article[] $articles */
$articles  = $tplMng->getDataValueArrayRequired('articles');
$listClass = $tplMng->getDataValueString('list_class');
?>
<?php if (count($articles) > 0) : ?>
    <ul <?php echo strlen($listClass) > 0 ? 'class="' . esc_attr($listClass) . '"' : ''; ?>>
        <?php foreach ($articles as $article) : ?>
            <li class="dupli-help-article" data-id="<?php echo (int) $article->getId(); ?>">
                <i aria-hidden="true" class="fa fa-file-alt"></i>
                <a href="<?php echo esc_url($article->getLink()); ?>" target="_blank"><?php echo esc_html($article->getTitle()); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

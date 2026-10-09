<?php

defined('ABSPATH') || exit;

final class DCE_Comparison_Table_Widget {

    public static function init() {
        add_action('dce_register_widgets', [__CLASS__, 'register']);
    }

    public static function register($registry) {
        $registry->register([
            'name'  => 'comparison_table',
            'label' => 'Comparison Table',
            'icon'   => 'table',
            'editor' => [
                'builder' => 'comparison_table',
            ],
            'fields' => [],
            'dynamic_fields' => false,
            'renderer' => [__CLASS__, 'render'],
        ]);
    }

    public static function render($data) {
        $products = isset($data['products']) && is_array($data['products'])
            ? array_values($data['products'])
            : [];

        $products = array_pad($products, 5, []);
        $products = array_slice($products, 0, 5);

        $sections = isset($data['sections']) && is_array($data['sections'])
            ? $data['sections']
            : [];

        $table_id = 'dce-comparison-' . wp_rand(1000, 999999);

        ob_start();
        ?>
        <div class="dce-comparison-wrap">
            <div class="dce-comparison-scroll">
                <table class="dce-comparison-table">
                    <thead>
                        <tr class="dce-comparison-products">
                            <th class="dce-comparison-label-cell"></th>

                            <?php foreach ($products as $product) : ?>
                                <th>
                                    <?php
                                    $logo = isset($product['logo']) ? trim((string) $product['logo']) : '';
                                    $name = isset($product['name']) ? (string) $product['name'] : '';
                                    ?>
                                    <?php if ($logo) : ?>
                                        <img class="dce-comparison-logo"
                                             src="<?php echo esc_url($logo); ?>"
                                             alt="<?php echo esc_attr($name); ?>">
                                    <?php endif; ?>

                                    <?php if ($name) : ?>
                                        <span class="dce-comparison-product-name"><?php echo esc_html($name); ?></span>
                                    <?php endif; ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        self::render_fixed_row($products, 'Pricing', 'pricing');
                        self::render_fixed_row($products, 'Best For', 'best_for');
                        self::render_fixed_row($products, 'Social Profiles', 'social_profiles');
                        self::render_rating_row($products, 'Ease of Use', 'ease');
                        self::render_rating_row($products, 'Support', 'support');

                        foreach ($sections as $index => $section) :
                            $section_title = isset($section['title']) ? (string) $section['title'] : '';

                            if (!$section_title) {
                                continue;
                            }

                            $section_id = $table_id . '-section-' . $index;
                            $open = !empty($section['open']);
                            ?>
                            <tr class="dce-comparison-section-row">
                                <td colspan="6">
                                    <button type="button"
                                            class="dce-comparison-section-toggle"
                                            aria-expanded="<?php echo $open ? 'true' : 'false'; ?>"
                                            aria-controls="<?php echo esc_attr($section_id); ?>">
                                        <span><?php echo esc_html($section_title); ?></span>
                                        <span class="dce-comparison-chevron" aria-hidden="true"></span>
                                    </button>
                                </td>
                            </tr>

                            <tr id="<?php echo esc_attr($section_id); ?>"
                                class="dce-comparison-section-content <?php echo $open ? 'is-open' : ''; ?>">
                                <td colspan="6">
                                    <div class="dce-comparison-section-inner">
                                        <table class="dce-comparison-inner-table">
                                            <tbody>
                                                <?php
                                                $rows = isset($section['rows']) && is_array($section['rows'])
                                                    ? $section['rows']
                                                    : [];

                                                foreach ($rows as $row) :
                                                    $label = isset($row['label']) ? (string) $row['label'] : '';
                                                    ?>
                                                    <tr>
                                                        <th><?php echo esc_html($label); ?></th>

                                                        <?php
                                                        $cells = isset($row['cells']) && is_array($row['cells'])
                                                            ? array_values($row['cells'])
                                                            : [];

                                                        $cells = array_pad($cells, 5, []);

                                                        for ($i = 0; $i < 5; $i++) :
                                                            ?>
                                                            <td>
                                                                <?php self::render_cell($cells[$i]); ?>
                                                            </td>
                                                        <?php endfor; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <tr class="dce-comparison-cta-row">
                            <td></td>
                            <?php foreach ($products as $product) : ?>
                                <td>
                                    <?php
                                    $cta_text = isset($product['cta_text']) ? (string) $product['cta_text'] : '';
                                    $cta_url = isset($product['cta_url']) ? (string) $product['cta_url'] : '';
                                    ?>
                                    <?php if ($cta_text && $cta_url) : ?>
                                        <a class="dce-comparison-cta"
                                           href="<?php echo esc_url($cta_url); ?>">
                                            <?php echo esc_html($cta_text); ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php

        return ob_get_clean();
    }

    private static function render_fixed_row($products, $label, $key) {
        ?>
        <tr>
            <th><?php echo esc_html($label); ?></th>
            <?php foreach ($products as $product) : ?>
                <td><?php echo esc_html(isset($product[$key]) ? $product[$key] : ''); ?></td>
            <?php endforeach; ?>
        </tr>
        <?php
    }

    private static function render_rating_row($products, $label, $key) {
        ?>
        <tr>
            <th><?php echo esc_html($label); ?></th>
            <?php foreach ($products as $product) : ?>
                <td><?php self::render_rating(isset($product[$key]) ? $product[$key] : ''); ?></td>
            <?php endforeach; ?>
        </tr>
        <?php
    }

    private static function render_rating($rating) {
        $rating = is_numeric($rating) ? max(0, min(5, (float) $rating)) : 0;
        $full = (int) floor($rating);
        $half = ($rating - $full) >= 0.5;

        echo '<span class="dce-rating" aria-label="' . esc_attr($rating . ' out of 5') . '">';

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $full) {
                echo '<span class="dce-star is-full" aria-hidden="true">★</span>';
            } elseif ($half && $i === $full + 1) {
                echo '<span class="dce-star is-half" aria-hidden="true">★</span>';
            } else {
                echo '<span class="dce-star is-empty" aria-hidden="true">★</span>';
            }
        }

        echo '</span>';
    }

    private static function render_cell($cell) {
        if (!is_array($cell)) {
            echo esc_html((string) $cell);
            return;
        }

        $type = isset($cell['type']) ? sanitize_key($cell['type']) : 'text';
        $value = isset($cell['value']) ? (string) $cell['value'] : '';

        switch ($type) {
            case 'check':
                echo '<span class="dce-cell-check" aria-label="Yes">✓</span>';
                break;

            case 'cross':
                echo '<span class="dce-cell-cross" aria-label="No">×</span>';
                break;

            case 'stars':
                self::render_rating($value);
                break;

            default:
                echo esc_html($value);
                break;
        }
    }
}

<?php
add_shortcode('lab_problem', 'healthray_lab_shortcode');

function healthray_lab_shortcode($atts)
{
    $atts = shortcode_atts([], $atts, 'lab_problem');
    ob_start();
?>

    <style>
        .blob-wrap { background: #F1F1FF; border-radius: 32px; padding: 32px; position: relative; overflow: hidden; }
        .blob-wrap::before,
        .blob-wrap::after { content: ""; position: absolute; width: 300px; height: 300px; background: radial-gradient(circle, rgba(59, 130, 246, 0.15), transparent 70%); filter: blur(40px); z-index: 0; }
        .blob-wrap::before { top: -80px; left: -80px; }
        .blob-wrap::after { bottom: -80px; right: -80px; }
        .blob-wrap #connector-svg { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0; }
        .blob-wrap .conn-line { fill: none; stroke: #3b82f6; stroke-width: 2; stroke-dasharray: 6 6; opacity: 0.5; animation: drawLine 8s linear infinite; }
        .blob-wrap .diagram { position: relative; z-index: 1; display: flex; flex-direction: column; align-items: center; gap: clamp(1.5rem, 4vw, 2.5rem); }
        .blob-wrap .row-top { display: flex; justify-content: center; gap: clamp(0.75rem, 2vw, 3.5rem); width: 100%; flex-wrap: wrap; }
        .blob-wrap .problem-card { width: calc((100% - (clamp(0.75rem, 2vw, 3.5rem) * 2)) / 3); position: relative; background: rgba(255, 255, 255, 0.35); backdrop-filter: blur(14px); border-radius: 18px; padding: 18px; border: 1px solid rgba(255, 255, 255, 0.6); box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.15), 0 12px 35px rgba(59, 130, 246, 0.12); transition: all 0.3s ease; }
        .blob-wrap .problem-card:hover { transform: scale(1.02); background: rgba(255, 255, 255, 0.48); box-shadow: 0 0 0 1.5px rgba(59, 130, 246, 0.35), 0 20px 50px rgba(59, 130, 246, 0.25); }
        .blob-wrap .problem-card h3 { font-size: 18px; font-weight: 700; color: #0b1220; }
        .blob-wrap .problem-card p { font-size: 14px; color: #475569; line-height: 1.6; }
        .blob-wrap .cloud-body { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(16px); border-radius: 999px; padding: 16px 40px; border: 1px solid rgba(255, 255, 255, 0.7); box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2), 0 15px 40px rgba(59, 130, 246, 0.2); }
        .blob-wrap .cloud-body h3 { font-size: 18px; color: #2563eb; letter-spacing: 0.05em; margin: 0; text-transform: uppercase; }
        @keyframes drawLine {
            from { stroke-dashoffset: 100; }
            to { stroke-dashoffset: 0; }        
        }

        @media (max-width: 768px) { .blob-wrap .problem-card { width: calc((100% - clamp(0.75rem, 2vw, 3.5rem)) / 2); } }
        @media (max-width: 500px) { .blob-wrap .problem-card { width: 100%; } }
        @media (max-width: 650px) {
            .blob-wrap #connector-svg { display: none; }
            .blob-wrap { padding: 16px; border-radius: 16px; }        
        }
    </style>

    <div class="blob-wrap" id="blobWrap">
        <svg id="connector-svg" aria-hidden="true"></svg>

        <div class="diagram">
            <?php if (have_rows('lab_problems')) : ?>
                <div class="row-top">
                    <?php while (have_rows('lab_problems')) : the_row(); ?>
                        <article class="problem-card">
                            <?php if ($title = get_sub_field('title')) : ?>
                                <h3><?= esc_html($title); ?></h3>
                            <?php endif; ?>

                            <?php if ($text = get_sub_field('text')) : ?>
                                <p><?= esc_html($text); ?></p>
                            <?php endif; ?>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

            <div class="row-result">
                <div class="result-cloud" id="resultCloud">
                    <div class="cloud-body">
                        <?php if ($result = get_field('problem_result')) : ?>
                            <h3><?= esc_html($result); ?></h3>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function drawConnectors() {
            const svg = document.getElementById('connector-svg');
            const blob = document.getElementById('blobWrap');
            const result = document.querySelector('#resultCloud .cloud-body');
            const cards = document.querySelectorAll('.problem-card');

            if (!svg || !blob || !result || !cards.length) return;

            svg.innerHTML = '';

            const blobRect = blob.getBoundingClientRect();
            const resRect = result.getBoundingClientRect();

            svg.setAttribute('viewBox', `0 0 ${blobRect.width} ${blobRect.height}`);
            svg.setAttribute('preserveAspectRatio', 'none');

            const tx = resRect.left + resRect.width / 2 - blobRect.left;
            const ty = resRect.top - blobRect.top;

            cards.forEach(card => {
                const r = card.getBoundingClientRect();

                const sx = r.left + r.width / 2 - blobRect.left;
                const sy = r.bottom - blobRect.top;

                const cy1 = sy + (ty - sy) * 0.5;
                const cy2 = ty - (ty - sy) * 0.25;

                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('class', 'conn-line');
                path.setAttribute('d', `M${sx},${sy} C${sx},${cy1} ${tx},${cy2} ${tx},${ty}`);

                svg.appendChild(path);
            });
        }

        window.addEventListener('load', () => {
            setTimeout(drawConnectors, 120);
        });

        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(drawConnectors, 150);
        });
    </script>

<?php
    return ob_get_clean();
}
<?php

/**
 * Healthray HIMS – "How It Works" Section
 * Shortcode: [healthray_how_it_works]
 */

if (! defined('ABSPATH')) {
    exit;
}

add_shortcode('healthray_how_it_works', 'healthray_hims_render_section');
function healthray_hims_get_steps(): array
{
    return [
        [
            'id'    => 'registration',
            'label' => 'Registration',
            'short' => 'Patient arrives & UHID / ABHA ID created instantly',
            'color' => '#378ADD',
            'flow'  => [
                'Patient arrives at hospital',
                'UHID + ABHA ID generated instantly',
                'Record linked to every department',
            ],
            'icon'  => '<path d="M9 2a4 4 0 100 8 4 4 0 000-8zM3 18c0-3.314 2.686-6 6-6s6 2.686 6 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" fill="none"/>',
        ],
        [
            'id'    => 'opd',
            'label' => 'OPD / IPD',
            'short' => 'Appointments booked, queue managed, patient admitted digitally',
            'color' => '#185FA5',
            'flow'  => [
                'Appointment booked online or at counter',
                'Queue managed intelligently',
                'Patient admitted or consulted digitally',
            ],
            'icon'  => '<rect x="3" y="4" width="14" height="14" rx="2" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="M3 9h14M8 2v4M12 2v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
        ],
        [
            'id'    => 'emr',
            'label' => 'EMR',
            'short' => 'Complete history - doctor writes orders from one screen',
            'color' => '#1D9E75',
            'flow'  => [
                'Full patient history displayed',
                'Doctor writes orders & prescriptions',
                'All changes saved in real time',
            ],
            'icon'  => '<path d="M6 2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2z" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="M8 8h4M8 11h3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>',
        ],
        [
            'id'    => 'lab',
            'label' => 'Lab & Radiology',
            'short' => 'Orders received instantly - results flow back into EMR',
            'color' => '#0F6E56',
            'flow'  => [
                'Lab / radiology orders received',
                'Tests processed immediately',
                'Results auto-posted to EMR',
            ],
            'icon'  => '<path d="M7 2v8l-3 5h10l-3-5V2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" fill="none"/><path d="M5 2h8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>',
        ],
        [
            'id'    => 'pharmacy',
            'label' => 'Pharmacy',
            'short' => 'Digital Rx dispensed and automatically charged to bill',
            'color' => '#185FA5',
            'flow'  => [
                'Prescription received digitally',
                'Medicine dispensed to patient',
                'Charge posted to patient bill',
            ],
            'icon'  => '<path d="M12 4H8a2 2 0 00-2 2v10a2 2 0 002 2h4a2 2 0 002-2V6a2 2 0 00-2-2z" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="M10 8v4M8 10h4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>',
        ],
        [
            'id'    => 'billing',
            'label' => 'Billing',
            'short' => 'Every service captured in real time - GST invoice generated',
            'color' => '#378ADD',
            'flow'  => [
                'Every service captured in real time',
                'Insurance eligibility validated',
                'GST-compliant invoice generated',
            ],
            'icon'  => '<path d="M4 4h12v2H4zM4 8h12M4 12h8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><rect x="3" y="3" width="14" height="14" rx="1.5" stroke="currentColor" stroke-width="1.6" fill="none"/>',
        ],
        [
            'id'    => 'inventory',
            'label' => 'Inventory',
            'short' => 'Stock updated on every dispensing - reorder triggered automatically',
            'color' => '#1D9E75',
            'flow'  => [
                'Stock updated on every dispensing',
                'Low-level alert fires automatically',
                'Reorder placed without manual action',
            ],
            'icon'  => '<rect x="2" y="7" width="16" height="10" rx="1.5" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="M6 7V5a4 4 0 018 0v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
        ],
        [
            'id'    => 'mis',
            'label' => 'MIS Dashboard',
            'short' => 'Leadership sees live bed, revenue & compliance data instantly',
            'color' => '#0C447C',
            'flow'  => [
                'Live bed occupancy visible',
                'Revenue & compliance tracked',
                'No end-of-day wait for reports',
            ],
            'icon'  => '<rect x="2" y="3" width="16" height="12" rx="1.5" stroke="currentColor" stroke-width="1.6" fill="none"/><path d="M6 15v2M10 15v2M14 15v2M4 17h12" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><path d="M6 11V9M9 11V7M12 11V8M15 11v-2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>',
        ],
    ];
}

function healthray_hims_render_section($atts): string
{
    define('HEALTHRAY_HIMS_LOADED', true);

    $steps = healthray_hims_get_steps();
    $uid   = 'hrhiw';

    $steps_json = wp_json_encode(array_map(function ($s) {
        return [
            'id'    => $s['id'],
            'label' => $s['label'],
            'color' => $s['color'],
            'flow'  => $s['flow'],
        ];
    }, $steps));

    $cx = 210;
    $cy = 210;
    $r = 163;
    $nodeR = 34;
    $angles = [270, 315, 0, 45, 90, 135, 180, 225];

    ob_start();
?>
    <style id="<?php echo esc_attr($uid); ?>-css">
        :root {
            --hr-blue-900: #042C53;
            --hr-blue-800: #0C447C;
            --hr-blue-700: #185FA5;
            --hr-blue-400: #378ADD;
            --hr-blue-200: #85B7EB;
            --hr-teal-400: #1D9E75;
            --hr-white: #fff;
            --hr-radius: 12px;
            --hr-card-bg: rgba(255, 255, 255, .055);
            --hr-card-border: rgba(255, 255, 255, .09);
            --hr-card-active-bg: rgba(55, 138, 221, .10);
            --hr-card-active-border: #378ADD;
        }
        .hr-hiw-section { background: var(--hr-blue-900); padding: 60px 20px; position: relative; overflow: hidden; border-radius: 16px; }
        .hr-hiw-section::before,
        .hr-hiw-section::after { content: ''; position: absolute; border-radius: 50%; pointer-events: none; }
        .hr-hiw-section::before { top: -140px; right: -140px; width: 480px; height: 480px; background: radial-gradient(circle, rgba(55, 138, 221, .16) 0%, transparent 70%); }
        .hr-hiw-section::after { bottom: -100px; left: -100px; width: 380px; height: 380px; background: radial-gradient(circle, rgba(29, 158, 117, .14) 0%, transparent 70%); }
        .hr-hiw-inner { max-width: 1160px; margin: 0 auto; position: relative; z-index: 2; }
        .hr-hiw-label { display: inline-flex; align-items: center; gap: 7px; background: rgba(55, 138, 221, .15); color: var(--hr-blue-200); font-size: 11px; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; padding: 6px 14px; border-radius: 100px; border: 1px solid rgba(55, 138, 221, .25); margin-bottom: 16px; }
        .hr-hiw-label svg { width: 13px; height: 13px; flex-shrink: 0; }
        .hr-hiw-grid { display: grid; grid-template-columns: 1fr 400px 1fr; gap: 28px; align-items: center; }
        .hr-hiw-col { display: flex; flex-direction: column; gap: 14px; }
        .hr-step-card { background: var(--hr-card-bg); border: 1px solid var(--hr-card-border); border-radius: var(--hr-radius); padding: 14px; cursor: pointer; transition: background .22s, border-color .22s, box-shadow .22s; -webkit-tap-highlight-color: transparent; outline: none; text-align: left; width: 100%; }
        .hr-step-card:hover,
        .hr-step-card.hr-active { background: var(--hr-card-active-bg); border-color: var(--hr-card-active-border); }
        .hr-step-card:focus-visible { outline: 2px solid var(--hr-blue-400); outline-offset: 2px; }
        .hr-step-head { display: flex; align-items: center; gap: 9px; margin-bottom: 5px; }
        .hr-step-num { width: 25px; height: 25px; border-radius: 50%; background: var(--hr-blue-800); color: var(--hr-blue-200); font-size: 11px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid var(--hr-blue-700); transition: background .22s, color .22s; }
        .hr-step-card.hr-active .hr-step-num { background: var(--hr-blue-400); color: var(--hr-white); border-color: var(--hr-blue-400); }
        .hr-step-title { font-size: 14px; color: var(--hr-white); letter-spacing: 1px; text-transform: uppercase; }
        .hr-step-desc { font-size: 12px; color: rgba(255, 255, 255, .45); padding-left: 34px; transition: color .22s; }
        .hr-step-card.hr-active .hr-step-desc { color: rgba(255, 255, 255, .72); }
        .hr-hiw-diagram { width: 100%; max-width: 400px; margin: 0 auto; display: block; flex-shrink: 0; }
        .hr-hiw-diagram svg { width: 100%; height: auto; display: block; overflow: visible; }
        .hr-detail-panel { margin-top: 40px; background: rgba(255, 255, 255, .045); border: 1px solid rgba(55, 138, 221, .28); border-radius: 16px; padding: 26px 28px; min-height: 100px; transition: border-color .3s; }
        .hr-detail-panel h4 { font-size: 16px; color: var(--hr-white); margin: 0 0 14px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .hr-detail-tag { background: var(--hr-blue-800); color: var(--hr-blue-200); font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 100px; letter-spacing: .07em; text-transform: uppercase; }
        .hr-flow-steps { display: flex; flex-wrap: wrap; gap: 6px 0; align-items: center; }
        .hr-flow-step { font-size: 13px; color: rgba(255, 255, 255, .65); padding-left: 10px; }
        .hr-flow-step:not(:last-child)::after { content: '→'; margin-left: 7px; color: var(--hr-blue-400); font-weight: 700; font-size: 14px; }
        .elementor-widget-shortcode .hr-hiw-section { margin-left: calc(-1 * var(--e-con-boxed-padding-left, 0px)); margin-right: calc(-1 * var(--e-con-boxed-padding-right, 0px)); }
        @media (max-width:1024px) { .hr-hiw-grid { grid-template-columns: 1fr 340px 1fr; gap: 20px; } }
        @media (max-width:820px) {
            .hr-hiw-grid { grid-template-columns: 1fr; gap: 24px; }
            .hr-col-right { order: 3; }
            .hr-hiw-diagram { max-width: 300px; }        
        }
        @media (max-width:520px) {
            .hr-hiw-section { padding: 60px 16px 56px; }
            .hr-hiw-diagram { max-width: 260px; }
            .hr-flow-step { font-size: 12px; }
            .hr-detail-panel { padding: 18px 16px; }
            .hr-flow-step:not(:last-child)::after { margin-left: 5px; }        
        }
        @media (prefers-reduced-motion:reduce) {
            .hr-step-card,
            .hr-step-num,
            .hr-step-desc { transition: none; }        
        }
    </style>

    <?php ?>
    <section class="hr-hiw-section" id="<?php echo esc_attr($uid); ?>" aria-label="How Healthray's Hospital Information Management System Works" itemscope itemtype="https://schema.org/HowTo">
        <div class="hr-hiw-inner">
            <div class="hr-hiw-grid" role="region" aria-label="Department workflow steps">
                <div class="hr-hiw-col hr-col-left" id="<?php echo esc_attr($uid); ?>-col-left">
                    <?php foreach (array_slice($steps, 0, 4) as $idx => $s) : ?>
                        <button class="hr-step-card<?php echo $idx === 0 ? ' hr-active' : ''; ?>" id="<?php echo esc_attr($uid . '-step-' . $idx); ?>" data-step="<?php echo esc_attr($idx); ?>" aria-pressed="<?php echo $idx === 0 ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr($uid . '-detail'); ?>" itemprop="step" itemscope itemtype="https://schema.org/HowToStep">
                            <div class="hr-step-head">
                                <span class="hr-step-num" aria-hidden="true"><?php echo esc_html($idx + 1); ?></span>
                                <span class="hr-step-title" itemprop="name"><?php echo esc_html($s['label']); ?></span>
                            </div>
                            <div class="hr-step-desc" itemprop="text"><?php echo esc_html($s['short']); ?></div>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="hr-hiw-diagram" aria-hidden="true" role="img" aria-label="Circular workflow diagram with Healthray at centre">
                    <svg viewBox="0 0 420 420" xmlns="http://www.w3.org/2000/svg" id="<?php echo esc_attr($uid); ?>-svg" role="presentation" focusable="false">
                        <title>Healthray HIMS Workflow Diagram</title>
                        <defs>
                            <marker id="<?php echo esc_attr($uid); ?>-arrow" markerWidth="7" markerHeight="7" refX="5" refY="3" orient="auto">
                                <path d="M0,0 L0,6 L7,3 z" fill="rgba(55,138,221,.65)" />
                            </marker>
                        </defs>
                        <circle cx="<?php echo $cx; ?>" cy="<?php echo $cy; ?>" r="<?php echo $r; ?>" fill="none" stroke="rgba(55,138,221,.10)" stroke-width="1.5" stroke-dasharray="6 5" />
                        <?php foreach ($steps as $i => $s) :
                            $a  = deg2rad($angles[$i]);
                            $nx = $cx + $r * cos($a);
                            $ny = $cy + $r * sin($a);
                            $innerR = 74;
                            $lx1 = $cx + ($r - $nodeR - 2) * cos($a);
                            $ly1 = $cy + ($r - $nodeR - 2) * sin($a);
                            $lx2 = $cx + $innerR * cos($a);
                            $ly2 = $cy + $innerR * sin($a);
                        ?>
                            <line id="<?php echo esc_attr($uid . '-conn-' . $i); ?>" x1="<?php echo round($lx1, 2); ?>" y1="<?php echo round($ly1, 2); ?>" x2="<?php echo round($lx2, 2); ?>" y2="<?php echo round($ly2, 2); ?>" stroke="rgba(55,138,221,.18)" stroke-width="1" stroke-dasharray="4 3" />
                        <?php endforeach; ?>

                        <circle cx="<?php echo $cx; ?>" cy="<?php echo $cy; ?>" r="74" fill="#0C447C" stroke="#185FA5" stroke-width="1.5" />
                        <circle cx="<?php echo $cx; ?>" cy="<?php echo $cy; ?>" r="62" fill="#042C53" />
                        <text x="<?php echo $cx; ?>" y="<?php echo $cy - 10; ?>" text-anchor="middle" fill="#85B7EB" font-size="15" font-weight="700" letter-spacing="1.5">HEALTHRAY</text>
                        <text x="<?php echo $cx; ?>" y="<?php echo $cy + 6; ?>" text-anchor="middle" fill="rgba(181,212,244,.65)" font-size="13" margin-top="5px">HIMS</text>
                        <?php foreach ($steps as $i => $s) :
                            $a   = deg2rad($angles[$i]);
                            $nx  = round($cx + $r * cos($a), 2);
                            $ny  = round($cy + $r * sin($a), 2);
                            $words = explode(' ', $s['label']);
                            $line1 = $words[0];
                            $line2 = count($words) > 1 ? implode(' ', array_slice($words, 1)) : '';
                            $isFirst = ($i === 0);
                        ?>
                            <g id="<?php echo esc_attr($uid . '-node-' . $i); ?>" data-step="<?php echo esc_attr($i); ?>" class="hr-svg-node" style="cursor:pointer" role="button" tabindex="0" aria-label="<?php echo esc_attr($s['label']); ?>">
                                <circle id="<?php echo esc_attr($uid . '-nc-' . $i); ?>" cx="<?php echo $nx; ?>" cy="<?php echo $ny; ?>" r="<?php echo $nodeR; ?>" fill="<?php echo $isFirst ? esc_attr($s['color']) : '#0C447C'; ?>" stroke="<?php echo $isFirst ? esc_attr($s['color']) : 'rgba(55,138,221,.28)'; ?>" stroke-width="1.5" />
                                <text x="<?php echo $nx; ?>" y="<?php echo $line2 ? $ny - 3 : $ny + 4; ?>" text-anchor="middle" fill="#B5D4F4" font-size="10" font-weight="700"><?php echo esc_html($line1); ?></text>
                                <?php if ($line2) : ?>
                                    <text x="<?php echo $nx; ?>" y="<?php echo $ny + 9; ?>" text-anchor="middle" fill="#B5D4F4" font-size="10"><?php echo esc_html($line2); ?></text>
                                <?php endif; ?>
                            </g>
                        <?php endforeach; ?>
                    </svg>
                </div>

                <div class="hr-hiw-col hr-col-right" id="<?php echo esc_attr($uid); ?>-col-right">
                    <?php foreach (array_slice($steps, 4) as $k => $s) :
                        $idx = $k + 4;
                    ?>
                        <button class="hr-step-card" id="<?php echo esc_attr($uid . '-step-' . $idx); ?>" data-step="<?php echo esc_attr($idx); ?>" aria-pressed="false" aria-controls="<?php echo esc_attr($uid . '-detail'); ?>" itemprop="step" itemscope itemtype="https://schema.org/HowToStep">
                            <div class="hr-step-head">
                                <span class="hr-step-num" aria-hidden="true"><?php echo esc_html($idx + 1); ?></span>
                                <span class="hr-step-title" itemprop="name"><?php echo esc_html($s['label']); ?></span>
                            </div>
                            <div class="hr-step-desc" itemprop="text"><?php echo esc_html($s['short']); ?></div>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="hr-detail-panel" id="<?php echo esc_attr($uid . '-detail'); ?>" role="region" aria-live="polite" aria-atomic="true" aria-label="Workflow detail">
                <h4>
                    <span id="<?php echo esc_attr($uid . '-dtitle'); ?>">
                        <?php echo esc_html($steps[0]['label']); ?>
                    </span>
                    <span class="hr-detail-tag" id="<?php echo esc_attr($uid . '-dtag'); ?>">Step 1 of 8</span>
                </h4>
                <div class="hr-flow-steps" id="<?php echo esc_attr($uid . '-dflow'); ?>">
                    <?php foreach ($steps[0]['flow'] as $f) : ?>
                        <div class="hr-flow-step">
                            <span class="hr-flow-dot" aria-hidden="true"></span>
                            <?php echo esc_html($f); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </section>

    <?php ?>
    <script>
        (function() {
            'use strict';
            var UID = <?php echo wp_json_encode($uid); ?>;
            var STEPS = <?php echo $steps_json; ?>;
            var section = document.getElementById(UID);
            if (!section) return;

            function q(id) {
                return document.getElementById(UID + '-' + id);
            }

            var dTitle = q('dtitle');
            var dTag = q('dtag');
            var dFlow = q('dflow');
            var stepCards = section.querySelectorAll('.hr-step-card');
            var svgNodes = section.querySelectorAll('.hr-svg-node');
            var activeIdx = 0;

            function activate(idx) {
                if (idx === activeIdx && document.getElementById(UID + '-step-0').classList.contains('hr-active')) return;
                activeIdx = idx;

                stepCards.forEach(function(c, j) {
                    var isActive = (j === idx);
                    c.classList.toggle('hr-active', isActive);
                    c.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                });

                STEPS.forEach(function(s, j) {
                    var circle = document.getElementById(UID + '-nc-' + j);
                    var conn = document.getElementById(UID + '-conn-' + j);
                    if (j === idx) {
                        circle.setAttribute('fill', s.color);
                        circle.setAttribute('stroke', s.color);
                        if (conn) {
                            conn.setAttribute('stroke', s.color);
                            conn.setAttribute('stroke-width', '2');
                            conn.setAttribute('stroke-dasharray', '0');
                        }
                    } else {
                        circle.setAttribute('fill', '#0C447C');
                        circle.setAttribute('stroke', 'rgba(55,138,221,.28)');
                        if (conn) {
                            conn.setAttribute('stroke', 'rgba(55,138,221,.18)');
                            conn.setAttribute('stroke-width', '1');
                            conn.setAttribute('stroke-dasharray', '4 3');
                        }
                    }
                });

                var s = STEPS[idx];
                dTitle.textContent = s.label;
                dTag.textContent = 'Step ' + (idx + 1) + ' of 8';

                var html = '';
                s.flow.forEach(function(f) {
                    html += '<div class="hr-flow-step"><span class="hr-flow-dot" aria-hidden="true"></span>' +
                        escHtml(f) + '</div>';
                });
                dFlow.innerHTML = html;
            }

            function escHtml(str) {
                return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            }

            stepCards.forEach(function(c) {
                c.addEventListener('click', function() {
                    activate(parseInt(this.getAttribute('data-step'), 10));
                });
                c.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        activate(parseInt(this.getAttribute('data-step'), 10));
                    }
                });
            });

            svgNodes.forEach(function(g) {
                g.addEventListener('click', function() {
                    activate(parseInt(this.getAttribute('data-step'), 10));
                });
                g.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        activate(parseInt(this.getAttribute('data-step'), 10));
                    }
                });
            });

            var autoRotate = true;
            var timer;

            function startRotate() {
                if (!autoRotate) return;
                timer = setInterval(function() {
                    activate((activeIdx + 1) % STEPS.length);
                }, 3200);
            }

            function stopRotate() {
                autoRotate = false;
                clearInterval(timer);
            }

            section.addEventListener('mouseenter', stopRotate);
            section.addEventListener('focusin', stopRotate);
            section.addEventListener('touchstart', stopRotate, {
                passive: true
            });

            setTimeout(startRotate, 1500);
            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function(entries) {
                    entries.forEach(function(e) {
                        if (!e.isIntersecting) autoRotate = false;
                        else if (autoRotate) startRotate();
                    });
                }, {
                    threshold: 0.25
                });
                io.observe(section);
            }

        })();
    </script>
<?php
    return ob_get_clean();
}
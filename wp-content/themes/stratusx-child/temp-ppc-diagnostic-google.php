<?php

/**
 * Template Name: PPC - Diagnostic Google
 *
 * Page 82661, /ppc/diagnostic-google/. CSS/JS: css/ppc-diagnostic.css, js/ppc-diagnostic.js (loaded by functions.php, key "diagnostic").
 * Form: CF7 "PPC - Diagnostic centre", 2 steps; step 1 scans go in bed_size; all fields required; Odoo keys bed_size, name, mobile, email, city, ref.
 * Popup (data-dg-pop): top bar, 6 "Book a free demo", "Get my price", "Show me Healthray for my centre", FAQ link, phone bar.
 * Label per ad group: ?k=dc|path|scan. SEO title, description and noindex: Yoast.
 */

defined('ABSPATH') || exit;

$hr_dg_form = '[contact-form-7 id="845366c" title="PPC - Diagnostic centre"]';
$hr_dg_up   = 'https://healthray.com/wp-content/uploads';
$hr_dg_m    = $hr_dg_up . '/2026/11';

// Label above the heading, per ad group (same list in js/ppc-diagnostic.js, for when a cache drops ?k=)
$hr_dg_heads = array(
	'dc'   => 'Diagnostic centre software',
	'path' => 'Pathology lab and radiology software',
	'scan' => 'Radiology and USG reporting software',
);
$hr_dg_k   = isset($_GET['k']) ? sanitize_key(wp_unslash($_GET['k'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$hr_dg_eye = isset($hr_dg_heads[$hr_dg_k]) ? $hr_dg_heads[$hr_dg_k] : 'Diagnostic centre software';

// <picture> with a phone file (343 wide) and a desktop file (640 x 440)
$hr_dg_pic = function ($name, $alt, $class = '') use ($hr_dg_m) {
	return '<picture' . ('' !== $class ? ' class="' . esc_attr($class) . '"' : '') . '><source media="(max-width:820px)" srcset="' . esc_url($hr_dg_m . '/' . $name . '-phone.webp') . '" width="343" height="300"><img src="' . esc_url($hr_dg_m . '/' . $name . '.webp') . '" width="640" height="440" loading="lazy" alt="' . esc_attr($alt) . '"></picture>';
};
$hr_dg_logos = array(
	array('2024/06/Om-Pathology-Lab-Healthray.webp', 'Om Pathology Lab', 'Surat, Gujarat'),
	array('2024/01/Pratham-lab-1.webp', 'Pratham Laboratory', 'Surat, Gujarat'),
	array('2024/01/Diabcare-lab-1.webp', 'Diabcare Laboratory', 'Mumbai, Maharashtra'),
	array('2024/06/Bansal-Diagnostics-Healthray.webp', 'Bansal Diagnostics', 'Kurukshetra, Haryana'),
	array('2024/06/Omed-Diagnostics-Healthray.webp', 'Omed Diagnostics', 'Cachar, Assam'),
	array('2024/06/Chakraa-Medical-Center-Healthray.webp', 'Chakraa Medical Center', 'Tiruppur, Tamil Nadu'),
);
?>
<script>
	window.HR_DG = <?php echo wp_json_encode(array('uploads' => $hr_dg_up)); ?>;
</script>
<?php get_template_part('templates/ppc/header', null, array('attrs' => array('data-dg-pop' => ''))); ?>

<main>

	<section class="hero" id="demo">
		<div class="wrap">
			<div class="h-copy">
				<p class="eb" id="eyebrow"><?php echo esc_html($hr_dg_eye); ?></p>
				<h1 id="headline">Your lab, X&#8209;ray and sonography in one software. <span>One bill for blood tests and scans.</span></h1>
				<p class="sub">For centres with a lab and X&#8209;ray, sonography (USG), ECG, CT or MRI. Make scan reports from your own formats. Send blood test reports on WhatsApp.</p>
				<ul class="ticks">
					<li>We move your old data for you</li>
					<li>Help on call, 24/7</li>
					<li>Free demo. Free trial available.</li>
				</ul>
				<p class="mini"><b>★ 4.8</b><i>·</i><b>5,000+</b> labs<i>·</i>ABDM<i>·</i>ISO 27001</p>
			</div>
			<div class="fm" id="hr-dg-form">
				<h2>Book a free demo</h2>
				<p class="fs">30 minutes. We show it for your kind of centre.</p>
				<div class="prog">STEP <span class="stepno">1</span> OF 2</div>
				<div class="progbar" aria-hidden="true"><i></i></div>
				<?php echo do_shortcode($hr_dg_form); ?>
				<p class="safe">No unwanted calls. We call only to fix your demo time.</p>
			</div>
			<div class="h-proof">
				<p class="count"><b>5,000+ labs</b> in India use Healthray. <b>4M+</b> reports printed.</p>
				<div class="sealrow">
					<div class="seals">
						<span class="seal"><img src="<?php echo esc_url($hr_dg_up . '/2026/07/NABH-ehr-certified-150x150.webp'); ?>" alt="NABH"></span>
						<span class="seal"><img src="<?php echo esc_url($hr_dg_up . '/2026/03/abdm-healthray-150x150.webp'); ?>" alt="ABDM"></span>
						<span class="seal"><img src="<?php echo esc_url($hr_dg_up . '/2025/07/NHA.webp'); ?>" alt="NHA"></span>
						<span class="seal"><img src="<?php echo esc_url($hr_dg_up . '/2026/03/iso-27001-healthray-150x150.webp'); ?>" alt="ISO 27001"></span>
					</div>
					<p class="sealtext"><b>NABH certified · Works with ABDM</b>NHA approved · ISO 27001 for data safety</p>
				</div>
				<div class="stars" aria-label="Ratings">
					<span><b>4.8</b><i aria-hidden="true">★★★★★</i>Capterra</span>
					<span><b>4.8</b><i aria-hidden="true">★★★★★</i>SoftwareSuggest</span>
					<span><b>5.0</b><i aria-hidden="true">★★★★★</i>G2</span>
				</div>
			</div>
		</div>
	</section>

	<section class="labs" aria-label="Labs and hospitals that use Healthray">
		<div class="wrap labs-h">
			<h2>Diagnostic centres, labs and hospitals that use <span class="hl">Healthray</span></h2>
		</div>
		<div class="rail"><div class="track" id="track"></div></div>
	</section>

	<section class="see">
		<div class="wrap">
			<div class="head2">
				<h2 class="big">Watch one patient's <span class="hl">blood tests and X&#8209;ray</span> go on one bill.</h2>
				<p>1 minute: billing at the counter, the lab, the X&#8209;ray report, both reports on WhatsApp, and your collection report at night.</p>
			</div>
			<div class="player" id="player" data-src="<?php echo esc_url($hr_dg_m . '/diag-tour-web.mp4'); ?>">
				<button type="button" class="pl-btn" id="playbtn" aria-label="Play the 1-minute video"><img src="<?php echo esc_url($hr_dg_m . '/lab-tour-poster.webp'); ?>" width="1920" height="1080" alt="Healthray diagnostic centre software: one bill for blood tests and an X&#8209;ray, and both reports on WhatsApp" loading="lazy"><span class="play"></span><span class="dur">1:07<small>Watch the video</small></span></button>
				<button type="button" class="pl-pp" id="playpp" aria-label="Play"><svg class="i-play" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.2v13.6L19.2 12z" /></svg><svg class="i-pause" viewBox="0 0 24 24" aria-hidden="true"><path d="M6.8 5h3.7v14H6.8zM13.5 5h3.7v14h-3.7z" /></svg></button>
				<button type="button" class="pl-fs" id="playfs" aria-label="Full screen"><svg class="i-max" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5" /></svg><svg class="i-min" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 4v5H4M15 4v5h5M9 20v-5H4M15 20v-5h5" /></svg></button>
			</div>
		</div>
	</section>

	<section class="scan" aria-labelledby="scan-h">
		<div class="wrap">
			<h2 class="big" id="scan-h">Write USG and X&#8209;ray reports <span class="hl">in your own format</span>.</h2>
			<p class="sh-sub">Add your USG, X&#8209;ray, CT and MRI Word formats once. Then, for each patient, open the format, change only what is different, and sign.</p>
			<div class="v1grid">
				<div class="v1list">
					<button type="button" class="v1it st on" data-i="0"><span class="no">01 · YOUR FORMATS</span><span class="h3">Add your Word formats once</span><span class="p">Bring each USG, X&#8209;ray, CT or MRI format from Word, or copy one and edit it. Use it again for every patient.</span><i class="fill"></i></button>
					<button type="button" class="v1it st" data-i="1"><span class="no">02 · THE REPORT</span><span class="h3">Change only what is new for this patient</span><span class="p">The patient's name, age and case number fill in by themselves. Bold, lists and tables work like Word. Check the PDF before you save.</span><i class="fill"></i></button>
					<button type="button" class="v1it st" data-i="2"><span class="no">03 · IMAGES AND SIGN</span><span class="h3">Attach the machine's images, then sign</span><span class="p">Attach the machine's images (DICOM zip) or its PDF to the patient's case. Signed reports show "Signed off" in your list.</span><i class="fill"></i></button>
				</div>
				<div class="v1pic">
					<div class="ptile">
						<?php
						echo $hr_dg_pic('s4-formats', "Healthray's list of USG report formats with the Import from Word (.docx) button", 'on'); // phpcs:ignore WordPress.Security.EscapeOutput
						echo $hr_dg_pic('s4-editor', "A USG neck report open in Healthray: the patient's name, age and case number on top, one line of the report being changed"); // phpcs:ignore WordPress.Security.EscapeOutput
						echo $hr_dg_pic('s4-sign', "Healthray's USG case list: Upload .dcm zip and Upload PDF buttons on a new case, and cases marked Signed off"); // phpcs:ignore WordPress.Security.EscapeOutput
						?>
					</div>
				</div>
			</div>
			<div class="scan-cta">
				<p>Bring one of your USG formats to the free demo. We show it in Healthray.</p><a class="bt" href="#demo" data-dg-pop>Book a free demo</a>
			</div>
		</div>
	</section>

	<section class="s11" aria-labelledby="s11-h">
		<div class="wrap">
			<h2 class="big" id="s11-h">Your centre does sonography, X&#8209;ray or CT too? <span class="hl">Make their reports and bills in Healthray.</span></h2>
			<p class="sh-sub">Healthray has 8 scans along with the lab. Each scan has its own report formats and its own list of patients. All of them go on one bill with blood tests.</p>
			<div class="g11">
				<?php
				$hr_dg_scans = array(
					array('usg', 'USG', 'Sonography reports', 'Hands in gloves on an ultrasound machine'),
					array('xray', 'X&#8209;ray', 'Chest PA, knee and more', 'An X-ray room with the X-ray machine and table'),
					array('dxray', 'Digital X&#8209;ray', 'With DICOM images', 'A technician looking at digital X-ray images on a computer'),
					array('ct', 'CT scan', 'CT brain, CECT chest and more', 'A technician helping a patient at a CT scanner'),
					array('mri', 'MRI', 'MRI brain, spine and more', 'A doctor with a patient at an MRI machine'),
					array('ecg', 'ECG', 'Rest ECG reports', 'ECG leads being put on a patient'),
					array('echo', '2D Echo', 'Echo and cardiogram reports', 'An ultrasound screen showing the heart'),
					array('tmt', 'TMT', 'Treadmill test reports', 'A patient walking on a treadmill during a stress test'),
				);
				foreach ($hr_dg_scans as $s) {
					echo '<figure class="pc"><img src="' . esc_url($hr_dg_m . '/s11-photo-' . $s[0] . '.webp') . '" width="600" height="450" loading="lazy" alt="' . esc_attr($s[3]) . '"><figcaption><b>' . $s[1] . '</b><span>' . esc_html($s[2]) . '</span></figcaption></figure>';
				}
				?>
			</div>
			<div class="s11-end">
				<p>Tell us which scans you do. In the free demo, we show you those.</p><a class="bt" href="#demo" data-dg-pop>Book a free demo</a>
			</div>
		</div>
	</section>

	<section class="m5" aria-labelledby="m5-h">
		<div class="wrap">
			<div class="c3top">
				<div>
					<h2 class="big" id="m5-h">See today's collection of <span class="hl">the lab and each scan, separately</span>.</h2>
					<p class="sh-sub">Blood tests and scans are on one bill. But your evening report shows each one alone, with cash, discount and pending money.</p>
				</div>
				<div class="c3arr"><button type="button" class="prev" aria-label="Previous"><svg viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7" /></svg></button><button type="button" class="next" aria-label="Next"><svg viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" /></svg></button></div>
			</div>
			<div class="c3" tabindex="0" aria-label="Four reports, slide to see all">
				<div class="sl">
					<div class="pt"><?php echo $hr_dg_pic('s5-dept', "Healthray's business analysis for today by case type: Lab, X-Ray, USG and CT Scan, each with tests, billing, discount and income, and the total"); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<h3>Collection of lab, X&#8209;ray, USG and CT, each one separately</h3>
					<p>Bills, discount and money received for each. For today or any date.</p>
				</div>
				<div class="sl">
					<div class="pt"><?php echo $hr_dg_pic('s5-pay', "Healthray's daily business report: total income, refund and net income, then the money by cash, UPI and card"); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<h3>Cash, UPI and card totals, ready</h3>
					<p>See how much came in cash, UPI and card. Easy to match the cash at your counter.</p>
				</div>
				<div class="sl">
					<div class="pt"><?php echo $hr_dg_pic('s5-disc', "Healthray's payment details with the Discount reference box showing who allowed the discount, and the user wise report listing each discount with the staff member who made the bill"); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<h3>See who gave each discount</h3>
					<p>The bill asks who allowed the discount. One report shows every discount and the staff who gave it.</p>
				</div>
				<div class="sl">
					<div class="pt"><?php echo $hr_dg_pic('s5-due', "Healthray's transactions screen with the total due amount and number of cases, and the setting Restrict actions on payment due switched on"); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<h3>No report until the bill is paid</h3>
					<p>See how much money is pending. Turn on one setting, and an unpaid patient's report cannot be printed or sent.</p>
				</div>
			</div>
			<div class="c3dots"><button type="button" class="on" aria-label="Show report 1" aria-pressed="true"></button><button type="button" aria-label="Show report 2" aria-pressed="false"></button><button type="button" aria-label="Show report 3" aria-pressed="false"></button><button type="button" aria-label="Show report 4" aria-pressed="false"></button></div>
			<div class="m5-cta">
				<p>In the free demo, ask us to show these reports.</p><a class="bt" href="#demo" data-dg-pop>Book a free demo</a>
			</div>
		</div>
	</section>

	<section class="r6" aria-labelledby="r6-h">
		<div class="wrap">
			<h2 class="big" id="r6-h">Reports reach the patient and the doctor <span class="hl">on WhatsApp, by themselves</span>.</h2>
			<p class="sh-sub">When the report is ready and the bill is paid, Healthray sends the PDF. Your staff does not send each one, and patients don't call to ask.</p>
			<div class="c6grid">
				<div class="c6steps">
					<div class="st on">
						<div class="tile"><?php echo $hr_dg_pic('s6-auto', "Healthray's message settings: Auto Send Report to Patient on Full Payment, ticked for WhatsApp"); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
						<span class="no">01</span>
						<h3>Sent when the bill is fully paid</h3>
						<p class="txt">Turn on one setting. The report PDF goes to the patient's WhatsApp when the report is ready and the bill is fully paid.</p>
					</div>
					<div class="st">
						<div class="tile"><?php echo $hr_dg_pic('s6-doctor', "Healthray's message settings: Instant Alert to Referral Dr After Finalizing Report, ticked for WhatsApp"); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
						<span class="no">02</span>
						<h3>The referral doctor gets it too</h3>
						<p class="txt">Another setting sends the final report to the doctor who sent the patient. The doctor does not wait for the patient to come back.</p>
					</div>
					<div class="st">
						<div class="tile"><?php echo $hr_dg_pic('s6-pdf', "Healthray's Share report window: WhatsApp, letterhead, QR code and signature ticked, sent to the patient and the referral doctor"); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
						<span class="no">03</span>
						<h3>On your letterhead, with signature and QR code</h3>
						<p class="txt">Choose letterhead, signature and QR code for the PDF. You can also send any report by hand to the patient, the doctor or a B2B lab.</p>
					</div>
				</div>
				<div class="c6pic" aria-hidden="true">
					<div class="tile">
						<picture class="on"><img src="<?php echo esc_url($hr_dg_m . '/s6-auto.webp'); ?>" width="640" height="440" loading="lazy" alt=""></picture>
						<picture><img src="<?php echo esc_url($hr_dg_m . '/s6-doctor.webp'); ?>" width="640" height="440" loading="lazy" alt=""></picture>
						<picture><img src="<?php echo esc_url($hr_dg_m . '/s6-pdf.webp'); ?>" width="640" height="440" loading="lazy" alt=""></picture>
					</div>
				</div>
			</div>
			<div class="r6-cta">
				<p>See a report go out on WhatsApp in the free demo.</p><a class="bt" href="#demo" data-dg-pop>Book a free demo</a>
			</div>
		</div>
	</section>

	<section class="p7" aria-labelledby="p7-h">
		<div class="wrap">
			<h2 class="big" id="p7-h">Pay for your lab, and <span class="hl">only for the scans you do</span>.</h2>
			<p class="sh-sub">Every plan has the lab. Add only the scans your centre does. After the demo, we send your price.</p>
			<div class="c7">
				<div class="c7zero" aria-label="Zero rupees to start"><span>&#8377;</span>0<small>to start: free demo and free trial</small></div>
				<ul class="p7inc">
					<li><svg viewBox="0 0 24 24"><path d="M5 12.5l4.5 4.5L19 7.5" /></svg><span><b>Free demo and free trial.</b> See it, then use it in your own centre before you pay.</span></li>
					<li><svg viewBox="0 0 24 24"><path d="M5 12.5l4.5 4.5L19 7.5" /></svg><span><b>We move your old data.</b> No extra charge, in every plan.</span></li>
					<li><svg viewBox="0 0 24 24"><path d="M5 12.5l4.5 4.5L19 7.5" /></svg><span><b>Staff training, no extra charge.</b></span></li>
					<li><svg viewBox="0 0 24 24"><path d="M5 12.5l4.5 4.5L19 7.5" /></svg><span><b>Help 24 hours a day, no extra charge.</b></span></li>
				</ul>
			</div>
			<div class="c7after">
				<div>
					<p class="p7dep"><b>Every plan has the lab:</b> blood tests from bill to report, and your money reports. <b>Add only the scans you do:</b> USG, X&#8209;ray, Digital X&#8209;ray (DICOM), CT scan, MRI, ECG.</p>
					<p class="p7dep"><b>Your price depends on:</b> the scans you add, how many branches and collection centres you have, and how big your centre is.</p>
				</div>
				<a class="bt" href="#demo" data-dg-pop>Get my price</a>
			</div>
		</div>
	</section>

	<section class="s8" aria-labelledby="s8-h">
		<div class="wrap">
			<h2 class="big" id="s8-h">Moving from Word files or <span class="hl">another software</span>?</h2>
			<p class="sh-sub">Our team moves your old data and brings in your scan formats. You don't pay extra for it.</p>
			<div class="f8">
				<div><b>&#8377;0</b><strong>to move your old data</strong><span>From Word files, registers, offline or online software. In every plan.</span></div>
				<div><b>10 min</b><strong>to make your first bill and report</strong><span>The test list is ready in the software.</span></div>
				<div><b>2 weeks</b><strong>to set up everything, in most centres</strong><span>Your tests, normal ranges and scan formats. Many centres finish in a few days.</span></div>
				<div><b>24/7</b><strong>help on call</strong><span>Day and night, every day.</span></div>
			</div>
			<div class="f8x">
				<p class="ln"><b>Your USG and X&#8209;ray formats</b> are added from Word once, with one button.</p>
				<p class="ln"><b>We train each person on their own work:</b> the front desk, the technician, the pathologist and the radiologist.</p>
			</div>
			<div class="s8-end">
				<p>Tell us what you use today. We plan your move in the free demo.</p><a class="bt" href="#demo" data-dg-pop>Book a free demo</a>
			</div>
		</div>
	</section>

	<section class="say say9" aria-labelledby="r9-h">
		<div class="wrap">
			<h2 class="big" id="r9-h">What lab and centre owners <span class="hl">say about Healthray</span>.</h2>
			<div class="say-sub">
				<p>Real reviews from labs in Surat, Mumbai, Kurukshetra, Assam and Tiruppur, as written on healthray.com.</p>
				<div class="rv-arrows"><button type="button" class="prev" aria-label="Previous reviews"></button><button type="button" class="next" aria-label="Next reviews"></button></div>
			</div>
			<div class="rv-row" tabindex="0" aria-label="Six reviews, slide to see all">
				<?php
				$hr_dg_reviews = array(
					array('', 'Since we started using Healthray\'s LIMS, our staff\'s daily work has become much simpler, and its features have <mark>helped us bring down our overall lab expenses</mark>.'),
					array('k2', 'I\'m very happy with Healthray\'s LIMS. The system is <mark>easy to use, runs reliably</mark>, and has made our laboratory workflow much smoother while helping us maintain high quality standards.'),
					array('k3', 'Healthray\'s LIMS has comprehensive features that <mark>improve collaboration among our internal staff</mark> and contribute to real patient satisfaction.'),
					array('', 'This tailor-made solution makes our tasks easier and more simpler. Moreover, it has the AI properties that <mark>saves a lot of time</mark>.'),
					array('k2', 'This is an <mark>all-in-one lab solution</mark> and embracing Healthray\'s LIMS is one of our leading decisions. It transforms our whole laboratory system.'),
					array('k3', 'We are thrilled to use Healthray\'s LIMS solution to <mark>automate our laboratory operations and minimize errors</mark>. Recommend this software to all!'),
				);
				foreach ($hr_dg_reviews as $i => $rv) :
					$lg = $hr_dg_logos[$i];
				?>
					<blockquote class="rv<?php echo $rv[0] ? ' ' . esc_attr($rv[0]) : ''; ?>">
						<div class="rv-who"><span class="lg"><img src="<?php echo esc_url($hr_dg_up . '/' . $lg[0]); ?>" alt="<?php echo esc_attr($lg[1] . ' logo'); ?>" loading="lazy"></span>
							<div><b><?php echo esc_html($lg[1]); ?></b><span><?php echo esc_html($lg[2]); ?></span></div>
						</div>
						<p><?php echo wp_kses($rv[1], array('mark' => array())); ?></p>
					</blockquote>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="faq faq10" aria-labelledby="q10-h">
		<div class="wrap">
			<div>
				<h2 class="big" id="q10-h">Still have a question? <span class="hl">Most owners ask these.</span></h2>
				<p class="sh-sub faq-note">Did not find yours? <a href="#demo" data-dg-pop>Ask it in the free demo</a></p>
			</div>
			<div>
				<details name="dg-faq" open><summary>Can I start with the lab and add scans later?</summary><p>Yes. Every plan has the pathology lab. When you start USG, X&#8209;ray, CT, MRI or ECG, you add that one to your plan.</p></details>
				<details name="dg-faq"><summary>Is Healthray also radiology reporting software?</summary><p>Yes. Write USG (ultrasound), X&#8209;ray, CT, MRI and ECG reports in your own Word formats. Add the radiologist's signature and send them on WhatsApp. Your lab works in the same software.</p></details>
				<details name="dg-faq"><summary>Will it work with my lab machines?</summary><p>Healthray connects with your lab machines, so results come into the report by themselves. Tell us your machine names in the demo and we will check each one.</p></details>
				<details name="dg-faq"><summary>Can I add the scan machine's images to the case?</summary><p>Yes. Attach the machine's images (DICOM zip) and its PDF to each scan. You can also add up to 5 more files: PDF, JPG or PNG.</p></details>
				<details name="dg-faq"><summary>Will scan reports have my letterhead and the doctor's signature?</summary><p>Yes. Your letterhead goes on top and the radiologist's signature at the bottom of every scan report. You set it one time.</p></details>
				<details name="dg-faq"><summary>Do you have home blood collection?</summary><p>Yes. Your collection staff get their own screen. The patient gets a WhatsApp message with a live link when the staff is on the way.</p></details>
				<details name="dg-faq"><summary>Can I run more than one branch or collection centre?</summary><p>Yes. Add your branches and collection centres. You switch between branches without logging out, and your money reports show each one separately.</p></details>
				<details name="dg-faq"><summary>Is it online? Is my patients' data safe?</summary><p>Yes, it works online on any computer with internet. Healthray has ISO 27001 for data safety. Deleting a bill can need an OTP, and every change is saved with who did it.</p></details>
			</div>
		</div>
	</section>

	<section class="lc10" id="book" aria-labelledby="lc-h">
		<div class="wrap">
			<div class="logos" aria-hidden="true">
				<?php foreach (array(1, 2, 0, 3, 4, 5) as $i) : ?><span><img src="<?php echo esc_url($hr_dg_up . '/' . $hr_dg_logos[$i][0]); ?>" alt="" loading="lazy"></span><?php endforeach; ?>
			</div>
			<h2 class="big" id="lc-h">See how Healthray works for your centre, <span class="hl">before you decide</span>.</h2>
			<p class="sub">In a free 30-minute demo we show your blood tests, your scans, the bill and the reports.</p>
			<a class="go go1" href="#demo" data-dg-pop>Show me Healthray for my centre<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" /></svg></a>
			<p class="promise">No unwanted calls. We call only to fix your demo time.</p>
		</div>
	</section>

</main>

<?php get_template_part('templates/ppc/footer'); ?>

<div class="bar off"><a class="bt" href="#demo" data-dg-pop>Book free demo</a></div>

<div class="dg-pop" id="dg-pop" role="dialog" aria-modal="true" aria-label="Book a free demo" hidden>
	<div class="dg-pop-in"><button type="button" class="dg-pop-x" aria-label="Close">&times;</button></div>
</div>

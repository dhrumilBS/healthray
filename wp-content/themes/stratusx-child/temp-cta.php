<?php
/*
Template Name: CTA
*/
?>
<style>
:root{
  --blue:#0057FF;--blue-d:#003FCC;--blue-pale:#EEF4FF;
  --teal:#00BFA6;--teal-pale:#E6FAF8;--navy:#0A1F5C;
  --surf:#F4F7FB;--bdr:#E2E8F2;--bdr-mid:#C7D0DF;
  --tx:#1A2B45;--tx-mid:#4B5C72;--tx-soft:#8496AE;
  --r-sm:8px;--r-md:12px;--r-lg:18px;
}

/* layout */
.layout{display:flex;height:calc(100vh - 50px);overflow:hidden}
.sidebar{width:200px;background:#fff;border-right:1px solid var(--bdr);overflow-y:auto;flex-shrink:0}
.sb-sec{padding:12px 0 4px}
.sb-lbl{font-size:9.5px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--tx-soft);padding:0 16px 5px}
.sb-item{display:flex;align-items:center;gap:8px;padding:7px 16px;font-size:12px;font-weight:500;color:var(--tx);text-decoration:none;border-left:3px solid transparent;transition:all .12s}
.sb-item:hover{background:var(--surf);color:var(--blue)}
.sb-item.active{background:var(--blue-pale);color:var(--blue);border-left-color:var(--blue);font-weight:700}
.sb-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0}
.main{flex:1;overflow-y:auto;padding:24px 24px 64px}
.sec-div{display:flex;align-items:center;gap:12px;margin-bottom:20px;margin-top:4px}
.sec-div span{font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--tx-soft);white-space:nowrap}
.sec-div::before,.sec-div::after{content:'';flex:1;height:1px;background:var(--bdr)}

/* card */
.card{background:#fff;border-radius:var(--r-lg);border:1.5px solid var(--bdr);overflow:hidden;margin-bottom:20px}
.card__hdr{display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-bottom:1px solid var(--bdr);gap:10px}
.card__meta{display:flex;align-items:center;gap:8px;min-width:0;flex-wrap:wrap}
.card__idx{font-size:10px;font-weight:700;letter-spacing:.08em;background:var(--surf);color:var(--tx-soft);padding:2px 7px;border-radius:4px;flex-shrink:0}
.card__name{font-size:12.5px;font-weight:700;color:var(--tx)}
.og-badge{background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0;border-radius:4px;padding:1px 7px;font-size:10px;font-weight:700;letter-spacing:.03em}
.new-badge{background:#EEF4FF;color:#0057FF;border:1px solid #C7D9FF;border-radius:4px;padding:1px 7px;font-size:10px;font-weight:700;letter-spacing:.03em}
.card__acts{display:flex;gap:6px;flex-shrink:0}
.hbtn{font-family:var(--fb);font-size:11px;font-weight:600;padding:5px 12px;border-radius:6px;border:none;cursor:pointer;transition:all .12s;line-height:1;white-space:nowrap}
.hbtn--edit{background:var(--surf);color:var(--tx-mid)}.hbtn--edit:hover{background:var(--bdr-mid)}
.hbtn--code{background:var(--blue-pale);color:var(--blue)}.hbtn--code:hover{background:#dce9ff}
.hbtn--copy{background:var(--blue);color:#fff}.hbtn--copy:hover{background:var(--blue-d)}.hbtn--copy.ok{background:var(--teal)}

/* edit panel */
.ep{display:none;padding:14px 16px;background:#fafbfd;border-bottom:1px solid var(--bdr)}
.ep.open{display:block}
.ep-rows{display:flex;flex-wrap:wrap;gap:10px}
.f{display:flex;flex-direction:column;gap:3px;min-width:150px;flex:1}
.f--w{flex:2;min-width:260px}.f--f{flex:1 0 100%}
.f label{font-size:9.5px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--tx-soft)}
.f input,.f textarea{font-family:var(--fb);font-size:12.5px;color:var(--tx);padding:6px 9px;border:1.5px solid var(--bdr);border-radius:6px;outline:none;transition:border-color .12s;background:#fff;width:100%}
.f input:focus,.f textarea:focus{border-color:var(--blue)}
.f textarea{resize:vertical;min-height:52px;line-height:1.5}

/* dynamic rows */
.dyn-section{flex:1 0 100%;display:flex;flex-direction:column;gap:6px}
.dyn-section__label{font-size:9.5px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--tx-soft);margin-bottom:2px;display:flex;align-items:center;justify-content:space-between}
.dyn-row{display:flex;gap:6px;align-items:center}
.dyn-row input{font-family:var(--fb);font-size:12.5px;color:var(--tx);padding:6px 9px;border:1.5px solid var(--bdr);border-radius:6px;outline:none;background:#fff;flex:1;transition:border-color .12s}
.dyn-row input:focus{border-color:var(--blue)}
.dyn-row input.url{min-width:220px}
.dyn-add,.dyn-rm{font-family:var(--fb);font-size:11px;font-weight:600;padding:4px 10px;border-radius:5px;border:none;cursor:pointer;line-height:1;transition:all .12s;white-space:nowrap}
.dyn-add{background:var(--blue-pale);color:var(--blue)}.dyn-add:hover{background:#dce9ff}
.dyn-rm{background:#FEE2E2;color:#DC2626;padding:4px 8px}.dyn-rm:hover{background:#FECACA}

/* preview */
.card__preview{padding:20px;background:var(--surf)}

/* code pane */
.codepane{display:none;border-top:1px solid var(--bdr)}
.codepane.open{display:block}
.code-hdr{display:flex;align-items:center;justify-content:space-between;padding:6px 14px;background:#18243A}
.code-hdr span{font-size:9.5px;font-weight:600;letter-spacing:.08em;color:rgba(255,255,255,.3)}
.code-cp{font-family:var(--fb);font-size:11px;font-weight:600;padding:3px 11px;border-radius:4px;border:1px solid rgba(255,255,255,.18);background:transparent;color:rgba(255,255,255,.55);cursor:pointer;transition:all .12s}
.code-cp:hover{background:rgba(255,255,255,.08);color:#fff}.code-cp.ok{border-color:var(--teal);color:var(--teal)}
.code-body{background:#0D1525;padding:16px 18px;max-height:300px;overflow:auto}
.code-body pre{font-family:'Courier New',monospace;font-size:11.5px;line-height:1.7;color:#C9D3E8;white-space:pre}

/* toast */
.toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(10px);background:var(--navy);color:#fff;font-family:var(--fb);font-size:13px;font-weight:600;padding:9px 20px;border-radius:8px;opacity:0;pointer-events:none;transition:all .2s;z-index:9999;white-space:nowrap}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0)}

/* ── CTA A styles ── */
.hr-cta-inline{background:#fff;border-radius:var(--r-md);border:1.5px solid var(--bdr);padding:34px 42px;display:flex;align-items:center;justify-content:space-between;gap:32px}
.hr-cta-inline__body{flex:1;min-width:0}
.hr-cta-inline__headline{font-family:var(--fh);font-size:21px;font-weight:800;color:var(--navy);line-height:1.25;margin-bottom:8px}
.hr-cta-inline__subtext{font-size:14px;color:var(--tx-mid);line-height:1.65;max-width:500px;margin-bottom:16px}
.hr-cta-inline__trust{display:flex;flex-wrap:wrap;gap:14px}
.hr-cta-inline__trust-item{display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:var(--tx-soft)}
.hr-cta-inline__trust-dot{width:5px;height:5px;border-radius:50%;background:var(--teal);flex-shrink:0}
.hr-cta-inline__actions{display:flex;flex-direction:column;gap:10px;flex-shrink:0;min-width:155px}
.hr-cta-inline__btn{display:inline-flex;align-items:center;justify-content:center;font-family:var(--fb);font-size:13px;font-weight:600;padding:11px 22px;border-radius:var(--r-sm);text-decoration:none;cursor:pointer;transition:all .15s;white-space:nowrap;line-height:1;border:none}
.hr-cta-inline__btn--primary{background:var(--blue);color:#fff}.hr-cta-inline__btn--primary:hover{background:var(--blue-d);color:#fff}
.hr-cta-inline__btn--secondary{background:transparent;border:1.5px solid var(--blue);color:var(--blue)}.hr-cta-inline__btn--secondary:hover{background:var(--blue-pale)}

/* ── CTA B styles ── */
.hr-cta-split{background:#fff;border-radius:var(--r-md);border:1.5px solid var(--bdr);display:flex;overflow:hidden;}
.hr-cta-split__accent{width:5px;background:linear-gradient(180deg,var(--blue),var(--teal));flex-shrink:0}
.hr-cta-split__body{flex:1;padding:30px 38px;min-width:0}
.hr-cta-split__eye{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--blue);margin-bottom:10px;display:block}
.hr-cta-split__headline{font-family:var(--fh);font-size:22px;font-weight:800;color:var(--navy);line-height:1.25;margin-bottom:9px}
.hr-cta-split__sub{font-size:14px;color:var(--tx-mid);line-height:1.65;max-width:540px;margin-bottom:20px}
.hr-cta-split__checks{display:flex;flex-direction:column;gap:8px;margin-bottom:22px}
.hr-cta-split__chk{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--tx);font-weight:500}
.hr-cta-split__chk-ic{width:18px;height:18px;border-radius:50%;background:var(--blue-pale);color:var(--blue);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.hr-cta-split__foot{display:flex;align-items:center;flex-wrap:wrap;gap:10px}
.hr-cta-split__btn{font-family:var(--fb);font-size:13px;font-weight:600;padding:11px 24px;border-radius:var(--r-sm);text-decoration:none;cursor:pointer;transition:all .14s;white-space:nowrap;border:none;display:inline-flex;align-items:center}
.hr-cta-split__btn--primary{background:var(--blue);color:#fff}.hr-cta-split__btn--primary:hover{background:var(--blue-d);color:#fff}
.hr-cta-split__btn--ghost{background:transparent;border:1.5px solid var(--blue);color:var(--blue)}.hr-cta-split__btn--ghost:hover{background:var(--blue-pale)}
.hr-cta-split__pills{display:flex;flex-wrap:wrap;gap:6px;margin-top:16px}
.hr-cta-split__pill{font-size:10.5px;font-weight:600;padding:3px 10px;border-radius:99px;background:var(--surf);color:var(--tx-soft);border:1px solid var(--bdr)}

/* ── Blog component preview fallbacks (builder only) ── */

/* SVG check icon inside preview */
.chk-svg{width:18px;height:18px;flex-shrink:0}

/* ── CTA G — blog-cta-section preview (builder only) ── */
.blog-cta-section__headline{color:#211f40;font-size:26px;font-style:normal;font-weight:700;margin-bottom:12px;line-height:1.3}
.blog-cta-section__sub{color:var(--tx-mid);font-size:15px;line-height:1.65;max-width:560px;margin:0 auto 24px}
.blog-cta-section__btn{display:inline-flex;align-items:center;justify-content:center;background:#477bff;color:#fff;font-size:16px;font-weight:700;padding:13px 32px;border-radius:8px;text-decoration:none;border:none;cursor:pointer;transition:all .15s}
.blog-cta-section__btn:hover{background:#2d5fdb;color:#fff}

/* ── Blog H — Info Box (.info-callout) ── */
.info-callout{display:flex;gap:14px;align-items:flex-start;background:var(--blue-pale);border:1px solid #CFE0FF;border-left:4px solid var(--blue);border-radius:var(--r-sm);padding:18px 20px}
.info-callout__icon{width:22px;height:22px;flex-shrink:0;margin-top:2px}
.info-callout__body{flex:1;min-width:0}
.info-callout__label{display:block;font-size:10.5px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--blue);margin-bottom:6px}
.info-callout__text{font-size:14px;color:var(--tx);line-height:1.6;margin:0}

/* ── Blog I — Pros & Cons + Review (.proscons-review-box) ──
   Each side (Pros / Cons) carries its own supporting review quote,
   so the list and the social proof for that side sit together. */
.proscons-review-box{border:1.5px solid var(--bdr);border-radius:var(--r-md);overflow:hidden;background:#fff}
.proscons-review-box__title{font-family:var(--fh);font-weight:800;font-size:15px;color:var(--navy);padding:14px 20px;background:var(--surf);border-bottom:1px solid var(--bdr);margin:0}
.proscons-review-box__col{padding:20px;display:flex;flex-direction:column}
.proscons-review-box__col-hd{margin:0 0 10px;font-size:11px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;display:flex;align-items:center;gap:6px}
.proscons-review-box__col--pros .proscons-review-box__col-hd{color:var(--teal)}
.proscons-review-box__col--cons .proscons-review-box__col-hd{color:#DC5B3E}
.proscons-review-box__list{margin:0 0 16px;padding:0;list-style:none;display:flex;flex-direction:column;gap:7px}
.proscons-review-box__list li{font-size:13px;color:var(--tx);line-height:1.5;padding-left:20px;position:relative}
.proscons-review-box__col--pros .proscons-review-box__list li::before{content:"+";position:absolute;left:0;color:var(--teal);font-weight:800}
.proscons-review-box__col--cons .proscons-review-box__list li::before{content:"–";position:absolute;left:0;color:#DC5B3E;font-weight:800}
.proscons-review-box__review{margin-top:auto;background:var(--surf);border:1px solid var(--bdr);border-radius:var(--r-sm);padding:14px 16px}
.proscons-review-box__review-top{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:8px}
.proscons-review-box__badge{display:inline-flex;align-items:center;gap:5px;font-size:10px;font-weight:700;color:var(--tx-soft)}
.proscons-review-box__mark{width:16px;height:16px;border-radius:3px;background:#FF492C;color:#fff;display:flex;align-items:center;justify-content:center;font-size:8px;font-weight:800;flex-shrink:0}
.proscons-review-box__stars{display:flex;align-items:center;gap:2px}
.proscons-review-box__stars svg{width:12px;height:12px}
.proscons-review-box__score{font-size:10.5px;font-weight:700;color:var(--navy);margin-left:3px}
.proscons-review-box__quote{margin:0 0 8px;font-family:var(--fh);font-style:italic;font-size:13px;color:var(--tx);line-height:1.55}
.proscons-review-box__author{font-size:11px;color:var(--tx-soft)}
.proscons-review-box__author b{color:var(--tx);font-style:normal}
.proscons-review-box__verdict{margin:0;padding:12px 20px;background:var(--surf);border-top:1px solid var(--bdr);font-size:12.5px;color:var(--tx-soft)}
@media (max-width:640px){
	.proscons-review-box__cols{flex-direction:column}
	.proscons-review-box__col+.proscons-review-box__col{border-left:none;border-top:1px solid var(--bdr)}
}
</style>

<div class="layout">
	<nav class="sidebar">
		<div class="sb-sec">
			<div class="sb-lbl">CTA Blocks</div>
			<a class="sb-item active" href="#card-a"><span class="sb-dot" style="background:var(--blue)"></span>Inline Banner</a>
			<a class="sb-item" href="#card-b"><span class="sb-dot" style="background:var(--navy)"></span>Split Card</a>
			<a class="sb-item" href="#card-g"><span class="sb-dot" style="background:#477bff"></span>Blog CTA Section</a>
		</div>
		<div class="sb-sec">
			<div class="sb-lbl">Blog Components</div>
			<a class="sb-item" href="#card-c"><span class="sb-dot" style="background:var(--blue)"></span>Learn More (Full)</a>
			<a class="sb-item" href="#card-d"><span class="sb-dot" style="background:var(--blue)"></span>Learn More (Link)</a>
			<a class="sb-item" href="#card-e"><span class="sb-dot" style="background:#F6E05E"></span>Note Box</a>
			<a class="sb-item" href="#card-f"><span class="sb-dot" style="background:#86EFAC"></span>Pro Tip Box</a>
			<a class="sb-item" href="#card-h"><span class="sb-dot" style="background:var(--blue)"></span>Info Box</a>
			<a class="sb-item" href="#card-i"><span class="sb-dot" style="background:#FDBA74"></span>Pros &amp; Cons + Review</a>
		</div>
	</nav>

	<main class="main">
		<div class="sec-div"><span>CTA Blocks</span></div>

		<!-- ════════════ CTA A ════════════ -->
		<div class="card" id="card-a">
			<div class="card__hdr">
				<div class="card__meta">
					<span class="card__idx">CTA A</span>
					<span class="card__name">Minimal Inline Banner — .hr-cta-inline</span>
				</div>
				<div class="card__acts">
					<button class="hbtn hbtn--edit" onclick="toggleEP('ep-a')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M8.5 1.5L10.5 3.5L4 10H2V8L8.5 1.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"></path></svg>Edit
					</button>
					<button class="hbtn hbtn--code" onclick="toggleCode('cp-a','pv-a')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M4 3L1 6L4 9M8 3L11 6L8 9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path></svg>Code
					</button>
					<button class="hbtn hbtn--copy" id="hca" onclick="copyPrev('pv-a','hca')">Copy Code</button>
				</div>
			</div>

			<div class="ep" id="ep-a">
				<div class="ep-rows">
					<div class="f f--w"><label>Headline</label><input id="a-hl" value="Stop Losing Revenue to Fragmented Billing" oninput="uA()"></div>
					<div class="f f--f"><label>Subtext</label><textarea id="a-sub" oninput="uA()">Healthray connects your EMR, coding engine, and payer network in one unified platform — eliminating the gaps where revenue leaks.</textarea></div>

					<div class="dyn-section" id="a-trust-wrap">
						<div class="dyn-section__label">
							Trust Items
							<button class="dyn-add" onclick="addTrust()">+ Add Item</button>
						</div>
						<div id="a-trust-rows">
							<div class="dyn-row"><input placeholder="Trust item text" value="HIPAA Ready" oninput="uA()"><button class="dyn-rm" onclick="rmRow(this,'a-trust-rows',uA)">✕</button></div>
							<div class="dyn-row"><input placeholder="Trust item text" value="No Lock-In Contracts" oninput="uA()"><button class="dyn-rm" onclick="rmRow(this,'a-trust-rows',uA)">✕</button></div>
							<div class="dyn-row"><input placeholder="Trust item text" value="24/7 Dedicated Support" oninput="uA()"><button class="dyn-rm" onclick="rmRow(this,'a-trust-rows',uA)">✕</button></div>
						</div>
					</div>

					<div class="dyn-section" id="a-btn-wrap">
						<div class="dyn-section__label">
							Buttons
							<button class="dyn-add" onclick="addBtn('a')">+ Add Button</button>
						</div>
						<div id="a-btn-rows">
							<div class="dyn-row"><input placeholder="Button text" value="Start Free Trial" oninput="uA()"><input class="url" placeholder="https://..." value="https://healthray.com/contact/" oninput="uA()"><button class="dyn-rm" onclick="rmRow(this,'a-btn-rows',uA)">✕</button></div>
							<div class="dyn-row"><input placeholder="Button text" value="See Pricing" oninput="uA()"><input class="url" placeholder="https://..." value="https://healthray.com/pricing/" oninput="uA()"><button class="dyn-rm" onclick="rmRow(this,'a-btn-rows',uA)">✕</button></div>
						</div>
					</div>
				</div>
			</div>

			<div class="card__preview">
				<div id="pv-a">
					<div class="hr-cta-inline">
						<div class="hr-cta-inline__body">
							<h3 class="hr-cta-inline__headline" id="al-hl">Stop Losing Revenue to Fragmented Billing</h3>
							<p class="hr-cta-inline__subtext" id="al-sub">Healthray connects your EMR, coding engine, and payer network in one unified platform — eliminating the gaps where revenue leaks.</p>
							<div class="hr-cta-inline__trust" id="al-trust"><div class="hr-cta-inline__trust-item"><span class="hr-cta-inline__trust-dot"></span><span>HIPAA Ready</span></div><div class="hr-cta-inline__trust-item"><span class="hr-cta-inline__trust-dot"></span><span>No Lock-In Contracts</span></div><div class="hr-cta-inline__trust-item"><span class="hr-cta-inline__trust-dot"></span><span>24/7 Dedicated Support</span></div></div>
						</div>
						<div class="hr-cta-inline__actions" id="al-btns"><a class="hr-cta-inline__btn hr-cta-inline__btn--primary" href="https://healthray.com/contact/">Start Free Trial</a><a class="hr-cta-inline__btn hr-cta-inline__btn--secondary" href="https://healthray.com/pricing/">See Pricing</a></div>
					</div>
				</div>
			</div>
			<div class="codepane" id="cp-a">
				<div class="code-hdr"><span>HTML — paste into your page</span><button class="code-cp" id="pca" onclick="copyPre('pre-a','pca')">Copy</button></div>
				<div class="code-body"><pre id="pre-a"></pre></div>
			</div>
		</div>

		<!-- ════════════ CTA B ════════════ -->
		<div class="card" id="card-b">
			<div class="card__hdr">
				<div class="card__meta">
					<span class="card__idx">CTA B</span>
					<span class="card__name">Split Card / .hr-cta-split</span>
				</div>
				<div class="card__acts">
					<button class="hbtn hbtn--edit" onclick="toggleEP('ep-b')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M8.5 1.5L10.5 3.5L4 10H2V8L8.5 1.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"></path></svg>Edit
					</button>
					<button class="hbtn hbtn--code" onclick="toggleCode('cp-b','pv-b')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M4 3L1 6L4 9M8 3L11 6L8 9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path></svg>Code
					</button>
					<button class="hbtn hbtn--copy" id="hcb" onclick="copyPrev('pv-b','hcb')">Copy Code</button>
				</div>
			</div>

			<div class="ep" id="ep-b">
				<div class="ep-rows">
					<div class="f f--w"><label>Eyebrow</label><input id="b-eye" value="Hospital · Clinic · Laboratory" oninput="uB()"></div>
					<div class="f f--w"><label>Headline</label><input id="b-hl" value="Transform Your Hospital's Revenue Cycle With AI" oninput="uB()"></div>
					<div class="f f--f"><label>Subtext</label><textarea id="b-sub" oninput="uB()">From HIMS to medical billing — Healthray is the all-in-one platform built for Indian healthcare providers.</textarea></div>

					<div class="dyn-section">
						<div class="dyn-section__label">
							Checklist Rows
							<button class="dyn-add" onclick="addCheck()">+ Add Row</button>
						</div>
						<div id="b-chk-rows">
							<div class="dyn-row"><input placeholder="Check row text" value="Full data ownership — confirmed in writing" oninput="uB()"><button class="dyn-rm" onclick="rmRow(this,'b-chk-rows',uB)">✕</button></div>
							<div class="dyn-row"><input placeholder="Check row text" value="ABHA, PMJAY &amp; FHIR R4 compliant out of the box" oninput="uB()"><button class="dyn-rm" onclick="rmRow(this,'b-chk-rows',uB)">✕</button></div>
							<div class="dyn-row"><input placeholder="Check row text" value="Migration support included — no extra cost" oninput="uB()"><button class="dyn-rm" onclick="rmRow(this,'b-chk-rows',uB)">✕</button></div>
						</div>
					</div>

					<div class="dyn-section">
						<div class="dyn-section__label">
							Buttons
							<button class="dyn-add" onclick="addBtn('b')">+ Add Button</button>
						</div>
						<div id="b-btn-rows">
							<div class="dyn-row"><input placeholder="Button text" value="Explore Products" oninput="uB()"><input class="url" placeholder="https://..." value="https://healthray.com/hospital-information-management-system/" oninput="uB()"><button class="dyn-rm" onclick="rmRow(this,'b-btn-rows',uB)">✕</button></div>
							<div class="dyn-row"><input placeholder="Button text" value="Contact Sales" oninput="uB()"><input class="url" placeholder="https://..." value="https://healthray.com/contact/" oninput="uB()"><button class="dyn-rm" onclick="rmRow(this,'b-btn-rows',uB)">✕</button></div>
						</div>
					</div>

					<div class="dyn-section">
						<div class="dyn-section__label">
							Pills / Tags
							<button class="dyn-add" onclick="addPill()">+ Add Pill</button>
						</div>
						<div id="b-pill-rows" style="display:flex;flex-wrap:wrap;gap:6px;align-items:center">
							<div class="dyn-row"><input placeholder="Pill text" value="ABDM Integrated" oninput="uB()" style="min-width:130px"><button class="dyn-rm" onclick="rmRow(this,'b-pill-rows',uB)">✕</button></div>
							<div class="dyn-row"><input placeholder="Pill text" value="ABHA Ready" oninput="uB()" style="min-width:130px"><button class="dyn-rm" onclick="rmRow(this,'b-pill-rows',uB)">✕</button></div>
							<div class="dyn-row"><input placeholder="Pill text" value="PMJAY Supported" oninput="uB()" style="min-width:130px"><button class="dyn-rm" onclick="rmRow(this,'b-pill-rows',uB)">✕</button></div>
							<div class="dyn-row"><input placeholder="Pill text" value="FHIR R4" oninput="uB()" style="min-width:130px"><button class="dyn-rm" onclick="rmRow(this,'b-pill-rows',uB)">✕</button></div>
						</div>
					</div>
				</div>
			</div>

			<div class="card__preview">
				<div id="pv-b">
					<div class="hr-cta-split">
						<div class="hr-cta-split__accent"></div>
						<div class="hr-cta-split__body">
							<span class="hr-cta-split__eye" id="bl-eye">Hospital · Clinic · Laboratory</span>
							<h3 class="hr-cta-split__headline" id="bl-hl">Transform Your Hospital's Revenue Cycle With AI</h3>
							<p class="hr-cta-split__sub" id="bl-sub">From HIMS to medical billing — Healthray is the all-in-one platform built for Indian healthcare providers.</p>
							<div class="hr-cta-split__checks" id="bl-chks"><div class="hr-cta-split__chk"><svg class="chk-svg" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="9" r="9" fill="#EEF4FF"></circle><path d="M5.5 9L7.8 11.5L12.5 6.5" stroke="#0057FF" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path></svg><span>Full data ownership — confirmed in writing</span></div><div class="hr-cta-split__chk"><svg class="chk-svg" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="9" r="9" fill="#EEF4FF"></circle><path d="M5.5 9L7.8 11.5L12.5 6.5" stroke="#0057FF" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path></svg><span>ABHA, PMJAY &amp; FHIR R4 compliant out of the box</span></div><div class="hr-cta-split__chk"><svg class="chk-svg" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="9" r="9" fill="#EEF4FF"></circle><path d="M5.5 9L7.8 11.5L12.5 6.5" stroke="#0057FF" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path></svg><span>Migration support included — no extra cost</span></div></div>
							<div class="hr-cta-split__foot" id="bl-btns"><a class="hr-cta-split__btn hr-cta-split__btn--primary" href="https://healthray.com/hospital-information-management-system/">Explore Products</a><a class="hr-cta-split__btn hr-cta-split__btn--ghost" href="https://healthray.com/contact/">Contact Sales</a></div>
							<div class="hr-cta-split__pills" id="bl-pills"><span class="hr-cta-split__pill">ABDM Integrated</span><span class="hr-cta-split__pill">ABHA Ready</span><span class="hr-cta-split__pill">PMJAY Supported</span><span class="hr-cta-split__pill">FHIR R4</span></div>
						</div>
					</div>
				</div>
			</div>
			<div class="codepane" id="cp-b">
				<div class="code-hdr"><span>HTML — paste into your page</span><button class="code-cp" id="pcb" onclick="copyPre('pre-b','pcb')">Copy</button></div>
				<div class="code-body"><pre id="pre-b"></pre></div>
			</div>
		</div>

		<!-- ════════════ CTA G — Blog CTA Section ════════════ -->
		<div class="card" id="card-g">
			<div class="card__hdr">
				<div class="card__meta">
					<span class="card__idx">CTA G</span>
					<span class="card__name">Blog CTA Section — .blog-cta-section</span>
					<span class="og-badge">original class</span>
				</div>
				<div class="card__acts">
					<button class="hbtn hbtn--edit" onclick="toggleEP('ep-g')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M8.5 1.5L10.5 3.5L4 10H2V8L8.5 1.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"></path></svg>Edit
					</button>
					<button class="hbtn hbtn--code" onclick="toggleCode('cp-g','pv-g')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M4 3L1 6L4 9M8 3L11 6L8 9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path></svg>Code
					</button>
					<button class="hbtn hbtn--copy" id="hcg" onclick="copyPrev('pv-g','hcg')">Copy Code</button>
				</div>
			</div>

			<div class="ep" id="ep-g">
				<div class="ep-rows">
					<div class="f f--w"><label>Headline</label><input id="g-hl" value="Step towards digital era with our healthcare solution" oninput="uG()"></div>
					<div class="f f--f"><label>Subtext</label><textarea id="g-sub" oninput="uG()">Revamp your hospital facilities and embrace change for better healthcare management. Ease in managing and organizing large medical datasets leads to effective analysis. Seize the opportunity now!</textarea></div>
					<div class="f"><label>Button Text</label><input id="g-bt" value="Request Demo Now" oninput="uG()"></div>
					<div class="f f--w"><label>Button URL</label><input id="g-bl" value="https://calendly.com/healthray/30min" oninput="uG()"></div>
				</div>
			</div>

			<div class="card__preview">
				<div id="pv-g">
					<div class="wp-block-group blog-cta-section">
						<div class="wp-block-group__inner-container is-layout-constrained wp-block-group-is-layout-constrained">
							<p class="has-text-align-center has-text-color has-link-color wp-elements-6a1cd55bb2157aef96bf96307fa5c0a9 wp-block-paragraph blog-cta-section__headline">
							    <strong id="gl-hl">Step towards digital era with our healthcare solution</strong>
						    </p>
							<p class="has-text-align-center wp-block-paragraph blog-cta-section__sub" id="gl-sub">
							    Revamp your hospital facilities and embrace change for better healthcare management. Ease in managing and organizing large medical datasets leads to effective analysis. Seize the opportunity now!
						    </p>
						    <div class="wp-block-button is-style-fill">
						        <a class="wp-block-button__link wp-element-button" href="https://calendly.com/healthray/30min" id="gl-btn" style="border-radius:8px;background-color:#477bff;font-size:16px" target="_blank" rel="noreferrer noopener">
						            <strong>Request Demo Now</strong>
					            </a>
				            </div>
						</div>
					</div>
				</div>
			</div>
			<div class="codepane" id="cp-g">
				<div class="code-hdr"><span>HTML — paste into your blog post</span><button class="code-cp" id="pcg" onclick="copyPre('pre-g','pcg')">Copy</button></div>
				<div class="code-body"><pre id="pre-g"></pre></div>
			</div>
		</div>

		<div class="sec-div"><span>Blog Components</span></div>

		<!-- ════════════ BLOG C ════════════ -->
		<div class="card" id="card-c">
			<div class="card__hdr">
				<div class="card__meta">
					<span class="card__idx">Blog C</span>
					<span class="card__name">Learn More — Full Text</span>
					<span class="og-badge">original class</span>
				</div>
				<div class="card__acts">
					<button class="hbtn hbtn--edit" onclick="toggleEP('ep-c')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M8.5 1.5L10.5 3.5L4 10H2V8L8.5 1.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"></path></svg>Edit
					</button>
					<button class="hbtn hbtn--code" onclick="toggleCode('cp-c','pv-c')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M4 3L1 6L4 9M8 3L11 6L8 9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path></svg>Code
					</button>
					<button class="hbtn hbtn--copy" id="hcc" onclick="copyPrev('pv-c','hcc')">Copy Code</button>
				</div>
			</div>
			<div class="ep" id="ep-c">
				<div class="ep-rows">
					<div class="f f--f"><label>Body text (before link)</label><textarea id="c-body" oninput="uC()">Waitlist automation solves the lag, but busy OPDs still face pressures that a single workflow fix cannot cover and</textarea></div>
					<div class="f f--w"><label>Link Text</label><input id="c-lt" value="How Clinic Software Simplifies High-Volume OPD and Daycare Operations" oninput="uC()"></div>
					<div class="f f--f"><label>Link URL</label><input id="c-ll" value="https://healthray.com/blog/clinic-management-systems/how-clinic-software-simplifies-high-volume-opd-and-daycare-operations/" oninput="uC()"></div>
					<div class="f f--f"><label>Text after link</label><input id="c-after" value="shows how clinics handle that scale day to day." oninput="uC()"></div>
				</div>
			</div>
			<div class="card__preview">
				<div id="pv-c">
					<div class="learn-more-box-alt">
						<p><b>Learn more:</b> <span id="cl-body">Waitlist automation solves the lag, but busy OPDs still face pressures that a single workflow fix cannot cover and</span> <a id="cl-link" href="https://healthray.com/blog/clinic-management-systems/how-clinic-software-simplifies-high-volume-opd-and-daycare-operations/" target="_blank" rel="noopener">How Clinic Software Simplifies High-Volume OPD and Daycare Operations</a> <span id="cl-after">shows how clinics handle that scale day to day.</span></p>
					</div>
				</div>
			</div>
			<div class="codepane" id="cp-c">
				<div class="code-hdr"><span>HTML — paste into your blog post</span><button class="code-cp" id="pcc" onclick="copyPre('pre-c','pcc')">Copy</button></div>
				<div class="code-body"><pre id="pre-c"></pre></div>
			</div>
		</div>

		<!-- ════════════ BLOG D ════════════ -->
		<div class="card" id="card-d">
			<div class="card__hdr">
				<div class="card__meta">
					<span class="card__idx">Blog D</span>
					<span class="card__name">Learn More — Link Only</span>
					<span class="og-badge">original class</span>
				</div>
				<div class="card__acts">
					<button class="hbtn hbtn--edit" onclick="toggleEP('ep-d')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M8.5 1.5L10.5 3.5L4 10H2V8L8.5 1.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"></path></svg>Edit
					</button>
					<button class="hbtn hbtn--code" onclick="toggleCode('cp-d','pv-d')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M4 3L1 6L4 9M8 3L11 6L8 9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path></svg>Code
					</button>
					<button class="hbtn hbtn--copy" id="hcd" onclick="copyPrev('pv-d','hcd')">Copy Code</button>
				</div>
			</div>
			<div class="ep" id="ep-d">
				<div class="ep-rows">
					<div class="f f--w"><label>Link Text</label><input id="d-lt" value="Why do realtors need AI?" oninput="uD()"></div>
					<div class="f f--f"><label>Link URL</label><input id="d-ll" value="https://botphonic.ai/why-realtors-need-ai-in-2025/" oninput="uD()"></div>
				</div>
			</div>
			<div class="card__preview">
				<div id="pv-d">
					<div class="learn-more-box-alt">
						<p><b>Learn more:</b> <a id="dl-link" href="https://botphonic.ai/why-realtors-need-ai-in-2025/" target="_blank" rel="noopener">Why do realtors need AI?</a></p>
					</div>
				</div>
			</div>
			<div class="codepane" id="cp-d">
				<div class="code-hdr"><span>HTML — paste into your blog post</span><button class="code-cp" id="pcd" onclick="copyPre('pre-d','pcd')">Copy</button></div>
				<div class="code-body"><pre id="pre-d"></pre></div>
			</div>
		</div>

		<!-- ════════════ BLOG E ════════════ -->
		<div class="card" id="card-e">
			<div class="card__hdr">
				<div class="card__meta">
					<span class="card__idx">Blog E</span>
					<span class="card__name">Note Box</span>
					<span class="og-badge">original class</span>
				</div>
				<div class="card__acts">
					<button class="hbtn hbtn--edit" onclick="toggleEP('ep-e')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M8.5 1.5L10.5 3.5L4 10H2V8L8.5 1.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"></path></svg>Edit
					</button>
					<button class="hbtn hbtn--code" onclick="toggleCode('cp-e','pv-e')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M4 3L1 6L4 9M8 3L11 6L8 9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path></svg>Code
					</button>
					<button class="hbtn hbtn--copy" id="hce" onclick="copyPrev('pv-e','hce')">Copy Code</button>
				</div>
			</div>
			<div class="ep" id="ep-e">
				<div class="ep-rows">
					<div class="f"><label>Label</label><input id="e-label" value="NOTE" oninput="uE()"></div>
					<div class="f f--f"><label>Content</label><textarea id="e-content" oninput="uE()">Ensure your AI voicebot supports CRM integration, multilingual responses, and HIPAA-compliant data handling before deployment.</textarea></div>
					<div class="f f--f"><label>Icon Image URL</label><input id="e-icon" value="https://healthray.com/wp-content/uploads/2025/10/note.svg" oninput="uE()"></div>
				</div>
			</div>
			<div class="card__preview">
				<div id="pv-e">
					<div class="remember-box-light">
						<div class="remember-icon">
							<img id="el-icon" src="https://healthray.com/wp-content/uploads/2025/10/note.svg" alt="Note Icon">
							<span id="el-label">NOTE</span>
						</div>
						<div class="remember-content" id="el-content">Ensure your AI voicebot supports CRM integration, multilingual responses, and HIPAA-compliant data handling before deployment.</div>
					</div>
				</div>
			</div>
			<div class="codepane" id="cp-e">
				<div class="code-hdr"><span>HTML — paste into your blog post</span><button class="code-cp" id="pce" onclick="copyPre('pre-e','pce')">Copy</button></div>
				<div class="code-body"><pre id="pre-e"></pre></div>
			</div>
		</div>

		<!-- ════════════ BLOG F ════════════ -->
		<div class="card" id="card-f">
			<div class="card__hdr">
				<div class="card__meta">
					<span class="card__idx">Blog F</span>
					<span class="card__name">Pro Tip Box</span>
					<span class="og-badge">original class</span>
				</div>
				<div class="card__acts">
					<button class="hbtn hbtn--edit" onclick="toggleEP('ep-f')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M8.5 1.5L10.5 3.5L4 10H2V8L8.5 1.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"></path></svg>Edit
					</button>
					<button class="hbtn hbtn--code" onclick="toggleCode('cp-f','pv-f')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M4 3L1 6L4 9M8 3L11 6L8 9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path></svg>Code
					</button>
					<button class="hbtn hbtn--copy" id="hcf" onclick="copyPrev('pv-f','hcf')">Copy Code</button>
				</div>
			</div>
			<div class="ep" id="ep-f">
				<div class="ep-rows">
					<div class="f"><label>Label</label><input id="f-label" value="PRO TIP" oninput="uF()"></div>
					<div class="f f--f"><label>Content</label><textarea id="f-content" oninput="uF()">"Choose an AI voice bot that not only handles today's tasks but scales with your clinic's patient volume and integrates natively with your EMR system."</textarea></div>
					<div class="f f--f"><label>Icon Image URL</label><input id="f-icon" value="https://healthray.com/wp-content/uploads/2025/10/pro-tips.svg" oninput="uF()"></div>
				</div>
			</div>
			<div class="card__preview">
				<div id="pv-f">
					<div class="blog-tip-box">
						<div class="tip-icon">
							<img id="fl-icon" src="https://healthray.com/wp-content/uploads/2025/10/pro-tips.svg" alt="Pro Tips">
							<span id="fl-label">PRO TIP</span>
						</div>
						<div class="tip-content" id="fl-content">"Choose an AI voice bot that not only handles today's tasks but scales with your clinic's patient volume and integrates natively with your EMR system."</div>
					</div>
				</div>
			</div>
			<div class="codepane" id="cp-f">
				<div class="code-hdr"><span>HTML — paste into your blog post</span><button class="code-cp" id="pcf" onclick="copyPre('pre-f','pcf')">Copy</button></div>
				<div class="code-body"><pre id="pre-f"></pre></div>
			</div>
		</div>

		<!-- ════════════ BLOG H — Info Box ════════════ -->
		<div class="card" id="card-h">
			<div class="card__hdr">
				<div class="card__meta">
					<span class="card__idx">Blog H</span>
					<span class="card__name">Info Box — .info-callout</span>
					<span class="new-badge">new</span>
				</div>
				<div class="card__acts">
					<button class="hbtn hbtn--edit" onclick="toggleEP('ep-h')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M8.5 1.5L10.5 3.5L4 10H2V8L8.5 1.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"></path></svg>Edit
					</button>
					<button class="hbtn hbtn--code" onclick="toggleCode('cp-h','pv-h')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M4 3L1 6L4 9M8 3L11 6L8 9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path></svg>Code
					</button>
					<button class="hbtn hbtn--copy" id="hch" onclick="copyPrev('pv-h','hch')">Copy Code</button>
				</div>
			</div>
			<div class="ep" id="ep-h">
				<div class="ep-rows">
					<div class="f"><label>Label</label><input id="h-label" value="QUICK TAKE" oninput="uH()"></div>
					<div class="f f--f"><label>Content</label><textarea id="h-content" oninput="uH()">Use this box for anything that needs a light callout but isn't a warning (Note Box) or an editorial recommendation (Pro Tip Box) — definitions, "at a glance" summaries, or a quick aside.</textarea></div>
					<div class="f f--f"><label>Icon Image URL</label><input id="h-icon" value="https://healthray.com/wp-content/uploads/2025/10/note.svg" oninput="uH()"></div>
				</div>
			</div>
			<div class="card__preview">
				<div id="pv-h">
					<div class="info-callout">
						<img class="info-callout__icon" id="hl-icon" src="https://healthray.com/wp-content/uploads/2025/10/note.svg" alt="">
						<div class="info-callout__body">
							<span class="info-callout__label" id="hl-label">QUICK TAKE</span>
							<p class="info-callout__text" id="hl-content">Use this box for anything that needs a light callout but isn't a warning (Note Box) or an editorial recommendation (Pro Tip Box) — definitions, "at a glance" summaries, or a quick aside.</p>
						</div>
					</div>
				</div>
			</div>
			<div class="codepane" id="cp-h">
				<div class="code-hdr"><span>HTML — paste into your blog post</span><button class="code-cp" id="pch" onclick="copyPre('pre-h','pch')">Copy</button></div>
				<div class="code-body"><pre id="pre-h"></pre></div>
			</div>
		</div>

		<!-- ════════════ BLOG I — Pros & Cons + Review ════════════ -->
		<div class="card" id="card-i">
			<div class="card__hdr">
				<div class="card__meta">
					<span class="card__idx">Blog I</span>
					<span class="card__name">Pros &amp; Cons + Review — .proscons-review-box</span>
					<span class="new-badge">new</span>
				</div>
				<div class="card__acts">
					<button class="hbtn hbtn--edit" onclick="toggleEP('ep-i')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M8.5 1.5L10.5 3.5L4 10H2V8L8.5 1.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"></path></svg>Edit
					</button>
					<button class="hbtn hbtn--code" onclick="toggleCode('cp-i','pv-i')">
						<svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="margin-right:4px"><path d="M4 3L1 6L4 9M8 3L11 6L8 9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"></path></svg>Code
					</button>
					<button class="hbtn hbtn--copy" id="hci" onclick="copyPrev('pv-i','hci')">Copy Code</button>
				</div>
			</div>
			<div class="ep" id="ep-i">
				<div class="ep-rows">
					<div class="f f--w"><label>Title (optional)</label><input id="i-title" value="MocDoc — Pros &amp; Cons" oninput="uI()"></div>

					<div class="dyn-section" id="i-pros-wrap">
						<div class="dyn-section__label">
							Pros Items
							<button class="dyn-add" onclick="addProsConsRow('i-pros-rows')">+ Add Pro</button>
						</div>
						<div id="i-pros-rows">
							<div class="dyn-row"><input placeholder="Pro item text" value="Established India presence with sector-specific modules" oninput="uI()"><button class="dyn-rm" onclick="rmRow(this,'i-pros-rows',uI)">✕</button></div>
							<div class="dyn-row"><input placeholder="Pro item text" value="Separate editions for clinics, diagnostics and hospitals" oninput="uI()"><button class="dyn-rm" onclick="rmRow(this,'i-pros-rows',uI)">✕</button></div>
						</div>
					</div>

					<div class="f"><label>Pros-side Rating (0–5)</label><input id="i-pros-rating" type="number" min="0" max="5" step="0.1" value="4.6" oninput="uI()"></div>
					<div class="f f--f"><label>Pros-side Review Quote</label><textarea id="i-pros-quote" oninput="uI()">Onboarding was fast and the clinic module covered everything we needed on day one.</textarea></div>
					<div class="f f--w"><label>Pros-side Reviewer Name</label><input id="i-pros-name" value="Dr. Ketan Rupala" oninput="uI()"></div>
					<div class="f f--w"><label>Pros-side Reviewer Title</label><input id="i-pros-title" value="Urologist, Rupala Kidney &amp; Prostate Hospital" oninput="uI()"></div>

					<div class="dyn-section" id="i-cons-wrap">
						<div class="dyn-section__label">
							Cons Items
							<button class="dyn-add" onclick="addProsConsRow('i-cons-rows')">+ Add Con</button>
						</div>
						<div id="i-cons-rows">
							<div class="dyn-row"><input placeholder="Con item text" value="ABDM/ABHA depth varies — confirm with sales directly" oninput="uI()"><button class="dyn-rm" onclick="rmRow(this,'i-cons-rows',uI)">✕</button></div>
							<div class="dyn-row"><input placeholder="Con item text" value="Module packaging can mean paying for tiers you don't need" oninput="uI()"><button class="dyn-rm" onclick="rmRow(this,'i-cons-rows',uI)">✕</button></div>
						</div>
					</div>

					<div class="f"><label>Cons-side Rating (0–5)</label><input id="i-cons-rating" type="number" min="0" max="5" step="0.1" value="3.4" oninput="uI()"></div>
					<div class="f f--f"><label>Cons-side Review Quote</label><textarea id="i-cons-quote" oninput="uI()">We had to call support twice to confirm ABHA linking actually worked end to end.</textarea></div>
					<div class="f f--w"><label>Cons-side Reviewer Name</label><input id="i-cons-name" value="Anita Sharma" oninput="uI()"></div>
					<div class="f f--w"><label>Cons-side Reviewer Title</label><input id="i-cons-title" value="Practice Manager, Sharma Clinic" oninput="uI()"></div>

					<div class="f f--f"><label>Verdict (optional)</label><input id="i-verdict" value="Best if your needs are still close to a booking-and-basic-EMR tool, not full hospital operations." oninput="uI()"></div>
				</div>
			</div>
			<div class="card__preview">
				<div id="pv-i">
					<div class="proscons-review-box">
						<p class="proscons-review-box__title" id="il-title">MocDoc — Pros &amp; Cons</p>
						<div class="proscons-review-box__cols">

							<div class="proscons-review-box__col proscons-review-box__col--pros">
								<h4 class="proscons-review-box__col-hd">Pros</h4>
								<ul class="proscons-review-box__list" id="il-pros">
									<li>Established India presence with sector-specific modules</li>
									<li>Separate editions for clinics, diagnostics and hospitals</li>
								</ul>
								<div class="proscons-review-box__review">
									<div class="proscons-review-box__review-top">
										<span class="proscons-review-box__badge"><span class="proscons-review-box__mark">G2</span> Review</span>
										<span class="proscons-review-box__stars" id="il-pros-stars" aria-label="4.6 out of 5 stars"></span>
									</div>
									<blockquote class="proscons-review-box__quote" id="il-pros-quote">"Onboarding was fast and the clinic module covered everything we needed on day one."</blockquote>
									<div class="proscons-review-box__author"><b id="il-pros-name">Dr. Ketan Rupala</b> · <span id="il-pros-titletext">Urologist, Rupala Kidney &amp; Prostate Hospital</span></div>
								</div>
							</div>

							<div class="proscons-review-box__col proscons-review-box__col--cons">
								<h4 class="proscons-review-box__col-hd">Cons</h4>
								<ul class="proscons-review-box__list" id="il-cons">
									<li>ABDM/ABHA depth varies — confirm with sales directly</li>
									<li>Module packaging can mean paying for tiers you don't need</li>
								</ul>
								<div class="proscons-review-box__review">
									<div class="proscons-review-box__review-top">
										<span class="proscons-review-box__badge"><span class="proscons-review-box__mark">G2</span> Review</span>
										<span class="proscons-review-box__stars" id="il-cons-stars" aria-label="3.4 out of 5 stars"></span>
									</div>
									<blockquote class="proscons-review-box__quote" id="il-cons-quote">"We had to call support twice to confirm ABHA linking actually worked end to end."</blockquote>
									<div class="proscons-review-box__author"><b id="il-cons-name">Anita Sharma</b> · <span id="il-cons-titletext">Practice Manager, Sharma Clinic</span></div>
								</div>
							</div>

						</div>
						<p class="proscons-review-box__verdict" id="il-verdict">Best if your needs are still close to a booking-and-basic-EMR tool, not full hospital operations.</p>
					</div>
				</div>
			</div>
			<div class="codepane" id="cp-i">
				<div class="code-hdr"><span>HTML — paste into your blog post</span><button class="code-cp" id="pci" onclick="copyPre('pre-i','pci')">Copy</button></div>
				<div class="code-body"><pre id="pre-i"></pre></div>
			</div>
		</div>

	</main>
</div>
<div class="toast" id="toast">Copied to clipboard!</div>

<script>
const $ = id => document.getElementById(id);
const v = id => { const e=$(id); return e?e.value:''; };

const CHK_SVG = `<svg class="chk-svg" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="9" r="9" fill="#EEF4FF"/><path d="M5.5 9L7.8 11.5L12.5 6.5" stroke="#0057FF" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>`;

function toggleEP(id){ $(id).classList.toggle('open'); }

function toggleCode(cpId,pvId){
	const p=$(cpId);
	p.classList.toggle('open');
	if(p.classList.contains('open')) render(cpId,pvId);
}

function addTrust(){
	const wrap=$('a-trust-rows');
	const row=document.createElement('div');
	row.className='dyn-row';
	row.innerHTML=`<input placeholder="Trust item text" oninput="uA()"><button class="dyn-rm" onclick="rmRow(this,'a-trust-rows',uA)">✕</button>`;
	wrap.appendChild(row);uA();
}
function addCheck(){
	const wrap=$('b-chk-rows');
	const row=document.createElement('div');
	row.className='dyn-row';
	row.innerHTML=`<input placeholder="Check row text" oninput="uB()"><button class="dyn-rm" onclick="rmRow(this,'b-chk-rows',uB)">✕</button>`;
	wrap.appendChild(row);uB();
}
function addPill(){
	const wrap=$('b-pill-rows');
	const row=document.createElement('div');
	row.className='dyn-row';
	row.innerHTML=`<input placeholder="Pill text" oninput="uB()" style="min-width:130px"><button class="dyn-rm" onclick="rmRow(this,'b-pill-rows',uB)">✕</button>`;
	wrap.appendChild(row);uB();
}
function addBtn(which){
	const wrap=$(which+'-btn-rows');
	const row=document.createElement('div');
	row.className='dyn-row';
	const fn=which==='a'?'uA()':'uB()';
	row.innerHTML=`<input placeholder="Button text" oninput="${fn}"><input class="url" placeholder="https://..." oninput="${fn}"><button class="dyn-rm" onclick="rmRow(this,'${which}-btn-rows',${which==='a'?'uA':'uB'})">✕</button>`;
	wrap.appendChild(row);
	which==='a'?uA():uB();
}
function rmRow(btn,wrId,fn){ btn.parentElement.remove(); fn(); }

function uA(){
	$('al-hl').textContent = v('a-hl');
	$('al-sub').textContent = v('a-sub');
	const tw=$('al-trust'); tw.innerHTML='';
	$('a-trust-rows').querySelectorAll('.dyn-row').forEach(row=>{
		const txt=row.querySelector('input').value.trim(); if(!txt) return;
		const d=document.createElement('div'); d.className='hr-cta-inline__trust-item';
		d.innerHTML=`<span class="hr-cta-inline__trust-dot"></span><span>${txt}</span>`;
		tw.appendChild(d);
	});
	const bw=$('al-btns'); bw.innerHTML='';
	$('a-btn-rows').querySelectorAll('.dyn-row').forEach((row,i)=>{
		const ins=row.querySelectorAll('input');
		const txt=ins[0].value.trim(),url=ins[1].value.trim(); if(!txt) return;
		const a=document.createElement('a');
		a.className='hr-cta-inline__btn '+(i===0?'hr-cta-inline__btn--primary':'hr-cta-inline__btn--secondary');
		a.textContent=txt; a.href=url||'#'; bw.appendChild(a);
	});
	rc('cp-a','pv-a');
}

function uB(){
	$('bl-eye').textContent = v('b-eye');
	$('bl-hl').textContent  = v('b-hl');
	$('bl-sub').textContent = v('b-sub');
	const cw=$('bl-chks'); cw.innerHTML='';
	$('b-chk-rows').querySelectorAll('.dyn-row').forEach(row=>{
		const txt=row.querySelector('input').value.trim(); if(!txt) return;
		const d=document.createElement('div'); d.className='hr-cta-split__chk';
		d.innerHTML=`${CHK_SVG}<span>${txt}</span>`; cw.appendChild(d);
	});
	const bw=$('bl-btns'); bw.innerHTML='';
	$('b-btn-rows').querySelectorAll('.dyn-row').forEach((row,i)=>{
		const ins=row.querySelectorAll('input');
		const txt=ins[0].value.trim(),url=ins[1].value.trim(); if(!txt) return;
		const a=document.createElement('a');
		a.className='hr-cta-split__btn '+(i===0?'hr-cta-split__btn--primary':'hr-cta-split__btn--ghost');
		a.textContent=txt; a.href=url||'#'; bw.appendChild(a);
	});
	const pw=$('bl-pills'); pw.innerHTML='';
	$('b-pill-rows').querySelectorAll('.dyn-row').forEach(row=>{
		const txt=row.querySelector('input').value.trim(); if(!txt) return;
		const s=document.createElement('span'); s.className='hr-cta-split__pill';
		s.textContent=txt; pw.appendChild(s);
	});
	rc('cp-b','pv-b');
}

function uG(){
	$('gl-hl').textContent  = v('g-hl');
	$('gl-sub').textContent = v('g-sub');
	const btn=$('gl-btn');
	btn.querySelector('strong').textContent = v('g-bt');
	btn.href = v('g-bl');
	rc('cp-g','pv-g');
}

function uC(){
	$('cl-body').textContent = v('c-body');
	$('cl-link').textContent = v('c-lt');
	$('cl-link').href        = v('c-ll');
	$('cl-after').textContent= ' '+v('c-after');
	rc('cp-c','pv-c');
}
function uD(){
	$('dl-link').textContent = v('d-lt');
	$('dl-link').href        = v('d-ll');
	rc('cp-d','pv-d');
}
function uE(){
	$('el-label').textContent   = v('e-label');
	$('el-content').textContent = v('e-content');
	$('el-icon').src            = v('e-icon');
	rc('cp-e','pv-e');
}
function uF(){
	$('fl-label').textContent   = v('f-label');
	$('fl-content').textContent = v('f-content');
	$('fl-icon').src            = v('f-icon');
	rc('cp-f','pv-f');
}

function uH(){
	$('hl-label').textContent   = v('h-label');
	$('hl-content').textContent = v('h-content');
	$('hl-icon').src             = v('h-icon');
	rc('cp-h','pv-h');
}

function addProsConsRow(wrapId){
	const isPro=wrapId.indexOf('pros')>-1;
	const wrap=$(wrapId);
	const row=document.createElement('div');
	row.className='dyn-row';
	row.innerHTML=`<input placeholder="${isPro?'Pro':'Con'} item text" oninput="uI()"><button class="dyn-rm" onclick="rmRow(this,'${wrapId}',uI)">✕</button>`;
	wrap.appendChild(row);uI();
}

const STAR_SVG = `<svg viewBox="0 0 20 20" fill="#DE9C34"><path d="M10 1l2.9 6 6.6.6-5 4.4 1.5 6.5L10 15.8 4 18.5l1.5-6.5-5-4.4 6.6-.6z"/></svg>`;
const STAR_SVG_EMPTY = `<svg viewBox="0 0 20 20" fill="#E2E8F2"><path d="M10 1l2.9 6 6.6.6-5 4.4 1.5 6.5L10 15.8 4 18.5l1.5-6.5-5-4.4 6.6-.6z"/></svg>`;

function starsHTML(rating){
	const filled=Math.max(0,Math.min(5,Math.round(rating)));
	let out='';
	for(let i=0;i<5;i++){ out += i<filled ? STAR_SVG : STAR_SVG_EMPTY; }
	out += `<span class="proscons-review-box__score">${rating}/5</span>`;
	return out;
}

function uI(){
	$('il-title').textContent = v('i-title');

	const pw=$('il-pros'); pw.innerHTML='';
	$('i-pros-rows').querySelectorAll('.dyn-row').forEach(row=>{
		const txt=row.querySelector('input').value.trim(); if(!txt) return;
		const li=document.createElement('li'); li.textContent=txt; pw.appendChild(li);
	});
	const cw=$('il-cons'); cw.innerHTML='';
	$('i-cons-rows').querySelectorAll('.dyn-row').forEach(row=>{
		const txt=row.querySelector('input').value.trim(); if(!txt) return;
		const li=document.createElement('li'); li.textContent=txt; cw.appendChild(li);
	});

	const prosRating=parseFloat(v('i-pros-rating'))||0;
	$('il-pros-stars').innerHTML = starsHTML(prosRating);
	$('il-pros-stars').setAttribute('aria-label', prosRating+' out of 5 stars');
	$('il-pros-quote').textContent = '"'+v('i-pros-quote')+'"';
	$('il-pros-name').textContent  = v('i-pros-name');
	$('il-pros-titletext').textContent = v('i-pros-title');

	const consRating=parseFloat(v('i-cons-rating'))||0;
	$('il-cons-stars').innerHTML = starsHTML(consRating);
	$('il-cons-stars').setAttribute('aria-label', consRating+' out of 5 stars');
	$('il-cons-quote').textContent = '"'+v('i-cons-quote')+'"';
	$('il-cons-name').textContent  = v('i-cons-name');
	$('il-cons-titletext').textContent = v('i-cons-title');

	const verdict=v('i-verdict');
	$('il-verdict').textContent = verdict;
	$('il-verdict').style.display = verdict ? '' : 'none';

	rc('cp-i','pv-i');
}

function rc(cpId,pvId){ if($(cpId).classList.contains('open')) render(cpId,pvId); }

function render(cpId,pvId){
	const preId='pre-'+cpId.replace('cp-','');
	const pre=$(preId),prev=$(pvId);
	if(!pre||!prev) return;
	pre.textContent=pretty(prev.innerHTML.trim());
}

function pretty(html){
	html=html.replace(/\s+id="[a-z]{2}-[a-z0-9-]+"/g,'');
	const T='  '; let d=0;
	return html.replace(/></g,'>\n<').split('\n').map(line=>{
		line=line.trim(); if(!line) return '';
		const isClose=/^<\//.test(line);
		const isSelf=/\/>$/.test(line);
		const isInline=/^<(a|span|strong|b|em|i|small|svg|path|circle)\b/.test(line);
		if(isClose) d=Math.max(0,d-1);
		const out=T.repeat(d)+line;
		if(!isClose&&!isSelf&&!isInline&&/^<[^!]/.test(line)) d++;
		return out;
	}).filter(Boolean).join('\n');
}

function copyPrev(pvId,btnId){
	const p=$(pvId),b=$(btnId); if(!p||!b) return;
	navigator.clipboard.writeText(pretty(p.innerHTML.trim())).then(()=>{
		b.textContent='Copied!'; b.classList.add('ok');
		showToast();
		setTimeout(()=>{b.textContent='Copy Code';b.classList.remove('ok');},2200);
	});
}
function copyPre(preId,btnId){
	const p=$(preId),b=$(btnId); if(!p||!b) return;
	navigator.clipboard.writeText(p.textContent).then(()=>{
		b.textContent='Copied'; b.classList.add('ok');
		showToast();
		setTimeout(()=>{b.textContent='Copy';b.classList.remove('ok');},2200);
	});
}
function showToast(){
	const t=$('toast'); t.classList.add('show');
	setTimeout(()=>t.classList.remove('show'),2400);
}

const obs=new IntersectionObserver(entries=>{
	entries.forEach(e=>{
		if(e.isIntersecting){
			const id=e.target.id;
			document.querySelectorAll('.sb-item').forEach(el=>{
				el.classList.toggle('active',el.getAttribute('href')==='#'+id);
			});
		}
	});
},{threshold:0.4});
['card-a','card-b','card-g','card-c','card-d','card-e','card-f','card-h','card-i'].forEach(id=>{
	const el=document.getElementById(id); if(el) obs.observe(el);
});

window.addEventListener('DOMContentLoaded',()=>{ uA(); uB(); uG(); uI(); });
</script>
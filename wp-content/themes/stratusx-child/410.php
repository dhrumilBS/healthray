<?php
status_header( 410 );
nocache_headers();
?>
<meta name="robots" content="noindex, nofollow">

<style>
	.hr-error-page {
		min-height: 65vh;
		display: flex;
		align-items: center;
		justify-content: center;
		background: linear-gradient(135deg, #ffffff 0%, var(--brand-tint) 100%);
		padding: 60px 16px;
	}
	.hr-error-page .hr-error-card {
		max-width: 560px;
		width: 100%;
		text-align: center;
		background: #fff;
		border: 1px solid var(--line);
		border-radius: var(--radius);
		padding: 48px 40px;
		box-shadow: 0 20px 60px rgba(15, 42, 102, .08);
	}
	.hr-error-page .hr-error-icon { margin-bottom: 20px; display: flex; justify-content: center; }
	.hr-error-page .hr-error-icon svg { width: 60px; height: 60px; color: var(--brand); }
	.hr-error-page .hr-error-code {
		font-family: var(--font-display);
		font-size: 5rem;
		font-weight: 800;
		line-height: 1;
		margin: 0;
		background: linear-gradient(135deg, var(--hr-primary-color), var(--brand));
		-webkit-background-clip: text;
		background-clip: text;
		color: transparent;
	}
	.hr-error-page .hr-error-title {
		font-size: 1.5rem;
		font-weight: 700;
		color: var(--hr-primary-color);
		margin-top: 8px;
		text-transform: none;
	}
	.hr-error-page .hr-error-desc {
		font-size: .95rem;
		color: var(--text);
		margin-top: 8px;
		max-width: 420px;
		margin-left: auto;
		margin-right: auto;
	}
	.hr-error-page .hr-error-btn {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		margin: 28px auto 0;
		background: var(--hr-secondary-color);
		color: #fff;
		font-weight: 600;
		padding: 12px 28px;
		border-radius: 10px;
		transition: background-color .25s ease, transform .25s ease;
	}
	.hr-error-page .hr-error-btn:hover { background: var(--hr-primary-color); color: #fff; transform: translateY(-1px); }
	.hr-error-page .hr-error-btn svg { width: 16px; height: 16px; flex-shrink: 0; }
	.hr-error-page .hr-error-divider { display: flex; align-items: center; gap: 12px; margin-top: 32px; }
	.hr-error-page .hr-error-divider span { flex: 1; height: 1px; background: var(--line); }
	.hr-error-page .hr-error-divider p { font-size: .8rem; color: var(--ink-soft); white-space: nowrap; }
	@media (max-width: 480px) {
		.hr-error-page .hr-error-card { padding: 32px 24px; }
		.hr-error-page .hr-error-code { font-size: 3.5rem; }
		.hr-error-page .hr-error-title { font-size: 1.25rem; }
		.hr-error-page .hr-error-divider { flex-direction: column; }
		.hr-error-page .hr-error-divider span { display: none; }
	}
</style>

<section class="hr-error-page">
	<div class="hr-error-card">
		<div class="hr-error-icon">
			<!-- archive/trash icon: "removed for good", distinct from 404's search icon -->
			<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M4.5 7.5H19.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
				<path d="M8.5 7.5V5.75C8.5 5.06 9.06 4.5 9.75 4.5H14.25C14.94 4.5 15.5 5.06 15.5 5.75V7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M6.5 7.5L7.2 18.1C7.26 18.96 7.98 19.63 8.84 19.63H15.16C16.02 19.63 16.74 18.96 16.8 18.1L17.5 7.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M10.25 11V16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
				<path d="M13.75 11V16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
			</svg>
		</div>
		<p class="hr-error-code">410</p>
		<h1 class="hr-error-title">This page has been removed</h1>
		<p class="hr-error-desc">Sorry, the content you're looking for has been permanently taken down and isn't coming back.</p>
		<a href="<?= esc_url( home_url( '/' ) ); ?>" class="hr-error-btn">
			Go back home
			<svg width="19" height="11" viewBox="0 0 19 11" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M1 5.5H18M18 5.5L13.5 1M18 5.5L13.5 10" stroke="currentcolor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</a>
		<div class="hr-error-divider">
			<span></span>
			<p>If you think this is a mistake, please contact support</p>
			<span></span>
		</div>
	</div>
</section>
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
			<!-- compass/search icon: "we couldn't find this" -->
			<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<circle cx="12" cy="12" r="9.25" stroke="currentColor" stroke-width="1.5"/>
				<path d="M15.5 8.5L13.2 13.2L8.5 15.5L10.8 10.8L15.5 8.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
			</svg>
		</div>
		<p class="hr-error-code">404</p>
		<h1 class="hr-error-title">Page not found</h1>
		<p class="hr-error-desc">Sorry, we couldn't find the page you're looking for. It may have moved, or the link might be out of date.</p>
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
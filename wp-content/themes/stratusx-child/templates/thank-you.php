<?php
/*
Template Name: Thank you
*/
?>
<style>
    .hero-section { background-color: #f3f5ff; display: flex; padding: 24px 10px; align-items: center; }
    .hero-section .page-width { max-width: 100%; margin: 0 auto; }
    .hero-section .heading { text-align: center; }
    .hero-section .hero-card { margin-bottom: 20px; gap: 20px; flex-wrap: wrap; }
    .hero-section .hero-card .hero-card-item { background: #fff; border-radius: 8px; padding: 18px; box-shadow: rgba(0, 0, 0, 0.1) -4px 9px 25px -6px; width: 100%; border: 1px solid var(--hr-primary-color); text-align: center; }
    .hero-section .hero-card .hero-card-item .hero-number { padding: 10px; color: #fff; margin: 0 auto; margin-bottom: 10px; background: var(--hr-primary-color); border-radius: 50px; width: 50px; height: 50px; font-size: 27px; display: flex; justify-content: center; align-items: center; }
    .hero-section .hero-card .hero-card-item .hero-card-details {}

    .hero-section .hero-card .hero-card-item .hero-card-details .hero-card-title { margin-bottom: 8px; display: flex; flex-direction: column; }
    .hero-section .hero-card .hero-card-item .hero-card-details .hero-card-title span { font-size: 62.5%; display: block; margin-top: 4px; opacity: .75; }

    .hero-section .home-btn-wrap { display: flex; justify-content: center; margin-top: 50px; }
    .hero-section .home-btn-wrap a { display: flex; align-items: center; gap: 12px; background-color: var(--hr-secondary-color); color: #FFF; padding: 12px 24px; border-radius: 8px; font-weight: 700; }
    .hero-section .home-btn-wrap a:hover { background-color: var(--hr-primary-color); }
    .hero-section .home-btn-wrap .icon { display: flex; align-items: center; }

    @media only screen and (min-width: 767px) {
        .hero-section .hero-card { flex-wrap: nowrap; }
        .hero-section .hero-card .hero-card-item { width: calc((100% - 40px) / 3); text-align: left; }
        .hero-section .hero-card .hero-card-item .hero-number { margin: 0 0 10px 0; }
    }
    @media only screen and (min-width: 991px) {
        .hero-section .hero-card .hero-card-item .hero-number { float: left; }
        .hero-section .hero-card .hero-card-item .hero-card-details .hero-card-title { margin-left: 60px; }
    }
    @media only screen and (min-width: 1100px) {
        .hero-section { padding: 50px; }
        .hero-section .page-width { max-width: 1140px; }
    }
</style>

<div class="hero-section section">
    <div class="page-width">
        <div class="heading">
            <h1 class="title">Thank You For Contacting Us!</h1>
            <p class="text">Please wait for our response.</p>
        </div>

        <div class="d-flex hero-card">
            <div class="hero-card-item dark-bg">
                <h2 class="hero-number">01</h2>
                <div class="hero-card-details">
                    <h3 class="hero-card-title">Receive Reply <span>Within A Business Day</span> </h3>
                    <p class="hero-card-text">An engagement manager at Healthray will get back to you. If you have any further information to share, we’d be glad to hear from you.
                </div>
            </div>
            <div class="hero-card-item dark-bg">
                <h2 class="hero-number">02</h2>
                <div class="hero-card-details">
                    <h3 class="hero-card-title"> Proposal <span>Expect It 2-3 Days</span> </h3>
                    <p class="hero-card-text">Our team will get back to you to prepare a proposal based on the project requirement. It will take 2-3 days. Our proposal will then be awaiting your response.
                </div>
            </div>
            <div class="hero-card-item dark-bg">
                <h2 class="hero-number">03</h2>
                <div class="hero-card-details">
                    <h3 class="hero-card-title">Execution <span>Keeping You Safe Is Our Top Priority</span></h3>
                    <p class="hero-card-text">For healthcare software, we offer end-to-end support from idea to launch, ensuring secure and innovative solutions tailored to the industry’s needs.
                </div>
            </div>
        </div>

        <div class="home-btn-wrap">
            <a href="<?= site_url(); ?>" target="_blank" class="theme-btn btn">
                <span>Back To Home</span>
                <span class="icon icon-24">
                    <svg aria-hidden="true" width="8" height="16" viewBox="0 0 256 512" xmlns="http://www.w3.org/2000/svg" fill="currentcolor">
                        <path d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path>
                    </svg>
                </span>
            </a>
        </div>
    </div>
</div>
<?php if(1==2){ ?>
<script>  fbq('track', 'Contact'); </script>
<?php } ?>

<?php // the_content(); ?>
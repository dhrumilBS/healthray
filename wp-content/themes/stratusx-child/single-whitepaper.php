    <main class="min-h-screen bg-background">
        <section class="sec-padded hero">
            <div class="container">
                <div class="hero-grid">
                    <div class="hero-text heading">
                        <h1><?= get_field('hero_title'); ?></h1>
                        <p><?= get_the_content(); ?></p>
                        <a href="#downloadForm" class="btn btn-primary"><?= get_field('hero_btn_text') ? get_field('hero_btn_text') : 'Get Free Whitepaper'; ?> </a>

                    </div>
                    <div class="hero-image text-center">
                        <div class="image-wrapper">
                            <?= get_the_post_thumbnail(get_the_ID(), 'full'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <section class="sec-padded features">
            <div class="container">
                <div class="section-header heading">
                    <h2><?= get_field('why_choose_title'); ?></h2>
                    <p><?= get_field('why_choose_text'); ?></p>
                </div>

                <?php if (have_rows('why_choose')) { ?>
                    <div class="features-grid">
                        <?php while (have_rows('why_choose')) {
                            the_row(); ?>
                            <div class="card-professional">
                                <div class="icon-circle">
                                    <!-- Icon -->
                                    <?= get_sub_field('icon'); ?>
                                </div>
                                <h3><?= get_sub_field('title'); ?></h3>
                                <p> <?= get_sub_field('text'); ?></p>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </section>


        <section class="sec-padded download-form" id="downloadForm">
            <div class="container">
                <div class="section-header heading">
                    <h2>Get Your Free Healthcare Analytics Report</h2>
                    <p>Join thousands of healthcare professionals who trust our insights</p>
                </div>
                <div class="whitepaper-form-wrapper form-wrapper" data-form-id="61816" data-post-id="<?php the_ID(); ?>" data-nonce="<?= esc_attr(wp_create_nonce('whitepaper_pdf_nonce')); ?>">
                    <?php
                    // Rendered inside the loop so Contact Form 7 submits this whitepaper as the
                    // form's container post, which hr_whitepaper_feedback_response() uses to
                    // return the right PDF.
                    while (have_posts()) {
                        the_post();
                        echo do_shortcode('[contact-form-7 id="e99fa6d" title="Whitepaper Form"]');
                    }
                    ?>
                </div>
            </div>
        </section>


        <section class="sec-padded why-research">
            <div class="container">
                <div class="grid">
                    <!-- Left Content -->
                    <div class="content heading">
                        <h2><?= get_field('text_image_title'); ?></h2>
                        <p class="intro"><?= get_field('text_image_text'); ?></p>

                        <?php if (have_rows('text_image')) { ?>
                            <div class="features-list">
                                <?php while (have_rows('text_image')) {
                                    the_row(); ?>
                                    <div class="feature">
                                        <div class="icon-box">
                                            <!-- Chart Column Icon -->
                                            <?= get_sub_field('icon'); ?>
                                        </div>
                                        <div>
                                            <h3> <?= get_sub_field('title'); ?> </h3>
                                            <p> <?= get_sub_field('text'); ?> </p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <div class="trusted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">                             <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>                             <path d="m9 11 3 3L22 4"></path>                         </svg>
                            <span><?= get_field('text_image_note'); ?></span>
                        </div>
                    </div>

                    <!-- Right Card -->
                    <div class="card-wrapper">
                        <div class="card-box">
                            <div class="stats-card">
                                <div class="skeleton"></div>
                                <div class="skeleton full"></div>
                                <div class="skeleton medium"></div>

                                <div class="stats-wrap">
                                    <div class="stats">
                                        <div class="stat">
                                            <div class="value">95%</div>
                                            <div class="label">Accuracy</div>
                                        </div>
                                        <div class="stat">
                                            <div class="value">24/7</div>
                                            <div class="label">Support</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="stats">
                                    <div class="stat">
                                        <div class="value">99.9%</div>
                                        <div class="label">Uptime</div>
                                    </div>
                                    <div class="stat">
                                        <div class="value">>2 Min</div>
                                        <div class="label">Response</div>
                                    </div>
                                </div>

                                <div class="chart-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M3 3v16a2 2 0 0 0 2 2h16"></path>
                                        <path d="M18 17V9"></path>
                                        <path d="M13 17V5"></path>
                                        <path d="M8 17v-3"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <div class="elementor-shortcode">
            <?php echo do_shortcode('[elementor-template id="25539"]'); ?>
        </div>


        <section class="sec-padded industry-leadership">
            <div class="container">
                <div class="text-center heading section-header">
                    <h2>Recognized Industry Leader</h2>
                    <p>Our commitment to excellence in healthcare analytics has earned recognition from leading
                        industry organizations</p>
                </div>
                <div class="awards-grid">
                    <div class="award-card">
                        <div class="award-icon trophy">
                            <!-- SVG here -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24"
                                fill="none" stroke="currentcolor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-trophy w-10 h-10 text-white">
                                <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
                                <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
                                <path d="M4 22h16"></path>
                                <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path>
                                <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path>
                                <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path>
                            </svg>
                        </div>
                        <h3>Healthcare Innovation Award 2024</h3>
                        <p class="award-org">Healthcare Technology Association</p>
                        <p class="award-desc">Best Analytics Platform for Patient Outcomes</p>
                    </div>
                    <div class="award-card">
                        <div class="award-icon award">
                            <!-- SVG here -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24"
                                fill="none" stroke="currentcolor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-award w-10 h-10 text-white">
                                <path
                                    d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526">
                                </path>
                                <circle cx="12" cy="8" r="6"></circle>
                            </svg>
                        </div>
                        <h3>Digital Health Excellence</h3>
                        <p class="award-org">Modern Healthcare Magazine</p>
                        <p class="award-desc">Top 10 Healthcare Analytics Solutions</p>
                    </div>
                    <div class="award-card">
                        <div class="award-icon star">
                            <!-- SVG here -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24"
                                fill="none" stroke="currentcolor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-star w-10 h-10 text-white">
                                <path
                                    d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                                </path>
                            </svg>
                        </div>
                        <h3>Customer Choice Award</h3>
                        <p class="award-org">G2 Healthcare Software</p>
                        <p class="award-desc">Highest Rated Analytics Platform</p>
                    </div>
                    <div class="award-card">
                        <div class="award-icon medal">
                            <!-- SVG here -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24"
                                fill="none" stroke="currentcolor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-medal w-10 h-10 text-white">
                                <path
                                    d="M7.21 15 2.66 7.14a2 2 0 0 1 .13-2.2L4.4 2.8A2 2 0 0 1 6 2h12a2 2 0 0 1 1.6.8l1.6 2.14a2 2 0 0 1 .14 2.2L16.79 15">
                                </path>
                                <path d="M11 12 5.12 2.2"></path>
                                <path d="m13 12 5.88-9.8"></path>
                                <path d="M8 7h8"></path>
                                <circle cx="12" cy="17" r="5"></circle>
                                <path d="M12 18v-2h-.5"></path>
                            </svg>
                        </div>
                        <h3>Industry Leader Recognition</h3>
                        <p class="award-org">HIMSS Analytics</p>
                        <p class="award-desc">Most Innovative Data Solutions Provider</p>
                    </div>
                </div>
            </div>
        </section>

        <?php echo do_shortcode('[elementor-template id="62278"]'); ?>

        <section class="sec-padded events-news d-none">
            <div class="container">
                <div class="text-center section-header heading">
                    <h2>Latest Events & News</h2>
                    <p>Stay updated with our latest announcements, events, and industry insights</p>
                </div>
                <div class="events-grid">
                    <div class="event-card">
                        <div class="event-labels">
                            <span class="label upcoming">Upcoming</span>
                            <span class="type">Webinar</span>
                        </div>
                        <h3>Healthcare Analytics: 2024 Trends & Predictions</h3>
                        <div class="event-meta">
                            <div class="meta-item">
                                <span class="icon calendar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewbox="0 0 24 24" fill="none" stroke="currentcolor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-calendar w-4 h-4">
                                        <path d="M8 2v4"></path>
                                        <path d="M16 2v4"></path>
                                        <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                        <path d="M3 10h18"></path>
                                    </svg>
                                </span>March 15, 2024 • 2:00 PM EST
                            </div>
                            <div class="meta-item">
                                <span class="icon location">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewbox="0 0 24 24" fill="none" stroke="currentcolor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-map-pin w-4 h-4">
                                        <path
                                            d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0">
                                        </path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                </span>Virtual Event
                            </div>
                            <div class="meta-item">
                                <span class="icon users"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewbox="0 0 24 24" fill="none" stroke="currentcolor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-users w-8 h-8 text-white">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg></span>2,500+
                                attendees
                            </div>
                        </div>
                        <p>Join our experts as they discuss the latest trends in healthcare analytics and what to
                            expect in 2024.</p>
                        <button class="btn btn-primary">Register Now</button>
                    </div>
                    <div class="event-card">
                        <div class="event-labels">
                            <span class="label completed">Completed</span>
                            <span class="type">Conference</span>
                        </div>
                        <h3>HIMSS24 Conference Presentation</h3>
                        <div class="event-meta">
                            <div class="meta-item">
                                <span class="icon calendar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewbox="0 0 24 24" fill="none" stroke="currentcolor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-calendar w-4 h-4">
                                        <path d="M8 2v4"></path>
                                        <path d="M16 2v4"></path>
                                        <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                        <path d="M3 10h18"></path>
                                    </svg>
                                </span>March 8, 2024 • 10:30 AM CST
                            </div>
                            <div class="meta-item">
                                <span class="icon location">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewbox="0 0 24 24" fill="none" stroke="currentcolor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-map-pin w-4 h-4">
                                        <path
                                            d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0">
                                        </path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                </span>Orlando, FL
                            </div>
                            <div class="meta-item">
                                <span class="icon users"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewbox="0 0 24 24" fill="none" stroke="currentcolor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-users w-8 h-8 text-white">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg></span>500+
                                attendees
                            </div>
                        </div>
                        <p>Our CEO presented groundbreaking research on AI-driven patient outcome improvements.</p>
                        <button class="btn btn-primary">View Recording</button>
                    </div>
                    <div class="event-card">
                        <div class="event-labels">
                            <span class="label news">News</span>
                            <span class="type">Announcement</span>
                        </div>
                        <h3>New Partnership with Mayo Clinic</h3>
                        <div class="event-meta">
                            <div class="meta-item">
                                <span class="icon calendar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewbox="0 0 24 24" fill="none" stroke="currentcolor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-calendar w-4 h-4">
                                        <path d="M8 2v4"></path>
                                        <path d="M16 2v4"></path>
                                        <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                        <path d="M3 10h18"></path>
                                    </svg>
                                </span>February 28, 2024
                            </div>
                            <div class="meta-item">
                                <span class="icon location">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewbox="0 0 24 24" fill="none" stroke="currentcolor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-map-pin w-4 h-4">
                                        <path
                                            d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0">
                                        </path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                </span>Rochester, MN
                            </div>
                        </div>
                        <p>Excited to announce our strategic partnership to advance precision medicine through
                            analytics.</p>
                        <button class="btn btn-primary">Read More</button>
                    </div>
                </div>
                <div class="text-center">
                    <button class="btn-secondary">View All Events & News</button>
                </div>
            </div>
        </section>
    </main>
    <?php echo do_shortcode('[elementor-template id="26869"]'); ?>
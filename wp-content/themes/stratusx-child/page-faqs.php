<section class="sec-padded hero-section blog-hero">
    <div class="container">
        <div class="heading text-center">
            <h1> <span>Frequently Asked Questions</span> </h1>
            <?php if (get_the_content()) { ?>
                <?php the_content(); ?>
            <?php } else { ?>
                <p style="margin-bottom: 0;">If you can't find an answer that you're looking for, feel free to drop us a line.</p>
            <?php } ?>
        </div>
    </div>
</section>

<?php
$healthray_faqs = [
    'EHR' => [
        [
            "question" => "What are the steps a user must take to start Healthray EHR?",
            "answer" => "<p>For Healthray implementation, users must contact Healthray teams. The Healthray team will provide you with a demo, assessment and onboarding roadmap.</p>"
        ],
        [
            "question" => "How does the Healthray system ensure compliance?",
            "answer" => "<p>Healthray ensures compliance through regular audits, documentation and staff training.</p>"
        ],
        [
            "question" => "What is the cost structure of Healthray implementation?",
            "answer" => "<p>Healthray provides flexible payment options—Monthly, quarterly and annual subscriptions. For detailed pricing, connect with the Healthray team.</p>"
        ],
        [
            "question" => "What are the features Healthray provides for billing and financial management?",
            "answer" => "<p>Healthray automates system billing, invoicing and reporting. These features minimize the manual errors and raise the efficacy.</p>"
        ],
        [
            "question" => "Can patients view their records in Healthray software?",
            "answer" => "<p>Yes, patients can view their records in the healthcare application, book their appointments, and securely connect with their doctors.</p>"
        ],
        [
            "question" => "How does Healthray EHR improve patient safety?",
            "answer" => "<p>The Healthray system provides real-time and updated medical records to help doctors make concrete decisions, avoid duplicate tests and minimize errors.</p>"
        ],
        [
            "question" => "What happens if the Healthray system fails or has an outage?",
            "answer" => "<p>The Healthray system has an inbuilt automated backup and disaster recovery system. It guarantees business continuity for cloud users and provides IT support for on-premises users.</p>"
        ],
        [
            "question" => "How does Healthray handle upgrades and updates?",
            "answer" => "<p>Cloud users get automatic updates, and on-premises users get notifications, and they can upgrade the system as per their convenience.</p>"
        ],
        [
            "question" => "Can a user switch from an existing EHR to a new platform?",
            "answer" => "<p>Yes, Healthray supports data migration features. Old patient data can be securely and accurately transferred to the new system.</p>"
        ],
        [
            "question" => "Can user customize the Healthray system?",
            "answer" => "<p>Yes, the Healthray system can be customized according to different specialties. Templates, workflows and forms can be modified as per different systems.</p>"
        ],
        [
            "question" => "Can new users get training?",
            "answer" => "<p>Yes, Healthray provides online or on-site training. Healthray provides user manuals and ongoing support to keep the onboarding operation smooth.</p>"
        ],
        [
            "question" => "How much time does Healthray take for implementation?",
            "answer" => "<p>Normally, it takes 6 to 8 weeks depending on hospital size or system complexity. Healthray implementations are assigned by managers and provide complete training.</p>"
        ],
        [
            "question" => "How does the Healthray system provide support?",
            "answer" => "<p>Healthray provides phone, email and ticket-based support during business hours. Response times are set based on priority.</p>
                        <div><strong>Critical (system down):</strong> 1-hour response, 4-hour resolution</div>
                        <div><strong>Medium (minor issue):</strong> 4-hour response, 2-day resolution</div>
                        <div><strong>Low (feature request):</strong> 5-day response, next upgrade release.</div>"
        ],
        [
            "question" => "Does Healthray provide support for telehealth and remote integration?",
            "answer" => "<p>Yes, Healthray allows secure remote access, where doctors can do teleconsultations, e-prescriptions and remote patient monitoring efficiently.</p>"
        ],
        [
            "question" => "Can the Healthray EHR system effectively connect with different platforms?",
            "answer" => "<p>Yes, Healthray can efficiently integrate with Healthray labs, pharmacies, diagnostic devices and other healthcare software. This ensures seamless data exchange and interoperability.</p>"
        ],
        [
            "question" => "What are the deployment options available in the Healthray system?",
            "answer" => "<p>Healthray provides two options—cloud-based (SaaS) or on-premise. Cloud users get the benefits of remote access, automatic updates and disaster recovery. On-premises users get full control of data and infrastructure.</p>"
        ],
        [
            "question" => "What are the features available in the Healthray EHR system?",
            "answer" => "<ul>
                <li>Centralized patient records and visit histories</li>
                <li>Customizable templates and workflows</li>
                <li>Integrated lab, pharmacy and prescription management</li>
                <li>ICD code compliance</li>
                <li>Patient portal or secure messaging</li>
                <li>Automated reminders and appointment scheduling</li>
                <li>Real-time analytics and reporting</li>
            </ul>"
        ],
        [
            "question" => "How is the Healthray EHR system different from paper records?",
            "answer" => "<p>The Healthray EHR system stores patient data in digital format. This makes the information instantly accessible, searchable, and easily sharable among authorized staff. It minimizes errors, saves time and improves coordination among departments.</p>"
        ],
        [
            "question" => "How does Healthray ensure seamless interoperability with third-party healthcare systems or devices?",
            "answer" => "<p>The Healthray application is based on HL7, FHIR, and DICOM standards. With secure APIs and middleware, labs, pharmacies, radiology, billing, insurance portals and IoT-based medical devices support real-time bidirectional data exchange.</p>"
        ],
        [
            "question" => "How does Healthray maintain integrity during data migration?",
            "answer" => "<p>Healthray uses automated tools and manual validation to migrate the data accurately. The process includes:</p>
            <ul>
                <li>Source data profiling and cleansing</li>
                <li>Mapping rules and transformation logic</li>
                <li>Trial migrations and validation reports</li>
                <li>Secure transfer through encrypted protocols</li>
                <li>Post-migration audit to ensure completeness and accuracy.</li>
            </ul>"
        ],
        [
            "question" => "What is the security framework for Healthray during the integration and implementation process?",
            "answer" => "<ul>
            <li>Data encryption (AES-256 for rest, TLS 1.3 for transit)</li>
            <li>Role-based access or multi-factor authentication</li>
            <li>Detailed audit logs of every change</li>
            <li>HIPAA, GDPR and industry best-practice compliance</li>
            <li>Network segmentation or virtual private clouds for cloud users</li>
            <li>Continuous monitoring and incident response protocols</li>
        </ul>"
        ],
        [
            "question" => "How does Healthray provide post-implementation support or continuous improvement?",
            "answer" => "<ul>
            <li>24×7 technical support via phone, email & ticket system</li>
            <li>Regular performance health checks and optimization</li>
            <li>User feedback channels for quick issue resolution</li>
            <li>Scheduled refresher trainings and update webinars</li>
            <li>Custom feature requests and workflow improvements as part of continuous deployment</li>
        </ul>"
        ],
        [
            "question" => "How does the Healthray EHR system ensure smooth operations during software updates and patch deployments?",
            "answer" => "<p>Cloud deployments benefit from zero-downtime rolling updates handled via container orchestration. On-premise upgrades are planned during low-usage windows, backed by comprehensive pre-deployment testing and rollback features.</p>"
        ],
        [
            "question" => "What detailed steps are involved in the Healthray EHR implementation process?",
            "answer" => "<ol>
            <li><strong>Needs Assessment:</strong> Comprehensive stakeholder interviews and workflow analyses</li>
            <li><strong>Customization & Configuration:</strong> Tailoring templates, workflows, user roles, and reports</li>
            <li><strong>Data Migration:</strong> Secure extraction, validation, and import of historical records</li>
            <li><strong>Training & Change Management:</strong> Role-based training modules and communication plans</li>
            <li><strong>Go-Live & Stabilization:</strong> Parallel running, technical support, and optimization</li>
            <li><strong>Post-Implementation Review:</strong> Performance audits and improvement planning</li>
        </ol>"
        ],
        [
            "question" => "How do we assess organizational readiness for successful EHR adoption?",
            "answer" => "<p>Hospitals should evaluate their current clinical workflows, IT infrastructure, and staff digital literacy. Involve key stakeholders and conduct a detailed gap analysis to identify risks and requirements.</p>"
        ],
        [
            "question" => "Which approach is better for EHR implementation: phased rollout or big bang?",
            "answer" => "<p>Phased rollout is more secure, allowing gradual adoption and pilot testing. Big bang is faster but riskier. The choice depends on organization size, complexity, and risk tolerance.</p>"
        ],
        [
            "question" => "What are the advanced interoperability standards an EHR system should follow?",
            "answer" => "<p>Standards like HL7, FHIR, and DICOM are essential for real-time data exchange with labs, pharmacies, imaging systems, and external devices. They ensure workflow smoothness and compliance.</p>"
        ],
        [
            "question" => "Which security layers are essential to keep the EHR system secure?",
            "answer" => "<p>Use AES-256 encryption for data at rest and TLS 1.3 for data in transit, detailed audit logs, and continuous monitoring. Compliance with HIPAA, GDPR, and other regulations is mandatory to safeguard PHI.</p>"
        ],
        [
            "question" => "How can EHR customization be balanced without hindering upgrade compatibility?",
            "answer" => "<p>Customize templates, workflows, and reports through a modular framework. Avoid deep changes in core code; use API extensions so that vendor upgrades and interoperability remain intact.</p>"
        ],
        [
            "question" => "What are the best practices for user training to maximize adoption and minimize resistance?",
            "answer" => "<p>Design role-specific training modules, use super-users as champions, establish continuous feedback loops, and provide post-go-live support to boost user confidence.</p>"
        ],
        [
            "question" => "How to monitor and optimize EHR performance after implementation?",
            "answer" => "<p>Regularly monitor KPIs such as user satisfaction, documentation accuracy, patient wait times, and system uptime. Use analytics to identify bottlenecks and improve workflows.</p>"
        ],
        [
            "question" => "What are the best strategies for EHR disaster recovery or business continuity?",
            "answer" => "<p>Use automated backups, encrypted data transfer, redundant servers, regular recovery testing, and a trained incident team to minimize downtime and protect data.</p>"
        ],
        [
            "question" => "How does the EHR system evolve with future healthcare trends like telehealth and AI?",
            "answer" => "<p>Adopt modular and API-driven architecture that integrates with telehealth platforms and AI-based decision support or remote monitoring devices to ensure scalability and future readiness.</p>"
        ]
    ],
    'HMS' => [
        [
            "question" => "What training does Healthray provide to HMS users?",
            "answer" => "<p>Yes, Healthray provides detailed training sessions, user manuals, video tutorials and on-demand support so that staff can confidently use the system.</p>"
        ],
        [
            "question" => "How does the HMS system handle updates and maintenance?",
            "answer" => "<p>Cloud users get automatic updates. On-premise hospitals get scheduled upgrades and a support team to minimize downtime.</p>"
        ],
        [
            "question" => "How does HMS help in regulatory compliance?",
            "answer" => "<p>The HMS system provides HIPAA, ICD codes, local authority standards compliance tools, and audit-ready reports, which makes the regulatory checks easy.</p>"
        ],
        [
            "question" => "How does HMS manage user sessions for security?",
            "answer" => "<p>Inactive sessions automatically get logged out and require re-login. Admin can actively audit active sessions through dashboards.</p>"
        ],
        [
            "question" => "How does HMS manage user roles and permissions?",
            "answer" => "<p>The HMS dashboard includes Role-based access control (RBAC) features, ensuring only permitted users (doctor, nurse, pharmacist, or admin) get access to dashboards.</p>"
        ],
        [
            "question" => "How does HMS ensure remote access?",
            "answer" => "<p>Cloud users can securely log in from multiple devices. On-premise setup, through VPN and a secure gateway, users can access the HMS system remotely.</p>"
        ],
        [
            "question" => "What are the reporting capabilities in HMS?",
            "answer" => "<p>The HMS system includes reporting tools to handle all the essential reports such as financial summaries, patient data, inventory usage, compliance reports and performance analytics.</p>"
        ],
        [
            "question" => "How does the HMS system integrate with the laboratory and radiology system?",
            "answer" => "<p>HMS system uses HL7 and DICOM-like standard protocols to seamlessly connect with lab and radiology systems; this makes the reports faster and accurate.</p>"
        ],
        [
            "question" => "Can HMS generate automatic billings and claim handling?",
            "answer" => "<p>Yes, HMS creates automated billing on the basis of services and generates electronic insurance claims to make the approval process faster.</p>"
        ],
        [
            "question" => "In what way does HMS schedule appointments?",
            "answer" => "<p>HMS sets appointments on the basis of doctors and appointment schedules. The system sends automated reminders to patients and reduces no-shows.</p>"
        ],
        [
            "question" => "Is HMS cloud-based or on-premise?",
            "answer" => "<p>Healthray HMS provides two options—cloud-based (for remote access and automatic updates) and on-premises (for full-data control and in-house hosting).</p>"
        ],
        [
            "question" => "Can HMS be customized for different departments?",
            "answer" => "<p>Yes, Healthray can be customized for different departments such as OPD, IPD, ICU, Emergency, Radiology, Laboratory, and Pharmacy and provide customized workflows and forms.</p>"
        ],
        [
            "question" => "What are the core modules of HMS?",
            "answer" => "<p>The core modules of HMS are Patient Registration and Admission, Appointment Scheduling, Billing and Insurance Claims, Pharmacy Management, Laboratory and Radiology Integration, Bed and Ward Management, EMR (Electronic Medical Record) linkage, Inventory Control, Staff Scheduling, Reporting and Analytics.</p>"
        ],
        [
            "question" => "How does HMS improve the workflows of hospitals?",
            "answer" => "<p>This system reduces manual paperwork, minimizes errors, streamlines patient flow and provides real-time data access to help doctors and staff make concrete decisions.</p>"
        ],
        [
            "question" => "How does HMS ensure patient data security?",
            "answer" => "<p>HMS uses strong encryption, access control, audit trails, and HIPAA-compliant security measures to keep patient information safe and away from cyberattacks.</p>"
        ],
        [
            "question" => "What are the steps a user needs to take for starting HMS implementation?",
            "answer" => "<p>To start HMS implementation, the user needs to contact the Healthray team. Healthray provides customization and staff training as per the hospital's size and requirements.</p>"
        ],
        [
            "question" => "How does the Healthray HMS system provide training to new users?",
            "answer" => "<p>Healthray HMS provides onboarding training, video tutorials, and manuals. This training helps users confidently leverage the full power of the system.</p>"
        ],
        [
            "question" => "What is the process of system upgrades?",
            "answer" => "<p>Cloud users get automatic updates. For on-premise users, scheduled maintenance or IT support is available.</p>"
        ],
        [
            "question" => "What kind of assistance is available to users who encounter issues while utilizing HMS?",
            "answer" => "<p>The Healthray HMS system has built-in options to provide assistance to users whenever they get stuck. They can also directly connect to the HMS team for any assistance they require.</p>"
        ],
        [
            "question" => "What are the options Healthray provides to connect with the team?",
            "answer" => "<p>Users can connect with the Healthray team by phone call, email, or in-app support. For urgent support, users can directly call the team and resolve their issues.</p>"
        ],
        [
            "question" => "What is the support response time?",
            "answer" => "<p>Normal queries get a reply within 2 to 3 hours. For critical issues like technical errors or downtime, the team provides a response within 1 hour.</p>"
        ],
        [
            "question" => "Does the user need to purchase a support plan?",
            "answer" => "<p>In the cloud version, support is already included. For on-premises users, through AMC (Annual Maintenance Contract), the user will get updates and support.</p>"
        ],
        [
            "question" => "What should a user do if they encounter data loss and errors in the system?",
            "answer" => "<p>Do not panic; Healthray HMS delivers regular backups. Immediately inform the support team for instant help; they will assist in data restoration or any other technical issues.</p>"
        ],
        [
            "question" => "Does Healthray HMS provide remote support?",
            "answer" => "<p>Yes, the support team through remote access can check the system and resolve all issues, without the need for multiple hospital visits.</p>"
        ],
        [
            "question" => "What should a user do when they need any suggestions or want to add new features?",
            "answer" => "<p>Users should submit their suggestions in feedback sessions. The Healthray product team reviews feedback and includes useful ideas in future updates.</p>"
        ],
        [
            "question" => "Do HMS Healthray teams provide regular updates and maintenance?",
            "answer" => "<p>Yes, the support team applies scheduled updates or security patches so that systems always remain updated and operate smoothly.</p>"
        ],
        [
            "question" => "Does Healthray HMS provide a free trial?",
            "answer" => "<p>Yes, users get free trials and demos to help them test the features and usability of the system before making a long-term commitment.</p>"
        ],
        [
            "question" => "Do users have a monthly plan or are they required to make full payments?",
            "answer" => "<p>Healthray HMS provides both options—monthly subscription and one-time license purchase. Hospitals can choose a plan as per their budget and requirements.</p>"
        ],
        [
            "question" => "What is the price difference between the cloud and on-premises prices?",
            "answer" => "<p>Cloud version includes both hosting and maintenance, so it is available only in subscription models. In the on-premises version, the user needs to purchase a one-time license, plus the maintenance cost is added separately.</p>"
        ],
        [
            "question" => "Do users need to pay extra charges for software updates?",
            "answer" => "<p>For cloud users, it is completely free. For on-premise users, through AMC (Annual Maintenance Contract), they need to pay for updates and upgrades.</p>"
        ],
        [
            "question" => "If a user needs additional modules, do they need to pay additional charges?",
            "answer" => "<p>Yes, if a user is adding additional modules like pharmacy, lab, or billing, then there is a minimal add-on cost for those features.</p>"
        ],
        [
            "question" => "Does Healthray HMS offer long-term contracts?",
            "answer" => "<p>Yes, yearly contracts are available where the user will get discounted pricing and dedicated support. The renewal process is simple and transparent.</p>"
        ],
        [
            "question" => "Are the training and onboarding costs of Healthray HMS different?",
            "answer" => "<p>Basic training plans are included in pricing. For advanced role-based and multi-location training, users need to pay additional charges.</p>"
        ],
        [
            "question" => "Will users be refunded if they decide to cancel their subscription?",
            "answer" => "<p>Users will not get refunds during the mid-cycle of monthly plans. But for annual plans, refund policies are available as per the terms and conditions.</p>"
        ],
        [
            "question" => "Does Healthray HMS include hidden charges?",
            "answer" => "<p>No, Healthray follows transparent pricing models. There are no hidden costs; whatever plan the user has selected is the final price.</p>"
        ],
        [
            "question" => "Are prices going to change if hospitals expand?",
            "answer" => "<p>Yes, if users and branches increase, then users need to upgrade the plan accordingly. This ensures smooth transitions and data safety.</p>"
        ],
        [
            "question" => "What is included in AMC (Annual Maintenance Contract)?",
            "answer" => "<p>AMC offers software updates, data backups, performance optimization, and technical support to ensure smooth and uninterrupted services.</p>"
        ],
        [
            "question" => "Does Healthray HMS provide customized packages?",
            "answer" => "<p>Yes, Healthray HMS provides customized packages as per hospital workflow or module requirements. It provides flexible and cost-effective options to users.</p>"
        ],
    ],
    'Pharmacy Management System' => [
        [
            "question" => "What is the process of starting the Healthray pharmacy management system?",
            "answer" => "<p>To begin using the <strong>Healthray Pharmacy Management System (PMS)</strong>, simply <strong>contact the Healthray team</strong> for setup, training, and a personalized demo.</p>"
        ],
        [
            "question" => "Does the Healthray PMS system follow government or healthcare regulations?",
            "answer" => "<p><strong>Yes</strong>, Healthray PMS adheres to <strong>HIPAA</strong> and <strong>Drug Control regulations</strong> to ensure legal compliance and secure operations.</p>"
        ],
        [
            "question" => "Does PMS allow multiple users to work on the same page?",
            "answer" => "<p><strong>Yes</strong>, Healthray PMS allows multiple users to work simultaneously. Each user gets a <strong>unique login and permission level</strong> to ensure proper control and data security.</p>"
        ],
        [
            "question" => "Does the user get support if any issues arise in Healthray PMS?",
            "answer" => "<p><strong>Yes</strong>, Healthray PMS provides comprehensive support via <strong>phone, email, and a ticket system</strong>. The support team guarantees resolution of critical issues within <strong>1 hour</strong>.</p>"
        ],
        [
            "question" => "Does Healthray PMS support mobile or remote access?",
            "answer" => "<p><strong>Yes</strong>, in the cloud version, users can securely log in from <strong>desktop or mobile devices</strong> and access the PMS system from anywhere in the world.</p>"
        ],
        [
            "question" => "Does Healthray PMS provide credit or return management features?",
            "answer" => "<p><strong>Yes</strong>, the system provides <strong>credit note</strong> and <strong>medicine return management</strong> features to keep all financial records accurate and up to date.</p>"
        ],
        [
            "question" => "Does Healthray PMS generate reports?",
            "answer" => "<p><strong>Yes</strong>, PMS generates multiple reports, including:</p>
            <ul>
                <li>Sales and purchase summaries</li>
                <li>Profit and loss statements</li>
                <li>Expiry and stock reports</li>
                <li>Department performance analytics</li>
            </ul>"
        ],
        [
            "question" => "Does PMS support barcoding or batch tracking?",
            "answer" => "<p><strong>Yes</strong>, Healthray PMS supports <strong>barcoding</strong> and <strong>batch tracking</strong> to ensure high accuracy and improved traceability in pharmacy operations.</p>"
        ],
        [
            "question" => "How does the billing process of Healthray PMS work?",
            "answer" => "<p>Select the medicine, choose a patient or department, and the system <strong>automatically calculates totals, taxes, and discounts</strong>. Users can print or share invoices instantly across departments.</p>"
        ],
        [
            "question" => "Can PMS handle vendor or purchase records?",
            "answer" => "<p><strong>Yes</strong>, Healthray PMS maintains detailed records of <strong>vendor purchase orders, invoices, and payments</strong>. It also triggers automatic reordering when stock levels fall below the threshold.</p>"
        ],
        [
            "question" => "How does Healthray PMS handle expiry and wastage control?",
            "answer" => "<p>Healthray PMS provides <strong>real-time expiry tracking</strong> and <strong>advance alerts</strong> to manage returns or replacements, minimizing losses and pilferage.</p>"
        ],
        [
            "question" => "Is PMS efficiently linked with hospital billing and the EMR system?",
            "answer" => "<p><strong>Yes</strong>, Healthray PMS integrates seamlessly with <strong>hospital billing and EMR</strong> systems. Any medicine issued is automatically reflected in the patient bill, avoiding duplicate entries.</p>"
        ],
        [
            "question" => "How does Hospital PMS handle stock management?",
            "answer" => "<p>Stock automatically updates with each sale or purchase. The system also generates <strong>low-stock</strong> and <strong>expiry alerts</strong> to prevent shortages and losses.</p>"
        ],
        [
            "question" => "Do users need special training to start using PMS?",
            "answer" => "<p><strong>No</strong>, Healthray PMS is <strong>intuitive and easy to use</strong>. Users with basic computer knowledge can manage stock, generate bills, and track alerts efficiently.</p>"
        ],
        [
            "question" => "Does the Healthray PMS system provide support to users for 24 hours?",
            "answer" => "<p><strong>Yes</strong>, Healthray PMS provides <strong>24/7 support</strong> for cloud users and <strong>business-hours support</strong> for on-premises users.</p>"
        ],
        [
            "question" => "What should a user do if they need support or training in PMS?",
            "answer" => "<p>Users can raise a request through the <strong>support portal or sales team</strong>. Healthray provides <strong>training videos, live demo sessions, and onboarding manuals</strong>.</p>"
        ],
        [
            "question" => "Does Healthray PMS provide a built-in guide?",
            "answer" => "<p><strong>Yes</strong>, the PMS includes a built-in <strong>Help</strong> section with <strong>step-by-step guides, FAQs, and video tutorials</strong> for quick assistance.</p>"
        ],
        [
            "question" => "What should a user do if the system displays wrong billing or stock data?",
            "answer" => "<p>Submit a <strong>Data Correction Request</strong> through the support portal. The technical team will verify and resolve the issue within <strong>24 hours</strong>.</p>"
        ],
        [
            "question" => "How does Healthray PMS provide notifications for upgrades and updates?",
            "answer" => "<p><strong>Cloud users</strong> receive automatic notifications when updates are released. <strong>On-premises users</strong> are informed by IT staff before scheduled maintenance.</p>"
        ],
        [
            "question" => "If the PMS system runs slowly or gets stuck, what steps should the user take?",
            "answer" => "<p>Clear the browser cache, check your internet connection, and if the issue persists, <strong>send log files</strong> to the support team for optimization.</p>"
        ],
        [
            "question" => "Can the support team fix problems through remote access?",
            "answer" => "<p><strong>Yes</strong>, with user consent, the support team can securely access the system remotely to resolve issues immediately.</p>"
        ],
        [
            "question" => "Does the Healthray PMS system provide feedback options?",
            "answer" => "<p><strong>Yes</strong>, users can submit feedback directly from the <strong>PMS dashboard or support portal</strong> to help improve system performance.</p>"
        ],
        [
            "question" => "Do users need to pay extra charges for support?",
            "answer" => "<p><strong>No</strong>, cloud users have support included. For on-premises users, support and updates are covered under the <strong>Annual Maintenance Contract (AMC)</strong>.</p>"
        ],
        [
            "question" => "How should a user go about implementing customization if it is necessary?",
            "answer" => "<p>Contact the <strong>Healthray support</strong> or <strong>account manager</strong> with your customization request. The team will provide an estimate and timeline.</p>"
        ],
        [
            "question" => "Does the support team provide regular maintenance?",
            "answer" => "<p><strong>Yes</strong>, monthly maintenance is provided for performance and security optimization. Cloud maintenance occurs automatically with minimal downtime.</p>"
        ],
        [
            "question" => "How to reset passwords in Healthray PMS system?",
            "answer" => "<p>Click <strong>'Forgot Password'</strong> on the login page, enter your registered email, and follow the reset link. For help, contact the support team.</p>"
        ],
        [
            "question" => "Does Healthray PMS provide special pricing for large pharmacy chains?",
            "answer" => "<p><strong>Yes</strong>, enterprise plans are available for large pharmacy chains, offering <strong>volume discounts</strong> and <strong>centralized dashboards</strong>.</p>"
        ],
        [
            "question" => "Does the Healthray PMS system provide refunds if users are not satisfied?",
            "answer" => "<p>The refund policy is <strong>transparent</strong>. After the trial period, refunds are unavailable, but transition and upgrade support are provided.</p>"
        ],
        [
            "question" => "What should the user do to upgrade the plan?",
            "answer" => "<p>Users can upgrade to a higher plan anytime. <strong>Data remains safe</strong>, and new features are activated automatically without reinstalling the software.</p>"
        ],
        [
            "question" => "What factors go into determining Healthray PMS pricing?",
            "answer" => "<p>Pricing depends on:</p>
            <ul>
                <li>Pharmacy size and number of users</li>
                <li>Selected modules (billing, inventory, reporting, etc.)</li>
                <li>Cloud or on-premises deployment</li>
            </ul>
            <p>Healthray provides customized quotations based on your needs.</p>"
        ]
    ],
    "Laboratory Management System" => [
        [
            "question" => "How does Healthray LMS provide benefits to users?",
            "answer" => "<p><strong>Healthray LMS</strong> is a digital platform that automates laboratory operations such as test booking, sample tracking, report generation, and billing. This minimizes manual work and paperwork.</p>"
        ],
        [
            "question" => "Do users require any technical knowledge to use Healthray LMS?",
            "answer" => "<p><strong>No</strong>, Healthray LMS is simple and user-friendly. Users just need basic computer knowledge. All forms, menus, and reports are guided for convenience.</p>"
        ],
        [
            "question" => "How does the test registration process work?",
            "answer" => "<p>When a user enters patient details and a test number, the LMS <strong>automatically generates a unique lab number</strong> and starts sample tracking instantly.</p>"
        ],
        [
            "question" => "Does Healthray LMS support sample tracking?",
            "answer" => "<p><strong>Yes</strong>, all samples are tagged through barcodes, making tracking easy from collection to testing and final report. The entire system is traceable.</p>"
        ],
        [
            "question" => "How does Healthray LMS generate test reports in the system?",
            "answer" => "<p>Reports are <strong>automatically generated</strong> once test results are entered. Predefined templates help maintain accuracy and consistency.</p>"
        ],
        [
            "question" => "Can doctors and patients both access reports in LMS?",
            "answer" => "<p><strong>Yes</strong>, both doctors and nurses can access reports via secure login. The LMS also sends <strong>SMS and email alerts</strong> when reports are ready.</p>"
        ],
        [
            "question" => "Can Healthray LMS handle billing and payments?",
            "answer" => "<p><strong>Yes</strong>, test rates are predefined and bills are generated automatically. The system supports <strong>cash, card, and online payments</strong>.</p>"
        ],
        [
            "question" => "How does Healthray LMS support lab inventory or reagent stock?",
            "answer" => "<p>The LMS automatically updates inventory whenever a test is performed. Users receive <strong>low-stock and expiry alerts</strong> to maintain smooth operations.</p>"
        ],
        [
            "question" => "Is Healthray LMS suitable for multiple branches?",
            "answer" => "<p><strong>Yes</strong>, multi-location labs can view all data on a <strong>centralized dashboard</strong> for real-time coordination.</p>"
        ],
        [
            "question" => "Can Healthray LMS customize lab reports?",
            "answer" => "<p><strong>Yes</strong>, hospitals can customize report templates with their branding, logo, and format for a professional appearance.</p>"
        ],
        [
            "question" => "Is Healthray LMS available for both cloud and on-premise versions?",
            "answer" => "<p><strong>Yes</strong>, the <strong>cloud version</strong> provides remote access, while the <strong>on-premise version</strong> suits users preferring an offline setup.</p>"
        ],
        [
            "question" => "Does Healthray LMS provide training to new users?",
            "answer" => "<p><strong>Yes</strong>, Healthray provides training sessions, video tutorials, and step-by-step manuals during onboarding.</p>"
        ],
        [
            "question" => "Does Healthray LMS provide reports and analytics?",
            "answer" => "<p><strong>Yes</strong>, automated analytics include daily test counts, revenue summaries, turnaround time, and staff performance insights.</p>"
        ],
        [
            "question" => "How does Healthray LMS provide support in case of failure or downtime?",
            "answer" => "<p>The Healthray support team is available <strong>24/7</strong> via phone, email, and ticket system. Urgent issues get a <strong>priority response within 1 hour</strong>.</p>"
        ],
        [
            "question" => "Does Healthray LMS keep the data secure?",
            "answer" => "<p><strong>Yes</strong>, all patient records are <strong>encrypted and password-protected</strong>. The system is <strong>HIPAA-compliant</strong> for maximum data security.</p>"
        ],
        [
            "question" => "Does the user need to share account details with the support team?",
            "answer" => "<p>Only <strong>authorized admin details</strong> should be shared. Healthray never asks for passwords or sensitive data.</p>"
        ],
        [
            "question" => "What should a user do if they are unable to produce reports?",
            "answer" => "<p>Restart the computer after clearing the cache. If the issue persists, <strong>raise a support ticket</strong> for remote troubleshooting.</p>"
        ],
        [
            "question" => "Where will the user get support contact details?",
            "answer" => "<p>In the system’s <strong>Help & Support</strong> section, users can find a phone number, email ID, and live chat for instant support.</p>"
        ],
        [
            "question" => "How will users ask for new features to be added to the system?",
            "answer" => "<p>Use the <strong>Feature Request</strong> option in the support portal to submit suggestions. The team reviews and includes them in future updates.</p>"
        ],
        [
            "question" => "Can the user check old tickets and query status?",
            "answer" => "<p><strong>Yes</strong>, in the <strong>Support Query Sessions</strong> section of the dashboard, users can view all previous tickets and their status.</p>"
        ],
        [
            "question" => "Does Healthray provide remote support?",
            "answer" => "<p><strong>Yes</strong>, the support team can remotely access your system (with consent) to resolve issues quickly.</p>"
        ],
        [
            "question" => "What should a user do if the system displays error messages?",
            "answer" => "<p>Take <strong>screenshots</strong> of the error messages and submit a ticket. Other modules can still be used until the issue is resolved.</p>"
        ],
        [
            "question" => "Does the Healthray LMS system provide monthly or yearly pricing?",
            "answer" => "<p>The LMS offers both <strong>monthly</strong> and <strong>yearly</strong> subscriptions, with discounts on yearly plans.</p>"
        ],
        [
            "question" => "Do users need to pay extra for setup charges?",
            "answer" => "<p><strong>Yes</strong>, a one-time setup and implementation fee covers installation, customization, and staff training.</p>"
        ],
        [
            "question" => "Does Healthray lower the cost if a user wants to use limited modules?",
            "answer" => "<p><strong>Yes</strong>, modular pricing allows users to pay only for the features they require.</p>"
        ],
        [
            "question" => "Does pricing vary for the cloud and on-premises versions?",
            "answer" => "<p><strong>Yes</strong>, the <strong>cloud version</strong> is more cost-effective, while the <strong>on-premise version</strong> includes local setup and maintenance costs.</p>"
        ],
        [
            "question" => "Are training charges included in the price?",
            "answer" => "<p><strong>Yes</strong>, basic training is included. Additional sessions or advanced training may have nominal fees.</p>"
        ],
        [
            "question" => "Do users need to pay extra charges at the time of renewals?",
            "answer" => "<p><strong>Yearly renewals</strong> include support and updates. Extra customizations may require an additional quote.</p>"
        ],
        [
            "question" => "Does Healthray offer refunds or a satisfaction guarantee?",
            "answer" => "<p>Refund policies are not explicitly stated. It’s recommended to use the <strong>free trial</strong> before subscribing.</p>"
        ],
        [
            "question" => "Are updates and upgrades included?",
            "answer" => "<p><strong>Yes</strong>, software updates and feature upgrades are included as long as the subscription is active.</p>"
        ],
        [
            "question" => "What are the pricing plans for Healthray LMS?",
            "answer" => "<p>Healthray offers <strong>subscription-based plans</strong> starting at around $100/month. One-time plans (₹79,999) may apply for larger HIMS packages.</p>"
        ]
    ],
    "Clinical Managemnt Software" => [
        [
            "question" => "Do users need any technical knowledge to get started with clinical management software?",
            "answer" => "<p><strong>No</strong>, Healthray CMS is simple and user-friendly. Users just need basic computer knowledge to operate the system.</p>"
        ],
        [
            "question" => "How does CMS track visitors and patient registration?",
            "answer" => "<p>The CMS automatically generates a <strong>patient ID</strong> and securely keeps past visits, test results, and prescriptions in one dashboard.</p>"
        ],
        [
            "question" => "Does CMS automate appointment scheduling?",
            "answer" => "<p><strong>Yes</strong>, users can view doctor availability, book slots, and receive <strong>SMS/email notifications</strong> to reduce no-shows.</p>"
        ],
        [
            "question" => "How does the billing and payment system work?",
            "answer" => "<p><strong>CMS automatically generates bills</strong> based on services provided. It supports <strong>online, cash, and card</strong> payments.</p>"
        ],
        [
            "question" => "Can Healthray CMS manage reports and prescriptions?",
            "answer" => "<p><strong>Yes</strong>, digital prescriptions and test reports are created automatically, allowing doctors to review patient history with one click.</p>"
        ],
        [
            "question" => "Is Healthray CMS useful for multi-doctor clinics?",
            "answer" => "<p><strong>Yes</strong>, it manages multiple doctors, departments, and staff with <strong>individual logins and access controls</strong>.</p>"
        ],
        [
            "question" => "Is patient data secure in the EMR system?",
            "answer" => "<p><strong>Yes</strong>, Healthray CMS uses <strong>data encryption and secure login</strong>. Only authorized staff can access patient data.</p>"
        ],
        [
            "question" => "Can users access CMS on tablets and mobile?",
            "answer" => "<p><strong>Yes</strong>, cloud-based CMS allows access through <strong>mobile, tablet, or laptop</strong> securely.</p>"
        ],
        [
            "question" => "How will users receive assistance if the system encounters problems?",
            "answer" => "<p>The Healthray support team is available via <strong>phone, email, and ticket portal</strong>. Urgent cases receive <strong>priority support</strong> within hours.</p>"
        ],
        [
            "question" => "Does the CMS system provide reports and analytics?",
            "answer" => "<p><strong>Yes</strong>, CMS provides analytics for <strong>revenue, visits, doctor performance,</strong> and <strong>treatment summaries</strong>.</p>"
        ],
        [
            "question" => "Does CMS provide automatic updates?",
            "answer" => "<p><strong>Cloud users</strong> get automatic updates, while <strong>on-premise users</strong> receive scheduled updates from the Healthray team.</p>"
        ],
        [
            "question" => "Does Healthray provide training to new CMS users?",
            "answer" => "<p><strong>Yes</strong>, Healthray provides training sessions, demo videos, and manuals during onboarding.</p>"
        ],
        [
            "question" => "What is the pricing model of Healthray CMS?",
            "answer" => "<p>Healthray CMS is <strong>subscription-based</strong>, offering monthly and yearly billing. Prices depend on <strong>users, modules, and deployment type</strong>.</p>"
        ],
        [
            "question" => "Do users get a free trial on Healthray CMS?",
            "answer" => "<p><strong>Yes</strong>, a free trial is available to explore all features before purchasing a paid plan.</p>"
        ],
        [
            "question" => "Does Healthray CMS provide different pricing plans?",
            "answer" => "<p><strong>Yes</strong>, pricing varies by clinic size and features — from <strong>basic</strong> CMS to <strong>premium plans</strong> with telehealth and analytics.</p>"
        ],
        [
            "question" => "Does Healthray CMS provide a lifetime license or one-time purchase?",
            "answer" => "<p>Healthray CMS is primarily <strong>subscription-based</strong>. For lifetime licenses, contact the support team.</p>"
        ],
        [
            "question" => "Does Healthray CMS software provide discounts on multi-year plans?",
            "answer" => "<p><strong>Yes</strong>, users get discounts for long-term or bulk license purchases. Contact sales for special offers.</p>"
        ],
        [
            "question" => "Are Healthray software updates included in the price?",
            "answer" => "<p><strong>Yes</strong>, all regular updates and new features are included in the subscription.</p>"
        ],
        [
            "question" => "What is the refund policy in the Healthcare CMS system?",
            "answer" => "<p>Refund policies are not publicly stated. It’s recommended to use the <strong>free trial</strong> before committing long-term.</p>"
        ],
        [
            "question" => "What type of support does Healthray CMS provide?",
            "answer" => "<p>Support is available through <strong>phone, email, and online ticketing</strong> during business hours.</p>"
        ],
        [
            "question" => "Does Healthray CMS provide 24/7 support?",
            "answer" => "<p><strong>24/7 support</strong> is available only for premium clients and high-priority cases.</p>"
        ],
        [
            "question" => "Does Healthray provide a dedicated account manager?",
            "answer" => "<p><strong>Yes</strong>, enterprise clients get a dedicated account manager and technical expert for personalized support.</p>"
        ],
        [
            "question" => "Does Healthray CMS provide on-site support and training?",
            "answer" => "<p><strong>Yes</strong>, Healthray offers both <strong>on-site and remote sessions</strong> for onboarding and training.</p>"
        ],
        [
            "question" => "What are the features available in clinical management software?",
            "answer" => "<p>Healthray CMS includes <strong>patient registration, appointment scheduling, billing, EMR, pharmacy, lab management, insurance, and analytics</strong>.</p>"
        ],
        [
            "question" => "Does Healthray support EMR features?",
            "answer" => "<p><strong>Yes</strong>, complete EMR features allow doctors to manage <strong>medical history, prescriptions, reports,</strong> and <strong>treatment plans</strong>.</p>"
        ],
        [
            "question" => "Can Healthray HIMS manage pharmacy and inventory?",
            "answer" => "<p><strong>Yes</strong>, Healthray includes pharmacy management with <strong>stock tracking, expiry alerts, and billing</strong> to improve efficiency.</p>"
        ],
        [
            "question" => "Is laboratory management included in Healthray CMS?",
            "answer" => "<p><strong>Yes</strong>, Laboratory Management features include <strong>sample tracking, test management, and diagnostic integration</strong>.</p>"
        ],
        [
            "question" => "Does Healthray CMS support insurance claim processing?",
            "answer" => "<p><strong>Yes</strong>, Healthray automates <strong>insurance claim submission and tracking</strong> for faster reimbursements.</p>"
        ]
    ]
];
?>

<section class="sec-padded faq-section">
    <div class="container">

        <div class="category-tabs d-flex align-items-center justify-content-between mb-4 gap-4">
            <h2 class="section-title text-primary">Categories</h2>
            <div class="faq-tabs">
                <?php
                $cat_i = 1;
                foreach ($healthray_faqs as $faq_name => $faqs) {
                    $class = ($cat_i === 1) ? 'active' : '';
                    echo "<button class=\"faq-tab $class\" data-target='" . esc_attr($faq_name) . "'> " . esc_html($faq_name) . "</button>";
                    $cat_i++;
                } ?>
            </div>
        </div>

        <?php $cat_i = 1;
        foreach ($healthray_faqs as $faq_name => $faqs) {
            $class = ($cat_i === 1) ? 'active' : '';
            $cat_i++;
            $faq_columns = array_chunk($faqs, ceil(count($faqs) / 2));
            ?>
            <div class="accordion-faq-wrapper <?= $class; ?>" id="<?= esc_attr($faq_name); ?>">
                <div class="faq-wrapper">
                    <?php foreach ($faq_columns as $col_index => $column) { ?>
                        <div class="elementor-toggle accordion-list" style="flex: 1; min-width: 320px;">
                            <?php foreach ($column as $item_index => $faq) {
                                $question = esc_html($faq['question']);
                                $answer = wp_kses_post($faq['answer']);
                                $item_num = $item_index + 1; // computed before use, no off-by-one
                                $is_first = ($item_num === 1);
                                $tab_id = esc_attr($faq_name) . '-' . $col_index . '-' . $item_num;
                                ?>
                                <div class="elementor-toggle-item<?= $is_first ? ' active' : ''; ?>">
                                    <div id="elementor-tab-title-<?= $tab_id; ?>" class="elementor-tab-title" data-tab="1" role="button" tabindex="0" aria-controls="elementor-tab-content-<?= $tab_id; ?>" aria-expanded="<?= $is_first ? 'true' : 'false'; ?>">
                                        <span class="elementor-toggle-icon elementor-toggle-icon-left" aria-hidden="true">
                                            <span class="elementor-toggle-icon-closed">
                                                <svg class="e-font-icon-svg e-fas-caret-down" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z"></path>
                                                </svg>
                                            </span>
                                            <span class="elementor-toggle-icon-opened">
                                                <svg class="elementor-toggle-icon-opened e-font-icon-svg e-fas-caret-up" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M288.662 352H31.338c-17.818 0-26.741-21.543-14.142-34.142l128.662-128.662c7.81-7.81 20.474-7.81 28.284 0l128.662 128.662c12.6 12.599 3.676 34.142-14.142 34.142z"></path>
                                                </svg>
                                            </span>
                                        </span>
                                        <div class="elementor-toggle-title" tabindex="0"><?= $question; ?></div>
                                    </div>

                                    <div id="elementor-tab-content-<?= $tab_id; ?>" class="elementor-tab-content" data-tab="1" role="region" aria-labelledby="elementor-tab-title-<?= $tab_id; ?>" style="<?= $is_first ? '' : 'display:none;'; ?>">
                                        <?= $answer; ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tabs = document.querySelectorAll(".faq-tab");
        const views = document.querySelectorAll(".accordion-faq-wrapper");

        tabs.forEach(tab => {
            tab.addEventListener("click", () => {
                tabs.forEach(t => t.classList.remove("active"));
                views.forEach(v => v.classList.remove("active"));

                tab.classList.add("active");
                document.getElementById(tab.dataset.target).classList.add("active");
            });
        });

        document.querySelectorAll(".elementor-toggle").forEach(list => {
            list.addEventListener("click", (e) => {
                const title = e.target.closest(".elementor-tab-title");
                if (!title || !list.contains(title)) return;

                const item = title.closest(".elementor-toggle-item");
                const content = document.getElementById(title.getAttribute("aria-controls"));
                const isOpen = title.getAttribute("aria-expanded") === "true";
                list.querySelectorAll(".elementor-tab-title").forEach(t => {
                    if (t !== title) {
                        t.setAttribute("aria-expanded", "false");
                        t.closest(".elementor-toggle-item").classList.remove("active");
                        const c = document.getElementById(t.getAttribute("aria-controls"));
                        if (c) c.style.display = "none";
                    }
                });

                title.setAttribute("aria-expanded", isOpen ? "false" : "true");
                item.classList.toggle("active", !isOpen);
                if (content) content.style.display = isOpen ? "none" : "block";
            });

            // Keyboard support (Enter / Space)
            list.addEventListener("keydown", (e) => {
                if (e.key !== "Enter" && e.key !== " ") return;
                const title = e.target.closest(".elementor-tab-title");
                if (!title) return;
                e.preventDefault();
                title.click();
            });
        });
    });
</script>
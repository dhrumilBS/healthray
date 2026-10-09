import { __ } from '@wordpress/i18n';
import {
	ChartNoAxesCombined,
	Cloud,
	Code,
	CodeXml,
	CreditCard,
	FileDown,
	GalleryVertical,
	GitBranch,
	Mail,
	PhoneForwarded,
	Pin,
	Users,
	Webhook,
	Workflow,
	Zap,
} from 'lucide-react';
import { useData } from './DataContext';

import { FeaturedAddonCard } from './components/addons/FeaturedAddonCard.jsx';
import { AddonCard } from './components/addons/AddonCard.jsx';
import { BenefitCard } from './components/addons/BenefitCard.jsx';
import { CallToActionSection } from './components/addons/CallToActionSection.jsx';

const AddonsTab = () => {
	const data = useData();
	const iconSize = 25;

	// Premium addons data
	const featuredAddons = [
		{
			id: 'webhook-integration',
			title: __( 'Webhook Integration', 'wpcf7-redirect' ),
			description: __(
				'Connect to any external service',
				'wpcf7-redirect'
			),
			details: __(
				'Send form data to any external service or API using webhooks. Perfect for integrating with custom systems or third-party services.',
				'wpcf7-redirect'
			),
			features: [
				__( 'POST, GET, PUT, DELETE methods', 'wpcf7-redirect' ),
				__( 'Custom headers and authentication', 'wpcf7-redirect' ),
				__( 'Dynamic data mapping from form fields', 'wpcf7-redirect' ),
				__( 'Webhook testing and validation', 'wpcf7-redirect' ),
			],
			isPremium: true,
			badgeLabel: __(
				'Available in the Business plan',
				'wpcf7-redirect'
			),
			learnMoreLink:
				'https://docs.themeisle.com/article/2024-using-contact-form-7-to-api-pardot-integration',
		},
		{
			id: 'conditional-logic',
			title: __( 'Conditional Logic', 'wpcf7-redirect' ),
			description: __(
				'Dynamic post-submission actions',
				'wpcf7-redirect'
			),
			details: __(
				'Create powerful conditional flows based on form inputs. Redirect users to different pages, send different emails, or trigger specific actions based on what they submit.',
				'wpcf7-redirect'
			),
			features: [
				__( 'Multiple conditions with AND/OR logic', 'wpcf7-redirect' ),
				__(
					'Compare form field values with operators',
					'wpcf7-redirect'
				),
				__(
					'Conditionally enable/disable any action',
					'wpcf7-redirect'
				),
				__(
					'Nested conditions for complex scenarios',
					'wpcf7-redirect'
				),
			],
			isPremium: true,
			badgeLabel: __( 'Available in the Starter plan', 'wpcf7-redirect' ),
			learnMoreLink:
				'https://docs.themeisle.com/article/2018-using-conditional-logic-redirect-for-contact-form-7',
		},
	];

	const allAddons = [
		{
			id: 'popup',
			title: __( 'Thank You Popup', 'wpcf7-redirect' ),
			description: __(
				'Display a customizable Thank You popup after each submission.',
				'wpcf7-redirect'
			),
			isPremium: true,
			icon: <GalleryVertical size={ iconSize } />,
			badgeLabel: __( 'Available in the Starter plan', 'wpcf7-redirect' ),
			learnMoreLink:
				'https://docs.themeisle.com/article/2020-using-contact-form-7-thank-you-popup',
		},
		{
			id: 'pdf-generation',
			title: __( 'PDF Generation', 'wpcf7-redirect' ),
			description: __(
				'Create branded PDF documents, receipts, and certificates from submissions.',
				'wpcf7-redirect'
			),
			isPremium: true,
			icon: <FileDown size={ iconSize } />,
			badgeLabel: __( 'Available in the Starter plan', 'wpcf7-redirect' ),
			learnMoreLink:
				'https://docs.themeisle.com/article/2227-how-to-create-pdf-from-form-submissions',
		},
		{
			id: 'conditional-logic',
			title: __( 'Conditional Logic', 'wpcf7-redirect' ),
			description: __(
				'Create intelligent workflows with if/then rules based on user input.',
				'wpcf7-redirect'
			),
			isPremium: true,
			icon: <GitBranch size={ iconSize } />,
			badgeLabel: __(
				'Available in the Business plan',
				'wpcf7-redirect'
			),
			learnMoreLink:
				'https://docs.themeisle.com/article/2018-using-conditional-logic-redirect-for-contact-form-7',
		},
		{
			id: 'firescript',
			title: __( 'Fire JavaScript code', 'wpcf7-redirect' ),
			description: __(
				'Run custom JavaScript code on form submission.',
				'wpcf7-redirect'
			),
			isPremium: true,
			icon: <CodeXml size={ iconSize } />,
			badgeLabel: __(
				'Available in the Starter plan',
				'wpcf7-redirect'
			),
		},
		{
			id: 'create-wordpress-posts',
			title: __( 'Create WordPress Posts', 'wpcf7-redirect' ),
			description: __(
				'Turn form submissions into custom post types with full field mapping.',
				'wpcf7-redirect'
			),
			isPremium: true,
			icon: <Pin size={ iconSize } />,
			badgeLabel: __( 'Available in the Starter plan', 'wpcf7-redirect' ),
			learnMoreLink:
				'https://docs.themeisle.com/article/2228-how-to-create-posts-from-contact-form-submissions',
		},
		{
			id: 'webhook-integration',
			title: __( 'Webhook Integration', 'wpcf7-redirect' ),
			description: __(
				'Trigger automations from Contact Form 7 to Zapier, Make, and more using simple webhooks.',
				'wpcf7-redirect'
			),
			isPremium: true,
			icon: <Webhook size={ iconSize } />,
			badgeLabel: __( 'Available in the Business plan', 'wpcf7-redirect' ),
			learnMoreLink:
				'https://docs.themeisle.com/article/2024-using-contact-form-7-to-api-pardot-integration',
		},
		{
			id: 'mailchimp-integration',
			title: __( 'Mailchimp Integration', 'wpcf7-redirect' ),
			description: __(
				'Grow your email list by syncing form submissions directly to Mailchimp.',
				'wpcf7-redirect'
			),
			isPremium: true,
			icon: <Mail size={ iconSize } />,
			badgeLabel: __(
				'Available in the Business plan',
				'wpcf7-redirect'
			),
			learnMoreLink:
				'https://docs.themeisle.com/article/2021-using-contact-form-7-to-mailchimp-integration',
		},
		{
			id: 'paypal-integration',
			title: __( 'PayPal Integration', 'wpcf7-redirect' ),
			description: __(
				'Accept secure payments and donations directly through your forms.',
				'wpcf7-redirect'
			),
			isPremium: true,
			icon: <CreditCard size={ iconSize } />,
			badgeLabel: __(
				'Available in the Business plan',
				'wpcf7-redirect'
			),
		},
		{
			id: 'stripe-integration',
			title: __( 'Stripe Integration', 'wpcf7-redirect' ),
			description: __(
				'Process credit card payments seamlessly with complete checkout flow.',
				'wpcf7-redirect'
			),
			isPremium: true,
			icon: <CreditCard size={ iconSize } />,
			badgeLabel: __(
				'Available in the Business plan',
				'wpcf7-redirect'
			),
		},
		{
			id: 'salesforce-integration',
			title: __( 'Salesforce Integration', 'wpcf7-redirect' ),
			description: __(
				'Create leads, contacts, and custom objects in Salesforce automatically.',
				'wpcf7-redirect'
			),
			isPremium: true,
			icon: <Cloud size={ iconSize } />,
			badgeLabel: __(
				'Available in the Enterprise plan',
				'wpcf7-redirect'
			),
			learnMoreLink:
				'https://docs.themeisle.com/article/2022-using-salesforce-integration-integrate-contact-form-7-to-salesforces',
		},
		{
			id: 'hubspot-integration',
			title: __( 'HubSpot Integration', 'wpcf7-redirect' ),
			description: __(
				'Sync contacts, deals, and custom properties with your HubSpot CRM.',
				'wpcf7-redirect'
			),
			isPremium: true,
			icon: <Workflow size={ iconSize } />,
			badgeLabel: __(
				'Available in the Enterprise plan',
				'wpcf7-redirect'
			),
			learnMoreLink:
				'https://docs.themeisle.com/article/2234-hubspot-integration',
		},
		{
			id: 'twilio-sms',
			title: __( 'Twilio SMS Notifications', 'wpcf7-redirect' ),
			description: __(
				'Send instant text notifications to admins or users when forms are submitted.',
				'wpcf7-redirect'
			),
			isPremium: true,
			icon: <PhoneForwarded size={ iconSize } />,
			badgeLabel: __(
				'Available in the Enterprise plan',
				'wpcf7-redirect'
			),
			learnMoreLink:
				'https://docs.themeisle.com/article/2221-using-twilio-addon-integrate-twilio-addon-to-send-sms-with-contact-form',
		},
	];

	const benefits = [
		{
			title: __( 'Enhanced Functionality', 'wpcf7-redirect' ),
			description: __(
				'Transform Contact Form 7 into a powerful lead generation and business automation tool with integrations to your favorite services.',
				'wpcf7-redirect'
			),
			icon: <Zap />,
			iconClass: 'variation-1',
		},
		{
			title: __( 'Priority Support', 'wpcf7-redirect' ),
			description: __(
				'Get fast, professional support from our team of experts who can help you implement even the most complex form workflows.',
				'wpcf7-redirect'
			),
			icon: <Users />,
			iconClass: 'variation-2',
		},
		{
			title: __( 'Better Conversions', 'wpcf7-redirect' ),
			description: __(
				'Create seamless user experiences that guide visitors through your sales funnel with conditional logic and integrations.',
				'wpcf7-redirect'
			),
			icon: <ChartNoAxesCombined />,
			iconClass: 'variation-3',
		},
	];

	return (
		<div className="rcf7-addons-tab">
			<div>
				<h2 className="rcf7-addon-section__title">
					{ __( 'Premium Features & Add-ons', 'wpcf7-redirect' ) }
				</h2>
				<p className="rcf7-addon-section__subtitle">
					{ __(
						'Extend your forms with our premium features to collect leads, accept payments, and integrate with external services.',
						'wpcf7-redirect'
					) }
				</p>
			</div>

			{ /* Featured Add-ons */ }
			<div className="rcf7-addon-section__spacing">
				<div className="rcf7-featured-addons-grid">
					{ featuredAddons.map( ( addon ) => (
						<FeaturedAddonCard key={ addon.id } { ...addon } />
					) ) }
				</div>
			</div>

			<div className="rcf7-addon-section__divider"></div>

			{ /* All Add-ons */ }
			<div className="rcf7-addon-section__spacing">
				<h2 className="rcf7-addon-section__title">
					{ __( 'All Premium Add-ons', 'wpcf7-redirect' ) }
				</h2>
				<div className="rcf7-addons">
					{ allAddons.map( ( addon ) => (
						<AddonCard key={ addon.id } { ...addon } />
					) ) }
				</div>
			</div>

			<div className="rcf7-addon-section__divider"></div>

			{ /* Why Upgrade */ }
			<div className="rcf7-addon-section__spacing">
				<h2 className="rcf7-addon-section__title">
					{ __( 'Why Upgrade to Premium?', 'wpcf7-redirect' ) }
				</h2>
				<div className="rcf7-benefits-grid">
					{ benefits.map( ( benefit, index ) => (
						<BenefitCard key={ index } benefit={ benefit } />
					) ) }
				</div>
			</div>

			<div className="rcf7-card">
				<CallToActionSection buttonHref={ data.links.upgrade } />
			</div>
		</div>
	);
};

export default AddonsTab;

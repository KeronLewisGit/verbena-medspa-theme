<?php
/**
 * Demo content for the fictional Verbena Aesthetics. Loaded only by the
 * importer. Business details, people and reviews are invented.
 *
 * @package Verbena
 */

defined( 'ABSPATH' ) || exit;

/**
 * Demo dataset.
 *
 * @return array
 */
function verbena_demo_data() {
	return array(
		'images'     => array(
			'hero-glow'               => 'Woman with eyes closed in soft daylight, skin glowing',
			'editorial-light'         => 'Close portrait with a band of warm light across the face',
			'treatment-neurotoxins'   => 'Gloved hand holding a fine syringe',
			'treatment-fillers'       => 'Provider in gloves preparing a lip filler treatment',
			'treatment-hydrafacial'   => 'Client receiving a hydrating facial with a handheld device',
			'treatment-laser'         => 'Laser hair removal handpiece on a leg',
			'treatment-peel'          => 'Esthetician brushing a peel solution onto a client\'s face',
			'treatment-microneedling' => 'Microneedling pen used on a client\'s cheek',
			'treatment-iv'            => 'IV drip line against a softly blurred window',
			'treatment-body'          => 'Smooth legs against a white background',
			'interior-hallway'        => 'Bright hallway with skincare products on open shelves',
			'interior-room'           => 'Calm treatment room with a padded bed and soft lamp',
			'product-dropper'         => 'Amber dropper bottle on a stone plinth',
			'texture-swatches'        => 'Swatches of gel, cream and serum textures on a beige surface',
			'facial-mask'             => 'Client relaxing during a facial with a cream mask',
			'serum-hand'              => 'Hand holding a small dark glass serum bottle',
			'team-elena'              => 'Portrait of Dr. Elena Marsh in a white coat',
			'team-amara'              => 'Portrait of Amara Okafor, RN, smiling',
			'team-lucia'              => 'Portrait of Lucía Ferrer outdoors',
		),

		'areas'      => array(
			'face'     => array( 'Face', 'Injectables, facials and skin resurfacing for the face and neck.' ),
			'body'     => array( 'Body', 'Laser hair removal and non-surgical body contouring.' ),
			'wellness' => array( 'Wellness', 'IV hydration and vitamin infusions.' ),
		),

		'options'    => array(
			'phone'                 => '(214) 555-0186',
			'email'                 => 'hello@verbena-aesthetics.example',
			'address_street'        => '3412 Routh Street, Suite 200',
			'address_city'          => 'Dallas',
			'address_region'        => 'TX',
			'address_postal'        => '75219',
			'geo_lat'               => '32.8069',
			'geo_lng'               => '-96.8007',
			'price_range'           => '$$$',
			'instagram_url'         => 'https://www.instagram.com/',
			'facebook_url'          => 'https://www.facebook.com/',
			'tiktok_url'            => '',
			'booking_provider'      => 'demo',
			'booking_url'           => '',
			'booking_service_param' => 'serviceId',
			'gtm_id'                => '',
			'ga4_id'                => '',
			'gf_form_id'            => '',
			'lead_email'            => '',
			'hours'                 => array(
				array(
					'label'  => 'Tuesday to Friday',
					'days'   => 'Tu,We,Th,Fr',
					'opens'  => '10:00',
					'closes' => '19:00',
				),
				array(
					'label'  => 'Saturday',
					'days'   => 'Sa',
					'opens'  => '09:00',
					'closes' => '16:00',
				),
				array(
					'label'  => 'Sunday and Monday',
					'days'   => '',
					'opens'  => '',
					'closes' => '',
				),
			),
			'team'                  => array(
				array(
					'name'  => 'Dr. Elena Marsh, MD',
					'role'  => 'Medical Director',
					'bio'   => 'Elena oversees every treatment protocol at Verbena and reviews each new client plan. She prefers small, staged changes over dramatic ones.',
					'photo' => 'team-elena',
				),
				array(
					'name'  => 'Amara Okafor, RN',
					'role'  => 'Aesthetic Nurse Injector',
					'bio'   => 'Amara leads our injectables program. Clients book her for careful, conservative neurotoxin and filler work and honest advice about what to skip.',
					'photo' => 'team-amara',
				),
				array(
					'name'  => 'Lucía Ferrer, LE',
					'role'  => 'Licensed Esthetician',
					'bio'   => 'Lucía runs facials, peels and microneedling, and builds the at-home routines that keep results going between visits.',
					'photo' => 'team-lucia',
				),
			),
		),

		'front_page' => array(
			'home_hero_eyebrow'  => 'Medical aesthetics in Uptown Dallas',
			'home_hero_title'    => 'Skin care with a clinician\'s eye and a lighter touch.',
			'home_hero_text'     => 'Injectables, facials, laser and wellness treatments, planned with you by licensed providers. Natural results, clear pricing, no pressure.',
			'home_hero_image'    => 'hero-glow',
			'home_intro_heading' => 'Small, considered changes. Planned properly.',
			'home_intro_text'    => "Verbena is a physician-led medspa on Routh Street. We start every relationship with a consultation, not a sales pitch, and we will tell you when a treatment is not worth your money.\n\nMost clients come to us for one thing and stay for a plan: a few treatments spaced through the year, and a home routine that does the quiet work in between.",
			'home_intro_image'   => 'interior-hallway',
			'home_steps'         => array(
				array(
					'title' => 'Consult',
					'text'  => 'Thirty minutes with a provider to talk through goals, history and budget. Complimentary, and there is no obligation to book.',
				),
				array(
					'title' => 'Plan',
					'text'  => 'You leave with a written plan: which treatments, in what order, what they cost and what downtime to expect.',
				),
				array(
					'title' => 'Treat',
					'text'  => 'Appointments run on time in private rooms. Your provider checks in after every treatment and adjusts the plan as you go.',
				),
			),
			'home_testimonials'  => array(
				array(
					'quote'     => 'Amara talked me out of the filler I came in for and into a treatment plan that actually suited my face. I have never had a provider do that.',
					'name'      => 'Danielle R.',
					'treatment' => 'Neurotoxin',
				),
				array(
					'quote'     => 'The HydraFacial membership is the one appointment I never move. My skin has not looked this even in years.',
					'name'      => 'Priya S.',
					'treatment' => 'HydraFacial',
				),
				array(
					'quote'     => 'Clear prices on the website, an honest consult and zero upsell. It is exactly what I wanted from a medspa.',
					'name'      => 'Marcus T.',
					'treatment' => 'Laser hair removal',
				),
			),
		),

		'pages'      => array(
			'treatments'  => array(
				'title'    => 'Treatments',
				'template' => '',
				'content'  => '',
				'fields'   => array(
					'hero_eyebrow'    => 'Treatment menu',
					'hero_intro'      => 'Eight treatments, each with a starting price, appointment length and downtime listed up front. Filter by area, or book a consultation and we will build the plan with you.',
					'hero_image'      => 'texture-swatches',
					'seo_title'       => 'Medspa Treatments in Dallas | Verbena Aesthetics',
					'seo_description' => 'Neurotoxins, dermal fillers, HydraFacial, laser hair removal, chemical peels, microneedling, IV therapy and body contouring in Uptown Dallas.',
				),
			),
			'about'       => array(
				'title'    => 'About',
				'template' => 'templates/template-about.php',
				'content'  => '<h2>A medspa run like a clinic</h2><p>Verbena opened on Routh Street with a simple idea: aesthetic treatments deserve the same care as any other medical appointment. That means a full consultation before anything is booked, written treatment plans, and providers who are licensed for exactly what they do.</p><p>We keep the menu short on purpose. Every treatment we offer is one our team performs every week, with equipment we maintain in-house and products we would use ourselves.</p><h2>What you can expect</h2><ul><li>A complimentary consultation with a provider, not a sales coordinator.</li><li>Prices published on the website and confirmed in writing before treatment.</li><li>Follow-up after every injectable or resurfacing treatment.</li><li>Private treatment rooms and appointments that start on time.</li></ul>',
				'fields'   => array(
					'hero_eyebrow'    => 'About Verbena',
					'hero_intro'      => 'Physician-led, appointment-only and deliberately unhurried. Here is how we work and who you will see.',
					'hero_image'      => 'interior-room',
					'seo_title'       => 'About Verbena Aesthetics | Physician-Led Medspa, Dallas',
					'seo_description' => 'Meet the providers at Verbena Aesthetics, a physician-led medspa in Uptown Dallas offering consult-first injectables, facials and laser treatments.',
				),
			),
			'memberships' => array(
				'title'    => 'Memberships',
				'template' => 'templates/template-memberships.php',
				'content'  => '<h2>How memberships work</h2><p>Memberships are a monthly credit toward the treatments you already get, plus member pricing on everything else. Unused monthly treatments roll over for 60 days, and you can pause for up to two months a year.</p><p>There is no long contract: after the first three months you can cancel with 30 days\' notice. Not sure which tier fits? Your provider will recommend one at your consultation, or tell you to skip it.</p>',
				'fields'   => array(
					'hero_eyebrow'     => 'Memberships & pricing',
					'hero_intro'       => 'Monthly plans for clients who treat regularly. Member pricing on every treatment, rollover credit and no long-term contract.',
					'hero_image'       => 'product-dropper',
					'seo_title'        => 'Medspa Memberships & Pricing | Verbena Aesthetics Dallas',
					'seo_description'  => 'Compare Verbena memberships: monthly facials, neurotoxin credit and member pricing on treatments. Cancel with 30 days notice after three months.',
					'membership_tiers' => array(
						array(
							'name'     => 'Essentials',
							'price'    => 129,
							'summary'  => 'For a steady skin routine.',
							'perks'    => "One signature facial or peel each month\n10% off all other treatments\n10% off skincare\nCredit rolls over for 60 days",
							'featured' => false,
						),
						array(
							'name'     => 'Signature',
							'price'    => 229,
							'summary'  => 'Our most chosen plan.',
							'perks'    => "One HydraFacial or microneedling session each month\n15% off injectables and laser\n15% off skincare\nPriority booking on Saturdays\nCredit rolls over for 60 days",
							'featured' => true,
						),
						array(
							'name'     => 'Verbena Circle',
							'price'    => 389,
							'summary'  => 'For a full-year treatment plan.',
							'perks'    => "Monthly facial plus quarterly neurotoxin credit\n20% off injectables, laser and body\n20% off skincare and IV therapy\nAnnual skin review with Dr. Marsh\nCredit rolls over for 90 days",
							'featured' => false,
						),
					),
					'membership_terms' => 'Prices are illustrative for this demo site. Memberships renew monthly, can be paused for up to two months a year, and can be cancelled with 30 days\' notice after the first three billing cycles. Credits have no cash value.',
				),
			),
			'book'        => array(
				'title'    => 'Book a Consultation',
				'template' => 'templates/template-contact.php',
				'content'  => '<p>Pick a time in the calendar below. New clients start with a complimentary consultation; returning clients can book treatments directly.</p>[verbena_booking]',
				'fields'   => array(
					'hero_eyebrow'    => 'Book & contact',
					'hero_intro'      => 'Book online in a couple of minutes, or send a consultation request and a provider will call you back within one business day.',
					'hero_image'      => 'facial-mask',
					'seo_title'       => 'Book a Consultation | Verbena Aesthetics, Uptown Dallas',
					'seo_description' => 'Book a complimentary consultation or treatment at Verbena Aesthetics, 3412 Routh Street, Dallas. Open Tuesday to Saturday.',
				),
			),
			'faq'         => array(
				'title'    => 'FAQ',
				'template' => 'templates/template-faq.php',
				'content'  => '',
				'fields'   => array(
					'hero_eyebrow'    => 'Questions',
					'hero_intro'      => 'Booking, pricing, consultations and what to expect on the day. Treatment-specific questions are on each treatment page.',
					'hero_image'      => 'serum-hand',
					'seo_title'       => 'Medspa FAQ | Verbena Aesthetics Dallas',
					'seo_description' => 'Answers to common questions about consultations, pricing, cancellations, payment and first visits at Verbena Aesthetics in Dallas.',
					'page_faqs'       => array(
						array(
							'question' => 'Is the consultation really free?',
							'answer'   => 'Yes. New-client consultations are complimentary and last about 30 minutes. If you decide to go ahead the same day and there is time, we can usually treat you then.',
						),
						array(
							'question' => 'Who performs the treatments?',
							'answer'   => 'Injectables are performed by our nurse injector under the supervision of our medical director. Facials, peels and microneedling are performed by licensed estheticians. Everyone is licensed for the treatments they provide.',
						),
						array(
							'question' => 'How do I prepare for my first visit?',
							'answer'   => 'Arrive with a clean face if you can, bring a list of medications and supplements, and allow ten minutes for intake forms. We will send everything you need by email after you book.',
						),
						array(
							'question' => 'What is your cancellation policy?',
							'answer'   => 'Please give us 24 hours\' notice to cancel or reschedule. Late cancellations and missed appointments may be charged a fee of up to 50% of the booked treatment.',
						),
						array(
							'question' => 'Do you offer financing?',
							'answer'   => 'Yes. We accept major cards, HSA and FSA cards where eligible, and offer monthly payment plans through a third-party financing partner, subject to approval.',
						),
						array(
							'question' => 'Can I combine treatments in one visit?',
							'answer'   => 'Often, yes. Neurotoxin and a HydraFacial, for example, are commonly done together. Your provider will tell you which combinations are appropriate and in what order.',
						),
						array(
							'question' => 'Is there parking?',
							'answer'   => 'There is free validated parking in the garage behind the building, entered from Cedar Springs Road. Street parking on Routh Street is metered.',
						),
					),
				),
			),
			'privacy'     => array(
				'title'    => 'Privacy Policy',
				'template' => '',
				'content'  => '<p><strong>Demo notice:</strong> Verbena Aesthetics is a fictional business created to demonstrate a custom WordPress theme. No real personal information is collected or processed by this demo.</p><h2>What we collect</h2><p>When you send a consultation request we collect your name, email address, phone number (optional), the treatment you are interested in and any message you write. This is used only to respond to your request.</p><h2>Analytics</h2><p>If analytics are enabled, we use Google Tag Manager and Google Analytics 4 to understand how visitors use the site, such as which treatments are viewed and which booking buttons are clicked. No analytics tags load unless the site owner has configured them.</p><h2>Booking</h2><p>Online booking is provided by a third-party booking platform. The booking calendar only loads when you choose to open it, and it is governed by that provider\'s privacy policy.</p><h2>Your choices</h2><p>You can ask us to access, correct or delete your information by emailing hello@verbena-aesthetics.example.</p>',
				'fields'   => array(
					'hero_eyebrow'    => 'Legal',
					'hero_intro'      => 'How this site handles the information you send us.',
					'hero_image'      => '',
					'seo_title'       => 'Privacy Policy | Verbena Aesthetics',
					'seo_description' => 'How Verbena Aesthetics handles consultation requests, analytics and online booking data.',
				),
			),
		),

		'treatments' => array(
			array(
				'slug'    => 'neurotoxins',
				'title'   => 'Botox & Neurotoxins',
				'area'    => 'face',
				'image'   => 'treatment-neurotoxins',
				'excerpt' => 'Softens expression lines on the forehead, between the brows and around the eyes with small, precise doses.',
				'fields'  => array(
					'summary'            => 'Softens expression lines on the forehead, between the brows and around the eyes with small, precise doses.',
					'price_from'         => 13,
					'price_unit'         => 'per unit',
					'duration'           => '20 to 30 minutes',
					'downtime'           => 'None; avoid exercise for 24 hours',
					'areas_treated'      => 'Forehead, frown lines, crow\'s feet, lip flip, jawline',
					'sessions'           => 'Every 3 to 4 months',
					'booking_service_id' => 'svc-neuro-01',
					'before_after_notes' => "Before: avoid alcohol, fish oil and aspirin-type painkillers for 48 hours if your doctor agrees, to reduce bruising.\n\nAfter: stay upright for four hours, skip the gym and facials for 24 hours, and do not massage the treated areas. Results build over 7 to 14 days; we book a two-week check-in for first-time clients.",
					'seo_title'          => 'Botox & Neurotoxin Injections in Dallas | Verbena Aesthetics',
					'seo_description'    => 'Botox, Dysport and Xeomin from $13 per unit in Uptown Dallas. Conservative dosing by a nurse injector, with a two-week check-in included.',
					'treatment_faqs'     => array(
						array(
							'question' => 'Which neurotoxin brands do you use?',
							'answer'   => 'We stock Botox, Dysport and Xeomin. They work in a similar way; your injector will recommend one based on the area and your past results.',
						),
						array(
							'question' => 'How many units will I need?',
							'answer'   => 'It depends on the area and muscle strength. Frown lines often take 15 to 25 units of Botox. You will get an estimate at your consultation before anything is injected.',
						),
						array(
							'question' => 'Will I look frozen?',
							'answer'   => 'Our default is a light dose that softens lines while keeping natural movement. It is easier to add a little at your check-in than to wait for too much to wear off.',
						),
					),
				),
				'content' => '<h2>What it is</h2><p>Neurotoxins are purified proteins that relax the specific muscles that crease the skin when you frown, raise your brows or squint. Softening that movement softens the lines it causes, and regular treatment can help keep new lines from setting in.</p><h2>What to expect</h2><p>Your injector maps the muscles while you make a few expressions, then places a small number of quick injections with a very fine needle. Most clients describe it as a brief pinch. You can go straight back to work.</p><h3>Good candidates</h3><p>Adults who want to soften dynamic lines (the ones that appear with expression). It is not suitable during pregnancy or breastfeeding or with certain neuromuscular conditions; we review your medical history first.</p>',
			),
			array(
				'slug'    => 'dermal-fillers',
				'title'   => 'Dermal Fillers',
				'area'    => 'face',
				'image'   => 'treatment-fillers',
				'excerpt' => 'Hyaluronic acid fillers to restore volume in the cheeks, lips and jawline, placed with a conservative hand.',
				'fields'  => array(
					'summary'            => 'Hyaluronic acid fillers to restore volume in the cheeks, lips and jawline, placed with a conservative hand.',
					'price_from'         => 695,
					'price_unit'         => 'per syringe',
					'duration'           => '45 to 60 minutes',
					'downtime'           => '1 to 3 days of possible swelling',
					'areas_treated'      => 'Lips, cheeks, chin, jawline, under-eyes',
					'sessions'           => 'Lasts roughly 6 to 18 months',
					'booking_service_id' => 'svc-filler-01',
					'before_after_notes' => "Before: avoid blood-thinning supplements for 48 hours where your doctor agrees, and do not book filler within two weeks of a big event.\n\nAfter: expect some swelling, especially in the lips, for one to three days. Avoid heavy exercise, saunas and pressure on the area for 24 hours. We review every filler treatment at two weeks.",
					'seo_title'          => 'Dermal Fillers in Dallas: Lips, Cheeks, Jawline | Verbena',
					'seo_description'    => 'Hyaluronic acid dermal fillers from $695 per syringe in Uptown Dallas. Natural-looking lip, cheek and jawline treatments with a two-week review.',
					'treatment_faqs'     => array(
						array(
							'question' => 'Can filler be reversed?',
							'answer'   => 'Hyaluronic acid fillers can be dissolved with an enzyme if needed, which is one reason we only use HA fillers.',
						),
						array(
							'question' => 'How long does filler last?',
							'answer'   => 'Typically 6 to 18 months depending on the product, the area and your metabolism. Lips tend to be at the shorter end.',
						),
						array(
							'question' => 'Does it hurt?',
							'answer'   => 'Most fillers contain a local anesthetic and we can apply numbing cream first. Most clients rate it as mildly uncomfortable.',
						),
					),
				),
				'content' => '<h2>What it is</h2><p>Dermal fillers are gels made from hyaluronic acid, a sugar your skin already produces. Placed beneath the skin, they restore volume that changes with age, support structure along the cheeks and jaw, or add shape and hydration to the lips.</p><h2>What to expect</h2><p>After numbing, your injector places small amounts of filler with a fine needle or a blunt cannula, checking symmetry as they go. You will see a change straight away, and the final result settles in about two weeks once swelling has gone down.</p><h3>Our approach</h3><p>We would rather under-fill and top up than overdo it. Your plan may spread treatment across two appointments so you can get used to the change.</p>',
			),
			array(
				'slug'    => 'hydrafacial',
				'title'   => 'HydraFacial',
				'area'    => 'face',
				'image'   => 'treatment-hydrafacial',
				'excerpt' => 'A deep-cleansing, hydrating facial that suits nearly every skin type and leaves no downtime.',
				'fields'  => array(
					'summary'            => 'A deep-cleansing, hydrating facial that suits nearly every skin type and leaves no downtime.',
					'price_from'         => 199,
					'price_unit'         => 'per session',
					'duration'           => '45 to 75 minutes',
					'downtime'           => 'None',
					'areas_treated'      => 'Face, neck, décolletage',
					'sessions'           => 'Monthly for best upkeep',
					'booking_service_id' => 'svc-hydra-01',
					'before_after_notes' => "Before: pause retinoids and exfoliating acids for 48 hours, and avoid waxing the face for a week.\n\nAfter: skin is ready for makeup the same day, but it is a good evening to keep it bare. Use SPF daily and keep up hydration at home for the glow to last.",
					'seo_title'          => 'HydraFacial in Dallas | Verbena Aesthetics Uptown',
					'seo_description'    => 'HydraFacial from $199 in Uptown Dallas. Deep cleansing, extraction and hydration with no downtime. Monthly membership available.',
					'treatment_faqs'     => array(
						array(
							'question' => 'Is HydraFacial good for sensitive skin?',
							'answer'   => 'Usually, yes. The serums can be adjusted, and your esthetician will patch-test if you have a history of reactions.',
						),
						array(
							'question' => 'Can I have it before an event?',
							'answer'   => 'It is one of the best pre-event treatments because there is no downtime. Two to five days before is ideal.',
						),
					),
				),
				'content' => '<h2>What it is</h2><p>HydraFacial uses a handheld device to cleanse, exfoliate and extract pores with gentle suction, then infuse hydrating and antioxidant serums. It is a reliable way to improve how the skin looks and feels in a single visit.</p><h2>What to expect</h2><p>The treatment feels cool and slightly tingly. Your esthetician can add boosters for brightening or congestion and finish with LED light therapy. You leave with fresh skin and no redness for most skin types.</p>',
			),
			array(
				'slug'    => 'laser-hair-removal',
				'title'   => 'Laser Hair Removal',
				'area'    => 'body',
				'image'   => 'treatment-laser',
				'excerpt' => 'Long-term hair reduction for face and body using a laser suitable for a wide range of skin tones.',
				'fields'  => array(
					'summary'            => 'Long-term hair reduction for face and body using a laser suitable for a wide range of skin tones.',
					'price_from'         => 95,
					'price_unit'         => 'per small area',
					'duration'           => '15 to 60 minutes',
					'downtime'           => 'Mild redness for a few hours',
					'areas_treated'      => 'Underarms, bikini, legs, face, back',
					'sessions'           => '6 to 8 sessions, 4 to 8 weeks apart',
					'booking_service_id' => 'svc-laser-01',
					'before_after_notes' => "Before: shave the area the day before, and avoid waxing, plucking and sun tanning for four weeks.\n\nAfter: skip hot showers, saunas and exercise for 24 hours, and use SPF on treated areas. Treated hairs shed over two to three weeks.",
					'seo_title'          => 'Laser Hair Removal in Dallas | Verbena Aesthetics',
					'seo_description'    => 'Laser hair removal from $95 per area in Uptown Dallas, using a laser suited to many skin tones. Package pricing for 6 sessions.',
					'treatment_faqs'     => array(
						array(
							'question' => 'Is it permanent?',
							'answer'   => 'Laser hair removal gives long-term reduction. Most clients see a large reduction after a full course, with occasional maintenance sessions.',
						),
						array(
							'question' => 'Does it work on darker skin?',
							'answer'   => 'Our laser is designed for a wide range of skin tones. We do a test patch first and adjust settings to your skin.',
						),
					),
				),
				'content' => '<h2>What it is</h2><p>The laser targets pigment in the hair follicle, heating it enough to slow future growth while leaving the surrounding skin protected by a cooling tip. Because hair grows in cycles, a series of sessions is needed to catch each follicle in its active phase.</p><h2>What to expect</h2><p>Each pulse feels like a quick snap with a cooling sensation. Small areas take minutes; full legs take about an hour. We patch-test first and set the laser to your skin tone and hair type.</p>',
			),
			array(
				'slug'    => 'chemical-peels',
				'title'   => 'Chemical Peels',
				'area'    => 'face',
				'image'   => 'treatment-peel',
				'excerpt' => 'Professional-strength peels to brighten dullness, even tone and refine texture, from gentle to deeper resurfacing.',
				'fields'  => array(
					'summary'            => 'Professional-strength peels to brighten dullness, even tone and refine texture, from gentle to deeper resurfacing.',
					'price_from'         => 150,
					'price_unit'         => 'per peel',
					'duration'           => '30 to 45 minutes',
					'downtime'           => '0 to 5 days of flaking',
					'areas_treated'      => 'Face, neck, hands, back',
					'sessions'           => 'Series of 3, every 4 weeks',
					'booking_service_id' => 'svc-peel-01',
					'before_after_notes' => "Before: stop retinoids and exfoliants five days ahead, and avoid sun exposure. Tell us about any history of cold sores.\n\nAfter: let skin flake naturally, moisturise often and wear SPF 30 or higher every day. Avoid other exfoliating treatments until fully healed.",
					'seo_title'          => 'Chemical Peels in Dallas | Verbena Aesthetics',
					'seo_description'    => 'Chemical peels from $150 in Uptown Dallas for dullness, uneven tone and texture. Light to medium-depth options chosen by a licensed esthetician.',
					'treatment_faqs'     => array(
						array(
							'question' => 'Will I peel a lot?',
							'answer'   => 'It depends on the peel. Light peels may cause no visible flaking; medium peels can flake for several days. We will tell you which to expect.',
						),
						array(
							'question' => 'Can I peel in summer?',
							'answer'   => 'Yes, with careful sun protection. We often choose lighter peels in the summer months.',
						),
					),
				),
				'content' => '<h2>What it is</h2><p>A chemical peel applies a controlled exfoliating solution to remove the outer layer of dull, uneven skin and encourage fresh cell turnover. We use a range of strengths, from gentle lactic acid peels to deeper blends for pigmentation and texture.</p><h2>What to expect</h2><p>After cleansing, the peel is brushed on in layers. You will feel warmth or tingling for a few minutes before it is neutralised or left to work. Your esthetician sends you home with the exact aftercare for your peel.</p>',
			),
			array(
				'slug'    => 'microneedling',
				'title'   => 'Microneedling',
				'area'    => 'face',
				'image'   => 'treatment-microneedling',
				'excerpt' => 'Collagen induction therapy that improves the look of texture, fine lines and acne scarring over a short series.',
				'fields'  => array(
					'summary'            => 'Collagen induction therapy that improves the look of texture, fine lines and acne scarring over a short series.',
					'price_from'         => 325,
					'price_unit'         => 'per session',
					'duration'           => '60 minutes',
					'downtime'           => '1 to 3 days of redness',
					'areas_treated'      => 'Face, neck, décolletage, scars',
					'sessions'           => '3 to 4 sessions, 4 to 6 weeks apart',
					'booking_service_id' => 'svc-micro-01',
					'before_after_notes' => "Before: no retinoids for five days and no sunburn or active breakouts in the area.\n\nAfter: skin feels like a mild sunburn for a day or two. Use only the gentle products we give you for 48 hours, no makeup for 24 hours, and strict SPF for two weeks.",
					'seo_title'          => 'Microneedling in Dallas | Verbena Aesthetics',
					'seo_description'    => 'Microneedling from $325 per session in Uptown Dallas to improve texture, fine lines and the look of acne scars. Short downtime.',
					'treatment_faqs'     => array(
						array(
							'question' => 'Does microneedling hurt?',
							'answer'   => 'We apply numbing cream for 30 minutes first. Most clients feel light pressure and vibration.',
						),
						array(
							'question' => 'When will I see results?',
							'answer'   => 'Skin looks fresher within a week, but collagen builds gradually; most clients notice the biggest change a month after their final session.',
						),
					),
				),
				'content' => '<h2>What it is</h2><p>Microneedling uses a pen-like device with fine sterile needles to create controlled micro-channels in the skin. The skin\'s natural repair response stimulates collagen, which can improve the look of texture, pores, fine lines and some acne scarring over a series of treatments.</p><h2>What to expect</h2><p>After numbing, the device glides across the skin in passes, with a hydrating serum applied throughout. The session takes about an hour including numbing. Expect pink skin, like a mild sunburn, for a day or two.</p>',
			),
			array(
				'slug'    => 'iv-therapy',
				'title'   => 'IV Therapy',
				'area'    => 'wellness',
				'image'   => 'treatment-iv',
				'excerpt' => 'Hydration and vitamin infusions administered by a registered nurse in a quiet private lounge.',
				'fields'  => array(
					'summary'            => 'Hydration and vitamin infusions administered by a registered nurse in a quiet private lounge.',
					'price_from'         => 165,
					'price_unit'         => 'per infusion',
					'duration'           => '45 to 60 minutes',
					'downtime'           => 'None',
					'areas_treated'      => 'Whole body (wellness)',
					'sessions'           => 'As needed, or monthly',
					'booking_service_id' => 'svc-iv-01',
					'before_after_notes' => "Before: eat a light meal and drink water beforehand. Wear a top with sleeves that roll up easily.\n\nAfter: you can return to normal activities straight away. Keep the small dressing on for an hour.",
					'seo_title'          => 'IV Hydration & Vitamin Therapy in Dallas | Verbena',
					'seo_description'    => 'IV hydration and vitamin infusions from $165 in Uptown Dallas, given by a registered nurse after a medical screening.',
					'treatment_faqs'     => array(
						array(
							'question' => 'Do I need a medical screening?',
							'answer'   => 'Yes. A nurse reviews your health history and takes vitals before every first infusion, and some infusions require approval from our medical director.',
						),
						array(
							'question' => 'What is in the drips?',
							'answer'   => 'Saline or lactated Ringer\'s for hydration, with options such as B-complex vitamins, vitamin C and electrolytes. The full ingredient list for each drip is available at the clinic.',
						),
					),
				),
				'content' => '<h2>What it is</h2><p>IV therapy delivers fluids, electrolytes and vitamins directly into the bloodstream through a small catheter. Clients use it for hydration after travel or exercise, or as part of a general wellness routine.</p><h2>What to expect</h2><p>A registered nurse screens your health history, takes your vitals and places the IV. You relax in a reclining chair for 45 to 60 minutes. IV therapy is not a substitute for medical care; please see your physician for any health concern.</p>',
			),
			array(
				'slug'    => 'body-contouring',
				'title'   => 'Body Contouring',
				'area'    => 'body',
				'image'   => 'treatment-body',
				'excerpt' => 'Non-surgical radiofrequency contouring to firm and smooth stubborn areas, with no needles and no downtime.',
				'fields'  => array(
					'summary'            => 'Non-surgical radiofrequency contouring to firm and smooth stubborn areas, with no needles and no downtime.',
					'price_from'         => 350,
					'price_unit'         => 'per session',
					'duration'           => '45 to 60 minutes',
					'downtime'           => 'None',
					'areas_treated'      => 'Abdomen, flanks, thighs, arms',
					'sessions'           => '4 to 6 sessions, weekly',
					'booking_service_id' => 'svc-body-01',
					'before_after_notes' => "Before: drink plenty of water the day before and the day of treatment, and remove jewellery near the area.\n\nAfter: mild warmth and redness fade within hours. Stay hydrated and keep active; changes develop gradually over the following weeks.",
					'seo_title'          => 'Non-Surgical Body Contouring in Dallas | Verbena',
					'seo_description'    => 'Radiofrequency body contouring from $350 per session in Uptown Dallas. No needles, no downtime; a plan of 4 to 6 weekly sessions.',
					'treatment_faqs'     => array(
						array(
							'question' => 'Is this a weight-loss treatment?',
							'answer'   => 'No. Body contouring is for shaping and firming specific areas in people who are already close to their goal weight.',
						),
						array(
							'question' => 'What does it feel like?',
							'answer'   => 'Like a warm stone massage. Most clients read or rest during the session.',
						),
					),
				),
				'content' => '<h2>What it is</h2><p>Our body contouring treatment uses radiofrequency energy to gently heat the deeper layers of skin and tissue. Over a course of sessions this can help firm skin and improve the look of contour in areas that do not respond to diet and exercise.</p><h2>What to expect</h2><p>A provider moves a warm handpiece across the area while monitoring skin temperature. There are no needles and no recovery time. We take measurements and photos at the start and end of your plan so you can judge the change for yourself.</p>',
			),
		),

		'redirects'  => array(
			array( '/services/botox/', '/treatments/neurotoxins/', 301 ),
			array( '/book-online/', '/book/', 302 ),
			array( '/specials/', '/memberships/', 301 ),
		),
	);
}

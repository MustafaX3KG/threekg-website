<?php
/**
 * Elementor editing layer for ThreeKG Core.
 *
 * Registers a `threekg` widget category and eight custom widgets that
 * render the canonical templates in templates/ — the same markup the
 * native ACF blocks use. Requires Elementor (Free); Elementor Pro is
 * optional for theme-builder templates.
 *
 * @package ThreeKG_Core
 */

defined( 'ABSPATH' ) || exit;

add_action( 'plugins_loaded', 'threekg_core_elementor_init', 20 );
/**
 * Defines the widget layer after Elementor has loaded its classes.
 */
function threekg_core_elementor_init(): void {
	if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
		return; // no Elementor: native ACF blocks and canonical defaults keep working.
	}

	/* ------------------------------------------------------------------
	 * Shared base: canonical category, keywords and template rendering.
	 * --------------------------------------------------------------- */
	abstract class ThreeKG_Widget_Base extends \Elementor\Widget_Base {

		public function get_categories(): array {
			return array( 'threekg' );
		}

		public function get_keywords(): array {
			return array( 'threekg', 'studio', 'section', 'landing' );
		}

		/**
		 * Renders a canonical ThreeKG section template with the widget values.
		 *
		 * @param string $slug   Template slug (templates/{$slug}.php).
		 * @param array  $values Raw widget settings keyed by control name.
		 */
		protected function render_threekg_template( string $slug, array $values ): void {
			$file = THREEKG_CORE_PATH . 'templates/' . sanitize_file_name( $slug ) . '.php';
			if ( ! is_readable( $file ) ) {
				return;
			}
			// $values is consumed directly by the template; provide a stable
			// instance id shape so templates keep working.
			$block = array( 'id' => uniqid( $slug . '-' ) );
			include $file; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile -- fixed plugin path
		}

		/**
		 * Adds a text control with a canonical default.
		 */
		protected function add_tkg_text( string $id, string $label, string $default, bool $textarea = false ): void {
			$this->add_control(
				$id,
				array(
					'label'       => $label,
					'type'        => $textarea ? \Elementor\Controls_Manager::TEXTAREA : \Elementor\Controls_Manager::TEXT,
					'default'     => $default,
					'placeholder' => $default,
				)
			);
		}
	}

	/* ------------------------------------------------------------------
	 * Hero
	 * --------------------------------------------------------------- */
	class ThreeKG_Hero_Widget extends ThreeKG_Widget_Base {

		public function get_name(): string { return 'threekg-hero'; }
		public function get_title(): string { return __( 'ThreeKG Hero', 'threekg-core' ); }
		public function get_icon(): string { return 'eicon-banner'; }
		public function get_keywords(): array { return array_merge( parent::get_keywords(), array( 'hero', 'kicker' ) ); }

		protected function register_controls(): void {
			$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'threekg-core' ) ) );

			$this->add_tkg_text( 'kicker', __( 'Kicker', 'threekg-core' ), 'ThreeKG — independent design studio, est. 2021' );
			$this->add_tkg_text( 'line_1', __( 'Headline line 1', 'threekg-core' ), 'We build quiet,' );
			$this->add_tkg_text( 'line_2', __( 'Headline line 2', 'threekg-core' ), 'confident work' );
			$this->add_tkg_text( 'line_3', __( 'Headline line 3 (soft)', 'threekg-core' ), 'for brands that whisper' );
			$this->add_tkg_text( 'accent', __( 'Accent word', 'threekg-core' ), 'loudly.' );
			$this->add_tkg_text( 'scroll_label', __( 'Scroll hint', 'threekg-core' ), 'scroll to explore ↓' );
			$this->add_tkg_text( 'location_label', __( 'Location label', 'threekg-core' ), threekg_core_option( 'location', 'working worldwide' ) );
			$this->add_control(
				'background_image',
				array( 'label' => __( 'Background image', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::MEDIA )
			);

			$this->end_controls_section();
		}

		protected function render(): void {
			$values = array();
			foreach ( array( 'kicker', 'line_1', 'line_2', 'line_3', 'accent', 'scroll_label', 'location_label' ) as $key ) {
				$values[ $key ] = $this->get_settings_for_display( $key );
			}
			$values['background_image'] = $this->get_settings_for_display( 'background_image' );
			$this->render_threekg_template( 'hero', $values );
		}
	}

	/* ------------------------------------------------------------------
	 * Showreel
	 * --------------------------------------------------------------- */
	class ThreeKG_Showreel_Widget extends ThreeKG_Widget_Base {

		public function get_name(): string { return 'threekg-showreel'; }
		public function get_title(): string { return __( 'ThreeKG Showreel', 'threekg-core' ); }
		public function get_icon(): string { return 'eicon-video-playlist'; }
		public function get_keywords(): array { return array_merge( parent::get_keywords(), array( 'showreel', 'video' ) ); }

		protected function register_controls(): void {
			$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'threekg-core' ) ) );

			$this->add_control(
				'video',
				array( 'label' => __( 'Video', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::MEDIA, 'description' => __( 'Empty uses the bundled assets/showreel.mp4.', 'threekg-core' ) )
			);
			$this->add_control(
				'poster',
				array( 'label' => __( 'Poster image', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::MEDIA )
			);
			$this->add_tkg_text( 'year', __( 'Year', 'threekg-core' ), gmdate( 'Y' ) );
			$this->add_tkg_text( 'duration', __( 'Duration', 'threekg-core' ), '0:10' );
			$this->add_tkg_text( 'caption', __( 'Caption', 'threekg-core' ), 'reel placeholder' );
			$this->add_tkg_text( 'play_label', __( 'Play label', 'threekg-core' ), 'play reel' );

			$this->end_controls_section();
		}

		protected function render(): void {
			$this->render_threekg_template(
				'showreel',
				array(
					'video'      => $this->get_settings_for_display( 'video' ),
					'poster'     => $this->get_settings_for_display( 'poster' ),
					'year'       => $this->get_settings_for_display( 'year' ),
					'duration'   => $this->get_settings_for_display( 'duration' ),
					'caption'    => $this->get_settings_for_display( 'caption' ),
					'play_label' => $this->get_settings_for_display( 'play_label' ),
				)
			);
		}
	}

	/* ------------------------------------------------------------------
	 * Marquee
	 * --------------------------------------------------------------- */
	class ThreeKG_Marquee_Widget extends ThreeKG_Widget_Base {

		public function get_name(): string { return 'threekg-marquee'; }
		public function get_title(): string { return __( 'ThreeKG Marquee', 'threekg-core' ); }
		public function get_icon(): string { return 'eicon-slider-push'; }
		public function get_keywords(): array { return array_merge( parent::get_keywords(), array( 'marquee', 'services' ) ); }

		protected function register_controls(): void {
			$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'threekg-core' ) ) );

			$repeater = new \Elementor\Repeater();
			$repeater->add_control( 'text', array( 'label' => __( 'Item', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ) );
			$this->add_control(
				'items',
				array(
					'label'       => __( 'Marquee items', 'threekg-core' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'title_field' => '{{{ text }}}',
					'default'     => array(
						array( 'text' => 'Brand Identity' ),
						array( 'text' => 'Web Design' ),
						array( 'text' => 'Creative Development' ),
						array( 'text' => 'Art Direction' ),
						array( 'text' => 'Motion' ),
					),
				)
			);

			$this->end_controls_section();
		}

		protected function render(): void {
			$this->render_threekg_template( 'marquee', array( 'items' => $this->get_settings_for_display( 'items' ) ) );
		}
	}

	/* ------------------------------------------------------------------
	 * Featured Work
	 * --------------------------------------------------------------- */
	class ThreeKG_Featured_Work_Widget extends ThreeKG_Widget_Base {

		public function get_name(): string { return 'threekg-featured-work'; }
		public function get_title(): string { return __( 'ThreeKG Featured Work', 'threekg-core' ); }
		public function get_icon(): string { return 'eicon-gallery-grid'; }
		public function get_keywords(): array { return array_merge( parent::get_keywords(), array( 'work', 'projects' ) ); }

		protected function register_controls(): void {
			$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'threekg-core' ) ) );

			$this->add_tkg_text( 'eyebrow', __( 'Eyebrow', 'threekg-core' ), '[03 — featured]' );
			$this->add_tkg_text( 'year_range', __( 'Year range', 'threekg-core' ), '2019 → ' . gmdate( 'Y' ) );
			$this->add_tkg_text( 'heading', __( 'Heading', 'threekg-core' ), 'Featured work.' );

			$repeater = new \Elementor\Repeater();
			$repeater->add_control( 'title', array( 'label' => __( 'Project title', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ) );
			$repeater->add_control( 'tag', array( 'label' => __( 'Tag / sector', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ) );
			$repeater->add_control(
				'layout',
				array(
					'label'   => __( 'Layout', 'threekg-core' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array( 'full' => 'Full', 'right' => 'Right', 'left' => 'Left', 'side' => 'Side' ),
					'default' => 'full',
				)
			);
			$repeater->add_control(
				'url',
				array( 'label' => __( 'Link', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::URL, 'default' => array( 'url' => '#' ) )
			);
			$repeater->add_control(
				'image',
				array( 'label' => __( 'Image', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::MEDIA )
			);
			$repeater->add_control( 'alt', array( 'label' => __( 'Alt text', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ) );

			$this->add_control(
				'cards',
				array(
					'label'       => __( 'Project cards', 'threekg-core' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'title_field' => '{{{ title }}}',
					'default'     => array(),
				)
			);

			$this->end_controls_section();
		}

		protected function render(): void {
			$s        = $this->get_settings_for_display();
			$s['url'] = ( is_array( $s['url'] ?? null ) ) ? ( $s['url']['url'] ?? '' ) : '';
			$this->render_threekg_template(
				'featured-work',
				array(
					'eyebrow'    => $s['eyebrow'] ?? '',
					'year_range' => $s['year_range'] ?? '',
					'heading'    => $s['heading'] ?? '',
					'cards'      => $s['cards'] ?? array(),
				)
			);
		}
	}

	/* ------------------------------------------------------------------
	 * Clients
	 * --------------------------------------------------------------- */
	class ThreeKG_Clients_Widget extends ThreeKG_Widget_Base {

		public function get_name(): string { return 'threekg-clients'; }
		public function get_title(): string { return __( 'ThreeKG Clients', 'threekg-core' ); }
		public function get_icon(): string { return 'eicon-logo'; }
		public function get_keywords(): array { return array_merge( parent::get_keywords(), array( 'clients', 'logos' ) ); }

		protected function register_controls(): void {
			$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'threekg-core' ) ) );

			$this->add_tkg_text( 'year', __( 'Year', 'threekg-core' ), (string) ( (int) gmdate( 'Y' ) - 1 ) );
			$this->add_tkg_text( 'heading', __( 'Heading', 'threekg-core' ), 'Clients we helped' );

			$repeater = new \Elementor\Repeater();
			$repeater->add_control( 'name', array( 'label' => __( 'Client name', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ) );
			$repeater->add_control( 'sector', array( 'label' => __( 'Sector', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ) );
			$repeater->add_control(
				'logo',
				array( 'label' => __( 'Logo', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::MEDIA )
			);
			$repeater->add_control( 'logo_alt', array( 'label' => __( 'Logo alt', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ) );

			$this->add_control(
				'clients',
				array(
					'label'       => __( 'Client tiles', 'threekg-core' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'title_field' => '{{{ name }}}',
					'default'     => array(),
				)
			);

			$this->end_controls_section();
		}

		protected function render(): void {
			$this->render_threekg_template(
				'clients',
				array(
					'year'    => $this->get_settings_for_display( 'year' ),
					'heading' => $this->get_settings_for_display( 'heading' ),
					'clients' => $this->get_settings_for_display( 'clients' ),
				)
			);
		}
	}

	/* ------------------------------------------------------------------
	 * Studio
	 * --------------------------------------------------------------- */
	class ThreeKG_Studio_Widget extends ThreeKG_Widget_Base {

		public function get_name(): string { return 'threekg-studio'; }
		public function get_title(): string { return __( 'ThreeKG Studio', 'threekg-core' ); }
		public function get_icon(): string { return 'eicon-post-content'; }
		public function get_keywords(): array { return array_merge( parent::get_keywords(), array( 'studio', 'about' ) ); }

		protected function register_controls(): void {
			$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'threekg-core' ) ) );

			$this->add_tkg_text( 'heading', __( 'Heading', 'threekg-core' ), "Enough range to win\nthe brief. Small enough\nto care about it.", true );
			$this->add_tkg_text( 'lead', __( 'Lead paragraph', 'threekg-core' ), 'ThreeKG is an independent design practice working with founders and cultural brands who would rather show than explain.', true );
			$this->add_tkg_text( 'body', __( 'Body paragraph', 'threekg-core' ), 'We take on a handful of projects at a time so the people you brief are the people doing the work.', true );
			$this->add_control(
				'image',
				array( 'label' => __( 'Image', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::MEDIA )
			);

			$repeater = new \Elementor\Repeater();
			$repeater->add_control( 'value', array( 'label' => __( 'Number', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ) );
			$repeater->add_control( 'label', array( 'label' => __( 'Caption', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ) );

			$this->add_control(
				'stats',
				array(
					'label'   => __( 'Statistics', 'threekg-core' ),
					'type'    => \Elementor\Controls_Manager::REPEATER,
					'fields'  => $repeater->get_controls(),
					'default' => array(
						array( 'value' => '48', 'label' => 'projects shipped' ),
						array( 'value' => '30', 'label' => 'clients worldwide' ),
						array( 'value' => '11', 'label' => 'industry awards' ),
						array( 'value' => '05', 'label' => 'years in practice' ),
					),
				)
			);

			$this->end_controls_section();
		}

		protected function render(): void {
			$this->render_threekg_template(
				'studio',
				array(
					'heading' => $this->get_settings_for_display( 'heading' ),
					'lead'    => $this->get_settings_for_display( 'lead' ),
					'body'    => $this->get_settings_for_display( 'body' ),
					'image'   => $this->get_settings_for_display( 'image' ),
					'stats'   => $this->get_settings_for_display( 'stats' ),
				)
			);
		}
	}

	/* ------------------------------------------------------------------
	 * Services Story
	 * --------------------------------------------------------------- */
	class ThreeKG_Services_Story_Widget extends ThreeKG_Widget_Base {

		public function get_name(): string { return 'threekg-services-story'; }
		public function get_title(): string { return __( 'ThreeKG Services Story', 'threekg-core' ); }
		public function get_icon(): string { return 'eicon-scroll'; }
		public function get_keywords(): array { return array_merge( parent::get_keywords(), array( 'services', 'morph' ) ); }

		protected function register_controls(): void {
			$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'threekg-core' ) ) );

			$this->add_tkg_text( 'heading', __( 'Heading', 'threekg-core' ), 'Six ways in.' );

			$repeater = new \Elementor\Repeater();
			$repeater->add_control( 'name', array( 'label' => __( 'Service name', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ) );
			$repeater->add_control( 'hint', array( 'label' => __( 'Hint', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ) );
			$repeater->add_control(
				'symbol_key',
				array(
					'label'       => __( 'SVG symbol key', 'threekg-core' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'options'     => array(
						'brand'      => 'brand',
						'design'     => 'design',
						'studio'     => 'studio',
						'campaigns'  => 'campaigns',
						'production' => 'production',
						'xperience'  => 'xperience',
					),
					'description' => __( 'Exactly six rows, in this order — the SVG geometry maps by index.', 'threekg-core' ),
				)
			);

			$this->add_control(
				'services',
				array(
					'label'       => __( 'Services (six)', 'threekg-core' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'title_field' => '{{{ name }}}',
					'default'     => array(
						array( 'name' => 'Brand identity', 'hint' => 'naming · systems · guidelines', 'symbol_key' => 'brand' ),
						array( 'name' => 'Web design', 'hint' => 'strategy · UX · art direction', 'symbol_key' => 'design' ),
						array( 'name' => 'Creative development', 'hint' => 'canvas · motion · interaction', 'symbol_key' => 'studio' ),
						array( 'name' => 'Art direction', 'hint' => 'campaigns · editorial · image', 'symbol_key' => 'campaigns' ),
						array( 'name' => 'Production', 'hint' => 'film · shoots · content', 'symbol_key' => 'production' ),
						array( 'name' => 'Motion & interaction', 'hint' => 'micro-interactions · video', 'symbol_key' => 'xperience' ),
					),
				)
			);

			$this->end_controls_section();
		}

		protected function render(): void {
			$this->render_threekg_template(
				'services-story',
				array(
					'heading'  => $this->get_settings_for_display( 'heading' ),
					'services' => $this->get_settings_for_display( 'services' ),
				)
			);
		}
	}

	/* ------------------------------------------------------------------
	 * Contact CTA
	 * --------------------------------------------------------------- */
	class ThreeKG_Contact_CTA_Widget extends ThreeKG_Widget_Base {

		public function get_name(): string { return 'threekg-contact-cta'; }
		public function get_title(): string { return __( 'ThreeKG Contact CTA', 'threekg-core' ); }
		public function get_icon(): string { return 'eicon-mail'; }
		public function get_keywords(): array { return array_merge( parent::get_keywords(), array( 'contact', 'get in touch' ) ); }

		protected function register_controls(): void {
			$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'threekg-core' ) ) );

			$this->add_control(
				'override_global',
				array( 'label' => __( 'Use custom CTA (instead of global settings)', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => '' )
			);
			$this->add_tkg_text( 'label', __( 'Eyebrow', 'threekg-core' ), 'get in touch' );
			$this->add_tkg_text( 'cta_text', __( 'CTA text', 'threekg-core' ), 'Start a project' );
			$this->add_control(
				'cta_link',
				array( 'label' => __( 'CTA link', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::URL, 'default' => array( 'url' => '' ), 'description' => __( 'Empty falls back to the global e-mail address.', 'threekg-core' ) )
			);

			$repeater = new \Elementor\Repeater();
			$repeater->add_control( 'heading', array( 'label' => __( 'Column heading', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ) );
			$repeater->add_control( 'text', array( 'label' => __( 'Column text', 'threekg-core' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => '' ) );

			$this->add_control(
				'columns',
				array(
					'label'       => __( 'Footer columns', 'threekg-core' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'title_field' => '{{{ heading }}}',
					'default'     => array(),
				)
			);

			$this->end_controls_section();
		}

		protected function render(): void {
			$s       = $this->get_settings_for_display();
			$linkUrl = ( is_array( $s['cta_link'] ?? null ) ) ? ( $s['cta_link']['url'] ?? '' ) : '';
			$this->render_threekg_template(
				'contact-cta',
				array(
					'override_global' => ! empty( $s['override_global'] ),
					'label'           => $s['label'] ?? '',
					'cta_text'        => $s['cta_text'] ?? '',
					'cta_link'        => $linkUrl,
					'columns'         => $s['columns'] ?? array(),
				)
			);
		}
	}

	/* ------------------------------------------------------------------
	 * Hook registration
	 * --------------------------------------------------------------- */
	add_action(
		'elementor/elements/categories_registered',
		function ( $categories_manager ): void {
			$categories_manager->add_category(
				'threekg',
				array(
					'title' => __( 'ThreeKG', 'threekg-core' ),
					'icon'  => 'eicon-polaroid',
				)
			);
		}
	);

	add_action(
		'elementor/widgets/register',
		function ( $widgets_manager ): void {
			foreach ( array(
				'ThreeKG_Hero_Widget',
				'ThreeKG_Showreel_Widget',
				'ThreeKG_Marquee_Widget',
				'ThreeKG_Featured_Work_Widget',
				'ThreeKG_Clients_Widget',
				'ThreeKG_Studio_Widget',
				'ThreeKG_Services_Story_Widget',
				'ThreeKG_Contact_CTA_Widget',
			) as $widget_class ) {
				if ( class_exists( $widget_class ) ) {
					$widgets_manager->register( new $widget_class() );
				}
			}
		}
	);

	/* Theme + animation assets for any Elementor-rendered ThreeKG section. */
	add_action(
		'elementor/frontend/after_enqueue_styles',
		function (): void {
			if ( ! wp_style_is( 'threekg-main', 'enqueued' ) ) {
				wp_enqueue_style( 'threekg-fonts', 'https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,400;0,500;0,600;1,400&family=IBM+Plex+Mono:wght@400;500&display=swap', array(), null );
				wp_enqueue_style( 'threekg-main', threekg_core_theme_asset( 'assets/css/main.css' ), array( 'threekg-fonts' ), null );
			}
		}
	);

	add_action(
		'elementor/frontend/after_enqueue_scripts',
		function (): void {
			if ( wp_script_is( 'threekg-main', 'enqueued' ) ) {
				return; // already loaded by the native enqueue layer.
			}
			foreach ( array( 'main', 'hero-canvas', 'services-morph', 'showreel' ) as $module ) {
				wp_enqueue_script( "threekg-{$module}", threekg_core_theme_asset( "assets/js/{$module}.js" ), array(), null, true );
			}
		}
	);
} // threekg_core_elementor_init

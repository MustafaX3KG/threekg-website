<?php
/**
 * Plugin Name: ThreeKG Elementor Sections
 * Description: Editable Elementor sections for the ThreeKG studio website.
 * Version: 1.0.0
 * Author: ThreeKG
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('elementor/elements/categories_registered', function ($manager) {
    $manager->add_category('threekg', array(
        'title' => __('ThreeKG Sections', 'threekg-elementor'),
        'icon'  => 'eicon-site-identity',
    ));
});

add_action('elementor/widgets/register', function ($widgets_manager) {
    if (!class_exists('Elementor\\Widget_Base')) {
        return;
    }

    abstract class ThreeKG_Widget_Base extends \Elementor\Widget_Base {
        public function get_categories() { return array('threekg'); }
        public function get_icon() { return 'eicon-site-identity'; }
        public function get_style_depends() { return array('threekg-revamp'); }
        public function get_script_depends() { return array('threekg-revamp'); }

        protected function text_control($id, $label, $default = '', $type = null) {
            $this->add_control($id, array(
                'label' => $label,
                'type' => $type ?: \Elementor\Controls_Manager::TEXT,
                'default' => $default,
                'label_block' => true,
            ));
        }
    }

    class ThreeKG_Hero_Widget extends ThreeKG_Widget_Base {
        public function get_name() { return 'threekg_hero'; }
        public function get_title() { return __('3KG — Header & Hero', 'threekg-elementor'); }
        protected function register_controls() {
            $this->start_controls_section('content', array('label' => __('Content', 'threekg-elementor')));
            $this->text_control('brand', __('Brand', 'threekg-elementor'), 'ThreeKG');
            $this->text_control('availability', __('Availability label', 'threekg-elementor'), 'Available for projects');
            $this->text_control('kicker', __('Intro label', 'threekg-elementor'), '(ThreeKG — independent design studio, est. 2021)');
            $this->text_control('line_1', __('Headline line 1', 'threekg-elementor'), 'We build quiet,');
            $this->text_control('line_2', __('Headline line 2', 'threekg-elementor'), 'confident work');
            $this->text_control('line_3', __('Headline line 3', 'threekg-elementor'), 'for brands that whisper');
            $this->text_control('accent', __('Italic accent', 'threekg-elementor'), 'loudly.');
            $this->text_control('email', __('Email', 'threekg-elementor'), 'hello@threekg.com');
            $this->end_controls_section();
        }
        protected function render() {
            $s = $this->get_settings_for_display();
            $theme = get_template_directory_uri();
            ?>
            <div class="loader" id="loader" aria-hidden="true"><video class="loader__video" id="loaderVideo" src="<?php echo esc_url($theme . '/assets/brand/motion/loader.mp4'); ?>" autoplay muted playsinline></video><span class="mono loader__skip">click to skip</span></div>
            <div class="cursor" aria-hidden="true"></div>
            <div class="menu" id="menu" aria-hidden="true"><div class="menu__inner"><span class="mono menu__cap">(navigation)</span><nav class="menu__nav">
              <a href="#top"><span class="mono">01</span> Index</a><a href="#showreel"><span class="mono">02</span> Showreel</a><a href="#work"><span class="mono">03</span> Work</a><a href="#clients"><span class="mono">04</span> Clients</a><a href="#studio"><span class="mono">05</span> Studio</a><a href="#contact"><span class="mono">06</span> Contact</a>
            </nav><div class="menu__foot"><a href="mailto:<?php echo esc_attr($s['email']); ?>"><?php echo esc_html($s['email']); ?></a></div></div></div>
            <header class="site-header"><a class="brand" href="#top"><?php echo esc_html($s['brand']); ?><span class="brand__reg">®</span></a><div class="mono site-header__meta"><span class="avail"><i class="avail__dot"></i><?php echo esc_html($s['availability']); ?></span><span class="hide-sm">minimalism · space · motion</span><span id="clock">--:--:--</span><span id="tz"></span></div><button class="menu-toggle mono" id="menuToggle" aria-controls="menu" aria-expanded="false">menu</button></header>
            <section class="hero" id="top"><canvas class="hero__canvas" id="field" aria-hidden="true"></canvas><div class="hero__inner"><p class="mono hero__kicker reveal"><?php echo esc_html($s['kicker']); ?></p><h1 class="hero__title"><span class="reveal"><?php echo esc_html($s['line_1']); ?></span><span class="reveal"><?php echo esc_html($s['line_2']); ?></span><span class="reveal hero__title--soft"><?php echo esc_html($s['line_3']); ?><em>&nbsp;<?php echo esc_html($s['accent']); ?></em></span></h1><div class="hero__foot"><span class="mono">scroll to explore ↓</span><span class="mono hide-sm">working worldwide</span><span class="mono hide-sm">[01/07]</span></div></div></section>
            <?php
        }
    }

    class ThreeKG_Showreel_Widget extends ThreeKG_Widget_Base {
        public function get_name() { return 'threekg_showreel'; }
        public function get_title() { return __('3KG — Showreel', 'threekg-elementor'); }
        protected function register_controls() {
            $this->start_controls_section('content', array('label' => __('Showreel', 'threekg-elementor')));
            $this->add_control('video', array('label' => __('Video', 'threekg-elementor'), 'type' => \Elementor\Controls_Manager::MEDIA, 'media_types' => array('video')));
            $this->text_control('year', __('Year', 'threekg-elementor'), '2026');
            $this->text_control('duration', __('Duration label', 'threekg-elementor'), '0:10 — showreel');
            $this->text_control('marquee', __('Marquee items (separate with |)', 'threekg-elementor'), 'Brand Identity|Web Design|Creative Development|Art Direction|Motion');
            $this->end_controls_section();
        }
        protected function render() {
            $s = $this->get_settings_for_display();
            $video = !empty($s['video']['url']) ? $s['video']['url'] : get_template_directory_uri() . '/assets/showreel.mp4';
            $items = array_filter(array_map('trim', explode('|', $s['marquee'])));
            ?>
            <section class="showreel" id="showreel"><div class="showreel__frame reveal" tabindex="0" role="button"><video class="showreel__video" id="showreelVideo" src="<?php echo esc_url($video); ?>" muted loop playsinline preload="metadata"></video><div class="showreel__scrim"></div><div class="showreel__overlay"><span class="mono showreel__cap">[02 — showreel] <?php echo esc_html($s['year']); ?></span><button class="showreel__play" id="showreelPlay"><span class="showreel__play-icon">▶</span><span class="mono">play reel</span></button><span class="mono showreel__dur hide-sm"><?php echo esc_html($s['duration']); ?></span></div></div></section>
            <section class="marquee" aria-hidden="true"><div class="marquee__track"><?php for ($set = 0; $set < 2; $set++) : ?><span class="marquee__set"><?php foreach ($items as $item) : ?><span><?php echo esc_html($item); ?></span><i>✳</i><?php endforeach; ?></span><?php endfor; ?></div></section>
            <?php
        }
    }

    class ThreeKG_Work_Widget extends ThreeKG_Widget_Base {
        public function get_name() { return 'threekg_work'; }
        public function get_title() { return __('3KG — Projects', 'threekg-elementor'); }
        protected function register_controls() {
            $this->start_controls_section('content', array('label' => __('Projects section', 'threekg-elementor')));
            $this->text_control('eyebrow', __('Section label', 'threekg-elementor'), '[03 — featured]');
            $this->text_control('title', __('Title', 'threekg-elementor'), 'Featured work.');
            $this->add_control('count', array('label' => __('Number of projects', 'threekg-elementor'), 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 6, 'min' => 1, 'max' => 12));
            $this->end_controls_section();
        }
        protected function render() {
            $s = $this->get_settings_for_display();
            $title = preg_split('/\s+/', trim($s['title']), 2);
            $q = new WP_Query(array('post_type' => 'project', 'post_status' => 'publish', 'posts_per_page' => (int) $s['count'], 'orderby' => array('menu_order' => 'ASC', 'date' => 'DESC')));
            ?>
            <section class="work" id="work"><div class="section-head reveal"><span class="mono"><?php echo esc_html($s['eyebrow']); ?></span><span class="mono">2019 → <?php echo esc_html(gmdate('Y')); ?></span><div class="work__titlerow"><h2><?php echo esc_html($title[0]); ?><br><em><?php echo esc_html($title[1] ?? 'work.'); ?></em></h2><button type="button" class="symbol-btn work__symbol" data-category="selected-work"><svg viewBox="-145 -145 290 290" aria-hidden="true"></svg></button></div></div><div class="work__editorial">
            <?php if ($q->have_posts()) : $i = 0; while ($q->have_posts()) : $q->the_post(); $id = get_the_ID(); $client = get_post_meta($id, '_3kg_project_client', true) ?: get_the_title(); $image = get_the_post_thumbnail_url($id, 'full') ?: threekg_fallback_image($i); $terms = get_the_terms($id, 'project_sector'); $tag = ($terms && !is_wp_error($terms)) ? $terms[0]->name : get_the_excerpt(); ?>
              <a class="work-block <?php echo esc_attr(threekg_archive_columns($i)); ?> reveal" href="<?php the_permalink(); ?>"><span class="work-block__media"><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($client); ?>"></span><span class="work-block__caption"><span class="work-block__title"><?php echo esc_html($client); ?></span><span class="work-block__tag mono"><?php echo esc_html($tag); ?></span></span></a>
            <?php $i++; endwhile; wp_reset_postdata(); else : foreach (array('Halcyon Hotels','Meridian Motion','North Light Archive','Onto & Co.','Studio Ferro','Vellum Editions') as $i => $name) : ?>
              <a class="work-block <?php echo esc_attr(threekg_archive_columns($i)); ?> reveal" href="#"><span class="work-block__media"><img src="<?php echo esc_url(threekg_fallback_image($i)); ?>" alt="<?php echo esc_attr($name); ?>"></span><span class="work-block__caption"><span class="work-block__title"><?php echo esc_html($name); ?></span><span class="work-block__tag mono">project placeholder</span></span></a>
            <?php endforeach; endif; ?>
            </div><div class="work__foot reveal"><span class="mono">Projects are managed from WordPress → Projects</span><a class="mono" href="<?php echo esc_url(get_post_type_archive_link('project') ?: '#'); ?>">view all projects ↗</a></div></section>
            <?php
        }
    }

    class ThreeKG_Clients_Widget extends ThreeKG_Widget_Base {
        public function get_name() { return 'threekg_clients'; }
        public function get_title() { return __('3KG — Clients', 'threekg-elementor'); }
        protected function register_controls() {
            $this->start_controls_section('content', array('label' => __('Clients', 'threekg-elementor')));
            $this->text_control('title', __('Title', 'threekg-elementor'), 'Clients we helped in 2025.');
            $repeater = new \Elementor\Repeater();
            $repeater->add_control('name', array('label' => __('Client name', 'threekg-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Client'));
            $repeater->add_control('tag', array('label' => __('Industry', 'threekg-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'industry'));
            $defaults = array(); foreach (array('Halcyon','Meridian','Ferro','North Light','Onto & Co.','Vellum') as $name) { $defaults[] = array('name' => $name, 'tag' => 'creative partner'); }
            $this->add_control('clients', array('label' => __('Client list', 'threekg-elementor'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => $defaults, 'title_field' => '{{{ name }}}'));
            $this->end_controls_section();
        }
        protected function render() { $s = $this->get_settings_for_display(); ?>
          <section class="clients" id="clients"><div class="section-head reveal"><span class="mono">[04 — clients]</span><span class="mono"><?php echo esc_html(gmdate('Y')); ?></span><h2><?php echo wp_kses_post(nl2br(esc_html($s['title']))); ?></h2></div><ul class="clients__grid"><?php foreach ($s['clients'] as $client) : ?><li class="clients__tile reveal"><span class="clients__name"><?php echo esc_html($client['name']); ?></span><span class="mono clients__tag"><?php echo esc_html($client['tag']); ?></span></li><?php endforeach; ?></ul></section>
        <?php }
    }

    class ThreeKG_Studio_Widget extends ThreeKG_Widget_Base {
        public function get_name() { return 'threekg_studio'; }
        public function get_title() { return __('3KG — Studio', 'threekg-elementor'); }
        protected function register_controls() {
            $this->start_controls_section('content', array('label' => __('Studio', 'threekg-elementor')));
            $this->text_control('title', __('Title', 'threekg-elementor'), 'Enough range to win the brief. Small enough to care about it.', \Elementor\Controls_Manager::TEXTAREA);
            $this->text_control('lead', __('Lead paragraph', 'threekg-elementor'), "ThreeKG is an independent design practice working with founders and cultural brands who'd rather show than explain.", \Elementor\Controls_Manager::TEXTAREA);
            $this->text_control('body', __('Second paragraph', 'threekg-elementor'), 'We take on a handful of projects at a time so the people you brief are the people doing the work.', \Elementor\Controls_Manager::TEXTAREA);
            $repeater = new \Elementor\Repeater();
            $repeater->add_control('number', array('label' => __('Number', 'threekg-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '48'));
            $repeater->add_control('label', array('label' => __('Label', 'threekg-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'projects shipped'));
            $this->add_control('stats', array('label' => __('Statistics', 'threekg-elementor'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => array(array('number'=>'48','label'=>'projects shipped'),array('number'=>'30','label'=>'clients worldwide'),array('number'=>'11','label'=>'industry awards'),array('number'=>'05','label'=>'years in practice')), 'title_field' => '{{{ number }}} — {{{ label }}}'));
            $this->end_controls_section();
        }
        protected function render() { $s = $this->get_settings_for_display(); ?>
          <section class="studio" id="studio"><div class="section-head reveal"><span class="mono">[05 — the studio]</span><span class="mono">est. 2021</span><h2><?php echo esc_html($s['title']); ?></h2></div><div class="studio__grid"><p class="reveal studio__lead"><?php echo esc_html($s['lead']); ?></p><p class="reveal studio__body"><?php echo esc_html($s['body']); ?></p></div><ul class="studio__stats reveal"><?php foreach ($s['stats'] as $stat) : ?><li><span class="mono stat__num"><?php echo esc_html($stat['number']); ?></span><span class="mono stat__cap"><?php echo esc_html($stat['label']); ?></span></li><?php endforeach; ?></ul></section>
        <?php }
    }

    class ThreeKG_Services_Widget extends ThreeKG_Widget_Base {
        public function get_name() { return 'threekg_services'; }
        public function get_title() { return __('3KG — Services Motion', 'threekg-elementor'); }
        protected function register_controls() {
            $this->start_controls_section('content', array('label' => __('Services', 'threekg-elementor')));
            $this->text_control('title', __('Title', 'threekg-elementor'), 'Six ways in.');
            $repeater = new \Elementor\Repeater();
            $repeater->add_control('name', array('label' => __('Service', 'threekg-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Service'));
            $repeater->add_control('hint', array('label' => __('Details', 'threekg-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'strategy · design · delivery'));
            $defaults = array(); foreach (array(array('Brand identity','naming · systems · guidelines'),array('Web design','strategy · UX · art direction'),array('Creative development','canvas · motion · interaction'),array('Art direction','campaigns · editorial · image'),array('Production','film · shoots · content'),array('Motion & interaction','micro-interactions · video')) as $row) { $defaults[] = array('name'=>$row[0],'hint'=>$row[1]); }
            $this->add_control('services', array('label' => __('Services (maximum six)', 'threekg-elementor'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => $defaults, 'title_field' => '{{{ name }}}'));
            $this->end_controls_section();
        }
        protected function render() { $s = $this->get_settings_for_display(); $keys = array('brand','design','studio','campaigns','production','xperience'); $services = array_slice($s['services'], 0, 6); ?>
          <section class="services" id="services"><div class="services__inner"><div class="services__stage"><div class="section-head reveal"><span class="mono">[06 — what we do]</span><span class="mono">capabilities</span><h2><?php echo esc_html($s['title']); ?></h2></div><div class="services__motion reveal" aria-live="polite"><div class="services__morph" aria-hidden="true"><svg class="services__morph-svg" viewBox="-120 -120 240 240" role="presentation"></svg></div><div class="services__motion-meta mono"><span class="services__motion-index">00 / 06</span><span class="services__motion-label">scroll to explore</span></div></div></div><ul class="services__list"><?php foreach ($services as $i => $service) : ?><li class="svc-card services__card" data-service="<?php echo esc_attr($keys[$i]); ?>"><span class="mono services__number"><?php echo esc_html(sprintf('%02d', $i + 1)); ?></span><h3 class="svc-card__name"><?php echo esc_html($service['name']); ?></h3><p class="mono svc-card__hint"><?php echo esc_html($service['hint']); ?></p></li><?php endforeach; ?></ul></div></section>
        <?php }
    }

    class ThreeKG_Contact_Widget extends ThreeKG_Widget_Base {
        public function get_name() { return 'threekg_contact'; }
        public function get_title() { return __('3KG — Contact Footer', 'threekg-elementor'); }
        protected function register_controls() {
            $this->start_controls_section('content', array('label' => __('Contact', 'threekg-elementor')));
            $this->text_control('cta', __('Call to action', 'threekg-elementor'), 'Start a project');
            $this->text_control('email', __('Email', 'threekg-elementor'), 'hello@threekg.com');
            foreach (array('instagram'=>'Instagram','linkedin'=>'LinkedIn','behance'=>'Behance') as $id=>$label) { $this->add_control($id, array('label'=>$label, 'type'=>\Elementor\Controls_Manager::URL, 'placeholder'=>'https://')); }
            $this->end_controls_section();
        }
        protected function render() { $s = $this->get_settings_for_display(); ?>
          <footer class="footer" id="contact"><span class="mono footer__cap reveal">(get in touch)</span><a class="footer__cta reveal" href="mailto:<?php echo esc_attr($s['email']); ?>"><?php echo esc_html($s['cta']); ?><span class="footer__arrow">→</span></a><div class="footer__grid"><div class="mono footer__col"><span class="footer__label">studio</span><span>ThreeKG®</span><span>working worldwide</span><span id="clockFooter">--:--:--</span></div><div class="mono footer__col"><span class="footer__label">contact</span><a href="mailto:<?php echo esc_attr($s['email']); ?>"><?php echo esc_html($s['email']); ?></a><?php foreach (array('instagram'=>'Instagram','linkedin'=>'LinkedIn','behance'=>'Behance') as $id=>$label) : if (!empty($s[$id]['url'])) : ?><a href="<?php echo esc_url($s[$id]['url']); ?>" rel="noopener"><?php echo esc_html($label); ?> ↗</a><?php endif; endforeach; ?></div><div class="mono footer__col"><span class="footer__label">site</span><a href="#top">back to top ↑</a><span>© 2021–<?php echo esc_html(gmdate('Y')); ?> ThreeKG</span><span>made with quiet intent</span></div></div></footer>
        <?php }
    }

    foreach (array(new ThreeKG_Hero_Widget(), new ThreeKG_Showreel_Widget(), new ThreeKG_Work_Widget(), new ThreeKG_Clients_Widget(), new ThreeKG_Studio_Widget(), new ThreeKG_Services_Widget(), new ThreeKG_Contact_Widget()) as $widget) {
        $widgets_manager->register($widget);
    }
});


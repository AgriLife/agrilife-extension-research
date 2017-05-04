<?php
namespace AgriLife\Extension;

class RequiredDOM {

	public function __construct() {

		// Alter header title
        add_filter( 'genesis_seo_title', array( $this, 'logo_title' ), 9, 3 );

        // Remove Site Description
        remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

        // Add Extension Body Class
        add_filter( 'body_class', array( $this, 'ext_body_class' ) );

        // Add Page Slug to Body Class
        add_filter( 'body_class', array( $this, 'slug_body_class' ) );

        // Modify Header Text
        add_filter( 'genesis_seo_description', array( $this, 'filter_tagline' ) );

        // Modify Primary Navigation
        add_filter( 'genesis_do_nav', array( $this, 'custom_nav_class' ), 12, 5 );

        // Render the Top Image
        add_action( 'genesis_after_header', array( $this, 'top_image' ), 11 );

        // Render the footer
        add_action( 'genesis_header', array( $this, 'add_extension_footer_content' ) );

	}

    /**
     * Adds logos to the header title
     *
     * @param $title The title text
     * @param $inside
     * @param $wrap
     *
     * @return string
     */
    public function logo_title( $title, $inside, $wrap ) {

        // Replace h1 with div for SEO
        $wrap = 'div';

        // Add extension logo
        $content = sprintf( '<a href="%s" title="%s"><span>%s</span></a>',
            esc_attr( get_bloginfo('url') ),
            esc_attr( get_bloginfo('name') ),
            get_bloginfo( 'name' ) );

        // Add other logos
        $content = sprintf( '<a href="%s" class="%s-logo" title="%s"><span>%s</span></a>', 'http://agriliferesearch.tamu.edu/', 'research-extension', 'Research and Extension', 'Research and Extension' );

        // Combine logos
        $title = sprintf( '<%s class="site-title" itemprop="headline">%s</%s>',
            $wrap,
            $content,
            $wrap
        );

        $title .= sprintf( '<%s class="site-description" itemprop="description"><span class="site-unit-name">%s</span></%s>',
            $wrap,
            get_bloginfo('name'),
            $wrap
        );

        return $title;
    }

    /**
     * Reformats the tagline
     *
     * @param $title The title text
     * @param $inside
     * @param $wrap
     *
     * @return string
     */
    public function filter_tagline( $title, $inside='', $wrap='' ) {

        return '';

    }

    /**
     * Add and Extension body class
     *
     * @param $classes The existing body classes
     *
     * @return string
     */
    public function ext_body_class( $classes ) {

        $classes[] = 'extension-research-site';
        return $classes;

    }

    /**
     * Add page slug and category to body class
     *
     * @param $classes The existing body classes
     *
     * @return string
     */
    public function slug_body_class( $classes ) {

        global $post;

        if ( isset( $post ) ) {
            $classes[] = $post->post_type . '-' . $post->post_name;

            $parent = get_page($post->post_parent);
            $classes[] = $parent->post_type . '-parent-' . $parent->post_name;
        }

        return $classes;

    }

    /**
     * Ensure primary navigation menu is right-aligned
     *
     * @param $nav_output The raw menu HTML
     *
     * @return string
     */
    public function custom_nav_class( $nav_output, $nav, $args ) {

        preg_match_all('/<ul[^>]+>/', $nav_output, $uls);

        foreach ($uls[0] as $value) {

            if( preg_match( '/\bmenu-primary\b/', $value ) === 1 ){

                $newvalue = preg_replace('/\bleft\b/', 'right', $value);
                $nav_output = str_replace( $value, $newvalue, $nav_output );
                break;

            }

        }

        return $nav_output;

    }

    /**
     * Add top image custom field to page
     * @since 1.0
     * @return void
     */
    public function top_image(){

        if( get_field('wideimage') ){

            $image = get_field( 'wideimage' );

            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

            if( $protocol == 'https://' && strpos($image['url'], 'http://') == 0 ){
                $image['url'] = str_replace( 'http://', 'https://', $image['url'] );
            }

            $output = sprintf( '<div class="top-image"><img src="%s" alt="%s"></div>',
                $image['url'],
                $image['alt']
            );

            echo $output;

        }

    }

    /**
     * Add extension info to bottom of page
     * @since 1.0
     * @return void
     */
    public function add_extension_footer_content()
    {
        remove_all_actions('genesis_footer');
        add_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
        add_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );

        add_action('genesis_footer', array($this, 'render_footer_widgets'));
        add_action('genesis_footer', array($this, 'render_required_links'));
        add_action('genesis_footer', array($this, 'render_tamus_logo'));
    }

	/**
	 * Render the widgets in the footer
	 * @since 1.0
	 * @return void
	 */
	public function render_footer_widgets() {

        if ( is_active_sidebar( 'footer-left' ) ) : ?>
            <div id="footer-left-widgets" class="footer-left widget-area" role="complementary">
                <?php dynamic_sidebar( 'footer-left' ); ?>
            </div><!-- #footer-center-widgets -->
        <?php endif;

	}

    /**
     * Render required links
     * @todo refactor this, repeated functionality
     * @since 1.0
     * @return string
     */
    public static function render_required_links()
    {

        $output = '
            <div class="footer-container-required">
                <ul class="req-links">
                    <li><a href="http://agrilife.org/required-links/compact/">Compact with Texans</a></li>
                    <li><a href="http://agrilife.org/required-links/privacy/">Privacy and Security</a></li>
                    <li><a href="http://itaccessibility.tamu.edu/" target="_blank">Accessibility Policy</a></li>
                    <li><a href="http://publishingext.dir.texas.gov/portal/internal/resources/DocumentLibrary/State%20Website%20Linking%20and%20Privacy%20Policy.pdf" target="_blank">State Link Policy</a></li>
                    <li><a href="http://www.tsl.state.tx.us/trail" target="_blank">Statewide Search</a></li>
                    <li><a href="http://www.tamus.edu/veterans/" target="_blank">Veterans Benefits</a></li>
                    <li><a href="http://fcs.tamu.edu/families/military_families/" target="_blank">Military Families</a></li>
                    <li><a href="https://secure.ethicspoint.com/domain/en/report_custom.asp?clientid=19681" target="_blank">Risk, Fraud &amp; Misconduct Hotline</a></li>
                    <li><a href="http://www.texashomelandsecurity.com/" target="_blank">Texas Homeland Security</a></li>
                    <li><a href="http://veterans.portal.texas.gov/">Texas Veteran&apos;s Portal</a></li>
                    <li><a href="http://agrilifeas.tamu.edu/hr/diversity/equal-opportunity-educational-programs/" target="_blank">Equal Opportunity</a></li>
                    <li class="last"><a href="http://agrilife.org/required-links/orpi/">Open Records/Public Information</a></li>
                </ul>
            </div>';

        echo $output;

    }


    /**
     * Render TAMUS logo
     * @todo refactor this, repeated functionality
     * @since 1.0
     * @return string
     */
    public static function render_tamus_logo()
    {

        $output = '
            <div class="footer-container-tamus">
                <a href="http://tamus.edu/" title="Texas A&amp;M University System"><img class="footer-tamus" src="'.AG_EXTRES_DIR_URL.'/img/logo-tamus.png" title="Texas A&amp;M University System Member" alt="Texas A&amp;M University System Member" />
                <noscript><img src="//agrilifecdn.tamu.edu/wp-content/themes/AgriLife-Beta/images/footer-tamus.png" title="Texas A&amp;M University System Member" alt="Texas A&amp;M University System Member" /></noscript></a>
            </div>';

        echo $output;

    }

}
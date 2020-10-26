<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Kids_Camp
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @since Kids Camp 1.0
 *
 * @param array $classes Classes for the body element.
 * @return array (Maybe) filtered body classes.
 */
function kids_camp_body_classes( $classes ) {
	// Adds a class of custom-background-image to sites with a custom background image.
	if ( get_background_image() ) {
		$classes[] = 'custom-background-image';
	}

	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Always add a front-page class to the front page.
	if ( is_front_page() && ! is_home() ) {
		$classes[] = 'page-template-front-page';
	}
	
	$classes[] = 'fluid-layout';

	// Adds a class with respect to layout selected.
	$layout  = kids_camp_get_theme_layout();
	$sidebar = kids_camp_get_sidebar_id();

	if ( 'no-sidebar' === $layout ) {
		$classes[] = 'no-sidebar content-width-layout';
	} elseif ( 'no-sidebar-full-width' === $layout ) {
		$classes[] = 'no-sidebar full-width-layout';
	} elseif ( 'left-sidebar' === $layout ) {
		if ( '' !== $sidebar ) {
			$classes[] = 'two-columns-layout content-right';
		}
	} elseif ( 'right-sidebar' === $layout ) {
		if ( '' !== $sidebar ) {
			$classes[] = 'two-columns-layout content-left';
		}
	}

	$header_media_sub_title = ( is_singular() && ! is_front_page() ) ? '' : get_theme_mod( 'kids_camp_header_media_sub_title', esc_html__( 'New Arrival', 'kids-camp' ) );
	$header_media_title = get_theme_mod( 'kids_camp_header_media_title', esc_html__( 'Furniture Store', 'kids-camp' ) );
	$header_media_text  = get_theme_mod( 'kids_camp_header_media_text' );

	$header_media_url = get_theme_mod( 'kids_camp_header_media_url', '#' );
	$header_media_button_text = get_theme_mod( 'kids_camp_header_media_url_text', esc_html__( 'View Details', 'kids-camp' ) );

	if ( ! $header_media_sub_title && ! $header_media_title && ! $header_media_text && ! $header_media_url && ! $header_media_button_text ) {
		$classes[] = 'no-header-media-text';
	}

	$classes[] = 'header-right-menu-disabled';

	if ( has_nav_menu( 'social-header-left' ) ) {
		$classes[] = 'header-center-layout';
	}

	$classes[] = 'footer-center';

	$enable_slider = kids_camp_check_section( get_theme_mod( 'kids_camp_slider_option', 'disabled' ) );
	$header_image = kids_camp_featured_overall_image();

	$classes[] = 'primary-nav-center';

	return $classes;
}
add_filter( 'body_class', 'kids_camp_body_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function kids_camp_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'kids_camp_pingback_header' );

/**
 * Remove first post from blog as it is already show via recent post template
 */
function kids_camp_alter_home( $query ) {
	if ( $query->is_home() && $query->is_main_query() ) {
		$cats = get_theme_mod( 'kids_camp_front_page_category' );

		if ( is_array( $cats ) && ! in_array( '0', $cats ) ) {
			$query->query_vars['category__in'] = $cats;
		}
	}
}
add_action( 'pre_get_posts', 'kids_camp_alter_home' );

if ( ! function_exists( 'kids_camp_content_nav' ) ) :
	/**
	 * Display navigation/pagination when applicable
	 *
	 * @since Kids Camp 1.0
	 */
	function kids_camp_content_nav() {
		global $wp_query;

		// Don't print empty markup in archives if there's only one page.
		if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) ) {
			return;
		}

		$pagination_type = get_theme_mod( 'kids_camp_pagination_type', 'default' );

		/**
		 * Check if navigation type is Jetpack Infinite Scroll and if it is enabled, else goto default pagination
		 * if it's active then disable pagination
		 */
		if ( ( 'infinite-scroll' === $pagination_type ) && class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' ) ) {
			return false;
		}

		if ( 'numeric' === $pagination_type && function_exists( 'the_posts_pagination' ) ) {
			the_posts_pagination( array(
				'prev_text'          => esc_html__( 'Previous', 'kids-camp' ),
				'next_text'          => esc_html__( 'Next', 'kids-camp' ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'kids-camp' ) . ' </span>',
			) );
		} else {
			the_posts_navigation();
		}
	}
endif; // kids_camp_content_nav

/**
 * Check if a section is enabled or not based on the $value parameter
 * @param  string $value Value of the section that is to be checked
 * @return boolean return true if section is enabled otherwise false
 */
function kids_camp_check_section( $value ) {
	return ( 'entire-site' == $value  || ( ( is_front_page() || ( is_home() && is_front_page() ) ) && 'homepage' == $value ) );
}

/**
 * Return the first image in a post. Works inside a loop.
 * @param [integer] $post_id [Post or page id]
 * @param [string/array] $size Image size. Either a string keyword (thumbnail, medium, large or full) or a 2-item array representing width and height in pixels, e.g. array(32,32).
 * @param [string/array] $attr Query string or array of attributes.
 * @return [string] image html
 *
 * @since Kids Camp 1.0
 */

function kids_camp_get_first_image( $postID, $size, $attr ) {
	ob_start();

	ob_end_clean();

	$image 	= '';

	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', get_post_field('post_content', $postID ) , $matches);

	if( isset( $matches [1] [0] ) ) {
		//Get first image
		$first_img = $matches [1] [0];

		return '<img class="pngfix wp-post-image" src="'. esc_url( $first_img ) .'">';
	}

	return false;
}

function kids_camp_get_theme_layout() {
	$layout = '';

	if ( is_page_template( 'templates/full-width-page.php' ) ) {
		$layout = 'no-sidebar-full-width';
	} elseif ( is_page_template( 'templates/right-sidebar.php' ) ) {
		$layout = 'right-sidebar';
	} else {
		$layout = get_theme_mod( 'kids_camp_default_layout', 'right-sidebar' );

		if ( is_front_page() ) {
			$layout = get_theme_mod( 'kids_camp_homepage_layout', 'no-sidebar-full-width' );
		} elseif ( is_home() || is_archive() || is_search() ) {
			$layout = get_theme_mod( 'kids_camp_archive_layout', 'right-sidebar' );
		}
	}

	return $layout;
}

function kids_camp_get_posts_columns() {
	$columns = 'layout-one';

	if ( is_front_page() ) {
		$columns = 'layout-three';
	}

	return $columns;
}

function kids_camp_get_sidebar_id() {
	$sidebar = '';

	$layout = kids_camp_get_theme_layout();

	$sidebaroptions = '';

	if ( 'no-sidebar-full-width' === $layout || 'no-sidebar' === $layout ) {
		return $sidebar;
	}

		global $post, $wp_query;

		// Front page displays in Reading Settings.
		$page_on_front  = get_option( 'page_on_front' );
		$page_for_posts = get_option( 'page_for_posts' );

		// Get Page ID outside Loop.
		$page_id = $wp_query->get_queried_object_id();
		
		if ( $page_id == $page_for_posts || $page_id == $page_on_front ) {
	    	// If blog page or homepage, get the blog page's id.
			$sidebaroptions = get_post_meta( $page_id, 'kids-camp-sidebar-option', true );
	    } elseif ( is_singular() ) {
	    	if ( is_attachment() ) {
				$parent 		= $post->post_parent;
				$sidebaroptions = get_post_meta( $parent, 'kids-camp-sidebar-option', true );

			} else {
				$sidebaroptions = get_post_meta( get_the_ID(), 'kids-camp-sidebar-option', true );
			}
		}

	if ( is_active_sidebar( 'sidebar-1' ) ) {
		$sidebar = 'sidebar-1'; // Primary Sidebar.
	}

	return $sidebar;
}

/**
 * Display social Menu
 */
function kids_camp_social_menu() {
	if ( has_nav_menu( 'social-menu' ) ) :
		?>
		<nav class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Social Links Menu', 'kids-camp' ); ?>">
			<?php
				wp_nav_menu( array(
					'theme_location' => 'social-menu',
					'link_before'    => '<span class="screen-reader-text">',
					'link_after'     => '</span>',
					'depth'          => 1,
				) );
			?>
		</nav><!-- .social-navigation -->
	<?php endif;
}

if ( ! function_exists( 'kids_camp_truncate_phrase' ) ) :
	/**
	 * Return a phrase shortened in length to a maximum number of characters.
	 *
	 * Result will be truncated at the last white space in the original string. In this function the word separator is a
	 * single space. Other white space characters (like newlines and tabs) are ignored.
	 *
	 * If the first `$max_characters` of the string does not contain a space character, an empty string will be returned.
	 *
	 * @since Kids Camp 1.0
	 *
	 * @param string $text            A string to be shortened.
	 * @param integer $max_characters The maximum number of characters to return.
	 *
	 * @return string Truncated string
	 */
	function kids_camp_truncate_phrase( $text, $max_characters ) {

		$text = trim( $text );

		if ( mb_strlen( $text ) > $max_characters ) {
			//* Truncate $text to $max_characters + 1
			$text = mb_substr( $text, 0, $max_characters + 1 );

			//* Truncate to the last space in the truncated string
			$text = trim( mb_substr( $text, 0, mb_strrpos( $text, ' ' ) ) );
		}

		return $text;
	}
endif; //catch-kids_camp_truncate_phrase

if ( ! function_exists( 'kids_camp_get_the_content_limit' ) ) :
	/**
	 * Return content stripped down and limited content.
	 *
	 * Strips out tags and shortcodes, limits the output to `$max_char` characters, and appends an ellipsis and more link to the end.
	 *
	 * @since Kids Camp 1.0
	 *
	 * @param integer $max_characters The maximum number of characters to return.
	 * @param string  $more_link_text Optional. Text of the more link. Default is "(more...)".
	 * @param bool    $stripteaser    Optional. Strip teaser content before the more text. Default is false.
	 *
	 * @return string Limited content.
	 */
	function kids_camp_get_the_content_limit( $max_characters, $more_link_text = '(more...)', $stripteaser = false ) {

		$content = get_the_content( '', $stripteaser );

		// Strip tags and shortcodes so the content truncation count is done correctly.
		$content = strip_tags( strip_shortcodes( $content ), apply_filters( 'get_the_content_limit_allowedtags', '<script>,<style>' ) );

		// Remove inline styles / .
		$content = trim( preg_replace( '#<(s(cript|tyle)).*?</\1>#si', '', $content ) );

		// Truncate $content to $max_char
		$content = kids_camp_truncate_phrase( $content, $max_characters );

		// More link?
		if ( $more_link_text ) {
			$link   = apply_filters( 'get_the_content_more_link', sprintf( '<span class="more-button"><a href="%s" class="more-link">%s</a></span>', esc_url( get_permalink() ), $more_link_text ), $more_link_text );
			$output = sprintf( '<p>%s %s</p>', $content, $link );
		} else {
			$output = sprintf( '<p>%s</p>', $content );
			$link = '';
		}

		return apply_filters( 'kids_camp_get_the_content_limit', $output, $content, $link, $max_characters );

	}
endif; //catch-kids_camp_get_the_content_limit

if ( ! function_exists( 'kids_camp_content_image' ) ) :
	/**
	 * Template for Featured Image in Archive Content
	 *
	 * To override this in a child theme
	 * simply fabulous-fluid your own kids_camp_content_image(), and that function will be used instead.
	 *
	 * @since Kids Camp 1.0
	 */
	function kids_camp_content_image() {
		if ( has_post_thumbnail() && kids_camp_jetpack_featured_image_display() && is_singular() ) {
			global $post, $wp_query;

			// Get Page ID outside Loop.
			$page_id = $wp_query->get_queried_object_id();

			if ( $post ) {
		 		if ( is_attachment() ) {
					$parent = $post->post_parent;

					$individual_featured_image = get_post_meta( $parent, 'kids-camp-single-image', true );
				} else {
					$individual_featured_image = get_post_meta( $page_id, 'kids-camp-single-image', true );
				}
			}

			if ( empty( $individual_featured_image ) ) {
				$individual_featured_image = 'default';
			}

			if ( 'disable' === $individual_featured_image ) {
				echo '<!-- Page/Post Single Image Disabled or No Image set in Post Thumbnail -->';
				return false;
			} else {
				$class = array();

				$image_size = 'post-thumbnail';

				if ( 'default' !== $individual_featured_image ) {
					$image_size = $individual_featured_image;
					$class[]    = 'from-metabox';
				} else {
					$layout = kids_camp_get_theme_layout();

					if ( 'no-sidebar-full-width' === $layout ) {
						$image_size = 'kids-camp-slider';
					}
				}

				$class[] = $individual_featured_image;
				?>
				<div class="post-thumbnail <?php echo esc_attr( implode( ' ', $class ) ); ?>">
					<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( $image_size ); ?>
					</a>
				</div>
		   	<?php
			}
		} // End if().
	}
endif; // kids_camp_content_image.

if ( ! function_exists( 'kids_camp_sections' ) ) :
	/**
	 * Display Sections on header and footer with respect to the section option set in kids_camp_sections_sort
	 */
	function kids_camp_sections( $selector = 'header' ) {
		get_template_part( 'template-parts/header/header-media' );
		get_template_part( 'template-parts/slider/content-slider' );
		get_template_part( 'template-parts/featured-content/display-featured' );
		get_template_part( 'template-parts/testimonial/display-testimonial' );
		get_template_part( 'template-parts/hero-content/content-hero' );
		get_template_part( 'template-parts/portfolio/display-portfolio' );
		get_template_part( 'template-parts/service/display-service' );	
	}
endif;

if ( ! function_exists( 'kids_camp_get_no_thumb_image' ) ) :
	/**
	 * $image_size post thumbnail size
	 * $type image, src
	 */
	function kids_camp_get_no_thumb_image( $image_size = 'post-thumbnail', $type = 'image' ) {
		$image = $image_url = '';

		global $_wp_additional_image_sizes;

		$size = $_wp_additional_image_sizes['post-thumbnail'];

		if ( isset( $_wp_additional_image_sizes[ $image_size ] ) ) {
			$size = $_wp_additional_image_sizes[ $image_size ];
		}

		$image_url  = trailingslashit( get_template_directory_uri() ) . 'assets/images/no-thumb.jpg';

		if ( 'post-thumbnail' !== $image_size ) {
			$image_url  = trailingslashit( get_template_directory_uri() ) . 'assets/images/no-thumb-' . $size['width'] . 'x' . $size['height'] . '.jpg';
		}

		if ( 'src' === $type ) {
			return $image_url;
		}

		return '<img class="no-thumb ' . esc_attr( $image_size ) . '" src="' . esc_url( $image_url ) . '" />';
	}
endif;

/**
 * Adds custom overlay for Header Media
 */
function kids_camp_header_media_image_overlay_css() {
	$overlay = get_theme_mod( 'kids_camp_header_media_image_opacity' );

	$css = '';

	$overlay_bg = $overlay / 100;

	$color_scheme = get_theme_mod( 'color_scheme', 'default' );

	if ( '0' !== $overlay ) {
			$css = '.custom-header:after {
				background-color: rgba(0,0,0,' . esc_attr( $overlay_bg ) . ');
		    } '; // Dividing by 100 as the option is shown as % for user
	}

	wp_add_inline_style( 'kids-camp-style', $css );
}
add_action( 'wp_enqueue_scripts', 'kids_camp_header_media_image_overlay_css', 11 );

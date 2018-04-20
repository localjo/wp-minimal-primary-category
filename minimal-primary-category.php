<?php
/**
 * Minimal Primary Category
 *
 * @package     Minimal_Primary_Category
 * @author      Josiah Sprague
 * @copyright   2018 Josiah Sprague
 * @license     MIT
 *
 * @wordpress-plugin
 * Plugin Name: Minimal Primary Category
 * Plugin URI: https://github.com/localjo/wp-minimal-primary-category
 * Description: A plugin that lets you designate a primary category for posts.
 * Version: 0.0.1
 * Author: Jo Sprague
 * Author URI: http://josiahsprague.com/
 * Text Domain: mp-category
 * License: MIT
 **/

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * Create the markup for the primary category metabox
 *
 * @param WP_POST $post The post object.
 */
function mp_category_meta_box_markup( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'mp-category-nonce' );
	?>
	<select name="mp-category-dropdown">
		<option disabled selected value> -- select a primary category -- </option>
		<?php
		$term_query = new WP_Term_Query( array( 'taxonomy' => 'category' ) );
		if ( ! empty( $term_query->terms ) ) {
			foreach ( $term_query->terms as $term ) {
				$primary_category = get_post_meta(
					$post->ID,
					'_minimal-primary-category',
					true
				);
				if ( (int) $primary_category === $term->term_id ) {
					?>
					<option selected value="<?php echo esc_attr( $term->term_id ); ?>">
						<?php echo esc_textarea( $term->name ); ?>
					</option>
					<?php
				} else {
					?>
					<option value="<?php echo esc_attr( $term->term_id ); ?>">
						<?php echo esc_textarea( $term->name ); ?>
					</option>
					<?php
				}
			}
		}
		?>
	</select>
	<?php
}

/**
 * Add a primary category metabox to the post editor
 */
function mp_category_add_meta_box() {
	add_meta_box(
		'mp-category-meta-box',
		'Primary Category',
		'mp_category_meta_box_markup',
		'post',
		'side',
		'default',
		null
	);
}
add_action( 'add_meta_boxes', 'mp_category_add_meta_box' );

/**
 * Save the primary category from the meta box value
 *
 * @param int     $post_id The post ID to save primary category for.
 * @param WP_POST $post The post object.
 * @return int    int
 */
function mp_category_save( $post_id, $post ) {
	if (
		! isset( $_POST['mp-category-nonce'] ) || // Input var okay.
		! wp_verify_nonce(
			sanitize_key( $_POST['mp-category-nonce'] ), // Input var okay.
			basename( __FILE__ )
		) ||
		! current_user_can( 'edit_post', $post_id ) ||
		( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
		'post' !== $post->post_type
	) {
		return $post_id;
	}

	$term_query = new WP_Term_Query( array( 'taxonomy' => 'category' ) );
	$categories = array_column( $term_query->terms, 'term_id', 'term_id' );

	if ( isset( $_POST['mp-category-dropdown'] ) ) { // Input var okay.
		$meta_box_dropdown_value = sanitize_text_field(
			wp_unslash( $_POST['mp-category-dropdown'] ) // Input var okay.
		);
	}

	$new_primary_category = isset(
		$categories[ $meta_box_dropdown_value ]
	) ? $meta_box_dropdown_value : '';

	update_post_meta(
		$post_id,
		'_minimal-primary-category',
		$new_primary_category
	);

}
add_action( 'save_post', 'mp_category_save', 10, 3 );

/**
 * Get the primary category for a post
 *
 * @param int $post_id The post ID to get primary category for.
 * @return WP_CATEGORY    Primary category
 */
function mp_category_get_primary_category( $post_id ) {
	$primary_category_id = get_post_meta( '1', '_minimal-primary-category', true );
	return get_category( $primary_category_id );
}

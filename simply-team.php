<?php
/**
 * Plugin Name: Simply Team
 * Plugin URI:  https://simplydesign.com
 * Description: Meet-the-team CPT with headshot, role, contact info, and a slide-over bio panel. Shortcode: [simply_team]
 * Author:      Simply Design
 * Author URI:  https://simplydesign.com
 * Version:     1.0.5
 * License:     GPL-2.0-or-later
 * Text Domain: simply-team
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'ST_VERSION', '1.0.5' );
define( 'ST_PATH',    plugin_dir_path( __FILE__ ) );
define( 'ST_URL',     plugin_dir_url( __FILE__ ) );

require_once ST_PATH . 'includes/class-github-updater.php';
new Simply_GitHub_Updater( 'plugin', plugin_basename( __FILE__ ), 'staceyzav/simply-team', ST_VERSION );


// ==========================================================================
// CPT — simply_team_member
// ==========================================================================

add_action( 'init', 'st_register_cpt' );

function st_register_cpt() {
	register_post_type( 'simply_team_member', array(
		'labels' => array(
			'name'               => __( 'Team Members', 'simply-team' ),
			'singular_name'      => __( 'Team Member', 'simply-team' ),
			'add_new'            => __( 'Add New Member', 'simply-team' ),
			'add_new_item'       => __( 'Add New Team Member', 'simply-team' ),
			'edit_item'          => __( 'Edit Team Member', 'simply-team' ),
			'new_item'           => __( 'New Team Member', 'simply-team' ),
			'search_items'       => __( 'Search Team Members', 'simply-team' ),
			'not_found'          => __( 'No team members found', 'simply-team' ),
			'not_found_in_trash' => __( 'No team members found in trash', 'simply-team' ),
			'menu_name'          => __( 'Team', 'simply-team' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'show_in_menu'  => true,
		'supports'      => array( 'title', 'editor', 'thumbnail' ),
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-groups',
		'menu_position' => 25,
	) );
}


// ==========================================================================
// META BOX — role, phone, email
// ==========================================================================

add_action( 'add_meta_boxes', 'st_add_meta_box' );

function st_add_meta_box() {
	add_meta_box(
		'st_member_details',
		__( 'Member Details', 'simply-team' ),
		'st_meta_box_cb',
		'simply_team_member',
		'normal',
		'high'
	);
}

function st_meta_box_cb( $post ) {
	wp_nonce_field( 'st_save_meta', 'st_nonce' );

	$role  = get_post_meta( $post->ID, '_st_role',  true );
	$phone = get_post_meta( $post->ID, '_st_phone', true );
	$email = get_post_meta( $post->ID, '_st_email', true );
	?>
	<style>
		.st-meta-field { margin-bottom: 14px; }
		.st-meta-field label { display: block; font-weight: 600; margin-bottom: 4px; font-size: 13px; }
		.st-meta-field input { width: 100%; padding: 6px 8px; border: 1px solid #ddd; border-radius: 3px; font-size: 13px; }
		.st-meta-note { font-size: 12px; color: #888; margin: 2px 0 0; }
	</style>

	<div class="st-meta-field">
		<label for="st_role"><?php esc_html_e( 'Role / Title', 'simply-team' ); ?></label>
		<input type="text" id="st_role" name="st_role"
			value="<?php echo esc_attr( $role ); ?>"
			placeholder="<?php esc_attr_e( 'e.g. Head Coach', 'simply-team' ); ?>">
	</div>

	<div class="st-meta-field">
		<label for="st_phone"><?php esc_html_e( 'Phone', 'simply-team' ); ?></label>
		<input type="text" id="st_phone" name="st_phone"
			value="<?php echo esc_attr( $phone ); ?>"
			placeholder="<?php esc_attr_e( 'e.g. 801-555-0100', 'simply-team' ); ?>">
	</div>

	<div class="st-meta-field">
		<label for="st_email"><?php esc_html_e( 'Email', 'simply-team' ); ?></label>
		<input type="email" id="st_email" name="st_email"
			value="<?php echo esc_attr( $email ); ?>"
			placeholder="<?php esc_attr_e( 'name@example.com', 'simply-team' ); ?>">
		<p class="st-meta-note"><?php esc_html_e( 'Bio goes in the main content editor below — shown in the slide-over panel.', 'simply-team' ); ?></p>
	</div>
	<?php
}

add_action( 'save_post_simply_team_member', 'st_save_meta' );

function st_save_meta( $post_id ) {
	if (
		! isset( $_POST['st_nonce'] ) ||
		! wp_verify_nonce( $_POST['st_nonce'], 'st_save_meta' ) ||
		( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
		! current_user_can( 'edit_post', $post_id )
	) {
		return;
	}

	$fields = array(
		'st_role'  => '_st_role',
		'st_phone' => '_st_phone',
		'st_email' => '_st_email',
	);

	foreach ( $fields as $post_key => $meta_key ) {
		if ( isset( $_POST[ $post_key ] ) ) {
			$value = $post_key === 'st_email'
				? sanitize_email( $_POST[ $post_key ] )
				: sanitize_text_field( $_POST[ $post_key ] );
			update_post_meta( $post_id, $meta_key, $value );
		}
	}
}


// ==========================================================================
// ENQUEUE
// ==========================================================================

add_action( 'wp_enqueue_scripts', 'st_enqueue' );

function st_enqueue() {
	wp_enqueue_style(  'simply-team', ST_URL . 'assets/css/simply-team.css', array(), ST_VERSION );
	wp_enqueue_script( 'simply-team', ST_URL . 'assets/js/simply-team.js',  array(), ST_VERSION, true );
}


// ==========================================================================
// SHORTCODE — [simply_team]
//
// Usage:
//   [simply_team]
//   [simply_team limit="6" columns="4"]
// ==========================================================================

add_shortcode( 'simply_team', 'st_shortcode' );

function st_shortcode( $atts ) {

	$atts = shortcode_atts( array(
		'limit'   => -1,
		'columns' => 3,
	), $atts, 'simply_team' );

	$members = new WP_Query( array(
		'post_type'      => 'simply_team_member',
		'posts_per_page' => intval( $atts['limit'] ),
		'post_status'    => 'publish',
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	) );

	if ( ! $members->have_posts() ) {
		return '';
	}

	$cols = max( 1, min( 5, intval( $atts['columns'] ) ) );

	ob_start();
	?>
	<div class="st-team" style="--st-columns:<?php echo $cols; ?>">

		<?php while ( $members->have_posts() ) : $members->the_post();
			$id    = get_the_ID();
			$role  = get_post_meta( $id, '_st_role',  true );
			$phone = get_post_meta( $id, '_st_phone', true );
			$email = get_post_meta( $id, '_st_email', true );
			$bio   = get_the_content();
			$name  = get_the_title();
			$panel_id = 'st-panel-' . $id;
		?>
		<div class="st-card">

			<div class="st-card__inner">

				<div class="st-card__photo">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'medium', array( 'alt' => esc_attr( $name ) ) ); ?>
					<?php else : ?>
						<div class="st-card__photo-placeholder"></div>
					<?php endif; ?>
				</div>

				<div class="st-card__body">
					<h3 class="st-card__name"><?php echo esc_html( $name ); ?></h3>
					<?php if ( $role ) : ?>
						<p class="st-card__role"><?php echo esc_html( $role ); ?></p>
					<?php endif; ?>

					<div class="st-card__contact">
						<?php if ( $phone ) : ?>
							<span class="st-card__phone"><?php echo esc_html( $phone ); ?></span>
						<?php endif; ?>
						<?php if ( $email ) : ?>
							<span class="st-card__email"><?php echo esc_html( $email ); ?></span>
						<?php endif; ?>
					</div>

					<?php if ( $bio ) : ?>
						<button class="st-card__more" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>">
							<?php esc_html_e( 'More Info', 'simply-team' ); ?>
						</button>
					<?php endif; ?>
				</div>

			</div>

		</div>

		<?php if ( $bio ) : ?>
		<div class="st-panel" id="<?php echo esc_attr( $panel_id ); ?>" role="dialog" aria-label="<?php echo esc_attr( $name ); ?>" aria-hidden="true">

			<div class="st-panel__header">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="st-panel__photo">
						<?php the_post_thumbnail( 'medium', array( 'alt' => esc_attr( $name ) ) ); ?>
					</div>
				<?php endif; ?>
				<div class="st-panel__intro">
					<h2 class="st-panel__name"><?php echo esc_html( $name ); ?></h2>
					<?php if ( $role ) : ?>
						<p class="st-panel__role"><?php echo esc_html( $role ); ?></p>
					<?php endif; ?>
					<?php if ( $phone ) : ?>
						<a class="st-panel__phone" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
					<?php endif; ?>
					<?php if ( $email ) : ?>
						<a class="st-panel__email" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
					<?php endif; ?>
				</div>
				<button class="st-panel__close" aria-label="<?php esc_attr_e( 'Close', 'simply-team' ); ?>">&times;</button>
			</div>

			<div class="st-panel__bio">
				<?php echo wp_kses_post( apply_filters( 'the_content', $bio ) ); ?>
			</div>

		</div>
		<?php endif; ?>

		<?php endwhile; wp_reset_postdata(); ?>

	</div>

	<div class="st-overlay" aria-hidden="true"></div>
	<?php
	return ob_get_clean();
}

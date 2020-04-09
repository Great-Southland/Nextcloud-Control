<?php // Admin Notices

// create the admin notice
function create_user_admin_notices() {

// Check if WordPress create user was successfull or created an error
		if ( isset( $_GET['result-wp'] ) ) {?>

				<div class="notice notice-success is-dismissible">
					<p><strong><?php echo esc_html( $_GET['result-wp'] ); ?></strong></p>
				</div>

			<?php
		}



  // Check if NextCloud HTTP connection returned an error

    if ( isset( $_GET['result-nc'] ) ) {
    		if ( is_numeric( $_GET['result-nc'] ) ) :
          ?>


    			<div class="notice notice-success is-dismissible">
    				<p><strong><?php echo('NextCloud: '. $_GET['nc_response-code'] . $_GET['nc_response-message']); ?></strong></p>
    			</div>

    		<?php else : ?>

    			<div class="notice notice-warning is-dismissible">
    				<p><strong><?php echo esc_html( 'NextCloud: '. $_GET['result-nc'] ); ?></strong></p>
    			</div>

    		<?php endif;

    }
}
add_action( 'admin_notices', 'create_user_admin_notices' );

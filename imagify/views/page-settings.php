<?php
defined( 'ABSPATH' ) || exit;

$settings     = Imagify_Settings::get_instance();
$options      = Imagify_Options::get_instance();
$option_name  = $options->get_option_name();
$hidden_class = Imagify_Requirements::is_api_key_valid() ? '' : ' hidden';
$lang         = imagify_get_current_lang_in( array( 'de', 'es', 'fr', 'it' ) );

/* Ads notice */
$plugins_list  = get_plugins();
$notice        = 'wp-rocket';
$user_id       = get_current_user_id();
$notices       = get_user_meta( $user_id, '_imagify_ignore_ads', true );
$notices       = $notices && is_array( $notices ) ? array_flip( $notices ) : array();
$wrapper_class = isset( $notices[ $notice ] ) || isset( $plugins_list['wp-rocket/wp-rocket.php'] ) ? 'imagify-have-rocket' : 'imagify-dont-have-rocket';
?>
<div class="wrap imagify-settings <?php echo $wrapper_class; ?> imagify-clearfix">

	<div class="imagify-col imagify-main">

		<?php $this->print_template( 'part-settings-header' ); ?>
		<div class="imagify-main-content">
			<form action="<?php echo esc_url( $settings->get_form_action() ); ?>" id="imagify-settings" method="post">

				<div class="imagify-settings-main-content<?php echo Imagify_Requirements::is_api_key_valid() ? '' : ' imagify-no-api-key'; ?>">

					<?php settings_fields( $settings->get_settings_group() ); ?>
					<?php wp_nonce_field( 'imagify-signup', 'imagifysignupnonce', false ); ?>
					<?php wp_nonce_field( 'imagify-check-api-key', 'imagifycheckapikeynonce', false ); ?>

					<?php
					if ( ! Imagify_Requirements::is_api_key_valid() ) {
						$this->print_template( 'part-settings-account' );
						$this->print_template( 'part-settings-footer' );
					}
					?>

					<div class="imagify-col imagify-shared-with-account-col<?php echo $hidden_class; ?>">
						<div class="imagify-settings-section">

							<h2 class="imagify-options-title"><?php _e( 'General Settings', 'imagify' ); ?></h2>

							<p class="imagify-setting-line">
							<?php
							$settings->field_checkbox(
								[
									'option_name' => 'auto_optimize',
									'label'       => __( 'Auto-Optimize images on upload', 'imagify' ),
									'info'        => __( 'Automatically optimize every image you upload to WordPress.', 'imagify' ),
								]
							);
							?>
							</p>

							<p class="imagify-setting-line">
								<?php
								$settings->field_checkbox(
									[
										'option_name' => 'backup',
										'label'       => __( 'Backup original images', 'imagify' ),
										'info'        => __( 'Keep your original images in a separate folder before optimization process.', 'imagify' ),
									]
								);

								$backup_error_class = $options->get( 'backup' ) && ! Imagify_Requirements::attachments_backup_dir_is_writable() ? '' : ' hidden';
								?>
								<br/><strong id="backup-dir-is-writable" class="imagify-error<?php echo $backup_error_class; ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'imagify_check_backup_dir_is_writable' ) ); ?>">
									<?php
									$backup_path = $this->filesystem->make_path_relative( get_imagify_backup_dir_path( true ) );
									/* translators: %s is a file path. */
									printf( __( 'The backup folder %s cannot be created or is not writable by the server, original images cannot be saved!', 'imagify' ), "<code>$backup_path</code>" );
									?>
								</strong>
							</p>

							<p class="imagify-setting-line">
							<?php
							$settings->field_checkbox(
								[
									'option_name' => 'lossless',
									'label'       => __( 'Lossless compression', 'imagify' ),
									'info'        => __( 'By default, Imagify optimizes your images by using a smart compression to get the best compression rate with an optimal quality.', 'imagify' ) . '<br><br>' . __( 'If you are a photographer or focus on the quality of your images rather than the performance, you may be interested in this option to make sure not a single pixel looks different in the optimized image compared with the original.', 'imagify' ),
								]
							);
							?>
							</p>
						</div>
					</div>

					<?php if ( Imagify_Requirements::is_api_key_valid() ) { ?>
						<div class="imagify-col imagify-account-info-col">
							<?php $this->print_template( 'part-settings-account' ); ?>
						</div>
					<?php } ?>
				</div>

				<div class="imagify-settings-main-content<?php echo $hidden_class; ?>">

					<div class="imagify-settings-section imagify-clear">
						<h2 class="imagify-options-title"><?php _e( 'Optimization', 'imagify' ); ?></h2>
						<?php
						$this->print_template( 'part-settings-webp' );
						$this->print_template( 'part-settings-library' );
						$this->print_template( 'part-settings-custom-folders' );
						?>
					</div>
				</div>

				<div class="imagify-settings-main-content imagify-pb0<?php echo $hidden_class; ?>">
					<div class="imagify-settings-section imagify-clear">
						<div>
							<h2 class="imagify-options-title"><?php _e( 'Our Plugins', 'imagify' ); ?></h2>
							<p class="imagify-options-subtitle"><?php _e( 'Build better, faster, safer', 'imagify' ); ?></p>
							<p class="">
								<?php
								_e( 'Beyond Imagify, there\'s a whole family of plugins designed to help you build better, faster, and safer websites. Each one is crafted with our unique blend of expertise, simplicity, and outstanding support. Combine our plugins below to build incredible WordPress websites!', 'imagify' );
								?>
							</p>
							<?php foreach ( $data['plugin_family'] as $plugin_name => $plugin_data ) : ?>
								<div class="imagify-plugin-family-col">
									<div class="imagify-card">
										<div class="imagify-card-header">
											<div class="imagify-card-logo">
												<img src="<?php echo esc_url( IMAGIFY_ASSETS_IMG_URL . $plugin_data['logo']['file'] ); ?>" loading="lazy" style="width: <?php echo esc_attr( $plugin_data['logo']['width'] ); ?>">
											</div>
											<h4><?php echo esc_html( $plugin_data['title'] ); ?></h4>
										</div>
										<div class="imagify-card-body">
											<p>
												<?php echo esc_html( $plugin_data['desc'] ); ?>
											</p>
										</div>
										<div class="imagify-card-footer">
											<?php if ( '#' === $plugin_data['cta']['url'] ) : ?>
												<span><?php echo esc_html( $plugin_data['cta']['text'] ); ?></span><span class="dashicons dashicons-yes"></span>
											<?php else : ?>
												<a href="<?php echo esc_url( $plugin_data['cta']['url'] ); ?>" class="imagify-card-btn imagify-btn-cta" <?php echo 'Get it Now' === $plugin_data['cta']['text'] ? 'target="_blank"' : ''; ?> rel="noopener"><?php echo esc_html( $plugin_data['cta']['text'] ); ?></a>
												<a href="<?php echo esc_url( $plugin_data['link'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( 'Learn more' ); ?></a>
											<?php endif; ?>
										</div>
									</div>
								</div>
								<?php
							endforeach;
							/**
							 * List of partners affected by this option.
							 * For internal use only.
							 *
							 * @since  1.8.2
							 * @author Grégory Viguier
							 *
							 * @param  array $partners An array of partner names.
							 * @return array
							 */
							$partners = apply_filters( 'imagify_deactivatable_partners', [] );

							if ( $partners ) {
								?>
								<h2 class="imagify-options-title"><?php esc_html_e( 'Partners', 'imagify' ); ?></h2>
								<p class="imagify-options-subtitle" id="imagify-partners-label">
									<span class="imagify-info">
										<span class="dashicons dashicons-info"></span>
										<a href="#imagify-partners-info" class="imagify-modal-trigger"><?php _e( 'More info?', 'imagify' ); ?></a>
									</span>
								</p>

								<p>
									<?php
									$settings->field_checkbox(
										[
											'option_name' => 'partner_links',
											'label'       => __( 'Display Partner Links', 'imagify' ),
										]
									);
									?>
								</p>
								<?php
							}
							?>
						</div>
					</div>

					<?php
					if ( Imagify_Requirements::is_api_key_valid() ) {
						$this->print_template( 'part-settings-footer' );
					}
					?>
				</div>
			</form>
		</div>
	</div>

	<?php
	$this->print_template( 'part-rocket-ad' );
	$this->print_template( 'modal-settings-infos' );
	$this->print_template( 'modal-settings-partners-infos' );
	$this->print_template( 'modal-settings-visual-comparison' );
	?>

</div>


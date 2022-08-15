<?php declare(strict_types=1);

/**
 * Downloads a WordPress plugin utilizing the WordPress API for
 * usage in automated tests.
 */
class Plugin_Downloader extends Downloader {

	/**
	 * Plugin data populated from the WordPress API.
	 */
	protected object $plugin_data;

	protected string $plugin_version;

	/**
	 * @param  string  $plugin_slug     The WordPress plugin slug, e.g. advanced-custom-fields.
	 * @param  string  $plugin_version  The version to download, defaults to the latest stable version, if empty.
	 */
	public function __construct( string $plugin_slug, string $plugin_version = '' ) {
		$this->plugin_version = $plugin_version;
		$this->plugin_data    = $this->getData( $plugin_slug );
	}

	/**
	 * @inheritDoc
	 */
	public function download(): string {
		$version       = $this->plugin_data->version;
		$download_link = $this->plugin_data->download_link;

		// Download a specific version of the plugin.
		if ( $this->plugin_version ) {
			$version       = $this->plugin_version;
			$download_link = $this->plugin_data->versions->{$this->plugin_version};
		}

		$file = sprintf( '/tmp/%s-%s.zip', $this->plugin_data->slug, $version );

		file_put_contents( $file, file_get_contents( $download_link ) );

		return $file;
	}

	/**
	 * @inheritDoc
	 */
	public function destination(): string {
		return __DIR__ . '/../../wordpress/wp-content/plugins/';
	}

	/**
	 * Retrieve plugin data from the WordPress API.
	 *
	 * @param  string  $plugin_slug
	 *
	 * @return object
	 */
	protected function getData( string $plugin_slug ): object {
		return json_decode(
			file_get_contents(
				sprintf( 'https://api.wordpress.org/plugins/info/1.0/%s.json', $plugin_slug )
			)
		);
	}

}

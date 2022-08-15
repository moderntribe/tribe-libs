<?php declare(strict_types=1);

final class Wordpress_Downloader extends Downloader {

	private object $data;

	public function __construct() {
		$this->data = $this->getData();
	}

	/**
	 * @inheritDoc
	 */
	public function download(): string {
		$latest = reset( $this->data->offers );
		$file   = sprintf( '/tmp/wordpress-%s.zip', $latest->version );

		file_put_contents( $file, file_get_contents( $latest->download ) );

		return $file;
	}

	/**
	 * @inheritDoc
	 */
	public function destination(): string {
		return __DIR__ . '/../../';
	}

	private function getData(): object {
		return json_decode( file_get_contents( 'https://api.wordpress.org/core/version-check/1.7/' ) );
	}

}

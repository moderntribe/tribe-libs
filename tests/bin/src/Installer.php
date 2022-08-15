<?php declare(strict_types=1);

use PhpZip\Exception\ZipException;
use PhpZip\ZipFile;

final class Installer {

	/**
	 * @var \Downloader[]
	 */
	private array $downloaders;

	public function __construct( array $downloaders ) {
		$this->downloaders = $downloaders;
	}

	public function install(): void {
		foreach ( $this->downloaders as $downloader ) {
			$file = $downloader->download();
			echo "Downloaded $file..." . PHP_EOL;
			$this->unzip( $file, $downloader->destination() );
			$this->cleanup( $file );
		}
	}

	private function unzip( string $file, string $destination ) {
		printf( 'Extracting %s to %s' . PHP_EOL, $file, realpath( $destination ) );

		$zip = new ZipFile();

		try {
			$zip->openFile( $file )
				->extractTo( $destination );
		} catch ( ZipException $e ) {
			$zip->close();
			throw $e;
		} finally {
			$zip->close();
		}
	}

	private function cleanup( string $file ) {
		printf( "Deleting %s..." . PHP_EOL, realpath( $file ) );
		unlink( $file );
	}

}

<?php declare(strict_types=1);

abstract class Downloader {

	/**
	 * The path to the downloaded zip.
	 *
	 * @return string
	 */
	abstract public function download(): string;


	/**
	 * The destination to extract the zip.
	 *
	 * @return string
	 */
	abstract public function destination(): string;

}

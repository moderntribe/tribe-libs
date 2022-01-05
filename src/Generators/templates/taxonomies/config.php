<?php

namespace Tribe\Project\Taxonomies\%1$s;

use Tribe\Libs\Taxonomy\Taxonomy_Config;

class Config extends Taxonomy_Config {

	protected string $taxonomy = '%4$s';

	/**
	 * @var string[]
	 */
	protected array $post_types = [ %5$s ];

	protected $version = 0;

	public function get_args(): array {
		return [
			'hierarchical' => false,
		];
	}

	public function get_labels(): array {
		return [
			'singular' => __( '%2$s', 'tribe' ),
			'plural'   => __( '%3$s', 'tribe' ),
			'slug'     => __( '%4$s', 'tribe' ),
		];
	}

	public function default_terms(): array {
		return [];
	}
}

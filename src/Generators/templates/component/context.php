<?php

namespace %3$s;

/**
 * Class %2$s
 *
 * %4$s
 * @property string[] $classes
 * @property string[] $attrs
 */
class %2$s extends \Tribe\Project\Templates\Components\Context {
	%5$s
	public const CLASSES = 'classes';
	public const ATTRS   = 'attrs';

	protected $path = __DIR__ . '/%1$s.twig';

	protected $properties = [
		%6$s
		self::CLASSES => [
			self::DEFAULT       => [],
			self::MERGE_CLASSES => [],
		],
		self::ATTRS   => [
			self::DEFAULT          => [],
			self::MERGE_ATTRIBUTES => [],
		],
	];
}

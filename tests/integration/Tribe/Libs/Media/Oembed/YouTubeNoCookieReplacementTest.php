<?php declare(strict_types=1);

namespace Tribe\Libs\Media\Oembed;

use Tribe\Libs\Tests\Test_Case;

final class YouTubeNoCookieReplacementTest extends Test_Case {

	private YouTube_Oembed_Filter $youtube_filter;

	protected function setUp(): void {
		parent::setUp();

		$this->youtube_filter = new YouTube_Oembed_Filter();
	}

	protected function tearDown(): void {
		remove_all_filters( 'oembed_dataparse' );

		// @phpstan-ignore-next-line
		parent::tearDown();
	}

	public function test_it_replaces_youtube_embeds_with_no_cookie_domain(): void {
		$input          = '<iframe width="560" height="315" src="https://www.youtube.com/embed/TcWPiHjIExA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
		$expected       = '<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/TcWPiHjIExA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

		$this->assertSame( $expected, $this->youtube_filter->force_youtube_no_cookie_embed( $input ) );
	}

	public function test_it_replaces_youtube_embeds_with_no_cookie_domain_using_camel_case_domain(): void {
		$input          = '<iframe width="560" height="315" src="https://www.YouTube.cOm/embed/TcWPiHjIExA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
		$expected       = '<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/TcWPiHjIExA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

		$this->assertSame( $expected, $this->youtube_filter->force_youtube_no_cookie_embed( $input ) );
	}

	public function test_it_passes_through_invalid_string_values(): void {
		$this->assertSame( '', $this->youtube_filter->force_youtube_no_cookie_embed( '' ) );
		$this->assertSame( 'test', $this->youtube_filter->force_youtube_no_cookie_embed( 'test' ) );
		$this->assertSame( 'https://tri.be/youtube', $this->youtube_filter->force_youtube_no_cookie_embed( 'https://tri.be/youtube' ) );
	}

	public function test_it_replaces_youtube_embeds_within_blocks(): void {
		add_filter(
			'oembed_dataparse',
			fn ( $html ) =>
			$this->youtube_filter->force_youtube_no_cookie_embed( (string) $html ),
			999,
			1
		);

		$post_id = $this->factory()->post->create( [
			'post_title'   => 'YouTube Embed',
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_content' => '<!-- wp:paragraph -->
<p>This is a test</p>
<!-- /wp:paragraph -->

<!-- wp:embed {"url":"https://www.youtube.com/watch?v=TcWPiHjIExA","type":"video","providerNameSlug":"youtube","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->
<figure class="wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
https://www.youtube.com/watch?v=TcWPiHjIExA
</div></figure>
<!-- /wp:embed -->',
		] );

		$content = apply_filters( 'the_content', get_post( $post_id )->post_content );

		$this->assertStringNotContainsString( 'youtube.com', $content );
		$this->assertStringContainsString( 'https://www.youtube-nocookie.com/embed/TcWPiHjIExA?feature=oembed', $content );
	}

}

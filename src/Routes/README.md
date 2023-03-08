## Routes

Examples can be found in: `wp-content/plugins/core/src/Routes/`

### Registering a route
#### REST Route
Create a new class in `wp-content/plugins/core/src/Routes/` that extends `Tribe\Libs\Routes\Abstract_REST_Route`

```php
    <?php declare( strict_types=1 );
	/**
	 * Sample REST route.
	 *
	 * @package Project
	 */
	
	namespace Tribe\Project\Routes;

	use Tribe\Libs\Routes\Abstract_REST_Route;

	/**
	 * Class for an example REST route.
	 */
	class Sample_REST_Route extends Abstract_REST_Route {
		/**
		 * Registers routes.
		 *
		 * @return void
		 */
		public function register(): void {
			register_rest_route(
				$this->get_project_namespace(),
				'/sample',
				[
					'methods' => \WP_REST_Server::READABLE,
					'callback' => [ $this, 'query' ],
					'args' => $this->get_supported_args(),
					'permission_callback' => '__return_true',
				]
			);
		}
		
		/**
		 * List of valid query parameters supported by the endpoint.
		 *
		 * @return array Valid parameters for this endpoint.
		 */
		public function get_supported_args(): array {
			return [
				'name' => [
					'type' => 'string',
					'default' => '',
					'description' => __( 'Example argument.', 'tribe' ),
				],
			];
		}
		
		/**
		 * Callback for REST endpoint.
		 *
		 * Example: https://square1.tribe/wp-json/tribe/v1/sample/?name=test
		 *
		 * @param \WP_REST_Request $request The rest request class.
		 * @return \WP_REST_Response|\WP_Error The response object, \WP_Error on failure.
		 */
		public function query( $request ) {
			return  rest_ensure_response(
				new \WP_Error(
					'sample_error',
					sprintf(
						esc_html__( 'Sample REST Endpoint Error. Params: {name: %1$s}', 'tribe' ),
						$request->get_param( 'name' )
					)
				)
			);
		}
	}
```
Inside `wp-content/plugins/core/src/Routes/Routes_Definer.php` add the class to the `Route_Definer::REST_ROUTES` array.

#### Rewrite Rule

```php
    <?php declare( strict_types=1 );
	/**
	 * Sample route.
	 *
	 * @package Project
	 */

	namespace Tribe\Project\Routes;

	use Tribe\Libs\Routes\Abstract_Route;

	/**
	 * Class to define a sample route.
	 */
	class Sample_Route extends Abstract_Route {
		/**
		 * Javascript configuration for this route.
		 *
		 * @param array $data The current core JS configuration.
		 * @return array Modified core JS configuration.
		 */
		public function js_config( array  $data = [] ): array {
			$data['FormSubmitEndpoint'] = rest_url( '/tribe/v1/submit-form/' );
			return  $data;
		}

		/**
		 * The request methods that are authorized on this route. Only GET
		 * is authorized by default.
		 *
		 * @return array Acceptable request methods for this route.
		 */
		public function get_request_methods(): array {
			return [
				\WP_REST_Server::READABLE,
			];
		}

		/**
		 * Returns the name for the route.
		 *
		 * @return  string The name for the route.
		 */
		public function get_name(): string {
			return 'sample';
		}

		/**
		 * Returns the pattern for the route.
		 *
		 * Example: https://square1.tribe/sample/2021/
		 *
		 * @return  string The pattern for the route.
		 */
		 public function get_pattern(): string {
			return '^sample\/?((?:19|20)\d{2}?)?\/?$';
		}

		/**
		 * Returns matches for the route.
		 *
		 * @return array Matches for the route.
		 */
		public function get_matches(): array {
			return [
				'year' => '$matches[1]',
			];
		}

		/**
		 * Returns the priority of the rewrite rule.
		 *
		 * @return  string
		 */
		public function get_priority(): string {
			return 'top';
		}

		/**
		 * Returns query var names for the route.
		 *
		 * @return array Query var names for the route.
		 */
		public function get_query_var_names(): array {
			return array_keys( $this->get_matches() );
		}

		/**
		 * The template to use for the route.
		 *
		 * @return  string The template name for the route.
		 */
		public function get_template(): string {
			return locate_template( 'routes/sample.php' );
		}

		/**
		 * Filter the title tag.
		 *
		 * @return  string Title for the page.
		 */
		public function get_title(): string {
			return esc_html__( 'Sample | Project', 'project' );
		}
	}
```

The `get_priority` method returns either "top" or "bottom" to determine where the route should be added in the rewrites array. The order is important since WordPress looks for the first available route that matches.

The `get_pattern` and `get_matches` methods are used to set query variables based on the regular expression set for the rewrite rule. In the example above, the first match after the route name will be set to the year. This will be important when setting up which template the rewrite rule uses.

#### Rewrite Rule Template
Rewrite rule templates are stored in `wp-content/themes/core/routes` by default, but can be overridden in the `get_template` method. An example template from the sample route above would be:

```php
    <?php declare( strict_types=1 );
	/**
	* Sample Route template.
	*
	* @package Project
	*/
	get_header();
	?>
        <p><?php  echo  esc_html__( 'Year: ') .  get_query_var( 'year' ); ?></p>
        <p><?php  esc_html_e( 'Sample Route', 'tribe' ); ?></p>
	<?php
	get_footer();
```

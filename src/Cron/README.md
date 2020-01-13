# moderntribe/square1-cron

This package provides some abstract classes to be used to create both custom schedules and cron jobs in WordPress.

### Example Custom Schedule

##### src/Cron/Thirty_Minutes.php

```
<?php declare( strict_types=1 );

namespace Tribe\Project\Cron;

use Tribe\Libs\Cron\Abstract_Schedule;

class Thirty_Minutes extends Abstract_Schedule {

    /** @var string array friendly key name **/
    const KEY      = 'thirty_minutes';

    /** @var int 30 minutes in seconds */
    const INTERVAL = 1800; 
}

```
### Example Cron Job

##### src/Cron/My_Cron.php

```
<?php declare( strict_types=1 );

namespace Tribe\Project\Cron;

use Tribe\Libs\Cron\Abstract_Cron;

class My_Cron extends Abstract_Cron {

    /**
    * Executes when the cron job runs
    *
    * @param  array  $args
    *
    * @return mixed
    */    
    public function run( array $args = [] ) {
        update_option( 'my_cron_option', 'ran', false );
    }

}

```

### Example Service Provider

##### src/Service_Providers/Cron_Provider.php

```
<?php

namespace Tribe\Project\Service_Providers;

use Pimple\Container;
use Tribe\Project\Container\Service_Provider;
use Tribe\Project\Cron\Thirty_Minutes;
use Tribe\Project\Cron\Schedule_Collection;
use Tribe\Project\Cron\My_Cron;
use Tribe\Libs\Cron\Abstract_Cron;

class Cron_Provider extends Service_Provider {   

    public function register( Container $container ) {
        $this->schedules( $container );
        $this->cron_jobs( $container );
    }
    
    private function schedules( Container $container ) {
        $container[ 'cron.thirty_minutes' ] = function() {
            return new Thirty_Minutes( 'Thirty Minutes' );
        };

        $container[ 'cron.schedule_collection' ] = function( Container $container ) {
            return new Schedule_Collection( $container[ 'cron.thirty_minutes' ] );
        };

        foreach( $container[ 'cron.schedule_collection' ] as $schedule ) {
            add_filter( 'cron_schedules', function( $schedules ) use ( $schedule ) {
                return array_merge( $schedules, $schedule->get() );
            }, 10, 1 );
        }
    }

    private function cron_jobs( Container $container ) {
        $container[ 'cron.my_cron' ] = function() {
            return new My_Cron( 'my_cron', Thirty_Minutes::KEY );
        };

        $container[ 'cron.cron_collection' ] = function( Container $container ) {
            return [
                $container[ 'cron.my_cron' ],
            ];
        };
        
        /** @var Abstract_Cron $cron */
        foreach( $container[ 'cron.cron_collection' ] as $cron ) {
            $cron->register( $cron );
        }
    }
}
```

Don't forget to register the above provider in `src/Core.php`  

### Activating Cron Jobs

Cron jobs need to be enabled, but only once. One solution is to use [square1-schema](https://github.com/moderntribe/square1-schema)
to do this.

##### src/Schema/Cron_Schema.php

```
<?php declare( strict_types=1 );

namespace Tribe\Project\Schema;

use Tribe\Libs\Schema\Schema;
use Tribe\Project\Cron\Abstract_Cron;

class Cron_Schema extends Schema {

protected $schema_version = 1;

    public function get_updates() {
        return [
            1 => [ $this, 'enable_cron_jobs' ]
        ];
    }
    
    /**
    * Enable Cron Jobs
    */
    public function enable_cron_jobs() {
        $container = tribe_project()->container();
        
        /** @var Abstract_Cron $cron */
        foreach( $container[ 'cron.cron_collection' ] as $cron ) {
            $cron->enable();        
        }
    }
}
```

##### src/Service_Providers/Schema_Provider.php

```
<?php

namespace Tribe\Project\Service_Providers;

use Pimple\Container;
use Tribe\Project\Container\Service_Provider;
use Tribe\Project\Schema\Cron_Schema;

class Schema_Provider extends Service_Provider {

    public function register( Container $container ) {
        $container[ 'schema.cron' ] = function () {
            return new Cron_Schema();
        };

        add_action( 'admin_init', function () use ( $container ) {
            if ( ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && ! wp_doing_cron() ) {
                if ( $container[ 'schema.cron' ]->update_required() ) {
                    $container[ 'schema.cron' ]->do_updates();
                }
            }
        }, 10, 0 );
    }

}
```
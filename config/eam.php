<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Holder Status Map
    |--------------------------------------------------------------------------
    |
    | Maps holder class name patterns (partial match on short class name) to
    | the asset status that should be applied when an asset is transferred
    | to that type of holder.
    |
    */

    'holder_status_map' => [
        'Employee' => 'assigned',
        'Warehouse' => 'available',
        'Maintenance' => 'maintenance',
        'Department' => 'assigned',
    ],

];

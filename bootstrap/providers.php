<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\JetstreamServiceProvider;
use App\Providers\MenuServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    JetstreamServiceProvider::class,
    FortifyServiceProvider::class,
    MenuServiceProvider::class,
];

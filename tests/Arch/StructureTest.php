<?php

declare(strict_types=1);

arch('examples')
    ->expect('App\Example')
    ->toBeClasses()
    ->toBeFinal()
    ->toOnlyBeUsedIn('App\Example');

arch('event')
    ->expect('App\Event')
    ->toBeClasses()
    ->toBeFinal()
    ->toBeReadonly();

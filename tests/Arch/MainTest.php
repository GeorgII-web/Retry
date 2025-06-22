<?php

declare(strict_types=1);

arch('strict types')
    ->expect('App')
    ->toUseStrictTypes();

arch('no debug')
    ->expect('App')
    ->not->toUse(['die', 'dd', 'dump']);

arch()->preset()->php();

arch()->preset()->security();

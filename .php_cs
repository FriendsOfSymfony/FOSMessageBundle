<?php

$finder = Symfony\CS\Finder::create()
    ->exclude('Resources')
    ->in(__DIR__)
;

return Symfony\CS\Config::create()
    ->finder($finder)
;

<?php

$context = \Timber\Timber::get_context();

return \Timber\Timber::render(
    [
        'views/page.twig',
    ],
    $context
);

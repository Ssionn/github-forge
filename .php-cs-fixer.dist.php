<?php declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

return (new Config())
    ->setRiskyAllowed(false)
    ->setParallelConfig(
        ParallelConfigFactory::detect()
    )
    ->setRules([
        '@PSR12' => true,
    ])
    ->setFinder(
        (new Finder())
            ->in(__DIR__)
            ->exclude('vendor')
            ->name('*.php')
    );

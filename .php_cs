<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . "/src")
    ->in(__DIR__ . "/tests");

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR1' => true,
        '@PSR2' => true,
        'blank_line_after_opening_tag' => true,
        'lowercase_static_reference' => true,
    ])
    ->setFinder($finder);
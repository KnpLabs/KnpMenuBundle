<?php

if (!$loader = @include __DIR__ . '/../vendor/autoload.php') {
    throw new \RuntimeException(
        'Please install project dependencies before running the test suite'
    );
}
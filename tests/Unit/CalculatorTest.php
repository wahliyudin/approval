<?php

use Wahliyudin\Calculator\Calculator;

it('can add two numbers', function () {
    $calculator = new Calculator();
    expect($calculator->add(1, 2))->toBe(3);
});

it('can subtract two numbers', function () {
    $calculator = new Calculator();
    expect($calculator->subtract(5, 3))->toBe(2);
});

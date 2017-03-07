<?php

if (!class_exists('PHPUnit\Framework\TestSuite') && class_exists('PHPUnit_Framework_TestSuite')) {
    class_alias('PHPUnit_Framework_TestSuite', 'PHPUnit\Framework\TestSuite');
    class_alias('PHPUnit_Framework_Test', 'PHPUnit\Framework\Test');
}
if (!class_exists('PHPUnit_Framework_TestCase') && class_exists('PHPUnit\Framework\TestCase')) {
    class_alias('PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase');
}

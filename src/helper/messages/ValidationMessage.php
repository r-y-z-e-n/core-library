<?php

namespace Ryzen\CoreLibrary\helper\messages;

class ValidationMessage
{
    /**
     * @var string
     */

    public static string $required = '{key} cannot be empty and is required.';
    public static string $minimum  = '{key} value should be greater than {val}';
    public static string $maximum  = '{key} value should be smaller than {val}';
    public static string $unique   = '{key} already exists';
    public static string $matches  = '{key} should match the {matching.key}';
    public static string $date     = '{key} must be in a validate date format (Year-month-day)';
}
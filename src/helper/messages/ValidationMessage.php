<?php

namespace Ryzen\CoreLibrary\helper\messages;

class ValidationMessage
{
    public static string $required      = '{key} is required.';
    public static string $minimum       = '{key} length should be greater than {val}';
    public static string $maximum       = '{key} length should be smaller than {val}';
    public static string $unique        = '{key} is already associated with another account or service';
    public static string $matches       = '{key} should match the {matching.key}';
    public static string $date          = '{key} must be in an validate date format (Year-month-day)';
    public static string $email         = '{key} should be a valid E-Mail address';
    public static string $url           = '{key} should be a valid URL address';
    public static string $starts_with   = '{key} should start with letter {val}';
    public static string $end_with      = '{key} should end with letter {val}';
}
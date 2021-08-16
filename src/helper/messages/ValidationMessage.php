<?php

namespace Ryzen\CoreLibrary\helper\messages;

class ValidationMessage
{
    public static string $required      = '{key} cannot be empty and is required.';
    public static string $minimum       = '{key} value should be greater than {val}';
    public static string $maximum       = '{key} value should be smaller than {val}';
    public static string $unique        = '{key} already exists';
    public static string $matches       = '{key} should match the {matching.key}';
    public static string $date          = '{key} must be in a validate date format (Year-month-day)';
    public static string $email         = '{key} should be a valid E-Mail address';
    public static string $url           = '{key} should be a valid URL address';
    public static string $starts_with   = '{key} should have the starting letter {val}';
    public static string $end_with      = '{key} should have the ending letter {val}';
}
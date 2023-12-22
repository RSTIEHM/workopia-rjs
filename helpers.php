<?php

/**
 * 
 * Get the base path ============
 *  @param string $path 
 *  @return string
 * 
 */

function basePath($path = '')
{
    return __DIR__ . '/' . $path;
}

/**
 * 
 * Load A View ==============
 * @param string $name
 * @return void
 * 
 * 
 */

function loadView($name, $data = [])
{
    $viewPath = basePath("App/views/{$name}.view.php");
    if (file_exists($viewPath)) {
        extract($data);
        require $viewPath;
    } else {
        echo "View '{$name} not found'";
    }
}

/**
 * 
 * Load A Partial
 * @param string $name
 * @return void
 * 
 * 
 */

function loadPartial($name)
{
    $partialPath = basePath("App/views/partials/{$name}.php");
    if (file_exists($partialPath)) {
        require $partialPath;
    } else {
        echo "View '{$name} not found'";
    }
}

/**
 * 
 * Inspect A Value
 * @param mixed $data
 * @return void
 * 
 * 
 */

function inspect($data)
{
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
}

/**
 * 
 * Inspect A Value And Die
 * @param mixed $data
 * @return void
 * 
 * 
 */

function inspectAndDie($data)
{
    echo "<pre>";
    die(var_dump($data));
    echo "</pre>";
}

/**
 * Format A Number with Comma
 * 
 * @param string $number 
 * @return string Formated number
 * 
 */

function formatSalary($salary)
{
    return '$'   . number_format(floatval($salary));
}

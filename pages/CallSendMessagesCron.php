<?

    $shell_output = shell_exec('php -c php.ini -f SendMessagesCron.php');
    echo "<pre>$shell_output</pre>";


?>

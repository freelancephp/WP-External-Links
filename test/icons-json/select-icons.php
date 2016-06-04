<?php

// get JSON from file
$dashiconsJSON = file_get_contents(__DIR__ . '/json/dashicons.json');
$dashicons = json_decode($dashiconsJSON);

$fontawesomeJSON = file_get_contents(__DIR__ . '/json/fontawesome.json');
$fontawesome = json_decode($fontawesomeJSON);

?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fonts</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel='stylesheet' id='dashicons-css'  href='dashicons.css' type='text/css' media='all'>
    <script type='text/javascript' src='https://code.jquery.com/jquery-1.11.3.min.js'></script>
<style>
    .select-fa-icon {font-family:'FontAwesome'; font-size:1.5em; padding:0.5em; }
    .select-dash-icon { font-family:'dashicons'; font-size:1.5em; padding:0.5em; }
</style>
</head>
<body>
    <h1>Fonts</h1>

    <h2><a href="https://fortawesome.github.io/Font-Awesome/icons/" target="_blank">Font Awesome Font</a></h2>
    <p>
        <select class="select-fa-icon">
            <?php foreach ($dashicons->icons as $item): ?>
            <option value="<?php echo $item->className; ?>"><?php echo '&#x' . $item->unicode; ?></option>
            <?php endforeach; ?>
        </select>
    </p>

    <h2><a href="https://developer.wordpress.org/resource/dashicons/" target="_blank">Dashicons Font</a></h2>
    <p>
        <select class="select-dash-icon">
            <?php foreach ($fontawesome->icons as $item): ?>
            <option value="<?php echo $item->className; ?>"><?php echo '&#x' . $item->unicode; ?></option>
            <?php endforeach; ?>
        </select>
    </p>
</body>
</html>

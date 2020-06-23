<?php
// since we unconditionally include this file, make sure we don't define the
// same function twice
if (!function_exists('startAutoTemplate')) {
    // this function automagically templates the main file by starting output
    // buffering and registering a shutdown function to output your script's
    // HTML inside the template
    function startAutoTemplate(string $title, $menuItems = []): void
    {
        // if menuItems is a function (which might be necessary for login/
        // logout links) we need to wait to evaluate it until the end of the
        // script. If not we will normalize it to a function.
        if (!is_callable($menuItems)) {
            $menuItems = function () use ($menuItems) {
                return $menuItems;
            };
        }
        
        ob_start();
        register_shutdown_function(function () use ($title, $menuItems) {
            $pageHtml = ob_get_clean();
?><!DOCTYPE html>
<html>
<head>
    <title><?=$title?></title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #334467;
            font-family: sans-serif;
            font-size: 12pt;
            color: #e5eeff;
        }
        h1, h2, h3 {
            margin: 0;
            padding: 0;
            color: white;
        }
        a {
            color: #16a085;
        }
        a:hover {
            text-decoration: none;
        }
        #header-wrapper {
            overflow: hidden;
            padding: 0;
        }
        #header {
            text-align: center;
            margin: 0 auto;
        }
        #header h1 {
            display: inline-block;
            margin-bottom: .2em;
            padding: .2em .9em;
            font-size: 3.5em;
        }
        #header a {
            text-decoration: none;
            color: white;
        }
        #menu ul {
            display: inline-block;
            padding: 0 2em;
            text-align: center;
        }
        #menu li {
            display: inline-block;
        }
        #menu li a {
            display: inline-block;
            padding: 1.3em 1.5em;
            text-decoration: none;
            font-size: .9em;
            font-weight: 600;
            text-transform: uppercase;
            outline: 0;
            color: white;
        }
        #menu li:hover a {
            background: #3e5a99;
        }
        #page-wrapper {
            overflow: hidden;
            padding: 0;
            margin: 0 1em;
        }
    </style>
</head>
<body>
    <div id="header-wrapper">
        <div id="header" class="container">
            <h1><?=$title?></h1>
            <div id="menu">
                <ul>
                    <?php
                        foreach ($menuItems() as $title => $link) {
                            $title = htmlspecialchars($title, ENT_HTML5);
                            $link = htmlspecialchars($link, ENT_HTML5 | ENT_COMPAT);
                            echo "<li><a href=\"$link\">$title</a></li>\n";
                        }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <div id="page-wrapper">
<?=$pageHtml?>
    </div>
</body>
</html><?php
        });
    }
}

<?php
namespace AutoTemplate;

class AutoTemplate
{
    private array $shutdownFuncs = [];
    public string $siteTitle;
    public string $pageTitle;
    public array $menuItems;
    public string $headHTML = '';
    // this is only for use in shutdown callbacks
    public string $bodyHTML = '';
    public string $css = '
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
    ';
    
    public function __construct(
        string $siteTitle,
        array $menuItems = [],
        ?string $pageTitle = null
    ) {
        // if a page title was not given use the site title
        $pageTitle ??= $siteTitle;
        
        $this->siteTitle = $siteTitle;
        $this->pageTitle = $pageTitle;
        $this->menuItems = $menuItems;
        
        // stop printing output to the browser and start saving it instead
        ob_start();
        
        // register a shutdown function to print all the saved stuff inside
        // our template. I'm not totally sure why we have to wrap the method
        // in an arrow function like this.
        register_shutdown_function(fn() => $this->runTemplate());
    }
    
    // this sets the page title prefixed by the site title. For a site title
    // of 'Example' and a page title of 'Home', the page title would end up
    // being 'Example - Home'
    public function setPageTitle(string $title): void
    {
        $this->pageTitle = "{$this->siteTitle} - {$this->pageTitle}";
    }
    
    // register a function to be called right before the template runs. This
    // can be used to make any last minute changes (like changing the menu
    // to have a log-in or log-out button based on weather of not the user
    // is logged in). The first function added will be the last function
    // called. The AutoTemplate instance will be passed as the only argument
    public function registerShutdownFunc(callable $func): void
    {
        array_unshift($this->shutdownFuncs, $func);
    }
    
    // write something to the <head> part of the template
    public function echoHead(string $content): void
    {
        $this->headHTML .= $content;
    }
    
    // write something to the <style> part of the template
    public function echoCSS(string $content): void
    {
        $this->css .= $content;
    }
    
    private function runTemplate(): void
    {
        // get main HTML from the output buffer
        $this->pageHtml = ob_get_clean();
        
        // call all the shutdown functions
        foreach ($this->shutdownFuncs as $func) {
            $func($this);
        }
        
        // start printing the template
?><!DOCTYPE html>
<html>
<head>
    <title><?=$this->pageTitle?></title>
    <style><?=$this->css?></style>
    <?=$this->headHTML?>
</head>
<body>
    <div id="header-wrapper">
        <div id="header" class="container">
            <h1><?=$this->siteTitle?></h1>
            <div id="menu">
                <ul>
                    <?php
                        foreach ($this->menuItems as $title => $link) {
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
        // end of printing the template
    }
}

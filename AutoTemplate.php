<?php
namespace AutoTemplate;

class AutoTemplate
{
    private array $shutdownFuncs = [];
    private bool $obActive = true;
    public float $responsiveWidth;
    // read only unless you know what you are doing
    public bool $enabled = true;
    public string $siteTitle;
    public string $pageTitle;
    public array $menuItems;
    public string $headHTML = '';
    // this is only for use in shutdown callbacks
    public string $bodyHTML = '';
    public string $css = '';
    
    public function __construct(
        string $siteTitle,
        array $menuItems = [],
        ?string $pageTitle = null,
        float $responsiveWidth = 45
    ) {
        // if a page title was not given use the site title
        $pageTitle ??= $siteTitle;
        
        $this->siteTitle = $siteTitle;
        $this->pageTitle = $pageTitle;
        $this->menuItems = $menuItems;
        $this->responsiveWidth = $responsiveWidth;
        
        // stop printing output to the browser and start saving it instead
        ob_start();
        
        // register a shutdown function to print all the saved stuff inside
        // our template. I'm not totally sure why we have to wrap the method
        // in an arrow function like this.
        register_shutdown_function(fn() => $this->runTemplate());
    }
    
    // allows you to abort applying the template
    public function disable(): void
    {
        $this->enabled = false;
        
        // in case we already got the contents of the output buffer
        echo $this->bodyHTML;
        $this->bodyHTML = '';
        
        if ($this->obActive) {
            // stop output buffering and send contents of the current buffer
            ob_end_flush();
            $this->obActive = false;
        }
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
        // if we got disabled, do nothing
        if (!$this->enabled) {
            return;
        }
        
        // get main HTML from the output buffer
        $this->pageHtml = ob_get_clean();
        $this->obActive = false;
        
        // call all the shutdown functions
        foreach ($this->shutdownFuncs as $func) {
            $func($this);
            // if a shutdown function disabled us, stop
            if (!$this->enabled) {
                return;
            }
        }
        
        // start printing the template
?><!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?=$this->pageTitle?></title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&family=Oswald&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: "Open Sans", sans-serif;
        }
        header {
            display: flex;
        }
        #logo {
            height: 1.5em;
            margin-right: 0.5em
        }
        header h1 {
            display: inline-block;
            font-family: "Oswald", sans-serif;
            font-weight: lighter;
            font-size: 120%;
            flex-grow: 1;
            margin: 0;
        }
        nav {
            display: inline-block;
        }
        nav  ul {
            display: inline-block;
            margin: 0;
            padding: 0;
        }
        nav li {
            display: inline-block;
            margin: .5em;
        }
        nav li a {
            font-size: 90%;
            text-transform: uppercase;
            color: inherit;
            text-decoration: none;
        }
        @media(max-width:  <?=$this->responsiveWidth + 15?>em) {
            #logo {
                display: none;
            }
        }
        @media(max-width: <?=$this->responsiveWidth?>em) {
            header h1 {
                display: none;
            }
        }
    </style>
    <style><?=$this->css?></style>
    <link rel="icon" href="data:image/svg+xml;base64,<?=base64_encode(file_get_contents(__DIR__ . '/favicon.svg'))?>" />
    <?=$this->headHTML?>
</head>
<body>
    <header>
        <?php require __DIR__ . '/logo.svg' ?>
        <h1><?=$this->siteTitle?></h1>
        <nav>
            <ul><?php
                foreach ($this->menuItems as $title => $link) {
                    $title = htmlspecialchars($title, ENT_HTML5);
                    $link = htmlspecialchars($link, ENT_HTML5 | ENT_COMPAT);
                    echo "<li><a href=\"$link\">$title</a></li>";
                }
            ?></ul>
        </nav>
    </header>
<?=$this->pageHtml?>
</body>
</html><?php
        // end of printing the template
    }
}

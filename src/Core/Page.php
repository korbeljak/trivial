<?php namespace Core;

class Page
{
    /** Page Title. */
    protected $title;
    
    /** Page Heading. */
    protected $heading;
    
    /** Page Description in the heading. */
    protected $headerDescription;
    
    /** Page Description in the content. */
    protected $contentDescription;
    
    /** Page Content. */
    protected $content;
    
    /** Page's Keywords. */
    protected $keywords;
    
    /** Page Theme. */
    protected $themePath;
    
    /** Page Theme's template path. */
    protected $templatePath;
    
    /** Page's properties. */
    protected $properties;
    
    public static $defaultTitle;
    public static $defaultDescription;
    public static $defaultKeywords = array();
    public static $defaultThemePath;
    
    public static function SetDefaultTitle($title)
    {
        self::$defaultTitle = $title;
    }
    public static function SetDefaultDescription($description)
    {
        self::$defaultDescription = $description;
    }
    public static function SetDefaultKeywords($keywords)
    {
        self::$defaultKeywords = explode(",", $keywords);
    }
    public static function SetDefaultThemePath($themePath)
    {
        self::$defaultThemePath = $themePath;
    }
    
    /**
     * Constructor. Any page needs title, description and keywords.
     * 
     * @param string $title Page Title.
     * @param string $headerDescription Page Description for the header.
     * @param array $keywords Page's Keywords.
     * @param string $themePath Page Theme.
     * @param string $content Page content.
     * @param string $contentDescription Page description beneath the content.
     * @param string $templatePath Page Theme's template path.
     * @param array $properties Page's properties.
     */
    public function __construct(string $title,
                                string $headerDescription,
                                array $keywords,
                                string $templatePath,
                                string $content = null,
                                string $contentDescription = null,
                                string $themePath = null,
                                array $properties = null)
    {
        $this->title = self::$defaultTitle." &ndash; ".$title;
        $this->heading = $title;
        $this->headingDescription = self::$defaultDescription." ".$headerDescription;
        $this->content = $content;
        $this->contentDescription = $contentDescription;
        $this->keywords = join(",", self::$defaultKeywords + $keywords);
        $this->templatePath = $templatePath;
        if ($themePath != null)
        {
            $this->themePath = $themePath;
        }
        else
        {
            $this->themePath = self::$defaultThemePath;
        }
        
        if ($properties != null)
        {
            $this->properties = $properties;
        }
        else
        {
            $this->properties = array();
        }
    }
    
    public function Render()
    {
        include $this->themePath.DS.$this->templatePath.".php";
    }
    
    public function RenderMainScriptArea()
    {
        echo "";
        /*
        $mainScArea  = "<script type=\"text/javascript\">\n";
        $mainScArea  .= ILVL[1]."window.validationRules = new Array();";
        $mainScArea .= "</script>\n";
        
        echo $mainScArea;*/
    }
    
    private static function GetNotFoundFormat($path)
    {
        // Get the extension.
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        switch ($ext)
        {
            case "png":
            case "jpg":
            case "gif":
            case "jpeg":
            case "ico":
                return "png";
            case "css":
            case "js":
            default:
                return "txt";
        }
    }
    
    public static function LibGetAsset(string $path)
    {
        $assetPath = "assets/".$path;
        if (!file_exists($assetPath))
        {
            // Try core assets instead.
            $corePath = CORE_PATH.DS."coreassets".DS.$path;
            
            if (file_exists($corePath))
            {
                $assetPath = "coreassets/".$path;
            }
            else
            {
                // Not found anywhere.
                $assetPath = "assets".DS."notfound.".self::GetNotFoundFormat($path);
            }
        }
        
        return $assetPath;
    }
    
    public function Asset(string $path)
    {
        echo $this->GetAsset($path);
    }
    

    public function GetAsset(string $path)
    {
        return self::LibGetAsset($path);
    }
    
    public function ImgFromText(string $text, int $charSize, string $fontPath)
    {
        if (empty($text) || $charSize <= 0 || !file_exists(CFG_DEFAULT_ASSET_PATH.DS.$fontPath))
        {
            \Core\Logger::Log(LOG_ERR,
                            "Cannot create an image, wrong parameters.",
                            \Core\Logger::O_SYSTEM_ALL);
            echo "assets".DS."notfound.png";
        }
        else
        {
            // Auto-generated filename.
            $name = base64_encode(md5("sa".$text."lt", true));
            $name = str_replace("/", "_l", $name);
            $name = str_replace("\\", "_w", $name);
            $fileName = CFG_DEFAULT_ASSET_PATH . DS ."generated".DS. $name . ".png";
            $assetName = "assets".DS."generated".DS. $name . ".png";
            
            if (!file_exists($fileName))
            {

                // create a bounding box for the text
                $boundBox = imagettfbbox($charSize,
                                0, CFG_DEFAULT_ASSET_PATH.DS.$fontPath, $text);
                $boundBoxHeight = $boundBox[3] - $boundBox[5];
                $boundBoxWidth = $boundBox[4] - $boundBox[6];
                
                // Create the image:
                $imgGd = imagecreatetruecolor($boundBoxWidth, $boundBoxHeight);
                $colorGrey = imagecolorallocate($imgGd, 138, 138, 138);
                $colorWhite = imagecolorallocate($imgGd, 255, 255, 255);
    
                imagefilledrectangle ($imgGd, 0, 0, $boundBoxWidth - 1, $boundBoxHeight - 1, $colorWhite);
                
                $x = 0;
                $y = $charSize;
                imagettftext($imgGd, $charSize, 0, $x, $y, $colorGrey, CFG_DEFAULT_ASSET_PATH.DS.$fontPath, $text);
    
                imagepng($imgGd, $fileName);
                imagedestroy($imgGd);
            }
            
            echo $assetName;
        }
    }
    
    public function __get($name)
    {
        if (isset($this->name))
        {
            return $this->name;
        }
        if (isset($this->properties[$name]))
        {
            return $this->properties[$name];
        }
        
        return null;
    }
    
    public static function GetHomepageLink()
    {
        echo "/";
    }
    
    public static function GetLink(string $slug)
    {
        
    }
}
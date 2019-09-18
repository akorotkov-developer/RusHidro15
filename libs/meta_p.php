<?
//error_reporting(0);
global $config;
$config['url_fonts'] = $config['DOCUMENT_ROOT']."/libs/fonts/"; // адрес до шрифтов
$config['form_img'] = $config['DOCUMENT_ROOT']."/libs/imgcode/"; // адрес до каталога с картинками
$config['form_imgurl'] = $config['server_url']."libs/imgcode/"; // адрес до каталога с картинками
/*/////////////////////////////////////////////////////////////////////////////////////////////*/

//////////////////////////////////////
 function digitimage()
        {
                 global $config;
                // $fie_name =  $_SERVER[DOCUMENT_ROOT].'/libs/634014.jpg';
                 $x_img = 150; $y_img = 90; //ширина и высота
                 $numsymb_img=5; //кол-во символов
                 $bgcolor_img = 0xFFFFFF; //задний фон
                // $bgcolor_img = 255255255; //задний фон
                 $font_size_from = 23;
                 $font_size_dis = 23;
                 function imagettftext_cr(&$im, $size, $angle, $x_img, $y_img, $color, $fontfile, $text) {
                        $bbox = imagettfbbox($size, $angle, $fontfile, $text);
                        $dx = ($bbox[2]-$bbox[0])/2.0 - ($bbox[2]-$bbox[4])/2.0; // deviation left-right
                        $dy = ($bbox[3]-$bbox[1])/2.0 + ($bbox[7]-$bbox[1])/2.0; // deviation top-bottom
                        $px = $x_img-$dx;
                        $py = $y_img-$dy;
                        imagettftext($im, $size, $angle, $px, $py, $color, $fontfile, $text);
                        return $bbox[2] - $bbox[0];
                }
                 $fonts = array('gothic');
               //  error_reporting(E_ALL);
              
                $im = @ImageCreateTrueColor ($x_img, $y_img)or die ("Ошибка инициализации библиотеки GD");
             if(is_file($fie_name))
             {
             	$im =   @ImageCreateFromjpeg($fie_name)or die ("Ошибка инициализации библиотеки GD");
             	ImageFilledRectangle ($im, 0, 0, $x_img - 1, $y_img - 1);
             }else {
             	ImageFilledRectangle ($im, 0, 0, $x_img - 1, $y_img - 1,$bgcolor_img);
             }
                 
                 $tx = 15 + rand(0, 5);
                 $s = '';
                 for ($i = 0; $i < $numsymb_img; $i++) {
                        $n = rand(0, 9); // случайная цифра
                        $nx = $tx; $ny = 25 + rand(0, 35); // координаты цифры
                        $f = rand(5, 10); // размер цифры
                        $font = $fonts[rand(0, count($fonts) - 1)] . '.ttf';
                        $size = rand($font_size_from,$font_size_dis);
                        $angle = -40 + rand(0, 80);
                        $color = rand(0, 180) * 65536 + rand(0, 180) * 256 + rand(0, 180);
                      //  $color = 255255255;
                        //$color = FFFFFF;
                        $dx = imagettftext_cr ($im, $size, $angle, $nx, $ny, $color, $config['url_fonts']."$font", $n );
                        $s .= $n;
                     $tx += 8 + $dx;
                };
                // Формирование пикселей
                /*$n = rand(100,150);
                for ($i = 0; $i < $n; $i++)
                {
                        $color = rand(100, 255) * 65536 + rand(100, 255) * 256 + rand(100, 255);
                        $nx = rand(1, $x_img - 2);
                        $ny = rand(1, $y_img - 2);
                        imagesetpixel($im, $nx, $ny, $color);
                        imagesetpixel($im, $nx - 1, $ny, $color);
                        imagesetpixel($im, $nx + 1, $ny, $color);
                        imagesetpixel($im, $nx, $ny - 1, $color);
                        imagesetpixel($im, $nx, $ny + 1, $color);
                };*/
                      $tmp_cifr_post = md5(base64_encode($config['md5'].$s));
                      ImageJpeg ($im, $config['form_img'].$tmp_cifr_post.".jpg", 50);
                      @imagedestroy($im);
                      foreach (glob($config['form_img']."*.jpg") as $filename)
                      if(date("YmdHis", @filemtime($filename))<date("YmdHis", mktime(date("H"), date("i")-5, date("s"), date("m"), date("d"), date("Y"))))
                      @unlink($filename);
                      return $tmp_cifr_post;
        }      
if (!defined("PATH_SEPARATOR"))
define("PATH_SEPARATOR", getenv("COMSPEC")? ";" : ":");
ini_set("include_path", ini_get("include_path").PATH_SEPARATOR.dirname(__FILE__));
class HTML_SemiParser
{   var $urlpars;
    var $sp_tags = array();
    var $sp_cons = array();
    var $sp_res = array();
    var $sp_precachers = array();
    var $sp_postprocs = array();
    var $sp_preprocs = array();
    var $sp_quoteHandler = null;
    var $sp_dequoteHandler = null;
    var $sp_preTag = "tag_";
    var $sp_preCon = "container_"; 
    var $sp_preRe  = "re_"; 
    var $sp_reTagIn = '(?>(?xs) (?> [^>"\']+ | " [^"]* " | \' [^\']* \' )* )';
    var $sp_IGNORED = array('script', 'iframe', 'textarea', 'select', 'title');
    var $sp_SKIP_IGNORED = true;
    var $sp_replaceHash; 
    
    function HTML_SemiParser()
    {
        // Add child handlers.
        $this->sp_selfAdd = true;
        $this->addObject($this);
        unset($this->sp_selfAdd);

        // Initialize quoters.
        $this->sp_quoteHandler = 'htmlspecialchars';
        $this->sp_dequoteHandler = array(get_class($this), '_unhtmlspecialchars');

        // Generate unique hash.
        static $num = 0;
        $uniq = md5(microtime() . ' ' . ++$num . ' ' . getmypid());
        $this->sp_replaceHash = $uniq;
    }
    function addTag($tagName, $handler, $atFront=false)
    {  
        $tagName = strtolower($tagName);
        if (!isSet($this->sp_tags[$tagName])) $this->sp_tags[$tagName] = array();
        if (!$atFront) array_push($this->sp_tags[$tagName], $handler);
        else array_unshift($this->sp_tags[$tagName], $handler);
    }
    function addContainer($tagName, $handler, $atFront=false)
    {
        $tagName = strtolower($tagName);
        if (!isSet($this->sp_cons[$tagName])) $this->sp_cons[$tagName] = array();
        if (!$atFront) array_push($this->sp_cons[$tagName], $handler);
        else array_unshift($this->sp_cons[$tagName], $handler);
    }
    function addReplace($re, $handler, $atFront=false)
    {
        if (!isSet($this->sp_res[$re])) $this->sp_res[$re] = array();
        if (!$atFront) array_push($this->sp_res[$re], $handler);
        else array_unshift($this->sp_res[$re], $handler);
    }
    function addObject(&$obj, $noPrecache=false, $atFront=false)
    {
        foreach (get_class_methods($obj) as $m) {
            if (strpos($m, $this->sp_preTag) === 0) {
                $this->addTag(substr($m, strlen($this->sp_preTag)), array(&$obj, $m), $atFront);
            }
            if (strpos($m, $this->sp_preCon) === 0) {
                $this->addContainer(substr($m, strlen($this->sp_preCon)), array(&$obj, $m), $atFront);
            }
            if (strpos($m, $this->sp_preRe) === 0) {
                $meth = substr($m, strlen($this->sp_preRe));
                $re = call_user_func(array(&$obj, $m));
                if ($re !== false && $re !== null) {
                    $this->addReplace($re, array(&$obj, $meth), $atFront);
                }
            }
        }
        if (!isset($this->sp_selfAdd)) {
            foreach (array('precacheTags'=>'sp_precachers', 'postprocText'=>'sp_postprocs', 'preprocText'=>'sp_preprocs') as $pname=>$var) {
                if (method_exists($obj, $pname)) {
                    if (!$atFront) array_push($this->$var, array(&$obj, $pname));
                    else array_unshift($this->$var, array(&$obj, $pname));
                }
            }
        }
    }
    function quoteHandler($value)
    {
        return call_user_func($this->sp_quoteHandler, $value);
    }
    function dequoteHandler($value)
    {
        return call_user_func($this->sp_dequoteHandler, $value);
    }
    function _unhtmlspecialchars($value)
    {
        static $sp_trans = null;
        if (!$sp_trans) {
            $sp_trans = array_flip(get_html_translation_table(HTML_SPECIALCHARS));
            $sp_trans['&#039;'] = "'";
        }
        return strtr($value, $sp_trans);
    }
    function process($buf)
    {
        $reTagIn = $this->sp_reTagIn;
        $new = $this->preprocText($buf);
        if ($new !== null) $buf = $new;
        $this->sp_ignored = array();
        if ($this->sp_SKIP_IGNORED) {
            $reIgnoredNames = join("|", $this->sp_IGNORED);
            $reIgnored = "{(<($reIgnoredNames) (?> \s+ $reTagIn)? >) (.*?) (</\\2>)}six";
            $oldLimit = ini_get('pcre.backtrack_limit');
            ini_set('pcre.backtrack_limit', 1024 * 1024 * 10);
            $buf = preg_replace_callback(
                $reIgnored,
                array(&$this, "_callbackIgnored2Hash"),
                $buf
            );
            ini_set('pcre.backtrack_limit', $oldLimit);
        }
        $sp_ignored = array($this->sp_ignored, array_keys($this->sp_ignored), array_values($this->sp_ignored));
        unset($this->sp_ignored);
        if ($this->sp_res) {
            foreach ($this->sp_res as $re => $handlers) {
                foreach ($handlers as $h) {
                    $buf = preg_replace_callback($re, $h, $buf);
                }
            }
        }
        $hashlen = strlen($this->sp_replaceHash) + 10;
        $reTagNames = join("|", array_keys($this->sp_tags));
        $reConNames = join("|", array_keys($this->sp_cons));
        $infos = array();
        if ($this->sp_tags)
            $infos["sp_tags"] = "/( <($reTagNames) (?> (\s+ $reTagIn) )? > () )/isx";
        if ($this->sp_cons)
            $infos["sp_cons"] = "/( <($reConNames) (?> (\s+ $reTagIn) )? > (.*?) (?: <\\/ \\2 \\s* > | \$ ) )/isx";
        foreach ($infos as $src => $re) {
            $chunks = preg_split($re, $buf, 0, PREG_SPLIT_DELIM_CAPTURE);
            $textParts = array($chunks[0]); // unparsed text parts
            $foundTags = array();           // found tags
            for ($i=1, $n=count($chunks); $i<$n; $i+=5) {
                $tOrig    = $chunks[$i];     // - original tag text
                $tName    = $chunks[$i+1];   // - tag name
                $tAttr    = $chunks[$i+2];   // - tag attributes
                $tBody    = $chunks[$i+3];   // - container body
                $tFollow  = $chunks[$i+4];   // - following unparsed text block

                $tag = array();
                $this->parseAttrib($tAttr, $tag);
                $tag['_orig'] = $tOrig;
                $tag['_tagName'] = $tName;
                if ($src == "sp_cons") {
                    if (strlen($tBody) < $hashlen && isset($sp_ignored[0][$tBody])) {

                        $tBody = $sp_ignored[0][$tBody];
                    } else {
                        $tBody = str_replace($sp_ignored[1], $sp_ignored[2], $tBody);
                    }
                    $tag['_text'] = $tBody;
                } else if (substr($tAttr, -1) == '/') {
                    $tag['_text'] = null;
                }
                $foundTags[] = $tag;
                $textParts[] = $tFollow;
            }

            $origTags = $foundTags;
            $this->precacheTags($foundTags);
            $buf = $textParts[0];
            for ($i=0, $n=count($foundTags); $i<$n; $i++) {
                $tag = $this->_runHandlersForTag($foundTags[$i]);
                if (!is_array($tag)) {
                    $buf .= $tag;
                } else {
                    $left  = isset($tag['_left'])?  $tag['_left']  : ""; unset($tag['_left']);
                    $right = isset($tag['_right'])? $tag['_right'] : ""; unset($tag['_right']);
                    if (!isset($tag['_orig']) || $tag !== $origTags[$i]) {
                        $text = $this->makeTag($tag);
                    } else {
                        $text = $tag['_orig'];
                    }
                    $buf .= $left . $text . $right;
                }
                $buf .= $textParts[$i+1];
            }
        }
        $buf = str_replace($sp_ignored[1], $sp_ignored[2], $buf);

        $new = $this->postprocText($buf);
        if ($new !== null) $buf = $new;

        return $buf;
    }
    function makeTag($attr)
    {
        $s = "";
        foreach($attr as $k => $v) {
            if ($k == "_text" || $k == "_tagName" || $k == "_orig") continue;
            $s .= " " . $k;
            if ($v !== null) $s .= '="' . $this->quoteHandler($v) . '"';
        }
        if (!@$attr['_tagName']) $attr['_tagName'] = "???";

        if (!array_key_exists('_text', $attr)) { // do not use isset()!
            $tag = "<{$attr['_tagName']}{$s}>";
        } else if ($attr['_text'] === null) { // null
            $tag = "<{$attr['_tagName']}{$s} />";
        } else {
            $tag = "<{$attr['_tagName']}{$s}>{$attr['_text']}</{$attr['_tagName']}>";
        }
        return $tag;
    }
    function precacheTags(&$foundTags)
    {
        foreach ($this->sp_precachers as $pk) {
            call_user_func_array($pk, array(&$foundTags));
        }
    }
    function preprocText($buf)
    {
        foreach ($this->sp_preprocs as $pk) {
            $new = call_user_func($pk, $buf);
            if ($new !== null) $buf = $new;
        }
        return $buf;
    }
    function postprocText($buf)
    {
        foreach ($this->sp_postprocs as $pk) {
            $new = call_user_func($pk, $buf);
            if ($new !== null) $buf = $new;
        }
        return $buf;
    }
    function _callbackIgnored2Hash($m)
    {
        static $counter = 0;
        $hash = $this->sp_replaceHash . ++$counter . "|";
        $this->sp_ignored[$hash] = $m[3];
        return $m[1] . $hash . $m[4];
    }
    function _runHandlersForTag($tag)
    {
        $tagName = strtolower($tag['_tagName']);
        if (isset($tag['_text'])) {
            $handlers = $this->sp_cons[$tagName];
        } else {
            $handlers = $this->sp_tags[$tagName];
        }
        for ($i = count($handlers)-1; $i >= 0; $i--) {
            $h = $handlers[$i];
            $result = call_user_func($h, $tag, $tagName);
            if ($result !== false && $result !== null) {
                if (!is_array($result)) return $result;
                $tag = $result;
            }
        }
        return $tag;
    }
    function parseAttrib($body, &$attr)
    {
        $preg = '/([-\w:]+) \s* ( = \s* (?> ("[^"]*" | \'[^\']*\' | \S*) ) )?/sx';
        $regs = null;
        preg_match_all($preg, $body, $regs);
        $names = $regs[1];
        $checks = $regs[2];
        $values = $regs[3];
        $attr = array();
        for ($i = 0, $c = count($names); $i < $c; $i++) {
            $name = strtolower($names[$i]);
            if (!@$checks[$i]) {
                $value = $name;
            } else {
                $value = $values[$i];
                if ($value[0] == '"' || $value[0] == "'") {
                    $value = substr($value, 1, -1);
                }
            }
            if (strpos($value, '&') !== false)
                $value = $this->dequoteHandler($value);
            $attr[$name] = $value;
        }
    }
}
class HTML_MetaForm extends HTML_SemiParser
{
    var $MF_ERRORS = array(
        'hidden_field_required' => 'Hidden field "%s" required for POST form!',
        'bad_signature'         => 'Form data signature check failed!',
    );
    var $MF_USE_VALUES  = true;
    var $MF_STORE_LABELS = true;
    var $MF_META_ELT    = "HTML_MetaForm";
    var $MF_USE_SESSION = false;
    var $MF_META_PREFIX = "meta:";
    var $MF_REQUEST_URI = null;
    var $MF_POST = null;
    var $MF_SIGN_SUFFIX = null;
    var $_mf_meta          = null;
    var $_mf_sign          = null;
    var $_mf_hash          = null;
    var $_mf_collectLabels = array();
    var $_mf_collectForms  = array();
    var $_mf_collectMetas  = array();
    var $_mf_collectHashes = null;
    var $_mf_errorHandler  = null;
    var $_mf_post          = null;
    var $_mf_lastError     = null;

    function HTML_MetaForm($signature)
    {
        global $config;
        $this->HTML_SemiParser();
        $this->_mf_hash = dechex(crc32(microtime() . uniqid("") . getmypid()));
        if (strlen($signature) < 10) {
            trigger_error("HTML_MetaForm constructor: digital signature is shorter than 10 characters. To be secure, you must always specify signature manually and keep it out of stranger eyes.", E_USER_ERROR);
        }
        $this->_mf_sign = $config['md5']; // . filemtime(__FILE__);
        $this->MF_REQUEST_URI = @$_SERVER['REQUEST_URI'];
        $this->MF_POST = strtoupper(@$_SERVER['REQUEST_METHOD']) == 'POST'? $this->_getFullPostData() : null;

        //@NikitOS
//        @include_once "d_img/imgcode.php";
        $this->d_img = digitimage($this->_mf_sign);
       
        $this->is_ajax = 0;
        //@
    }
    function getFormMeta()
    {
        if ($this->_mf_meta === null && is_array($this->MF_POST)) {
            // Solve metadata.
            if (isset($this->MF_POST[$this->MF_META_ELT])) {
                $packed = $this->MF_POST[$this->MF_META_ELT];
                // If session may be used, get data from session.
                if (isset($_SESSION[$this->MF_META_ELT][$packed])) {
                    $packed = $_SESSION[$this->MF_META_ELT][$packed];
                }
                // Unpack data & check digital signature (may cause errors!).
                $meta = $this->_unpackMeta($packed);
                // Make multidimension array from meta (for complex multi-fields).
                $this->_mf_meta = $meta? $this->_decodeFormMeta($meta, $this->MF_POST) : false;
            } else {
                // If method is POST and signature is mandatory...
                $this->_mf_meta = false; // false means 'unpacking error'.
                $this->_mf_lastError = array($this->MF_ERRORS['hidden_field_required'], $this->MF_META_ELT);
                return null;
            }
        }

        // False means 'unpacking error'.
        return $this->_mf_meta === false? null : $this->_mf_meta;
    }
    function getLastError()
    {
        return $this->_mf_lastError;
    }
    function tag_form($tag)
    {

        $hash = $this->_getHash('form');
        $tag['_right'] = $hash . @$tag['_right'];
        $this->_mf_collectForms[$hash] = $tag;
        $action = isset($tag['action'])? $tag['action'] : $this->MF_REQUEST_URI;
        $action = $this->_getUriByUrl($action); // only REQUEST_URI
        $this->_addItems("form", $tag, array(array(
            'original' => $action,
        )));
        return $tag;
    }
    function container_label($tag)
    {
        if (isset($tag['for'])) {
            $this->_mf_collectLabels[$tag['for']] = $tag['_text'];
        }
    }
    function tag_input($tag)
    {
        if (!isset($tag['name'])) return;
        $type = isset($tag['type'])? strtolower($tag['type']) : 'text';
        $item = array();
        switch ($type) {
            case "checkbox":
                $type = 'multiple';
                $item['key'] = isset($tag['value'])? $tag['value'] : "on";
                break;
            case "radio":
                $type = 'single';
                $item['key'] = isset($tag['value'])? $tag['value'] : null;
                break;
            case "hidden":
                if (substr($tag['name'], -7) == '_hidden') {
                    $tag['value'] = $this->d_img;
                }
                $type = 'text';
                $item['original'] = isset($tag['value'])? $tag['value'] : null;
                break;
            case "file":
                $type = 'file';
                break;
            case "submit":
            case "button":
            case "image":
                $type = 'action';
                break;
            default:
                $type = 'text';
                break;
        }

        //return $this->_addItems($type, $tag, array($item));
        return $this->_addItems($type, $tag, array($item));
    }
    function container_textarea($tag)
    {
        if (!isset($tag['name'])) return;
        return $this->_addItems('text', $tag, array(array()));
    }
    function container_button($tag)
    {
        if (!isset($tag['name']) || strtolower(@$tag['type']) !== 'submit') return;
        return $this->_addItems('action', $tag, array(array()));
    }
    function container_select($tag)
    {
        if (!isset($tag['name'])) return;
        // Parse all options.
        $type = isset($tag['multiple'])? 'multiple' : 'single';
        $parts = preg_split("/<option\s*({$this->sp_reTagIn})>/si", $tag['_text'], -1, PREG_SPLIT_DELIM_CAPTURE);
        $items = array();
        for ($options = array(), $i = 1, $n = count($parts); $i < $n; $i += 2) {
            $opt = array();
            $this->parseAttrib($parts[$i], $opt);
            $text = rtrim(preg_replace('{</?(option|optgroup)[^>]*>.*}si', '', $parts[$i + 1]));
            if (isset($opt['value'])) {
                $value = $opt['value'];
            } else {
                // Option without value: spaces are shrinked (experimented on IE).
                $value = trim($text);
                $value = preg_replace('/\s\s+/', ' ', $value);
                if (strpos($value, '&') !== false) {
                    $value = strtr($value, $this->trans);
                }
            }
            $items[] = array(
                'key'   => $value,
                'label' => $this->MF_STORE_LABELS? $text : null, // emulate "label" attribute
            );
        }
        $this->_addItems($type, $tag, $items);
        return $tag;
    }
    function tag_img($tag,$parsurl)
    {
        
        if (substr($tag['name'], -7) == '_hidden') {

		global $config;
            $tag['src']  = $config[form_imgurl]. $this->d_img . '.jpg';
            return $tag;
        }
    }
    function _addItems($type, &$tag, $items)
    {
        // Mark position of this tag in HTML by unique hash.
        $hash = $this->_getHash();
        $tag['_right'] = @$tag['_right'] . $hash;

        // Extract general metadata (meta:*) from tag attributes.
        $meta = array();
        foreach ($tag as $attrName=>$value) {
            if (strpos($attrName, $this->MF_META_PREFIX) === 0) {
                $meta[substr($attrName, strlen($this->MF_META_PREFIX))] = $value;
                unset($tag[$attrName]);
            }
        }

        // Add each item to _collectMetas.
        foreach ($items as $item) {
            // Grouped by unique hash.
            $this->_mf_collectMetas[$hash][] = $item + array(
                'name'     => @$tag['name'],
                'type'     => $type,
                'custom'   => $meta,
                'id'       => isset($tag['id'])? $tag['id'] : null,
            );
        }
                //var_dump($this->_mf_collectMetas);  echo "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
        return $tag;
        /*$form_hashes = array_keys($this->_mf_collectForms);      $fh = array_pop($form_hashes);
        $mStr = array();
        foreach ($meta as $mName=>$mValue) {
            $mStr[] = $mName . ': ' . $mValue . '{'.substr($fh, strpos($fh, 'form'), -1).'}';
        }

        return $this->makeTag($tag) . implode(', ', $mStr);*/
    }
    function preprocText($text)
    {
        $this->_mf_collectLabels = array();
        $this->_mf_collectMetas = array();
        $this->_mf_collectForms = array();
    }
    function postprocText($text)
    {
        // Split text by forms.
        $formChunks = preg_split('/('.$this->_mf_hash.'form\d+\|)/s', $text, 0, PREG_SPLIT_DELIM_CAPTURE);
        $text = $formChunks[0];
        // Remove hashes from text outside all the forms.
        $this->_getMetasInText($text);
        // Now process each form separately.
        for ($i=1, $n=count($formChunks); $i<$n; $i+=2) {
            $hash = $formChunks[$i];
            $content = $formChunks[$i+1];
            // This form tag.
            $formTag = $this->_mf_collectForms[$hash];
            // Extract stored hashes for form fields & clean hashes from text.
            $metas = $this->_getMetasInText($content);
//            printr($metas);

            // Process only POST forms!
            if (strtoupper(@$formTag['method']) == 'POST') {
                // Generate hidden tag.

                //@NikitOS
                /*$metas['is_ajax'] = $this->is_ajax;*/     //print_r($metas);
                //@

                $packed = $this->_packMeta($metas);
                if ($this->MF_USE_SESSION) {
                    // If session is used, store data in session.
                    $contentHash = $this->_getHashcode($packed);
                    $_SESSION[$this->MF_META_ELT][$contentHash] = $packed;
                    $packed = $contentHash;
                }
                // Add suffix (e.g. - current timestamp) to metadata as comment. This
                // should help debugging: we always know when form metadata was generated
                // and detect stupid proxy requests when page is cached for weeks. This
                // date must NOT be included in digital signature!
                if ($this->MF_SIGN_SUFFIX !== null) {
                    $packed .= " " . $this->MF_SIGN_SUFFIX;
                }
                $hidden = array(
                    '_tagName' => 'input',
                    'type'     => 'hidden',
                    'name'     => $this->MF_META_ELT,
                    'value'    => $packed,
                    '_text'    => null,
                );
                $text .= $this->makeTag($hidden);


                //@NikitOS
                $script_id = substr($this->_getHash(), 0 ,-1);
                $formMeta_arr = array();
                foreach ($metas['items'] as $val) {
                    $str_1 = '';
                    if (isset($val['type'])) {
                        $str_1 .= '    type: "' . $val['type'] . '",' . "\n";
                        unset($val['type']);
                    }
                    if (isset($val['id'])) {
                        $str_1 .= '    id: "' . $val['id'] . '",' . "\n";
                        unset($val['id']);
                    }
                    if (isset($val['name'])) {
                        $str_1 .= '    name: "' . $val['name'] . '",' . "\n";
                        unset($val['name']);
                    }
                    if (isset($val['label'])) {
                        $str_1 .= '    label: "' . $val['label'] . '",' . "\n";
                        unset($val['label']);
                    }
                    if (isset($val['items'])) {
                        $tmp_arr = array();
                        foreach ($val['items'] as $k => $v) {
                            $tmp_arr[] = '"' . $k . '": "' . $v . '"';
                        }
                        $str_1 .= '    items: {' . "\n" .                         // . implode(",\n", $tmp_arr) .
                                  '        ' . implode(",\n        ", $tmp_arr) . "\n" .
                                  '    },' . "\n";
                        unset($val['items']);
                    }

                    if (count($val) > 0) {
                        $str_2_arr = array();
                        foreach($val as $name=>$value) {
                            if ($name == 'validator') {
                                $str_2_arr[] = '      {' . "\n" . '        name: "'.$name.'",' . "\n" . '        value: "'.$value.'"' . "\n" . '      }';
                            }
                        }
                        if (count($str_2_arr)) {
                            $formMeta_arr[] = '  {' . "\n" . $str_1 . '    meta: [' . "\n" . implode(",\n", $str_2_arr) . "\n" . '    ]' . "\n" . '  }';
                        }
                    }
                    else {
                    }
                }
                $text .= '
<script id="'.$script_id.'">
var curr = document.getElementById(\''.$script_id.'\').parentNode;
while(curr.tagName != \'FORM\')
  curr = curr.parentNode;
curr.formMeta = [' . "\n" . implode(",\n", $formMeta_arr) . "\n" .']
curr.is_ajax = ' . ($metas['is_ajax']? '\'' . $metas['is_ajax'] . '\'' : 0) . ';
curr.onsubmit=checkForm;
</script>
';
                //@

            }

            $text .= $content;
        }
        return $text;
    }
    function _getMetasInText(&$content)
    {
        $this->_mf_collectHashes = array();
        $content = preg_replace_callback('/('.$this->_mf_hash.'\d+\|)/', array(&$this, '_extractHashCallback'), $content);
        $formMeta = null;
        $eltMetas = array();
        foreach ($this->_mf_collectHashes as $hash) {
            if (!@is_array($this->_mf_collectMetas[$hash])) continue;
            foreach ($this->_mf_collectMetas[$hash] as $eltMeta) {
                if ($eltMeta['type'] == 'form') {
                    $formMeta = $eltMeta;
                } else {
                    $eltMetas[] = $eltMeta;
                }
            }
        }
        if (!$formMeta) {
            return;
        }
        $metasByName = array();
        foreach ($eltMetas as $eltMeta) {
            $name = $eltMeta['name'];
            $meta =& $metasByName[$name]; // create new or get existed ALIAS!
            if (!$meta) {
                $meta = $eltMeta['custom'];
            } else {
                $meta = $eltMeta['custom'] + $meta;
            }
            if (!isset($eltMeta['label']) && isset($eltMeta['id']) && isset($this->_mf_collectLabels[$eltMeta['id']])) {
                $eltMeta['label'] = $this->_mf_collectLabels[$eltMeta['id']];
            }
            $meta['type'] = $eltMeta['type'];
            if (isset($eltMeta['id'])) $meta['id'] = $eltMeta['id'];
            if (isset($eltMeta['original'])) {
                $meta['original'] = $eltMeta['original'];
            }
            if ($meta['type'] == "multiple") {
                if (substr($name, -2) == '[]') {
                    $name = substr($name, 0, -2); // remove trailing []'s
                    $meta['items'][$eltMeta['key']] = @$eltMeta['label'];
                } else {
                    // Flag-based checkbox. Force 0 or non-zero.
                    $meta['type'] = "flag";
                    $meta['key'] = isset($eltMeta['value'])? $eltMeta['value'] : null;
                }
            } else if ($meta['type'] == "single") {
                // Single meta (SELECT or set of options), may not contain label attribute.
                $meta['items'][$eltMeta['key']] = @$eltMeta['label'];
            }

            // Save meta name.
            $meta['name'] = $name;

            if (isset($meta['items'])) {
                // Find label by ID on first item of single/multiple group.
                // In such group all elements contain same ID of group container (e.g. SELECT).
                if (count($meta['items']) == 1 && isset($eltMeta['id']) && isset($this->_mf_collectLabels[$eltMeta['id']])) {
                    $meta['label'] = $this->_mf_collectLabels[$eltMeta['id']];
                }
            } else {
                // All other metas (non-single and non-multiple).
                if (isset($eltMeta['label'])) {
                    // We have FormPersister pseudo-attribute "label".
                    $meta['label'] = $eltMeta['label'];
                    // Remove trailing "^" from label name if present (FormPersister legacy).
                    if (substr($meta['label'], -1) == '^') {
                        $meta['label'] = substr($meta['label'], 0, -1);
                    }
                }
            }
        }

        // All element metas are items of the FORM.
        $formMeta['items'] = $metasByName;

        // Merge custom fields with basics.
        $custom = $formMeta['custom'];
        unset($formMeta['custom']);
        $formMeta = $custom + $formMeta;

        return $formMeta;
    }
    function _extractHashCallback($m)
    {
        $this->_mf_collectHashes[] = $m[1];
        return '';
    }
    function _getHash($type='')
    {
        static $uniq = 0;
        $uniq++;
        return $this->_mf_hash . $type . $uniq . "|";
    }
    function _packMeta($meta)
    {
        $s = serialize($meta);
        if (is_callable($f='gzcompress')) $s = 'z' . $f($s);
        $s = base64_encode($s);
        $s = $this->_sign($s);
        return $s;
    }
    function _unpackMeta($s)
    {
        // First remove comments (started with space) from the end of metadata.
        // They may contain any debug information (e.g. - form generation date).
        $s = preg_replace('/\s.*/s', '', $s);
        // Unsign the string.
        $s = $this->_unsign($s);
        if ($s === null) {
            $this->_mf_lastError = array($this->MF_ERRORS['bad_signature']);
            return null;
        }
        // Note: we may be always sure that $s is fully valid here.
        // So - no @ needed in the future.
        $s = base64_decode($s);
        if (substr($s, 0, 1) == 'z') {
            if (is_callable($f='gzuncompress')) {
                $s = $f(substr($s, 1));
            } else {
                $this->_mf_lastError = array($this->MF_ERRORS['bad_signature']);
                return null;
            }
        }
        $meta = unserialize($s);
        return $meta;
    }
    function _getHashcode($s)
    {
        return md5($s);
    }
    function _sign($s)
    {
        $sign = $this->_getHashcode($s . $this->_mf_sign);
        return "$sign-$s";
    }
    function _unsign($signed)
    {
        @list ($sign, $s) = explode('-', $signed, 2);
        if (!isset($s)) return null;
        if (strcmp($this->_getHashcode($s . $this->_mf_sign), strval($sign))) return null;
        return $s;
    }
    function _decodeFormMeta($metas, $valuesArray)
    {
//        require_once 'FormPersister.php';

        // Second pass: make meta tree.
        $flatMetas = $metas['items'];
        $treeMetas = $autoindexes = $values = array();
        foreach ($flatMetas as $k => $meta) {
            // Get name structure.
            $name = $meta['name'];
            $nameParts = HTML_FormPersister::_splitMultiArray($name); // may be modified later!

            // Set values.
            if ($this->MF_USE_VALUES) {
                $value = null;

                // Fetch the value.
                if ($meta['type'] == "action" && count($nameParts) == 1) {
                    // This is possibly <input type="image" name="aaa"> field. E.g.,
                    // $_GET contains "aaa_x" and "aaa_y" fieds.
                    if (is_numeric($x = @$valuesArray["{$name}_x"]) && is_numeric($y = @$valuesArray["{$name}_y"])) {
                        $value = array($x, $y);
                    }
                }

                if ($value === null) {
                    // This is not "image" field, or "image" with [] parts
                    // (PHP always ignores .x and .y after [] part, so - remain only "y" coord).
                    if (($v = HTML_FormPersister::_deepFetch($valuesArray, $nameParts, $autoindexes)) !== false) {
                        $value = $v;
                    }
                }

                // For multi-selects value is ALWAYS array.
                if ($value === null && $meta['type'] == "multiple") {
                    $value = array();
                }

                if ($meta['type'] == 'flag') {
                    if ($meta['key'] === null) {
                        $value = intval(!!$value);
                    }
                    unset($meta['key']);
                }

                $curValue =& $values;
                foreach ($nameParts as $part) {
                    $curValue =& $curValue[$part];
                }
                $curValue = $value;

                // Save new value back to meta.
                $meta['value'] = $value;
            }

            // Make deep (tree-like) array.
            $curTree =& $treeMetas;
            foreach ($nameParts as $part) {
                if (!strlen($part)) break;
                $curTree =& $curTree[$part];
            }
            $curTree = $meta;

            // Save modified meta back to array.
            $flatMetas[$k] = $meta;
        }

        // Create resulting metadata.
        $metas['items'] = $flatMetas;
        $metas['tree'] = $treeMetas;
        if ($this->MF_USE_VALUES) {
            $metas['value'] = $values;
        }

        // Return full meta-information.
        return $metas;
    }
    function _getUriByUrl($url)
    {
        $uri = preg_replace('{^\w+://[^/]+}s', '', $url);
        $uri = preg_replace('{#.*}s', '', $uri);
        if (substr($uri, 0, 1) === '?') {
            // URL started with '?'. Prepend current script name.
            $sn = preg_replace('/\?.*/s', '', $this->MF_REQUEST_URI);
            $uri = $sn . $uri;
        }
        return $uri;
    }
    function _getFullPostData()
    {
        $data = $_POST;
        if (@$_FILES) {
            foreach ($_FILES as $firstKey => $container) {
                foreach ($container as $field => $values) {
                    $this->__transformFilesValueArr($field, $values, $data[$firstKey]);
                }
            }
        }
        return $data;
    }
    function __transformFilesValueArr($field, $hashSrc, &$hashDst)
    {
        if (!is_array($hashSrc)) {
            $hashDst[$field] = $hashSrc;
            return;
        }
        foreach ($hashSrc as $k => $v) {
            $this->__transformFilesValueArr($field, $v, $hashDst[$k]);
        }
    }
}
$metaForm =& new HTML_MetaForm('vladsokoov');
ob_start(array(&$metaForm, 'process'));
header("Content-type: text/html; charset=windows-1251");                 // For ajax
class HTML_MetaFormAction
{
    var $MFA_ERRORS = array(
        'bad_form_action'          => 'Bad FORM "action" attribute: expected %s, got %s!',
        'non_existed_value'        => 'Field "%s" (%s) contains non-existed value(s): expected %s, got %s!',
        'invalid_value'            => 'Field "%s" (%s) contains invalid value: expected %s, got %s!',
        'validator_not_registered' => 'Validator %s is not registered!',
    );
    var $MFA_ATTR_DYNAMIC = 'dynamic';
    var $MFA_ATTR_VALIDATOR = 'validator';
    var $MFA_MODIFIER_MANUAL = 'manual';
    var $metaForm = null;
    var $_mfa_errors = array();
    var $_mfa_errorHandler = null;
    function HTML_MetaFormAction(&$metaForm, $errorHandler=null)
    {
        $this->metaForm =& $metaForm;
        $this->_mfa_errorHandler = $errorHandler;
    }
    function process($fieldNames = null, $defaultAction = null)
    {
        
        $metas = $this->metaForm->getFormMeta();
        if ($metas === null) {
            $lastError = $this->metaForm->getLastError();
            if ($lastError) {
                $this->validationError(null, $lastError);
                return null;
            }
            return $defaultAction === null? 'INIT' : $defaultAction;
        }

        if (!$this->_checkDynamicField(
            @$metas[$this->MFA_ATTR_DYNAMIC], $metas['original'],  // allowed values
            $this->metaForm->MF_REQUEST_URI,                       // got values
            $this->MFA_ERRORS['bad_form_action'],                  // error message
            array(&$this->metaForm, '_getUriByUrl')                // allowed value modifier
        )) return null;
        $flat = $metas['items'];
        $action = 'UNKNOWN';
        foreach ($flat as $name => $meta) {
            if ($meta['type'] == 'action' && !empty($meta['value'])) {
                $action = $meta['name'];
                break;
            }
        }

        $checkAllFields = false;
        if ($fieldNames === null) {
            $fieldNames = array_keys($flat);
            $checkAllFields = true;
        } else {
            $fieldNames = (array)$fieldNames;
        }
        $numErrors = 0;
        foreach ($fieldNames as $name) {
            $meta = @$flat[$name];
            if (!$meta) continue;
            $value = $meta['value'];
            switch ($meta['type']) {
                case 'single':
                case 'multiple':
                    if (!$this->_checkDynamicField(
                        @$meta[$this->MFA_ATTR_DYNAMIC], array_keys($meta['items']),
                        $value,
                        array($this->MFA_ERRORS['non_existed_value'], $name, $meta['type']),
                        null,
                        $name
                    )) return null;
                    break;
                default:
                    if (isset($meta['original']) || isset($meta[$this->MFA_ATTR_DYNAMIC])) {
                        if (!$this->_checkDynamicField(
                            @$meta[$this->MFA_ATTR_DYNAMIC], trim(@$meta['original']),
                            trim($value), // trim() - for FF hidden field compatibility!!!
                            array($this->MFA_ERRORS['invalid_value'], $name, $meta['type']),
                            null,
                            $name
                        )) return null;
                    }
                    break;
            }

            if (isset($meta[$this->MFA_ATTR_VALIDATOR])) {
                $validators = preg_split('/\s+/s', $meta[$this->MFA_ATTR_VALIDATOR]);
                foreach ($validators as $validatorName) {
                    $noerr = $negate = $modifiers = false;
                    if (!$validatorName) continue;
                    if ($validatorName[0] == '@') {
                        $noerr = true;
                        $validatorName = substr($validatorName, 1);
                    }
                    if ($validatorName[0] == "!") {
                        $negate = true;
                        $validatorName = substr($validatorName, 1);
                    }
                    if (preg_match('/^(.*?):(.*)$/s', $validatorName, $p)) {
                        $validatorName = $p[1];
                        $modifiers = $p[2];
                    }
                    if ($modifiers == $this->MFA_MODIFIER_MANUAL && $checkAllFields) {
                        continue;
                    }
                    $func = "validator_$validatorName";
                    if (!is_callable($func) && method_exists($this, $func)) {
                        $func = array(&$this, $func);
                    }
                    if (is_callable($func)) {
                        if ($noerr) {
                            $oldER = error_reporting(E_ALL & ~E_NOTICE);
                        }
                        $status = call_user_func($func, $meta['value'], $meta);
                        if ($noerr) {
                            error_reporting($oldER);
                        }
                        $message = null;
                        if (!is_scalar($status)) {
                            $message = $status;
                            $status = false;
                        }

                        if (!$negate && !$status || $negate && $status) {
                            $this->validationError($name, $message, $func);
                            $numErrors++;
                            break;
                        }
                    } else {
                        $this->validationError($name, array($this->MFA_ERRORS['validator_not_registered'], $func));
                        $numErrors++;
                        break;
                    }
                }
            }
        }

        return !$numErrors? $action : null;
    }
    function validationError($name, $message, $validator=null)
    {
        $metas = $this->metaForm->getFormMeta();
        if (is_array($validator)) {
            $validatorName = (is_object($validator[0])? get_class($validator[0]) : $validator[0]) . '::' . $validator[1];
        } else {
            $validatorName = $validator === null? null : @strval($validator);
        }
        $this->_mfa_errors[] = $errorItem = array(
            'name'      => $name,
            'message'   => $message,
            'validator' => $validatorName === null? null : strtolower($validatorName), // strtolower - for PHP 4/5 compatibility
            'meta'      => $name !== null? @$metas['items'][$name] : null,
        );
        if ($this->_mfa_errorHandler) {
            call_user_func($this->_mfa_errorHandler, $errorItem);
        }
        return $errorItem;
    }
    function getErrors()
    {
        
        return $this->_mfa_errors;
    }
    function validator_empty($value)
    {
        return empty($value);
    }
    function validator_or($value)
	{
		return true;
	}
	  function validator_numbers()
	{
		return true;
	}
	  function validator_numbers2()
	{
		return true;
	}
	  function validator_pass($value)
    {
        return true;
    }
    function validator_filled($value, $meta)
    {
        return (is_scalar($value)? !!strlen(trim($value)) : !empty($value))? true : array('Поле "' . $meta['label'] . '" не заполнено.');
    }
    function validator_email($value, $meta)
    {
        return ($value == '' || preg_match('/^[\w\.\-]+@[\w\.\-]+\.[a-zA-Z]{2,}$/i', $value))? true : array('E-mail "' . $meta['value'] . ' указан неверно, пожалуйста, указывайте реальный адрес."');
    }
    function validator_login($value, $meta)
    {
        global $log;
        
     return ($log!= 1)? true : array('Введённый вами логин "' . $value . '" уже занят.');
    }


    function validator_code($value, $meta)
    {
      
      $auth=$meta['value'];
      $tmp1=$this->metaForm->getFormMeta();
      $tmp2=$tmp1['items'];
      
      reset($tmp2);
        while (list($key, $val) = each($tmp2)) 
        if($key==$meta['name'])
        {
          break;
        }

      $tmp3=current($tmp2);
      $hidden=$tmp3['value'];
      return (md5(base64_encode($this->metaForm->_mf_sign.$auth))==$hidden)? true : array('Введенные Вами цифры в поле "' . $meta['label'] . '" не соответствуют изображенным на картинке.');
    }

    function validator_selected($value, $meta)
    {
        return ($value != 0)? true : array('Выберите одно из значений в поле "' . $meta['label'] . '".');
    }
    function validator_checked($value, $meta)
    {
        return count($value)? true : array('Выберите хотя бы одно из значений в поле "' . $meta['label'] . '".');
    }
    function _checkDynamicField($dynamic, $allowed, $got, $error, $modifier=null, $name = null)
    {
        if ($dynamic == $this->metaForm->MF_META_PREFIX . $this->MFA_ATTR_DYNAMIC) {
            return true;
        }
        if (!is_array($allowed)) {
            $allowed = array($allowed);
        }
        if ($dynamic !== null) {
            $allowed = array_merge($allowed, explode(' ', $dynamic));
        }
        if ($modifier) {
            $allowed = array_map($modifier, $allowed);
        }
        if (!is_array($got)) {
            $got = array($got);
        }
        $got = array_map(array(&$this, '_normalizeCrLf'), $got);
        $allowed = array_map(array(&$this, '_normalizeCrLf'), $allowed);
        if (array_diff($got, $allowed)) {
            $exp = count($allowed) > 1? "(" . join('|', $allowed) . ')' : "'{$allowed[0]}'";
            $got = count($got) > 1? "(" . join(', ', $got) . ')' : "'{$got[0]}'";
            $error = array_merge((array)$error, array($exp, $got));
            $this->validationError($name, $error);
            return false;
        }

        return true;
    }
    function _normalizeCrLf($st)
    {
        return str_replace("\r", "", $st);
    }
}
class HTML_FormPersister extends HTML_SemiParser 
{
    function HTML_FormPersister()
    {
        $this->HTML_SemiParser();
    }
    function process($st)
    {
        $this->fp_autoindexes = array();
        return HTML_SemiParser::process($st);
    }
    function ob_formPersisterHandler($st)
    {
        $fp =& new HTML_FormPersister();
        $r = $fp->process($st);
        return $r;
    } 
    function tag_form($attr)
    {
        if (isset($attr['action'])) return;
        if (strtolower(@$attr['method']) == 'get') {
            $attr['action'] = preg_replace('/\?.*/s', '', $_SERVER['REQUEST_URI']);
        } else { 
            $attr['action'] = $_SERVER['REQUEST_URI'];
        }
        return $attr;
    }
    function tag_input($attr)
    { print $attr;
        static $uid = 0;
        $orig_attr = $attr;
        switch ($type = @strtolower($attr['type'])) {
            case 'text': case 'password': case 'hidden': case '':
                if (!isset($attr['name'])) return;
                if (!isset($attr['value']))
                    $attr['value'] = $this->getCurValue($attr);
                break;
            case 'radio':
                if (!isset($attr['name'])) return;
                if (isset($attr['checked']) || !isset($attr['value'])) return;
                if ($attr['value'] == $this->getCurValue($attr)) $attr['checked'] = 'checked';
                else unSet($attr['checked']);
                break;
            case 'checkbox':
                if (!isset($attr['name'])) return;
                if (isset($attr['checked'])) return;
                if (!isset($attr['value'])) $attr['value'] = 'on';
                if ($this->getCurValue($attr, true)) $attr['checked'] = 'checked';
                break;
            case 'image':
            case 'submit':
                if (isset($attr['confirm'])) {
                    $attr['onclick'] = 'return confirm("' . $attr['confirm'] . '")';
                    unSet($attr['confirm']);
                } 
                break;
            default:
                return;
        }
        // Handle label pseudo-attribute. Button is placed RIGHTER
        // than the text if label text ends with "^". Example:
        // <input type=checkbox label="hello">   ==>  [x]hello
        // <input type=checkbox label="hello^">  ==>  hello[x]
        if (isset($attr['label'])) {
            $text = $attr['label'];
            if (!isset($attr['id'])) $attr['id'] = 'FPlab' . ($uid++);
            $right = 1;
            if ($text[strlen($text)-1] == '^') {
                $right = 0;
                $text = substr($text, 0, -1);
            } 
            unSet($attr['label']);
            $attr[$right? '_right' : '_left'] = '<label for="'.$this->quoteHandler($attr['id']).'">' . $text . '</label>';
        }
        // We CANNOT return $orig_attr['_orig'] if attributes are not modified,
        // because we know nothing about following handlers. They may need
        // the parsed attributes, not a plain text.
        unset($attr['default']);
        return $attr;
    } 
    function container_textarea($attr)
    {
        if (trim($attr['_text']) == '') {
            $attr['_text'] = $this->quoteHandler($this->getCurValue($attr));
        }
        unset($attr['default']);
        return $attr;
    } 
    function container_select($attr)
    { 
        if (!isset($attr['name'])) return;
        
        // Multiple lists MUST contain [] in the name.
        if (isset($attr['multiple']) && strpos($attr['name'], '[]') === false) {
            $attr['name'] .= '[]';
        }

        $curVal = $this->getCurValue($attr);
        $body = "";

        // Get some options from variable?
        // All the text outside <option>...</option> container are treated as variable name.
        // E.g.: <select...> <option>...</option> ... some[global][options] ... <option>...</option> ... </select>
        $attr['_text'] = preg_replace_callback('{
                (
                    (?:^ | </option> | </optgroup> | <optgroup[^>]*>) 
                    \s*
                )
                \$?
                ( [^<>\s]+ ) # variable name
                (?=
                    \s*
                    (?:$ | <option[\s>] | <optgroup[\s>] | </optgroup>) 
                )
            }six', 
            array(&$this, '_optionsFromVar_callback'), 
            $attr['_text']
        );
        
        // Parse options, fetch its values and save them to array.
        // Also determine if we have at least one selected option.
        $body = $attr['_text'];
        $parts = preg_split("/<option\s*({$this->sp_reTagIn})>/si", $body, -1, PREG_SPLIT_DELIM_CAPTURE); 
        $hasSelected = 0;
        for ($i = 1, $n = count($parts); $i < $n; $i += 2) {
            $opt = array();
            $this->parseAttrib($parts[$i], $opt);
            if (isset($opt['value'])) {
                $value = $opt['value'];
            } else {
                // Option without value: spaces are shrinked (experimented on IE).
                $text = preg_replace('{</?(option|optgroup)[^>]*>.*}si', '', $parts[$i + 1]);
                $value = trim($text);
                $value = preg_replace('/\s\s+/', ' ', $value);
                if (strpos($value, '&') !== false) {
                    $value = strtr($value, $this->trans);
                }
            }
            if (isset($opt['selected'])) $hasSelected++;
            $parts[$i] = array($opt, $value);
        }

        // Modify options list - add selected attribute if needed, but ONLY
        // if we do not already have selected options!
        if (!$hasSelected) {
            foreach ($parts as $i=>$parsed) {
                if (!is_array($parsed)) continue;
                list ($opt, $value) = $parsed;
                if (isset($attr['multiple'])) {
                    // Inherit some <select> attributes.
                    if ($this->getCurValue($opt + $attr + array('value'=>$value), true)) { // merge
                        $opt['selected'] = 'selected';
                    }
                } else {
                    if ($curVal == $value) {
                        $opt['selected'] = 'selected';
                    }
                }
                $opt['_tagName'] = 'option';
                $parts[$i] = $this->makeTag($opt);
            }
            $body = join('', $parts);
        }
 
        $attr['_text'] = $body;
        unset($attr['default']);
        return $attr;
    }
    function makeOptions($options, $curId = false)
    {
        $body = '';
        foreach ($options as $k=>$text) {
            if (is_array($text)) {
                // option group
                $options = '';
                foreach ($text as $ko=>$v) {
                    $opt = array('_tagName'=>'option', 'value'=>$ko, '_text'=>$this->quoteHandler(strval($v)));
                    if ($curId !== false && strval($curId) === strval($ko)) {
                        $opt['selected'] = "selected";
                    }
                    $options .= HTML_SemiParser::makeTag($opt);
                }
                $grp = array('_tagName'=>'optgroup', 'label'=>$k, '_text'=>$options);
                $body .= HTML_SemiParser::makeTag($grp);
            } else {
                // single option
                $opt = array('_tagName'=>'option', 'value'=>$k, '_text'=>$this->quoteHandler($text));
                if ($curId !== false && strval($curId) === strval($k)) {
                    $opt['selected'] = "selected";
                }
                $body .= HTML_SemiParser::makeTag($opt);
            } 
        }
        return $body;
    }
    function getCurValue($attr, $isBoolean = false)
    {
        $name = @$attr['name'];
        if ($name === null) return null; 
        $isArrayLike = false; 
        if ($isBoolean && false !== ($p = strpos($name, '[]'))) {
            $isArrayLike = true;
            $name = substr($name, 0, $p) . substr($name, $p + 2);
        } 
        $fromForm = true;
        if (false !== ($v = $this->_deepFetch($_POST, $name, $this->fp_autoindexes[$name]))) $value = $v;
        elseif (false !== ($v = $this->_deepFetch($_GET, $name, $this->fp_autoindexes[$name]))) $value = $v;
        elseif (isset($attr['default'])) {
            $value = $attr['default'];
            if ($isBoolean) return $value !== '' && $value !== "0";

            if ($isArrayLike && !is_array($value)) $value = explode(';', $value);
            $fromForm = false;
        } else {
           $value = '';
        }
        if ($fromForm) {
            if (is_scalar($value) && get_magic_quotes_gpc()) { 
                $value = stripslashes($value);
            }
        }
        $attrValue = strval(isset($attr['value'])? $attr['value'] : 'on');
        if ($isArrayLike) {
            if (!is_array($value)) return false;
            return in_array($attrValue, $value);
        } else {
            if ($isBoolean) {
                return (bool)@strval($value) === (bool)$attrValue;
            } else {
                return @strval($value);
            }
        } 
    } 
    function _deepFetch(&$arr, &$name, &$autoindexes) // static
    {
        if (is_scalar($name) && strpos($name, '[') === false) {
            // Fast fetch.            
            return isset($arr[$name])? $arr[$name] : false;
        }
        // Else search into deep.
        $parts = HTML_FormPersister::_splitMultiArray($name);
        $leftPrefix = '';
        foreach ($parts as $i=>$k) {
            if (!strlen($k)) {
                // Perform auto-indexing.
                if (!isset($autoindexes[$leftPrefix])) $autoindexes[$leftPrefix] = 0;
                $parts[$i] = $k = $autoindexes[$leftPrefix]++;
            }
            if (!is_array($arr)) {
                // Current container is not array.
                return false;
            }
            if (!array_key_exists($k, $arr)) {
                // No such element.
                return false;
            }
            $arr = &$arr[$k];
            $leftPrefix = strlen($leftPrefix)? $leftPrefix . "[$k]" : $k;
        }
        if (!is_scalar($name)) {
            $name = $parts;
        } else {
            $name = $leftPrefix;
        }
        return $arr;
    } 
    function _splitMultiArray($name) // static
    {
        if (is_array($name)) return $name;
        if (strpos($name, '[') === false) return array($name);
        $regs = null;
        preg_match_all('/ ( ^[^[]+ | \[ .*? \] ) (?= \[ | $) /xs', $name, $regs);
        $arr = array();
        foreach ($regs[0] as $s) {
            if ($s[0] == '[') $arr[] = substr($s, 1, -1);
            else $arr[] = $s;
        } 
        return $arr;
    }
    function _optionsFromVar_callback($p)
    {
        $dummy = array();
        $name = trim($p[2]);
        $options = $this->_deepFetch($GLOBALS, $name, $dummy);
        if ($options === null || $options === false) return $p[1] . "<option>???</option>";
        return $p[1] . $this->makeOptions($options);
    }
}
 
ob_start(array('HTML_FormPersister', 'ob_formpersisterhandler'));
$metaFormAction =& new HTML_MetaFormAction($metaForm);
$HTML_MetaForm__Meta = $metaFormAction->metaForm->getFormMeta();
$HTML_MetaForm__Action = $metaFormAction->process();
$HTML_MetaForm__Errors = $metaFormAction->getErrors(); 

if ($HTML_MetaForm__Action != 'INIT' && count($HTML_MetaForm__Errors) == 0) 
    {
      $HTML_MetaForm__OK = true; 
      if ($HTML_MetaForm__Meta['is_ajax'])
       {
        echo '["@s@'.$reqwest_text.'"]';
       // die();
       }
    } elseif ($HTML_MetaForm__Action != 'INIT' && count($HTML_MetaForm__Errors) > 0)
    { 
       $HTML_MetaForm__OK = false;
       if ($HTML_MetaForm__Meta['is_ajax'])
        {
        $res = array();
        for ($i = 0; $i < count($HTML_MetaForm__Errors); $i++)
        {
            $res[$i] = "'" . $HTML_MetaForm__Errors[$i]['message'][0] . "'";
        }
        echo '[' . implode(', ', $res) . ']';
        die();
        } else {
        $HTML_MetaForm__Errors_arr = array();
        for ($i = 0; $i < count($HTML_MetaForm__Errors); $i++)
        {
            $HTML_MetaForm__Errors_arr[$i] = $HTML_MetaForm__Errors[$i]['message'][0];
        }
    }
}
?>
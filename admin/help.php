<?php

require 'templates/includes/new_top.php';

list($product, $version, $subversion) = parse_version($config['admin_version']);

$arVersion = explode('.', $version);
$minorVersion = $arVersion[1];
while ($minorVersion >= 0) {
    $version = $arVersion[0] . '.' . $minorVersion;
    // если суб-версия является текстовой строкой, то добавим её к пути - это будет кодовое название
    $path = 'http://help.softmajor.ru/' . $product . '/' . $version . (!is_numeric($subversion) ? '-' . $subversion : '');
    $head = get_headers($path, 1);
    if ((strpos($head[0], '200') !== false) || (isset($head[1]) && strpos($head[1], '200') !== false)) {
        break;
    }
    $minorVersion--;
}


echo <<<HTML
<div class="options">
    <div class="bg_b">
        <div class="bg_l">
            <div class="bg_r">
                <div class="bg_tr">
                    <div class="bg_tl">
                        <div class="bg_bl">
                            <div class="bg_br">
                                <div>
                                    <h1>Справочная система</h1>
                                    <h2></h2>
                                    <div class="nav"></div>
                                    <span class="clear"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<iframe id="helpframe" width="100%" frameborder="0" src="{$path}"></iframe>
<script>
_jQuery(document).ready(function(){
    _jQuery('#helpframe').height(_jQuery(window).height() - 260);
})
</script>
HTML;

require 'templates/includes/new_bottom.php';
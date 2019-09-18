<?
/*
  Класс для быстрого разбора небольших XML-документов.

  * FastXML( &$rh, $xml="" ) -- конструктор. Может вызывать SetXML().

  * SetXML($xml) -- привязывает к объекту xml-документ.

  * GetArray($tag) -- возвращает все найденные вхождения данного тэга как массив.

  * GetOne($tag) -- возвращает первое найденное вхождение данного тэга как сроку.

================================================================== v.1 (zharik@jetstyle)
*/
  
class FastXML {
  
  var $xml; //строка c xml-документом для разбора
  
  function _GetNodeData($tag){
    $re = "/<".$tag.".*?>(.*?)<\/".$tag.".*?>/is";
    preg_match_all( $re, $this->xml, $matches );
    return $matches[1];
  }
  
  function GetArray($tag){
    return $this->_GetNodeData($tag);
  }
  // ================================================================================
function get_tag($tag,$tag_content)
{
	$re = "/<".$tag.".*?>(.*?)<\/".$tag.".*?>/is";
    preg_match_all( $re, $tag_content, $matches );
    return $matches[1][0];
}
  function GetOne($tag){
    $A = $this->GetArray($tag);
    return $A[0];
  }
  
  function GetAttrs($tag,$all = false){
    //берём строку с аттрибут<h2></h2>ами
    $re = "/<".$tag."(.*?)\/{0,1}>/is";
    preg_match_all( $re, $this->xml, $matches );
    //раскладываем их в масив
    $R = array();
    foreach($matches[1] as $str){
//      echo $str."<br>";
      //каждый найденный отдельно
      $re = "/\s.*?=\".*?\"/i";
      preg_match_all( $re, $str, $matches );
      $A = $matches[0];
      $B = array();
      foreach($A as $s1){
        $C = explode("=",$s1);
        $B[ trim($C[0]) ] = trim($C[1],"\"");
      }
      //нужен только один?
      if(!$all) return $B;
      //собираем все
      $R[] = $B;
    }
    return $R;
  }
}

?>
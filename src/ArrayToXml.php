<?php


namespace Coas\TCU;


class ArrayToXml{

    /**
     * Convert Array to XML
     *
     * @param $arr
     * @return string
     */
    static function convert($array, $root = 'Request'){

        $arr = [$root => $array];
        $dom = new \DOMDocument('1.0', 'UTF-8');
        self::parser($dom,$arr,$dom);

        return $dom->saveXML();
    }

    /**
     * @param $root
     * @param $array
     * @param $dom
     */
    private static function parser(&$root, $array, &$dom){
        foreach($array as $key => $item){
            if(is_array($item) && !is_numeric($key)){
                $node = $dom->createElement($key);
                self::parser($node,$item,$dom);
                $root->appendChild($node);
            }elseif(is_array($item) && is_numeric($key)){
                self::parser($root,$item,$dom);
            }else{
                $node = $dom->createElement($key, $item);
                $root->appendChild($node);
            }
        }
    }
}

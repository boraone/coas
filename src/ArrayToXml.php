<?php
/**
 * Created by PhpStorm.
 * User: Amani
 * Date: 19/07/2018
 * Time: 22:07
 */

namespace Coas\TCU;


class ArrayToXml{

    /**
     * @param $arr
     * @return string
     */
    static function convert($arr, $root = 'Request'){
        $arr = [$root => $arr];
        $dom = new \DOMDocument('1.0', 'UTF-8');
        self::recursiveParser($dom,$arr,$dom);
        return $dom->saveXML();
    }

    private static function recursiveParser(&$root, $arr, &$dom){
        foreach($arr as $key => $item){
            if(is_array($item) && !is_numeric($key)){
                $node = $dom->createElement($key);
                self::recursiveParser($node,$item,$dom);
                $root->appendChild($node);
            }elseif(is_array($item) && is_numeric($key)){
                self::recursiveParser($root,$item,$dom);
            }else{
                $node = $dom->createElement($key, $item);
                $root->appendChild($node);
            }
        }
    }
}

<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 12/17/15
 * Time: 11:59 AM
 */

namespace Simnang\LoanPro\Collections;


class CollectionBase
{
    public static function GetCollectionLists(){
        return static::GetLists();
    }

    public static function GetCollectionListNames(){
        return static::GetListNames();
    }
}
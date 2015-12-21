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
    protected static $lists = [
    ];

    protected static $listNames = [
    ];

    public static function GetLists()
    {
        return static::$lists;
    }

    public static function GetListNames()
    {
        return static::$listNames;
    }

}
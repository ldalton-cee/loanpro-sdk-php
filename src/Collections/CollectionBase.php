<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 12/17/15
 * Time: 11:59 AM
 */

namespace Simnang\LoanPro\Collections;

/**
 * Class CollectionBase
 * @package Simnang\LoanPro\Collections
 *
 * This is the base for all collections
 *
 */
class CollectionBase
{
    /**
     * The list of items in a collection group
     * It is split in 2 tiers: the collection groups and the items
     * ex
     *
     * [
     *  "group"=>[...items...],
     *  ...
     * ]
     *
     * "group" is the group name as used in LoanPro
     *
     * The key is the alternate name for an item, the value is the item used in Loanpro
     * @var array
     */
    protected static $lists = [
    ];

    /**
     * The list of groups in a collection
     * The key is the alternate name of a group, the value is the name of the group used in loanpro
     * @var array
     */
    protected static $listNames = [
    ];

    /**
     * Returns the overriden lists item
     * @return array
     */
    public static function GetLists()
    {
        return static::$lists;
    }

    /**
     * Returns the overriden listNames item
     * @return array
     */
    public static function GetListNames()
    {
        return static::$listNames;
    }

}
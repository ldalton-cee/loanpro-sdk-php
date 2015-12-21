<?php
/**
 * Created by IntelliJ IDEA.
 * User: matt
 * Date: 12/17/15
 * Time: 11:02 AM
 */

namespace Simnang\LoanPro\Entities;


class Loans
{
    private $id;
    private $displayId;
    private $title;
    private $modTotal;
    private $modId;
    private $active; //Only change with Inactivate
    private $loanAlert;
    private $deleted; //Only change with Delete
    private $LoanSetup;

    public function GetId()
    {
        return $this->id;
    }

    public function GetDisplayId()
    {
        return $this->displayId;
    }

    public function GetTitle()
    {
        return $this->title;
    }

    public function GetModTotal()
    {
        return $this->modTotal;
    }

    public function GetModId()
    {
        return $this->modId;
    }

    public function GetActive()
    {
        if($this->active == 1)
            return true;
        return false;
    }

    public function GetLoanAlert()
    {
        return $this->loanAlert;
    }

    public function GetDeleted()
    {
        return $this->deleted;
    }

    public function GetLoanSetup()
    {
        return $this->LoanSetup;
    }

    public function SetDisplayId($displayId = "")
    {
        $this->displayId = $displayId;
    }

    public function SetTitle($title = "")
    {
        $this->title = $title;
    }

    public function SetModTotal($modTotal = 0)
    {
        if($modTotal < $this->modId)
            throw new \InvalidArgumentException("Mod ID cannot be greater than mod total!");
        $this->modTotal = $modTotal;
    }

    public function SetModId($modId = 0)
    {
        if($modId > $this->modTotal)
            throw new \InvalidArgumentException("Mod ID cannot be greater than mod total!");
        $this->modId = $modId;
    }

    public function SetLoanAlert($alert = "")
    {
        $this->loanAlert = $alert;
    }

    public function SetLoanSetup(LoanSetup $loanSetup)
    {
        $this->LoanSetup = $loanSetup;
    }
}
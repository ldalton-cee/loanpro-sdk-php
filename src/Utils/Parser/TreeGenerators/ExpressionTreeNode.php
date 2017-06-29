<?php
/**
 *
 * Copyright 2017 Simnang, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 */

namespace Simnang\LoanPro\Utils\Parser\TreeGenerators;
use Simnang\LoanPro\Utils\Parser\Token;

/**
 * Class ExpressionTreeNode
 *
 * @package Simnang\LoanPro\Utils\Parser\TreeGenerators
 */
class ExpressionTreeNode implements \JsonSerializable {
    public $token = null;
    public $leftNode = null;
    public $rightNode = null;
    public $parentNode = null;

    /**
     * Creates the expression tree node
     * @param Token $val - value to store in the node
     */
    public function __construct(Token $val){
        $this->token = $val;
    }

    /**
     * Adds a node as the left child, or does nothing if it exists
     * @param ExpressionTreeNode $node
     */
    public function AddLeftChildNode(ExpressionTreeNode $node){
        if(is_null($this->leftNode)) {
            $this->leftNode = $node;
            $node->parentNode = $this;
        }
    }

    /**
     * Adds a node as the right child, or does nothing if it exists
     * @param ExpressionTreeNode $node
     */
    public function AddRightChildNode(ExpressionTreeNode $node){
        if(is_null($this->rightNode)) {
            $this->rightNode = $node;
            $node->parentNode = $this;
        }
    }

    /**
     * Adds a node in the next possible spot or does nothing if all spots are taken
     * @param ExpressionTreeNode $node
     */
    public function AddNextChildNode(ExpressionTreeNode $node){
        if(is_null($this->leftNode)) {
            $this->leftNode = $node;
            $node->parentNode = $this;
        }
        else if(is_null($this->rightNode)) {
            $this->rightNode = $node;
            $node->parentNode = $this;
        }
    }

    /**
     * Returns whether or not both children are filled
     * @return bool
     */
    public function HasBothChildren(){
        return !$this->MissingLeftChild() && !$this->MissingRightChild();
    }

    /**
     * Returns whether or not the left child is missing
     * @return bool
     */
    public function MissingLeftChild(){
        return is_null($this->leftNode);
    }

    /**
     * Returns whether or not the right child is missing
     * @return bool
     */
    public function MissingRightChild(){
        return is_null($this->rightNode);
    }

    /**
     * Returns whether or not the parent is set
     * @return bool
     */
    public function HasParent(){
        return !is_null($this->parentNode);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *        which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return ['token'=>$this->token, 'left'=>$this->leftNode, 'right'=>$this->rightNode];
    }
}

/**
 * Class SingleChildExpressionTreeNode
 *
 * @package Simnang\LoanPro\Utils\Parser
 */
class SingleChildExpressionTreeNode extends ExpressionTreeNode{

    /**
     * Creates a single-child expression tree node
     * @param Token $val
     */
    public function __construct($val)
    {
        parent::__construct($val);
    }

    /**
     * Adds the left child node
     * @param ExpressionTreeNode $node
     */
    public function AddLeftChildNode(ExpressionTreeNode $node)
    {
        parent::AddLeftChildNode($node);
    }

    /**
     * Fails, there is no right child node
     * @param ExpressionTreeNode $node
     * @throws \OutOfBoundsException
     */
    public function AddRightChildNode(ExpressionTreeNode $node)
    {
        throw new \OutOfBoundsException("Single child nodes only have one child, cannot access second child");
    }

    /**
     * Fills the next child node
     * @param ExpressionTreeNode $node
     */
    public function AddNextChildNode(ExpressionTreeNode $node)
    {
        parent::AddLeftChildNode($node);
    }

    /**
     * Changes the child node
     * @param ExpressionTreeNode $node
     */
    public function ChangeChildNode(ExpressionTreeNode $node){
        if(is_null($this->leftNode)){
            parent::AddLeftChildNode($node);
        }
        else{
            $this->leftNode->parentNode = null;
            $this->leftNode = $node;
            $node->parentNode = $this;
        }
    }

    /**
     * Returns whether or not all children are filled
     * @return bool
     */
    public function HasBothChildren()
    {
        return !parent::MissingLeftChild();
    }

    /**
     * Returns whether or not the child is missing
     * @return bool
     */
    public function MissingLeftChild()
    {
        return parent::MissingLeftChild();
    }

    /**
     * Never missing right child since it doesn't exist
     * @return false
     */
    public function MissingRightChild()
    {
        return false;
    }

    /**
     * Returns whether or not it has a parent node
     * @return bool
     */
    public function HasParent()
    {
        return parent::HasParent();
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *        which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return ['token'=>$this->token, 'left'=>$this->leftNode];
    }
}

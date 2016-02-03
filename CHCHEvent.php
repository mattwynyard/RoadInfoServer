<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CHCHEvent
 *
 * @author Matt
 */
class CHCHEvent {
    //put your code here
    public $id;
    private $title;
    private $address;
    
    function getID() {
        //echo $id
        return $this->id;   
    }
    
    function getTitle() {
        return $this->title;   
    }
    
        function getAddress() {
        //echo $id
        return $this->address;   
    }
    
    function setID($id) {
        $this->id = $id;
    }
    
    function setTitle($title) {
        $this->title = $title;
    }
    
    function setAddress($address) {
        $this->address = $address;
    }

}

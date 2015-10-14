<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Entity;

/**
 * Description of Progress
 *
 * @author SGOURMELON
 */
class Progress {
    
    private $previsionnel;
    private $reel;
    private $week;
    private $day;
    
    function getPrevisionnel() {
        return $this->previsionnel;
    }

    function getReel() {
        return $this->reel;
    }

    function getWeek() {
        return $this->week;
    }

    function getDay() {
        return $this->day;
    }
    
    function setPrevisionnel($previsionnel) {
        $this->previsionnel = $previsionnel;
    }

    function setReel($reel) {
        $this->reel = $reel;
    }

    function setWeek($week) {
        $this->week = $week;
    }

    function setDay($day) {
        $this->day = $day;
    }




}

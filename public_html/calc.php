<?php
require '../engine/core.php';

function routeIndex(){
    echo render('calc/calc');
}

function routeCalc1(){
    echo render('calc/calc1');
}

function routeCalc2(){
    echo render('calc/calc2');
}

route();
?>

<?php
require '../engine/core.php';

function routeIndex() {
    systemLog('подключили логирование', 'error');
    echo render('site/home');

}

function routeHome() {
    echo render('site/home');
}

function routeError() {
    echo render('site/error');
}

route();

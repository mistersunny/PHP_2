<?php
/**
 * функция по созданию хэша пароля
 */

function hashPass($password='0000'){
    return password_hash($password, PASSWORD_DEFAULT);//если входящих параметров нет, то возвращает хэш для 0000
}
/**
 * получаем всю информацию о пользователе с данным логином
 */
function userdata(string $login){
    $user = getItemArray("select users.id, users.login, users.pass, users.avatar, roles.id as role_id, roles.role as role, roles.img_view ,roles.img_download ,roles.img_delete ,roles.comment_view ,roles.comment_create ,roles.comment_delete ,roles.goods_view ,roles.goods_CRUD ,roles.goods_buy from php1.users  left JOIN php1.roles ON users.role_id = roles.id where users.login = '{$login}';");
    return $user;
}

/**
 * Функция логирования пользователя
 */
function loginUser(array $user, bool $remember = false){
    $_SESSION['user'] = $user;//записываем в сессию полученные данные
    // var_dump($_SESSION['user']);
    // var_dump($_SESSION);

    if ($remember) {//если был указан параметр remember как true, то
        $auth = ['login' => $_SESSION['user']['login']];//создаем массив с логином пользователя
        setCook('auth', json_encode($auth));//записываем в куки сформированный массив
    }
}
/**
 * функция для выхода пользователя из системы
 */
function logoutUser(){
    resetCook('auth');//запускаем функцию для сброса куки
    unset($_SESSION['user']);//очищаем сессию
    session_destroy();//закрываем сессию
}
/**
 * функция автовхода
 */
function autologin(){
    // var_dump($_SESSION['user']['role']);
    // var_dump($_COOKIE);
    if (!empty($_COOKIE['auth']) && empty($_SESSION['user'])) {//если в куках есть логин и нет данных о пользователе в сессии, то
        $auth = json_decode($_COOKIE['auth'], true);//получаем массив из куки
        $user = userdata($auth['login']);//получаем данные о пользователе по логину из полученного массива
        loginUser($user);//запускаем функцию логирования
    }else if(empty($_SESSION['user'])){//если данных о пользователе в сессии нет, то
        $user = getItemArray("SELECT * FROM php1.roles where role = 'guest';");//получаем данные для доступа уровень Гость
        loginUser($user);//запускаем функцию логирования
    }
}

/**
 * Функция для упрощения записи COOKIES
 * @param string $key
 * @param $value
 */
function setCook(string $key, $value) {
    global $config;
    setcookie($key, $value, time() + 3600 * 2, '/', $config['app']['host']);
}

/**
 * Функция для сброса значения COOKIES
 * @param string $key
 */
function resetCook(string $key) {

    setcookie($key, '', time() - 3600);
}


?>
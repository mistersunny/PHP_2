<?php
require_once '../engine/core.php';
/**
 * личный кабинет
 */
function routeIndex(){
    echo render('user/lk');
}
/**
 * логинация
 */
function routeLogin(){
// var_dump($_POST);
    if (isset($_POST['login']) && isset($_POST['password'])) {//если есть логин и пароль, то
        // var_dump($_POST);
        $login = $_POST['login'];//берем логин
        $password = $_POST['password'];//берем пароль

        $user = userdata($login);//получаем данные о пользователе с таким логином
        if ($user) {//если данные полученв, то
            // var_dump($user);
            if (password_verify($password, $user['pass'])) {//сверяем пароль с тем, хэшем что в БД, если все ок, то
                (isset($_POST['remember']))?loginUser($user, true):loginUser($user);//запускаем логинацию пользователя, если пользователь указал 'запомнить меня', то передаем true
                header ('location: user.php');
            }else{
                message('danger', 'Неверный пароль.');//передаем сообщение
                echo render('user/login', ['login' => $login, 'submit' => 'Вход', 'action' => 'login']);
            }
        }else{
            message('warning', 'Пользователя с таким Логином не существует, пожалуйста зарегистрируйтесь.');//передаем сообщение
            echo render('user/register', ['login' => $login, 'password' => $password, 'submit' => 'Регистрация', 'action' => 'register']);
        }
    }else {
        message('warning', 'Введите логин и пароль.');//передаем сообщение
        echo render('user/login', ['submit' => 'Вход', 'action' => 'login']);
    }
}
/**
 * регистрация пользователя
 */
function routeRegister(){
    if (isset($_POST['login'])) {//если пришео логин, то
        // var_dump($_POST);
        $login = $_POST['login'];//присваиваем переменной логин
        if (isset($_POST['password'])) {//если пршел пароль, то
            $password = $_POST['password'];;//присваиваем переменной пароль
            $hashpassword = hashPass($password);//получаем хэш пароля
        }else {//если пароль не пришел, то
            $hashpassword =  hashPass();//получаем хэш стандартного пароля
        }
        $user = getItemArray("SELECT * from php1.users where login='{$login}'");//получаем инфо о пользователе с таким логином
        // var_dump ($user);
        if (empty($user)) {//если информации нет, то
            if (isset($_POST['role'])) {// если в POST указана роль
                $user = execute("INSERT into php1.users 
                (login, pass, role_id, registered_at) values 
                ('{$login}', '{$hashpassword}', '{$_POST['role']}' ,'".time()."')");//создаем пользователя в БД с указанной ролью
            }else {//если роль не указана, то
                $user = execute('INSERT into php1.users 
                (login, pass, role_id, registered_at) values 
                ("'.$login.'", "'.$hashpassword.'", 2 ,'.time().')');//создаем пользователя в БД с ролью user
            }
            if (!isset($password)) {//если пароль не был указан в POST, то
                header('Location: admin.php?action=users');
            }else {//если был, то
                if ($user) {//если добавление пользователя прошло успешно, то
                    $user = userdata($login);//получаем данные этого пользователя
                    // var_dump($user);
                    loginUser($user);//передаем данные пользователя для логинации нового пользователя             
                    echo render('user/lk');
                }else{//иначе
                    message('danger', 'Не удалось зарегистрировать, попробуйте, позже.');//передаем сообщение
                    render('user/register', ['submit' => 'Регистрация', 'action' => 'register']);
                }
            }
        }else {
            message('warning', 'Пользователь с таким Логином уже существует, пожалуйста придумайте другой логин.');
            echo render('user/register', ['password' => $password, 'submit' => 'Регистрация', 'action' => 'register']);
        }
    }else {
        message('warning', 'Пожалуйста введите логин и пароль');//передаем сообщение
        echo render('user/register', ['submit' => 'Регистрация', 'action' => 'register']);
    }
}
/**
 * выход пользователя
 */
function routeLogout(){  
    // var_dump($_SESSION);
    logoutUser();
    header ('Location: /');
}
/**
 * сброс пароля
 */
function routeResetpass(){
    if(isset($_GET)){//если пришел GET, то
        $id = $_GET['id'];//берем id
        $password = hashPass();//получаем хэш стандарного пароля
        $_POST['id'] = $id;//передаем в POST id
        $_POST['password'] = $password;//передаем в POST пароль
        routeUpdate();//запускаемапдейт
    }
}
/**
 * изменение данных пользователя
 */
function routeUpdate(){

    // var_dump($_POST);
    if (isset($_POST['id'])){//если есть id
        $id = $_POST['id'];//берем id
        $user = getItemArray("SELECT * from php1.users where id = '{$id}';");//получаем данные пользователя по id
        if (isset($_POST['login']) && $_POST['login'] != $user['login']) {//если пришел логин и он не равен тому что был, то
            $login = $_POST['login'];//берем логин
            $result = execute("UPDATE php1.users SET login = '{$login}' where id = '{$id}';");//меняем логин и этого пользователя
            if ($result) {//если успешно,то
                $user = userdata($login);//получаем данные этого пользователя
                if ($_SERVER['HTTP_REFERER'] == 'http://php1/admin.php?action=users') {//если запрос был выполнен из админки, то
                    header('Location: admin.php?action=users');//возвращаемся в админку
                } else {//если нет, то
                    loginUser($user);//авторизуемся с новыми данными
                }
            }
        }
        if (isset($_POST['password'])){//если пришел пароль
            $password = hashPass($_POST['password']);//берем пароль и получаем из него хэш
            $result = execute("UPDATE php1.users SET pass = '{$password}' where id = '{$id}';");//меняем пароль на новый
            if (!$result) {//если запрос не удался, то
                message('danger', 'Не удалось изменить пароль');//передаем сообщение
                routeIndex();//возвращаемся в личный кабинет
            }
        }
        if (isset($_POST['role']) && $_POST['role'] != $user['role']){//если пришла роль и она не равна старой роли, то
            $role = $_POST['role'];//берем роль
            $result = execute("UPDATE php1.users SET role_id = '{$role}' where id = '{$id}';");//меняем роль на новую
            if (!$result) {//если запрос не удался, то
                message('danger', 'Не удалось изменить роль');//передаем сообщение
            }else {
                message('success', 'Изменения внесены успешно!');//передаем сообщение
            }
            header('Location: admin.php?action=users');//возвращаемся в админку
        }
    }
}
/**
 * Удаление пользователя
 */
function routeDelete(){
    // var_dump($_GET);
    if (isset($_GET['id'])) {//если пришел id
        $id = $_GET['id'];// берем id
        $result = execute("DELETE FROM php1.users WHERE id = '{$id}'");//удаляем запись из БД по id
        if ($result) {//если удаление прошло успешно, то
            message('success', 'Пользователь удален!');//передаем сообщение
        }else{//иначе
            message('success', 'Пользователь удален!');//передаем сообщение
        }
        header('Location: admin.php?action=users');
    }
}
route();
?>
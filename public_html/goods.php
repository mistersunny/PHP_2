<?php
require '../engine/core.php';
/**
 * /gallery.php
 */
function routeIndex(){
    global $config;
    $cards = getItemArray('SELECT goods.id, goods.name, description, categories.name as category, price, created_at, balance from php1.goods left join php1.categories ON goods.category_id = categories.id;');// получаем массив имен упорядоченный по убыванию просмотров
    if ($cards) {//если запрос выполнен успешно, то
        echo render('goods/all',$cards);// 
    }else {//иначе
        message('danger', 'Не удалось получить данные о товарах');//Передаем сообщение
        echo render('site/error');// передаем адрес в рендер
    }
}
/**
 * страница товара
 */
function routeOne(){
    global $config;
    $name = $_GET['name'];//получаем имя товара
    $card = getItemArray("SELECT * from php1.goods where name = '$name'");//получаем данные о товаре
    if ($card){//если запрос выполнен успешно, то
        echo render('goods/one', $card);// передаем адрес в рендер
    }else{//иначе
        message('danger', 'Не удалось получить данные о товаре');//Передаем сообщение
        echo render('site/error');// передаем адрес в рендер
    }
}
/**
 * изменение товара
 */
function routeUpdate(){
    global $config;
    // var_dump($_POST);
    if (!isset($_POST['id'])) {//если нет id
        message('warning', 'Не получен id изменяемого товара');//Передаем сообщение
        header('Location: admin.php?action=catalogedit');
    }
    $id = $_POST['id'];//присваеваем значение переменной
    $old = getItemArray("SELECT * from php1.goods where id = '$id'");//полкчаем информацию о товаре
    if ($old) {//если запрос выполнен успешно, то 
        $execute = 'UPDATE php1.goods set ';//задаем начало запроса
        if ((!empty($_POST['name']) && $_POST['name'] != $old['name'])) {//если пришло новое имя, то
            $execute .= 'name = "'.$_POST['name'].'", ';//добавляем в запрос новое имя
        }
        if ((!empty($_POST['description']) && $_POST['description'] != $old['description'])) {//если пришло новое описание, то
            $execute .= 'description = "'.$_POST['description'].'", ';//добавляем в запрос новое описание
        }
        if ((!empty($_POST['category_id']) && $_POST['category_id'] != $old['category_id'])) {//если пришла новая категория, то
            $execute .= 'category_id = "'.$_POST['category_id'].'", ';//добавляем в запрос новую категорию
        }
        if ((!empty($_POST['price']) && $_POST['price'] != $old['price'])) {//если пришла новая цена
            $execute .= 'price = "'.$_POST['price'].'", ';//добавляем в запрос новую цену
        }
        if ((!empty($_POST['balance']) && $_POST['balance'] != $old['balance'])) {//если пришел новый остаток
            $execute .= 'balance = "'.$_POST['balance'].'", ';//добавляем в запрос новый остаток
        }
        if (substr($execute, -2) == ', '){//если в строке запроса в конце есть запятая, то
            $execute = substr($execute, 0, -2);//убираем её
        }
        $execute .= ' where id = "'.$id.'"';//завершаем формирование запроса поиском по id
        if (substr_count($execute,',') == 0) {//если в сформированном запросе нет запятых, то
            message('warning', 'Вы не внесли изменения');//передаем сообщение
            header('Location: admin.php?action=catalogedit');
        }
        $new = execute($execute);
        if ($new) {
            message('success', 'Изменения внесены успешно');//передаем сообщение
            header('Location: admin.php?action=catalogedit');
        }
    }else {
        message('danger', 'Изменяемый товар не найден в базе данных');//передаем сообщение
        header('Location: admin.php?action=catalogedit');
    }
}
/**
 * создание товара
 */
function routeCreate(){
    global $config;
    // var_dump($_POST);
    if (!empty($_POST)) {//если массив не пустой, то
        $name = (empty($_POST['name'])) ? '' : $_POST['name'];//если пришло имя, то помещаем в переменную
        $description = (empty($_POST['description'])) ? '' : $_POST['description'];//если пришло описание, то помещаем в переменную
        $category_id = (empty($_POST['category'])) ? '' : $_POST['category'];//если пришла категория, то помещаем в переменную
        $price = (empty($_POST['price'])) ? '' : $_POST['price'];//если пришла цена, то помещаем в переменную
        $balance = (empty($_POST['balance'])) ? '' : $_POST['balance'];//если пришлел остаток, то помещаем в переменную
        $good = getItemArray("select * from php1.goods where name = '$name'");//получаем данные о товаре
        if (empty($good)){//если данные получены, то
            $create = execute('insert into php1.goods 
            (name, description, category_id, price, created_at, balance) values 
            ("'.$name.'", "'.$description.'", "'.$category_id.'", "'.$price.'" ,'.time().', "'.$balance.'")');//создаем новый товар в БД
            if ($create) {//если упешно, то
                message('success', 'Товар успешно добавлен в каталог');//передаем сообщение
                header('Location: admin.php?action=catalogedit');
            }else{//иначе
                message('danger', 'Не удалось добавить товар в БД');//передаем сообщение
                header('Location: admin.php?action=catalogedit');
            }
        }else { //иначе
            message('warning', 'Товар с таким именем уже существует');//передаем сообщение
            header('Location: admin.php?action=catalogedit');
        }
    }else {//иначе
        message('warning', 'Введите данные нового товара');//передаем сообщение
        header('Location: admin.php?action=catalogedit');
    }
}
/**
 * удаление товара
 */
function routeDelete(){
    global $config;
    // var_dump($_GET);
    if (isset($_GET['id'])) {
        $id = $_GET['id'];// помещаем значение $_GET['image'] в переменную $image
        $result = execute("DELETE FROM php1.goods WHERE id = '$id'");//удаляем соответсвующую запись из БД
        if ($result) {
            message('success', 'Товар успешно удален');//передаем сообщение
            header('Location: admin.php?action=catalogedit');
        }else{
            message('danger', 'Не удалось удалить товар из БД');//передаем сообщение
            header('Location: admin.php?action=catalogedit');
        }
    }else {
        message('danger', 'Не удалось удалить товар из каталога, так как не получен id товара');//передаем сообщение
        header('Location: admin.php?action=catalogedit');
    }

}

route();
?>
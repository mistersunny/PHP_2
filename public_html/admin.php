<?php
require '../engine/core.php';//подключаем движок

/**
 * построение дерева из массива
 */
function getTreeCategories() {
    $categories = getItemArray("select * from php1.categories", '', 'id');//получаем масив где ключ массива является ID
    // var_dump($categories);
    if ($categories) {
        $tree = [];//создаем пустой массив
        foreach ($categories as $id => &$node) {//для каждого элемента массива 
            if ($node['parent_id'] == 0){//проверяем, если нет родителя
                $tree[$id] = &$node;//помещаем в массив массив
            }else{
                $categories[$node['parent_id']]['childs'][$id] = &$node;//Если есть потомки то перебераем массив
            }
        }
        // vardump($tree);
        return $tree;//возвращает сформированное дерево в виде массива
    }
}
/**
 * функция по отработке index
 */
function routeIndex(){
    echo render('admin/admin');//отрисовываем данный шаблон
}


/**
 * отображение всех пользователей
 */
function routeUsers(){
    $users = getItemArray("select users.id, users.login, users.pass, users.registered_at, users.avatar, roles.role as role from php1.users  left JOIN php1.roles ON users.role_id = roles.id");
    if ($users) {//если запрос удался, то
        $roles = getItemArray("select id, role from php1.roles", 'role');
        if ($roles) {//если запрос удался, то
            echo render('admin/Users',['users' => $users, 'roles' => $roles]);
            // var_dump($roles);
        }else {//если нет, то
            message('danger', 'Не удалось получить данные о ролях пользователей');//помещаем в сессию сообщение
            echo render('site/error');
        }
    }else {//если нет, то
        message('danger', 'Не удалось получить данные о пользователях');//помещаем в сессию сообщение
        echo render('site/error');
    }
}
/**
 * редактор каталога
 */
function routeCatalogedit(){

    $cards = getItemArray('select goods.id,	goods.name, description, price, balance, categories.name as category, categories.id as category_id, orders.sum as in_orders from php1.goods
    left JOIN php1.categories ON goods.category_id = categories.id
    left JOIN (SELECT id_good, SUM(quantity) as sum FROM php1.goods_in_order GROUP BY id_good) orders on goods.id = orders.id_good;');// получаем массив имен упорядоченный по убыванию просмотров
    if ($cards) {
        $tree = getTreeCategories();//получаем дерево каталога
        echo render('admin/catalogedit',['cards' => $cards, 'tree' => $tree]);
    }else {
        message('danger', 'Не удалось получить данные о товарах.');//помещаем в сессию сообщение
        echo render('site/error');// передаем адрес в рендер
    }
}
/**
 * редактор категорий
 */
function routeCategoryedit(){

    $categories = getItemArray("SELECT * FROM php1.categories");
    // var_dump($categories);
    if ($categories) {//если запрос удался, то
        $tree = getTreeCategories();
        echo render('admin/categoryedit',['tree' => $tree, 'categories' => $categories]);
    }else{//если нет, то
        message('danger', 'Не удалось получить данные о категориях товаров.');//помещаем в сессию сообщение
        echo render('site/error');
    }
}

/**
 * внесение изменений в категории
 */
function routeUpdatecategory(){

    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $old = getItemArray("select * from php1.categories where id = '$id'");
        $execute = 'UPDATE php1.categories set ';
        if ((!empty($_POST['name']) && $_POST['name'] != $old['name'])) {
            $execute .= 'name = "'.$_POST['name'].'", ';
        }
        if (isset($_POST['parent_id']) && $_POST['parent_id'] != $old['parent_id'] && $_POST['parent_id'] != $id) {
            $execute .= 'parent_id = "'.$_POST['parent_id'].'", ';
        }else {
            message('danger', 'Категория не может быть родителем сама себе!');;
            header('Location: admin.php?action=categoryedit');
            die;
        }

        if (substr($execute, -2) == ', '){//убираем запятую в конце строки
            $execute = substr($execute, 0, -2);
        }
        $execute .= ' where id = "'.$id.'"';//добавляем условия для запроса
        $result = execute($execute);
        if ($result) {
            message('success', 'Изменения внесены успешно!');//помещаем в сессию сообщение
        }else{
            message('danger', 'Не удалось внести изменения.');//помещаем в сессию сообщение
        }
        header('Location: admin.php?action=categoryedit');  
    }
}
/**
 * Добавление категории
 */
function routeCreatecategory(){

    if (!empty($_POST)) {
        $name = $_POST['name'];
        $parent_id = (empty($_POST['parent_id'])) ? '0' : $_POST['parent_id'];
    }
    $result = execute('INSERT into php1.categories 
    (name, parent_id) values ("'.$name.'", "'.$parent_id.'")');
    if ($result) {
        message('success', 'Категория добавлена!');//помещаем в сессию сообщение
    }else{
        message('danger', 'Не удалось добавить категорию.');//помещаем в сессию сообщение
    }
    header('Location: admin.php?action=categoryedit');  
}
/**
 * удаление категорий
 */
function routeDeletecategory(){

    // var_dump($_GET);
    $id = $_GET['id'] ?? '';// помещаем значение $_GET['image'] в переменную $image
    $result = execute("DELETE FROM php1.categories WHERE id = '$id'");//удаляем соответсвующую запись из БД
    if ($result) {
        message('success', 'Категория успешно удалена');//помещаем в сессию сообщение
        header('Location: admin.php?action=categoryedit');  
    }else{
        message('danger', 'Не удалось удалить категорию');//помещаем в сессию сообщение
        header('Location: admin.php?action=categoryedit');  
    }
}
/**
 * редактор заказов
 */
function routeOrdersedit(){

    $all_orders = getItemArray("SELECT id_order, status_id, status, user_id, login, goods.id, name, quantity, price from php1.orders 
    left join php1.goods_in_order ON orders.id = goods_in_order.id_order 
    left join php1.status_order ON orders.status_id = status_order.id 
    left join php1.goods ON goods_in_order.id_good = goods.id
    left join php1.users ON orders.user_id = users.id;");
    if ($all_orders) {
        $all_status = getItemArray("SELECT * FROM php1.status_order;",'status');
        echo render('admin/ordersedit', ['all_orders' => $all_orders, 'all_status' => $all_status]);
    }else{
        message('danger', 'Не удалось получить данные о заказах!');//помещаем в сессию сообщение
        echo render ('site/error');
    }
}
/**
 * изменение статуса заказа
 */
function routeStatusedit(){
    if (!empty($_POST)) {
        $status_id = $_POST['status_id'];
        $order_id = $_POST['order_id'];
        $old_status_id = getItemArray("SELECT status_id from php1.orders where id={$order_id};");
        var_dump($old_status_id);
        if ($old_status_id['status_id'] != $status_id) {
            $result = execute("UPDATE php1.orders SET status_id = '{$status_id}' where id={$order_id};");
            if ($result) {
                message('success', 'Статус успешно изменен.');//помещаем в сессию сообщение
            }else {
                message('danger', 'Не удалось изменить статус.');//помещаем в сессию сообщение
            }
        }else {
            message('warning', 'Выберите новый статус');//помещаем в сессию сообщение
        }
    }
    header('Location: admin.php?action=ordersedit');
}
/**
 * удаление заказа
 */
function routeDeleteorder(){
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $result = execute("DELETE FROM php1.orders WHERE id = '$id'");
        if ($result) {
            $result = execute("DELETE FROM php1.goods_in_order WHERE id_order = '$id'");
            if ($result) {
                message('success', 'Заказ успешно удален!');//помещаем в сессию сообщение
            }else {
                message('warning', 'Заказ удален, но не удалось удалить товары из заказа!');//помещаем в сессию сообщение
            }
        }else {
            message('danger', 'Заказ и товары из заказа не удалены!');//помещаем в сессию сообщение
        }
    }
    header('Location: admin.php?action=ordersedit');  
}

route();
?>
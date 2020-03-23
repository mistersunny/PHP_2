<?php
require '../engine/core.php';
/**
 * вывод все заказов пользователя
 */
function routeIndex(){
    $user_id = $_SESSION['user']['id'];//берем id из сессии
    $orders = getItemArray("SELECT id_order, status, goods.id, name, quantity, price from php1.orders
    left join php1.goods_in_order ON orders.id = goods_in_order.id_order
    left join php1.status_order ON orders.status_id = status_order.id
    right join php1.goods ON goods_in_order.id_good = goods.id
    where user_id ='{$user_id}';");//получаем информацию о заказах пользвателя
    if ($orders) {//если запрос выполнен успешно, то
        echo render('order/allorders', $orders);
    }else {
        message('danger', 'Не удалось получить информацию о заказах');//передаем сообщение
        echo render('site/error');
    }
}
/**
 * Добавление товара к корзину
 */
function routeAdd(){
    // var_dump($_SESSION['order']);
    if (!empty($_POST)) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        $balance = $_POST['balance'];
        if ($quantity > 0) {//если количество больще 0, то
            $_SESSION['order'][$id] = ['name' => $name, 'price' => $price, 'quantity' => $quantity, 'balance' => $balance];//помещаем все значения в сессию
        }else{//иначе
            unset($_SESSION['order'][$id]);//удаляем из сессии информацию об этом товаре
        }
    }
    header('Location: goods.php');
}
/**
 * изменение количества товара в корзине
 */
function routeUpdate(){
    $id = $_GET['id'];//получаем id товара
    $quantity = $_POST['quantity'];//помещаем полученное количество в переменную
    $_SESSION['order'][$id]['quantity'] = $quantity;//перезаписываем количество для этого товара
    header('Location: order.php?action=basket');
}
/**
 * удаление товара из корзины
 */
function routeDelete(){
    $id = $_GET['id'];
    unset($_SESSION['order'][$id]);

    if ($_SERVER['HTTP_REFERER'] == 'http://php1/order.php') {//если запрос был выполнен из корзины, то
        header('Location: order.php');//возвращаемся в корзину
    } else {//если нет, то
        header('Location: goods.php');//возвращаемся в каталог
    }
}
/**
 * создание нового заказа
 */
function routeNeworder(){
    $user_id = $_SESSION['user']['id'];//берем id пользователя из сессии
    $result = execute("INSERT into php1.orders (user_id, date) values ($user_id, ".time().");");//добавляем новый заказ в БД
    if ($result) {//если успешно, то
        $id_order = lastInsertedId();//получаем id созданного заказа
        // var_dump($id_order);
        foreach($_SESSION['order'] as $id => $good){//для каждого товара в корзине
            $quantity = $good['quantity'];//присваеваем количество
            $result = execute("INSERT into php1.goods_in_order (id_order, id_good, quantity) values ($id_order,$id,$quantity);");//добавляем товар к заказу
            if (!$result) {//если не успешно, то
                message('danger', 'Не удалось оформить заказ');//передаем сообщение
                echo render('site/error');
                break;
            }
        }
        $order = getItemArray("SELECT id_order, id_good, name, quantity, goods.price as price  from php1.goods_in_order
                                left join php1.goods ON goods_in_order.id_good = goods.id
                                where id_order='{$id_order}';",'','id_good');//получаем всю информацию по новому заказу
        if ($order) {//если получили, то
            message('success', 'Заказ успешно сформирован');//передаем сообщение
            echo render('order/neworder', $order);
            unset($_SESSION['order']);//опустошаем корзину
        }else {
            message('danger', 'Не удалось получить информацию по новому заказу');//передаем сообщение
            echo render('site/error');
        }
    }
}

/**
 * корзина
 */
function routeBasket(){
    echo render('order/basket');
}
route();
?>
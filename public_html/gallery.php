<?php


require '../engine/core.php';
/**
 * domain.com/gallery.php
 */

function routeIndex(){
    extract($_SESSION['user']);

    $images = getItemArray("SELECT * from php1.images order by views desc;", 'name');// получаем массив имен упорядоченный по убыванию просмотров
    // var_dump($images);
    if ($images) {
        echo render('gallery/all',$images);// передаем адрес и массив из названий картинок для отрисовки в рендер
    } else {
        echo render('gallery/download');// передаем адрес в рендер
    }
}

/**
 * domain.com/?action=one&image=1.jpg
 * @param $id
 */
function routeOne($id){
    global $config;
    // var_dump($_GET);
    $image = $_GET['image'];// помещаем значение $_GET['image'] в переменную $image
    $img = getItemArray("select * from php1.images where name = '$image'");// поллучаем количество просмотров этой фото
    extract($img);
    $comments = getItemArray("select image_comment.id, text, users.login as login, users.avatar as avatar from php1.image_comment  left JOIN php1.users ON image_comment.user_id = users.id where image_id = '$id'");
    $filePath = $config['app']['bigImagesPath'].'/'.$image;//создаем путь до картинки
    if(file_exists($filePath)){//проверяем наличие указанного файла и если он есть то
        multi_execute ("SET SQL_SAFE_UPDATES = 0;
                        UPDATE php1.images SET views = views + 1 where name = '$image';
                        SET SQL_SAFE_UPDATES = 1;");//увеличиваем количество просмотров этого файла на 1
        echo render('gallery/one', ['img'=>$img, 'comments'=>$comments]);// передаем в рендер адрес и кол-во просмотров
    }else{
        echo render('site/error');// если не удалось удалить, то передаем в рендер адрес ошибки
    }
}

/**
 * domain.com/?action=delete&image=1.jpg
 */
function routeDelete(){
    global $config;

    $image = $_GET['image'];// помещаем значение $_GET['image'] в переменную $image
    if (unlink($config['app']['smallImagesPath'].'/'.$image) && unlink($config['app']['bigImagesPath'].'/'.$image)) {//проверяем, если удалось удалить файл из обеих папок, то
        execute("DELETE FROM php1.images WHERE name = '$image'");//удаляем соответсвующую запись из БД
        if ($_SERVER['HTTP_REFERER'] == 'http://php1/user.php') {
            $_SESSION['user']['avatar'] = '';
            header("Location:". $_SERVER['HTTP_REFERER']);
        }
        message('success', 'Фото успешно удалено.');
        routeIndex();
    }else{
        echo render('site/error');// если не удалось удалить, то передаем в рендер адрес ошибки
    }
}

function routeCreatecomment(){

    // var_dump($_SESSION);
    $image = getItemArray('select * from images where id='.$_POST['image_id']);
    if (!empty($image)) {
        $imageId = (int)$_POST['image_id'];
        $text = $_POST['text'];
        $result = execute("INSERT into php1.image_comment 
                (text, created_at, image_id, user_id) values 
                ('{$text}', '".time()."', '{$imageId}', '".(isset($_SESSION['user']['login'])?$_SESSION['user']['id']:3)."')");
        if ($result) {
            header("Location: gallery.php?action=one&image=".$image['name']);
        }else {
            echo render('site/error');
        }
        // die();
    }
}

function routeDeletecomment(){

    $id = $_GET['id'];
    $image = $_GET['image'];
    var_dump($image);
    execute("DELETE FROM php1.image_comment WHERE id='{$id}'");
    header("Location: gallery.php?action=one&image=".$image);
}

function routeDownload(){
global $config;
extract ($_SESSION['user']);
// var_dump($_SESSION['user']);
    if (!empty($_FILES)) {
        $name_file = $_FILES['photo']['name'];//присваиваем имя полученного файла переменной
        $type_file = $_FILES['photo']['type'];//присваиваем тип полученного файла переменной
        $size_file = $_FILES['photo']['size'];//присваиваем размер полученного файла переменной
        $path = $config['app']['bigImagesPath'] . "/" . $name_file;
        
        if ($size_file != 0) {
            if (substr($type_file, 0, 5) == 'image') {//проверяем тип файла, если это картинка, то
                if ($size_file < 5e+6) {//проверяем размер полученного файла, если не более 5mb, то
                    if (!file_exists($path)){//проверяем наличие файла с таким именем в директории, если нет, то
                        if (move_uploaded_file($_FILES['photo']['tmp_name'], $path)) {//переносим полученный файл из временного хранилища в постоянное, если получается, то
                            list($width, $height) = getimagesize($path);// помещаем в переменные полученные размеры загруженного изображения
                            $new_width = 0.5 * $width; //задаем ширину для уменьшенного изображения
                            $new_height = 0.5 * $height; //задаем высоту для уменьшенного изображения
                            $thumb = imagecreatetruecolor($new_width, $new_height); // создаем новое полноцветное изображение
                            if (substr($type_file, -4) == 'jpeg') {
                                $source = imagecreatefromjpeg($path);//создает новое изображение из полученного файла если формат jpg
                            }
                            if (substr($type_file, -3) == 'png') {
                                $source = imagecreatefrompng($path);//создает новое изображение из полученного файла если формат png
                            }
                            if (substr($type_file, -3) == 'gif'){
                                $source = imagecreatefromgif($path);//создает новое изображение из полученного файла если формат gif
                            }
                            imagecopyresized($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height); // копируем изображение полученное из файла в созданное изображение с новыми размерами
                            imagejpeg($thumb, $config['app']['smallImagesPath'].'/'.$name_file);// сохраняем уменьшенное изображение в другую папку
                            // var_dump($_SERVER['HTTP_REFERER']);
                            if ($_SERVER['HTTP_REFERER'] == 'http://php1/user.php') {
                                execute("update php1.users set avatar = '$name_file' where id = '$id'");
                                $_SESSION['user']['avatar'] = $name_file;
                                header("Location:". $_SERVER['HTTP_REFERER']);
                            }
                            message('success', 'Файл успешно загружен.');
                            execute("insert into php1.images (name, smallimagepath, bigimagepath, views, size, user_id)
                            values ('$name_file', 'img/small', 'img/big', '0', '$size_file', '$id')");//выполняем запрос по добавлению строки в таблицу images
                        } else {
                            message('danger', 'Не удалось загрузить файл.');
                        }
                    }else {
                        message('warning', 'Файл с таким именем уже существует, переименуйте файл и попробуйте заново.');//прекращаем скрипт и выводим
                    }
                } else {
                    message('warning', 'Файл превышает максимальный размер.');
                }
            } else {
                message('warning', 'Неверный тип файла, выберите картинку.');
            }
        }else{
            message('warning', 'Выберите файл для загрузки.');
        }
    }
    routeIndex();
}


route();
?>
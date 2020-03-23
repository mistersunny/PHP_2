<?header('Location: dz4.php');?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script type="text/javascript" src="4.js"></script>
    <title>Четвертое домашнее задание</title>
    <style type="text/css">
        body {
        margin:0;
        }
        .image {
        margin:30px;
        float:left;
        cursor:pointer;
        max-height:100px;
        }
        .popup {
        position: fixed;
        height:100%;
        width:100%;
        top:0;
        left:0;
        display:none;
        text-align:center;
        }
        .popup_bg {
        background:rgba(0,0,0,0.4);
        position:absolute;
        z-index:1;
        height:100%;
        width:100%;
        }
        .popup_img {
        pointer-events: none;
        position: relative;
        margin:0 auto;
        z-index:2;
        max-height:94%;
        max-width:94%;
        margin:1% 0 0 0;
        }
    </style>
</head>
<body>
    <!-- 
1.	Создать галерею фотографий. Она должна состоять из одной страницы, 
на которой пользователь видит все картинки в уменьшенном виде. При 
клике на фотографию она должна открыться в браузере в новой вкладке. 
Размер картинок можно ограничивать с помощью свойства width.
 -->
<?php

include('download.php');
?>
<!-- <h1>Ночь и утро на озере Юлукс Ер</h1> -->
<!-- <p><a href="img/5.jpg" target="newframe"><img src="img/5.jpg" alt="foto 5" style="max-height:100px"></a></p>
<p><a href="img/2.jpg" target="newframe"><img src="img/2.jpg" alt="foto 2" style="max-height:100px"></a></p>
<p><a href="img/3.jpg" target="newframe"><img src="img/3.jpg" alt="foto 3" style="max-height:100px"></a></p>
<p><a href="img/4.jpg" target="newframe"><img src="img/4.jpg" alt="foto 4" style="max-height:100px"></a></p> -->

<!-- 2.	*Строить фотогалерею, не указывая статичные ссылки к 
файлам, а просто передавая в функцию построения адрес папки 
с изображениями. Функция сама должна считать список файлов 
и построить фотогалерею со ссылками в ней. -->
<!-- 3.	*[ для тех, кто изучал JS-1 ] При клике по миниатюре 
нужно показывать полноразмерное изображение в модальном окне -->
<?php

echo '<h1>Галерея Ваших фотографий</h1>';
$dir = 'img/small';

function galery($dir){
    if (is_dir($dir)) {// проверяем является ли входящий параметр директорий и если да, то
        $list = scandir($dir);//сканируем директорию и создаем массив из содержимого данной директории
        unset($list[0], $list[1]);//удаляем первые 2 элемента массива, так как, они являются ссылками(".""..")
        foreach ($list as $file) {//обходим оставшиеся элементы массива и для каждого из них
            if (is_dir($dir.DIRECTORY_SEPARATOR.$file)) {// проверяем является данный элемент директорией и если да, то
                galery($dir.DIRECTORY_SEPARATOR.$file);//запускаем функцию снова
            }else{//если не является, то
                if (exif_imagetype($dir.DIRECTORY_SEPARATOR.$file)) {//проверяем является ли данный файл картинкой, если да, то
                    echo '<p><img src="'.$dir.'/'.$file.'" class = "image" alt="'.$file.'"></p>';//выводим данную строку
                }
            }
            // include $dir.DIRECTORY_SEPARATOR.$file;
        }
    }else{//если входящий в функцию параметр не является директорий, то
        echo 'Папка не найдена';//выводим данную строку
    }
}
galery($dir);
?>
</body>
</html>
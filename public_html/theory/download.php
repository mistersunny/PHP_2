<form action="" method="POST" enctype="multipart/form-data">
    <p>Загрузите Ваши фото размером не более 5Mb в формате jpg, png или gif</p>
    <input type="file" name="photo"> <br>
    <input type="submit" value="Отправить">
</form>
<?php
// print_r($_FILES);
$name_file = $_FILES['photo']['name'];//присваиваем имя полученного файла переменной
$type_file = $_FILES['photo']['type'];//присваиваем тип полученного файла переменной
$size_file = $_FILES['photo']['size'];//присваиваем размер полученного файла переменной
$path = "img/big/".$name_file;//создаем путь сохранения файла
if ($size_file == 0) {
    echo 'Выберите файл для загрузки.';
}else{
    if (substr($type_file, 0, 5) == 'image') {//проверяем тип файла, если это картинка, то
    if ($size_file < 5e+6) {//проверяем размер полученного файла, если не более 5mb, то
        if (file_exists($path)) {//проверяем наличие файла с таким именем в директории, если есть, то
            echo 'Файл с таким именем уже существует, переименуйте файл и попробуйте заново.';//прекращаем скрипт и выводим
        } else {//если такого файла нет, то
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
                imagejpeg($thumb, 'img/small/'.$name_file);// сохраняем уменьшенное изображение в другую папку
                echo 'Файл ' , $name_file , ' успешно загружен';//выводим сообщение
            } else {
                echo 'Не удалось загрузить файл.';
            }
        }
    } else {
        echo 'Файл превышает максимальный размер.';
    }
    } else {
        echo 'Неверный тип файла, выберите картинку.';
    }
}
?>
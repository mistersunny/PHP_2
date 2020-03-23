<?php
/**
 * собираем все файлы находящиеся в этой папке и отдающие массив, в массив $config
 */
$config=[];//объявляем массив
$filelist = glob(ROOT.'\config\\'.'*.php');//массив с путями всех php файлов этой папки
// var_dump($filelist);
foreach ($filelist as $file) {//для каждого значения массива
    if ($file != __FILE__) {//проверяем если это не этот же файл, то
        if (is_array(require($file)) == true) {// проверяем, если этот файл возвращает массив, то
            $config = array_merge($config, require($file));//добавляем в $config полученный массив
        }
    }
}
// var_dump($config);

require(ROOT . '/engine/database.php');// подключаем файл работы с БД для подключения меню

/**
 * обновляем файл menu.php данными о пунктах меню из БД
 */
$result = getItemArray('SELECT * FROM php1.menu');
if ($result) {
    $menu = ['menu' => $result];//создаем массив с полученными пунктами меню из БД
    $menu = serialize($menu);//превращаем нашу перепеменную в строку
    $write = "<?php return unserialize('".$menu."')?>";//изменяем строку для записи ее в файл так, что бы она возвращала изначальный массив 
    file_put_contents('../config/menu.php', $write);// запись в файл полученных данных
}


/**
 * Функция по добавлению файлов в массив assets массива app
 */
function assets (string $dir, string $folder, string $extension){
    global $config;//используем переменную из вне фуекции
    if (!array_key_exists('assets',$config['app'])) {//если в массиве $config['app'] нет ключа assets, то
        $config['app']['assets'] = [];//добавляем в этот массив массив с таким ключом
    }
    if (!array_key_exists($extension, $config['app']['assets'])) {//если в массиве $config['app']['assets'] нет ключа $extension, то
        $config['app']['assets'][$extension] = [];//добавляем в этот массив массив с ключом $extension
    }
    $filelist = glob($dir.'\\*');//получаем массив всех файлов по полученному пути
    foreach ($filelist as $file) {//для каждого значения массива
        $info = pathinfo($file);//получаем массив инфомации о файле
        if (!array_key_exists('extension', $info)) {//если по этому пути нет расширения, то это папка
            assets($dir. '\\' . $info['basename'], '\\'.$extension, $extension);//запускаем рекурсию
            continue;
        }
        $file = $folder.'\\'.basename($info['dirname']).'\\'.$info['basename'];//фомируем запись 
        // var_dump($file);
        if ($info['extension'] == $extension){//если расширение файла соответсвует нужному, то
            if (!in_array($file, $config['app']['assets'][$extension])) {//проверяем есть ли такая запись в массиве, если нет то
                $config['app']['assets'][$extension][] = $file;//добавляем эту запись в массив
            }
        }
    }
}

assets (ROOT.'\public_html\\js','','js');//собираем все js файлы
assets (ROOT.'\public_html\\css','','css');//собираем все css файлы

// var_dump($config['app']);
// var_dump(get_included_files());
?>

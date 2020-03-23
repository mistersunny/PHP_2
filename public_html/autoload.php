<?php

spl_autoload_register(function($interface){
    $path = 'Interfaces/' . $interface . '.interface.php';
    if (file_exists($path)){
        include $path; 
    }
 });

spl_autoload_register(function($name){
    $path = 'Classes/' . $name . '.class.php';
    if (file_exists($path)){
        include $path; 
    }
});

spl_autoload_register(function($trait){
    $path = 'Traits/' . $trait . '.trait.php';
    if (file_exists($path)){
        include $path; 
    }
});


// var_dump(spl_autoload_functions());


?>
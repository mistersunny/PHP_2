<?php
    if(isset($_POST['op']) && $_POST['op'] != 'Выберите действие'){
        $a = $_POST['a'];
        $b = $_POST['b'];
        $op = $_POST['op'];
        switch ($op) {
            case '+':
                echo $a + $b;;
                break;
            case '-':
                echo $a - $b;
                break;
            case '*':
                echo $a * $b;
                break;
            case '/':
                if ((int)$b !== 0) {
                    $rezult = $a / $b; $z = "/";
                } else {
                echo 'На ноль делить нельзя';
                }
                break;
        }
    }else{
        echo 'Выберите действие';
    }
?>

<?php
/**
 * передаем сообщение в сессию
 */
function message(string $status, string $text){
    $_SESSION['message'] = ['status' => $status, 'text' => $text];
}
?>
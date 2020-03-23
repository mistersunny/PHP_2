<?php
global $config;

if (empty($_SESSION['user'])) {
    autologin();
}
// var_dump($_SESSION);
extract($_SESSION['user']);
if(empty($_SESSION['user']['login'])): ?>
    <div class="btn-group float-right">
        <a href="user.php?action=register" class="btn btn-success">Регистрация</a>
        <a href="user.php?action=login" class="btn btn-primary">Вход</a>
    </div>
<?php else: ?>

    <div class="btn-group float-right">
        <?php if(!empty($_SESSION['user']['login'])):?>
            <a class="nav-link disabled" href="order.php?action=basket">
                <img src="<?=$config['app']['imagesPath'].'/Корзина.jpg'?>" height="25px">
            </a>
            <a class="nav-link disabled" href="user.php">
            <?php if(!empty($_SESSION['user']['avatar'])):?>
                <img src="<?=$config['app']['smallImagesPath'].'/'.$avatar?>" height="25px" style="border-radius: 12px" alt="">
            <?php endif?>
            <?=$_SESSION['user']['login']?>
            </a>
        <?php endif?>
        <a href="user.php?action=logout" class="btn btn-danger">Выход</a>
    </div>
<?php endif?>
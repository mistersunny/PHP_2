<?php
/**
 * @var string $content - содержимое страницы на отрисовку
 */
global $config;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $config['app']['name'] ?></title>
<!--<link rel="shortcut icon" href="/img/logo.png">-->

<?php foreach ($config['app']['assets']['css'] as $file): ?>
    <link rel="stylesheet" href="<?= $file ?>">
<?php endforeach ?>
</head>
<body>





<div class="container-fluid">
    <div class="row justify-content-between">
        <div class="col-lg-2"></div>        
        <div class="col-lg-8">
            <?php echo render('layouts/widgets/login', [], false, '');?>
            <?php echo render('layouts/widgets/menu', [], false, '');?>
        </div>
        <div class="col-lg-2"></div>
    </div>
    <div class="row">
        <div class="col-lg-2"></div>                            
        <div class="col-md-auto">
        <?php echo render('layouts/widgets/message', [], false, '');?>
            <?= $content ?>
        </div>
    </div>
</div>







<?php
arsort($config['app']['assets']['js']);
foreach ($config['app']['assets']['js'] as $file) : ?>
    <script src="<?= $file ?>"></script>
<?php endforeach; ?>
</body>
</html> 
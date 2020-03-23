<?php if (isset($_SESSION['message']) && !empty($_SESSION['message'])):?>
    <div class="alert alert-<?=$_SESSION['message']['status']?>" role="alert">
        <?=$_SESSION['message']['text']?>
    </div>
<?php endif?>
<?php 
$_SESSION['message'] = '';
?>
<ul class="nav nav-tabs">
<?php
global $config;
$menu = $config['menu'];
// var_dump($_SESSION);
// var_dump($config['menu']);
extract($_SESSION['user']);

foreach ($menu as $key => $value) {
  if (isset($role) && $menu[$key][$role] == 1) {

    foreach ($menu as $value2) {
        $is_parent = $value['id'] == $value2['parent_id'];
        if ($is_parent) {?>
            <li class="nav-item dropdown ">
            <a class="nav-link dropdown-toggle <?=$_SERVER['SCRIPT_NAME'] == $value['file']?' active':'';?>" data-toggle="dropdown" href="<?=$value['file']?>" role="button" aria-haspopup="true" aria-expanded="false"><?=$value['name']?></a>
            <div class="dropdown-menu">
            <?php foreach ($menu as $key => $value2) {
                if ($value['id']==$value2['parent_id'] && (isset($role) && $menu[$key][$role] == 1)) {?>
                    <a class="dropdown-item" href="<?=$value2['file']?>"><?=$value2['name']?></a>
                <?php }
            }?>
            </div>
            </li>
          <?php 
        $value['parent_id'] = 1;
        break;
          }
    }
    if (empty($value['parent_id']) && (isset($role) && $menu[$key][$role] == 1)) {?>
      <li class="nav-item">
        <a class="nav-link <?=$_SERVER['SCRIPT_NAME'] == $value['file']?' active':'';?>" href="<?=$value['file']?>"><?=$value['name']?></a>
      </li>
      
  <?php }
  }
}
?>
</ul>
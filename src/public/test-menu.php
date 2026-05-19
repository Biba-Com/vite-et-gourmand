<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/MenuModel.php';

$pdo = getDbConnection();
$model = new MenuModel($pdo);
$menu = $model->getBySlug('menu-elegance');

echo '<pre>';
var_dump($menu);
echo '</pre>';
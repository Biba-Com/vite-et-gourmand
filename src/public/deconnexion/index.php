<?php

/**
 * Déconnexion — détruit la session et redirige.
 * Chemin : src/public/deconnexion/index.php
 */

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

$pdo            = getDbConnection();
$userModel      = new UserModel($pdo);
$authController = new AuthController($userModel, $pdo);

$authController->logout();

header('Location: /');
exit;

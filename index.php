<?php
    require_once 'settings/settings.php';
    require_once 'core/controller.php';
    require_once 'core/model.php';
    require_once 'core/view.php';

    $controller = new Controller();
	$controller->run();
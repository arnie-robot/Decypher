<?php
function Ariel_Autoload($class)
{
	// Already loaded
	if (class_exists($class, false)) {
		return;
	}

    $file = str_replace('_',DIRECTORY_SEPARATOR,$class) . '.php';

	foreach (explode(PATH_SEPARATOR, get_include_path()) as $path) {
		if (file_exists($path . DIRECTORY_SEPARATOR . $file)) {
			require_once($path . DIRECTORY_SEPARATOR . $file);
			return;
		}
	}

    throw new Exception('Class ' . $class . ' not found');
}

// register autoloader
spl_autoload_register('Ariel_Autoload');
<?php defined('BASE_PATH') OR exit('No direct script access allowed'); ?>

An uncaught Exception was encountered

Type:        <?php echo get_class($exception), "\n"; ?>
Message:     <?php echo $message, "\n"; ?>
Filename:    <?php echo $exception->getFile(), "\n"; ?>
Line Number: <?php echo $exception->getLine(); ?>

Backtrace:
<?php	foreach ($exception->getTrace() as $error): ?>
<?php		if (isset($error['file']) && strpos($error['file'], realpath(BASE_PATH)) !== 0): ?>
	File: <?php echo $error['file'], "\n"; ?>
	Line: <?php echo $error['line'], "\n"; ?>
	Function: <?php echo $error['function'], "\n\n"; ?>
<?php		endif ?>
<?php	endforeach ?>

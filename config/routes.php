<?php
return array(
	'enter' => 'login/enter',
	'logout' => 'login/out',
    'login' => 'login/index',
    'create' => 'tasks/create',
    'save' => 'tasks/save',
    'edit/([0-9]+)' => 'tasks/edit/$1',
    'update' => 'tasks/update',
    'tasks/([0-9]+)' => 'tasks/show/$1',
    'list' => 'tasks/index',
    '' => 'tasks/index',
	);

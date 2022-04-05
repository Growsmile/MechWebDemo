<?php

return [
	'/'                             => ['home@index', 'GET'],
	'/serviceRequest'               => ['home@serviceRequest', 'POST'],		
	'/login'                        => ['login@index', 'GET'],	
	'/logins'                       => ['login@login', 'POST'],		
	'/users'                        => ['users@index', 'GET'],
	'/users/save'                   => ['users@save', 'POST'],	
	'/users/create'                 => ['users@create', 'GET'],
	'/users/editMembers'            => ['users@editMembers', 'POST'],			
	'/users/delete'                 => ['users@delete', 'POST'],
	'/users/VehicleHistoryCreate'   => ['users@VehicleHistoryCreate', 'POST'],
	'/users/editCurrentUser'        => ['users@editCurrentUser', 'POST'],	
	'/users/historyPaidCheck'       => ['users@historyPaidCheck', 'POST'],	
	'/users/requestSeen'        	=> ['users@requestSeen', 'POST'],
	'/users/vehHistory/delete'      => ['users@vehHistoryDelete', 'POST'],
	'/users/vehHistoryForm/edit'    => ['users@vehHistoryFormEdit', 'POST'],	
	'/logout'                       => ['users@logout', 'GET'],	
];

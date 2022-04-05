<?php

class UsersController {
	public function indexAction()
	{
		if(!isset($_SESSION['user'])){
			header('location:/login');
		}
		$conn = db();

		$loader = new \Twig\Loader\FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . '/views/');


		$twig = new \Twig\Environment($loader, [
			'cache' => $_SERVER['DOCUMENT_ROOT'] . '/tmp/',
		]);


		$getdata = $conn->prepare("SELECT id, username, name, surname, password, phoneNr, email, class FROM users");
		$getdata->execute();	
		$users = $getdata->fetchAll(PDO::FETCH_ASSOC);

		$getdata2 = $conn->prepare("SELECT * FROM requests");
		$getdata2->execute();	
		$requests = $getdata2->fetchAll(PDO::FETCH_ASSOC);
//Auto paņemšana vēsturei
		$getdata3 = $conn->prepare("SELECT *
		FROM vehHistory 
		");
		$getdata3->execute();	
		$vehHistory = $getdata3->fetchAll(PDO::FETCH_ASSOC);

		foreach ($vehHistory as $i => $auto){
			$getdata5 = $conn->prepare("SELECT * FROM vehHistoryDiagnostic where diag_vehId = ".$auto['id']);
			$getdata5->execute();	
			$auto['diagnostics'] = $getdata5->fetchAll(PDO::FETCH_ASSOC);
			$vehHistory[$i] = $auto;
		}	

		foreach ($vehHistory as $j => $autoj){
			$getdata7 = $conn->prepare("SELECT * FROM vehHistoryJob where job_vehId = ".$autoj['id']);
			$getdata7->execute();	
			$autoj['vehHistoryJob'] = $getdata7->fetchAll(PDO::FETCH_ASSOC);
			$vehHistory[$j] = $autoj;
		}	
		
		foreach ($vehHistory as $p => $autop){
			$getdata6 = $conn->prepare("SELECT * FROM vehHistoryParts where parts_vehId = ".$autop['id']);
			$getdata6->execute();	
			$autop['vehHistoryParts'] = $getdata6->fetchAll(PDO::FETCH_ASSOC);
			$vehHistory[$p] = $autop;
		}			
		// echo '<pre>';
		// var_dump($vehHistory);
		// die;
////Auto paņemšana vēsturei beigas
		
		// $modalVehicleHistoryVehId = trim($_GET['modalVehicleHistoryVehId']);
		// $getdata4 = $conn->prepare("SELECT *
		// FROM vehHistoryDiagnostic 
		// WHERE diag_vehId Like $modalVehicleHistoryVehId
		// ");
		// $getdata4->execute();	
		// $vehHistoryDiag = $getdata4->fetchAll(PDO::FETCH_ASSOC);		




		$currentUser4 = $_SESSION['user'];
		$getdata4 = $conn->prepare("SELECT * FROM users WHERE id = $currentUser4 ");
		$getdata4->execute();	
		$currentUsers = $getdata4->fetchAll(PDO::FETCH_ASSOC);
		// vehHistory.id vehHistory.vehId, vehHistory.date, vehHistory.clientNr, vehHistory.brand, vehHistory.model, vehHistory.vin, vehHistory.engine, vehHistory.vehRange, vehHistory.registrationDate, vehHistory.nummberPlate, vehHistory.master, vehHistory.dateUpdated, vehHistoryDiagnostic.name
		// izrenderē datus uz twig
		return $twig -> render('users/index.twig', [
			'users' => $users,
			'requests' => $requests,
			'vehHistory' => $vehHistory,
			'currentUsers' => $currentUsers,
			// 'vehHistoryDiag' => $vehHistoryDiag,

		]);


		
		$twig->render('users/index.twig');
		$twig->render('basic.twig');
		$twig->render('homePage.twig');
		$twig->render('navigation.twig');
		$twig->render('options.twig');
		$twig->render('tableDemand.twig');
		$twig->render('userManager.twig');
		$twig->render('vehicleHistory.twig');	


		//$users = [
		//	['id' => 1, 'username' => 'Jānis'],
		//	['id' => 2, 'username' => 'Ieva']
		//];

		return $twig -> render('users/index.twig', [
			//	 'name' => 'Dagnis',
			//	 'users' => $users
	
			]);
	
			// $stmt = $pdo->query('SELECT name FROM users');
			// while ($row = $stmt->fetch())
			// {
			// 	echo $row['name']. "\n";
			// };
	
	}

	public function saveAction()
	{
		

		$loader = new \Twig\Loader\FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . '/views/');


		$twig = new \Twig\Environment($loader, [
			'cache' => $_SERVER['DOCUMENT_ROOT'] . '/tmp/',
		]);
	

		# Dati kuru vēlies ielikt ,,user,,

		  if(isset($_POST['addMember'])){  
			try {
				$conn3 = db();
				$conn3->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			 $username = trim($_POST['username']);
			 $name = trim($_POST['name']);
			 $surname = trim($_POST['surname']);
			 $password = trim($_POST['password']);
			 $phoneNr = trim($_POST['phoneNr']);
			 $email = trim($_POST['email']);
			 $class = trim($_POST['class']);
	
			$password = password_hash($password, PASSWORD_BCRYPT, array("cost" => 12));
			  
			 //Pārbauda vai lietotājvārds pastāv
			 $sql3 = "SELECT COUNT(username) AS num FROM users WHERE username = :username";
			 $stmt3 = $conn3->prepare($sql3);
	
			 $stmt3->bindValue(':username', $username);
			 $stmt3->execute();
			 $row = $stmt3->fetch(PDO::FETCH_ASSOC);
	
			 if($row['num'] > 0){
				 echo "<script>alert('Lietotājvārds jau pastāv!')</script>
                    <script>window.location = '/users'</script>
                    ";
				//  $usernameExistsError= "Lietotājvārds jau pastāv!";                   
				//  return $twig -> render('users/index.twig', [
				// 	 'usernameExistsError' => $usernameExistsError,
				//  ]);

			}else{
	
				$stmt3 = $conn3->prepare("INSERT INTO users (username, name, surname, password, phoneNr, email, class) 
				VALUES (:username, :name, :surname, :password, :phoneNr, :email, :class)");
				$stmt3->bindParam(':username', $username);
				$stmt3->bindParam(':name', $name);
				$stmt3->bindParam(':surname', $surname);
				$stmt3->bindParam(':password', $password);
				$stmt3->bindParam(':phoneNr', $phoneNr);
				$stmt3->bindParam(':email', $email);
				$stmt3->bindParam(':class', $class);				

				// header('Location:/users');
							

				if($stmt3->execute()){
					// echo '<script>alert("Jauns profils izveidots!.")</script>';

					//nosūta uz lapu
					header('Location:/users');
					exit(0);
					
				}else{
					echo "<script>alert('Netika izveidots!')</script>
                    <script>window.location = '/users'</script>
                    ";
					exit(0);
				}
				}
				}catch(PDOException $e){
					$error = "Error: " . $e->getMessage();
					echo '<script type="text/javascript">alert("'.$error.'");</script>';
					header('Location:/');
					exit(0);
				}
		 }
	

	}
	//Lietotāju labošana
	public function editMembersAction()
	{
		
		if(isset($_POST['editMember'])){
			$username = trim($_POST['username']);
			$name = trim($_POST['name']);
			$surname = trim($_POST['surname']);
			$password = trim($_POST['password']);
			$phoneNr = trim($_POST['phoneNr']);
			$email = trim($_POST['email']);
			$class = trim($_POST['class']);
			$oldPassword = trim($_POST['oldPassword']);
			$editMember_id = $_POST['editMember'];

			$password = password_hash($password, PASSWORD_BCRYPT, array("cost" => 12));

			if(empty($_POST['password']) || is_null($_POST['password']) || !isset($_POST['password']) || $_POST['password'] == "") {
				$password = $oldPassword;
			} 			

			$textAdmin = "Administrators";
			$textUser = "Lietotājs";

			if( ($_POST['class'] == $textAdmin) || ( $_POST['class'] == 1 )){
				$class = 1;
			}elseif(($_POST['class'] == $textUser) || ( $_POST['class'] == 0 )){
				$class = 0;
			}else{
				$class = 0;
			}

		
			try {
				$conn = db();
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$sql = "SELECT COUNT(username) AS num FROM users WHERE username = :username AND NOT id = '$editMember_id'";
				// OFFSET :$editMember_id
				$stmt = $conn->prepare($sql);
	   
				$stmt->bindValue(':username', $username);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
	   
				if($row['num'] > 0){
					echo "<script>alert('Lietotājvārds jau pastāv!')</script>
					   <script>window.location = '/users'</script>
					   ";


				    // $usernameExistsError= "Lietotājvārds jau pastāv!";                   
				    // return $twig -> render('users/index.twig', [
				   	//  'usernameExistsError' => $usernameExistsError,
				    // ]);
   
			   }else{

				

				$query = "UPDATE users SET username=:username, name=:name, surname=:surname, password=:password, phoneNr=:phoneNr, email=:email, class=:class WHERE id=:editMember_id LIMIT 1";
				$statement = $conn->prepare($query);
				$statement->bindParam(':username', $username);
				$statement->bindParam(':name', $name);
				$statement->bindParam(':surname', $surname);
				$statement->bindParam(':password', $password);
				$statement->bindParam(':phoneNr', $phoneNr);
				$statement->bindParam(':email', $email);
				$statement->bindParam(':class', $class);												
				$statement->bindParam(':editMember_id', $editMember_id, PDO::PARAM_INT);
				$query_execute = $statement->execute();
		
				if($query_execute)
				{
					//$_SESSION['message'] = "Atjaunots veiksmīgi!";
					header('Location:/users');
					exit(0);
				}
				else
				{
					//$_SESSION['message'] = "Radās problēma!";
					header('Location:/');
					exit(0);
				}
			}			
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
		}
	}
	//Leitotāju dzēšana
	public function deleteAction()
	{

			if( isset($_POST['deleteMember']) )
			{
				$member_id= $_POST['deleteMember'];

				try {
					$conn2 = db();
			
					$query2 = "DELETE FROM users WHERE id=:member_id";
					$statement2 = $conn2->prepare($query2);
					$data2 = [
						':member_id' => $member_id
					];
					$query_execute2 = $statement2->execute($data2);
			
					if($query_execute2)
					{
						header('Location:/users');
						exit(0);
					}
					else
					{
						// echo "Radās problēma! Lietotājs netika dzēsts!";
						echo "<script>alert('Radās problēma! Lietotājs netika dzēsts!')</script>
						<script>window.location = '/users'</script>
						";
						// header('Location:/');
						exit(0);
					}
			
				} catch(PDOException $e2){
					echo $e2->getMessage();
				}
			}
	}

	//Auto vēstures ievade
	public function VehicleHistoryCreateAction()
	{
	
		if(isset($_POST['VehicleHistoryCreate']))
		{
			
			$conn = db();
			$vehId = uniqid();
			// $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
			$date = trim($_POST['date']);
			$clientNr = trim($_POST['clientNr']);
			$brand = trim($_POST['brand']);
			$model = trim($_POST['model']);
			$vin = trim($_POST['vin']);
			$Engine	 = trim($_POST['Engine']);
			$vehRange = trim($_POST['vehRange']);
			$registrationDate = trim($_POST['registrationDate']);
			$nummberPlate = trim($_POST['nummberPlate']);
			$master = trim($_POST['master']);
			
			// $dateUpdated = $_POST['dateUpdated'];	
			
			try {

				$query = "INSERT INTO vehHistory (date, clientNr, brand, model, vin, Engine, vehRange, registrationDate, nummberPlate, master ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";
				$statement = $conn->prepare($query);
				// $statement->bindParam(1, $vehId);
				$statement->bindParam(1, $date);
				$statement->bindParam(2, $clientNr);
				$statement->bindParam(3, $brand);
				$statement->bindParam(4, $model);
				$statement->bindParam(5, $vin);
				$statement->bindParam(6, $Engine);
				$statement->bindParam(7, $vehRange);	
				$statement->bindParam(8, $registrationDate);
				$statement->bindParam(9, $nummberPlate);
				$statement->bindParam(10, $master);							
				$query_execute = $statement->execute();
		
				if($query_execute)
				{
					//$_SESSION['message'] = "Added Successfully";
					// header('Location:/users');
					// exit(0);
				}
				else
				{
					echo "<script>alert('Radās problēma!')</script>
					<script>window.location = '/users'</script>
					";
					// echo "Radās problēma!";
					// header('Location:/');
					exit(0);
				}
		
			} catch (PDOException $e) {
		
				echo "Error tips:". $e->getMessage();
			}

			$id = $conn->lastInsertId();


			// Diagnostika
				for($count = 0; $count < count($_POST["name"]); $count++)
				{
								
					$query2 = "
					INSERT INTO vehHistoryDiagnostic 
					(diag_vehId, name) 
					VALUES (:diag_vehId, :name)
					";
								
					$statement = $conn->prepare($query2);
								
					$statement->execute(
						array(
							':diag_vehId'		=>	$id,
							':name'	=>	$_POST["name"][$count]
							)
						);
								
				}
								
				$result = $statement->fetchAll(PDO::FETCH_ASSOC);
								
				if(isset($result))
				{
					echo 'ok';
					header('Location:/users');
				}
						
						
						
						
						
			// Darbs
				for($counts4 = 0; $counts4 < count($_POST["darbs"]); $counts4++)
				{
								
					$querys4 = "
					INSERT INTO vehHistoryJob 
					(job_vehId, job_name, job_price) 
					VALUES (:job_vehId, :job_name, :job_price)
					";
								
					$statements4 = $conn->prepare($querys4);
								
					$statements4->execute(
						array(
							':job_vehId'		=>	$id,
							':job_name'	=>	$_POST["darbs"][$counts4],
							':job_price'	=>	$_POST["darbsSumma"][$counts4]
							)
						);
								
					}
								
					$results4 = $statements4->fetchAll(PDO::FETCH_ASSOC);
								
					if(isset($results4))
						{
							echo 'ok';
							header('Location:/users');
						}
						
						
						
			// Detaļas
				for($counts2 = 0; $counts2 < count($_POST["detalas"]); $counts2++)
					{
								
					$querys2 = "
					INSERT INTO vehHistoryParts 
					(parts_vehId, parts_name, parts_price1x, parts_quantity) 
					VALUES (:parts_vehId, :parts_name, :parts_price1x, :parts_quantity)
					";
								
				$statements2 = $conn->prepare($querys2);
								
				$statements2->execute(
					array(
						':parts_vehId'		=>	$id,
						':parts_name'	=>	$_POST["detalas"][$counts2],
						':parts_price1x'	=>	$_POST["detalasPrice"][$counts2],
						':parts_quantity'	=>	$_POST["detalasQuantity"][$counts2]
					)
				);
								
			}
								
			$results2 = $statements2->fetchAll(PDO::FETCH_ASSOC);
								
			if(isset($results2))
			{
				echo 'ok';
				header('Location:/users');
			}

		}
	}
	//Lietotāja labošana kas ir sesijā ,,option panel,,
	public function editCurrentUserAction(){


		if(isset($_POST['editCurrentUser'])){
	

			try {

				$loader = new \Twig\Loader\FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . '/views/');


				$twig = new \Twig\Environment($loader, [
					'cache' => $_SERVER['DOCUMENT_ROOT'] . '/tmp/',
				]);

				$conn = db();
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$phoneNr = trim($_POST['phoneNr']);
				$email = trim($_POST['email']);
				$username = trim($_POST['username']);
				$oldPassword = trim($_POST['oldPassword']);
				$password = trim($_POST['password']);
				$editCurrentUser_id = trim($_POST['editCurrentUser']);

				$password = password_hash($password, PASSWORD_BCRYPT, array("cost" => 12));
	
				if(empty($_POST['password']) || is_null($_POST['password']) || !isset($_POST['password']) || $_POST['password'] == "") {
					$password = $oldPassword;
				} 

				//Pārbauda vai lietotājvārds pastāv
				$sql = "SELECT COUNT(username) AS num FROM users WHERE username = :username AND NOT id = '$editCurrentUser_id'";
				$stmt = $conn->prepare($sql);
					
				$stmt->bindValue(':username', $username);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
					
				if($row['num'] > 0){
					echo "<script>alert('Lietotājvārds jau pastāv!')</script>
						<script>window.location = '/users'</script>
					";
				
				}else{

				
				
				$query = "UPDATE users SET phoneNr=:phoneNr, email=:email, username=:username, password=:password WHERE id=:editCurrentUser_id LIMIT 1";
				$statement = $conn->prepare($query);
				$statement->bindParam(':phoneNr', $phoneNr);
				$statement->bindParam(':email', $email);
				$statement->bindParam(':username', $username);
				$statement->bindParam(':password', $password);														
				$statement->bindParam(':editCurrentUser_id', $editCurrentUser_id, PDO::PARAM_INT);
				$query_execute = $statement->execute();
		
				if($query_execute)
				{
					//$_SESSION['message'] = "Atjaunots veiksmīgi!";
					header('Location:/users');
					exit(0);
				}
				else
				{
					echo "<script>alert('Radās problēma!')</script>
					<script>window.location = '/users'</script>
					";
					//$_SESSION['message'] = "Radās problēma!";
					// header('Location:/');
					exit(0);
				}
			}	
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
		}

	}

	public function historyPaidCheckAction(){

		if(isset($_POST['btnPaidId']))
		{
			$btnValuePayHistoryId = $_POST['btnPaidId'];
			$PayValue1 = $_POST['checkValue'];;
			try {
				$conn = db();
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$query = "UPDATE vehHistory SET paid=:PayValue1 WHERE id=:btnValuePayHistoryId";
				$statement = $conn->prepare($query);
				$statement->bindParam(':PayValue1', $PayValue1);													
				$statement->bindParam(':btnValuePayHistoryId', $btnValuePayHistoryId, PDO::PARAM_INT);
				$query_execute = $statement->execute();
		
				if($query_execute)
				{
					// header('Location:/users');
					exit(0);
				}
				else
				{
					header('Location:/');
					exit(0);
				}
		
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
		}

	}

	public function requestSeenAction()
	{

		if(isset($_POST['sid']))
		{
			$btnValuePayHistoryId = $_POST['sid'];
			$PayValue1 = $_POST['checkVal'];;
			try {
				$conn = db();
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$query = "UPDATE requests SET req_Seen=:PayValue1 WHERE id=:btnValuePayHistoryId";
				$statement = $conn->prepare($query);
				$statement->bindParam(':PayValue1', $PayValue1);													
				$statement->bindParam(':btnValuePayHistoryId', $btnValuePayHistoryId, PDO::PARAM_INT);
				$query_execute = $statement->execute();
		
				if($query_execute)
				{
					// header('Location:/users');
					exit(0);
				}
				else
				{
					echo "<script>alert('Radās problēma!')</script>
					<script>window.location = '/users'</script>
					";
					// header('Location:/');
					exit(0);
				}
		
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
		}

	}
	
	public function vehHistoryDeleteAction()
	{
		if( isset($_POST['modalDeleteVehicleHistory']) )
		{
			$veh_id= $_POST['modalDeleteVehicleHistory'];

			try {
				$conn2 = db();
		
				$query2 = "DELETE FROM 	vehHistory WHERE id=:veh_id";
				$query3 = "DELETE FROM 	vehHistoryDiagnostic WHERE diag_vehId=:veh_id";
				$query4 = "DELETE FROM 	vehHistoryJob WHERE job_vehId=:veh_id";
				$query5 = "DELETE FROM 	vehHistoryParts WHERE parts_vehId=:veh_id";
				$statement2 = $conn2->prepare($query2);
				$statement3 = $conn2->prepare($query3);
				$statement4 = $conn2->prepare($query4);
				$statement5 = $conn2->prepare($query5);
				$data2 = [
					':veh_id' => $veh_id
				];
				$query_execute2 = $statement2->execute($data2);
				$query_execute3 = $statement3->execute($data2);
				$query_execute4 = $statement4->execute($data2);
				$query_execute5 = $statement5->execute($data2);
		
				if($query_execute2 || $query_execute3 || $query_execute4 || $query_execute5)
				{
					header('Location:/users');
					exit(0);
				}
				else
				{
					echo "<script>alert('Radās problēma!')</script>
					<script>window.location = '/users'</script>
					";
					// echo "Radās problēma! ";
					// header('Location:/');
					exit(0);
				}
		
			} catch(PDOException $e2){
				echo $e2->getMessage();
			}
		}

	}

	public function vehHistoryFormEditAction()
	{
		$conn = db();
		//define index of column
		$columns = array(
			0 => 'Diagnostic.name'
			// 1 => 'vehJob.job_name',
			// 2 => 'vehJob.job_price',
			// 3 => 'vehParts.parts_name',
			// 4 => 'vehParts.parts_price1x',
			// 5 => 'vehParts.parts_quantity'	
		);
		$error = true;
		$colVal = '';
		$colIndex = $rowId = 0;
		
		$msg = array('status' => !$error, 'msg' => 'Failed! updation in mysql');
		
		if(isset($_POST)){
			if(isset($_POST['val']) && !empty($_POST['val']) && $error) {
			$colVal = $_POST['val'];
			$error = false;
			
			} else {
			$error = true;
			}
			if(isset($_POST['index']) && $_POST['index'] >= 0 &&  $error) {
			$colIndex = $_POST['index'];
			$error = false;
			} else {
			$error = true;
			}
			if(isset($_POST['id']) && $_POST['id'] > 0 && $error) {
			$rowId = $_POST['id'];
			$error = false;
			} else {
			$error = true;
			}
		
			if(!$error) {
				$sql = "UPDATE vehHistoryDiagnostic SET ".$columns[$colIndex]." = '".$colVal."' WHERE id='".$rowId."'";
				$status = mysqli_query($conn, $sql) or die("database error:". mysqli_error($conn));
				$msg = array('status' => !$error, 'msg' => 'Success! updation in mysql');
			}
		}
		
		// send data as json format
		echo json_encode($msg);

	}

	//Iziešana no sesijas
	public function logoutAction()
	{

            unset($_SESSION['user']);
			header('location:/login');

	}	


}

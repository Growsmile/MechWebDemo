<?php

class HomeController {
	public function indexAction()
	{
		//  return 'sakumlapa';
		 $loader = new \Twig\Loader\FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . '/views/');


		 $twig = new \Twig\Environment($loader, [
			 'cache' => $_SERVER['DOCUMENT_ROOT'] . '/tmp/',
		 ]);	
		 return $twig -> render('home/index.twig', [
			 // 'name' => 'Dagnis',
			 // 'users' => $users
 
		 ]);	

	}
	public function serviceRequestAction()
	{
		 $loader = new \Twig\Loader\FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . '/views/');


		 $twig = new \Twig\Environment($loader, [
			 'cache' => $_SERVER['DOCUMENT_ROOT'] . '/tmp/',
		 ]);	
			# Dati kurus vēlies ielikt ,,requests,,

			if(isset($_POST['name'])){  
				try {
				$conn = db();
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
				
				 $name = $_POST['name'];
				 $surname = $_POST['surname'];
				 $telNr = $_POST['telNr'];
				 $email = $_POST['email'];			 
				 $veh = $_POST['veh'];
				 $vinOrReg = $_POST['vinOrReg'];
				 $date = $_POST['date'];
				 $problem = $_POST['problem'];
				 $req_Seen = 0;		 				 
	
		
					$stmt = $conn->prepare("INSERT INTO requests ( name, surname, telNr, email, veh, vinOrReg, problem, date, req_Seen) 
					VALUES ( :name, :surname, :telNr, :email, :veh, :vinOrReg, :problem, :date, :req_Seen)");
					$stmt->bindParam(':name', $name);
					$stmt->bindParam(':surname', $surname);
					$stmt->bindParam(':telNr', $telNr);
					$stmt->bindParam(':email', $email);
					$stmt->bindParam(':veh', $veh);
					$stmt->bindParam(':vinOrReg', $vinOrReg);
					$stmt->bindParam(':problem', $problem);
					$stmt->bindParam(':date', $date);		
					$stmt->bindParam(':req_Seen', $req_Seen);													
								
	
					if($stmt->execute()){

						$successRequestSend = 'Jūsu pieteikums tika nosūtīts!';
						return $twig -> render('home/index.twig', [
							'successRequestSend' => $successRequestSend,
						]);
	
						
					}else{
						echo "<script>alert('Netika izveidots!')</script>
						<script>window.location = '/'</script>
						";
						exit(0);
					}
					}catch(PDOException $e){
						$error = "Error: " . $e->getMessage();
						echo '<script type="text/javascript">alert("'.$error.'");</script>';
						
						exit(0);
					}
					
			 }
		
	
		
	}
	
}
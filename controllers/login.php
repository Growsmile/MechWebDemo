<?php

class LoginController {

	public function indexAction()
	{
		// return 'sakumlapa';	
		$loader = new \Twig\Loader\FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . '/views/');


		$twig = new \Twig\Environment($loader, [
			'cache' => $_SERVER['DOCUMENT_ROOT'] . '/tmp/',
		]);	
		return $twig -> render('login/index.twig', [
			// 'name' => 'Dagnis',
			// 'users' => $users

		]);

	}
	public function loginAction() {
    
        
        		
		$loader = new \Twig\Loader\FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . '/views/');


		$twig = new \Twig\Environment($loader, [
			'cache' => $_SERVER['DOCUMENT_ROOT'] . '/tmp/',
		]);	
 
        $conn = db();
     
        if(ISSET($_POST['login'])){
            if($_POST['username'] != "" || $_POST['password'] != ""){
                $username = $_POST['username'];
                $password = $_POST['password'];
                $sql = "SELECT * FROM users WHERE username = :username";

                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':username', $username);
                $stmt->execute();
                $fetch = $stmt->fetch(PDO::FETCH_ASSOC);

            if($fetch === false){
                $loginError= "Lūdzu ievadiet pareizu lietotājvārdu vai paroli!";                   
                return $twig -> render('login/index.twig', [
                    'loginError' => $loginError,
                ]);
             } else{
                  
                 $validPassword = password_verify($password, $fetch['password']);

                 if($validPassword){
                     
                     $_SESSION['user'] = $fetch['id']; 
                     header("location: /users");
                     exit;
                     
                 } else{
                    $loginError= "Lūdzu ievadiet pareizu lietotājvārdu vai paroli!";                   
                    return $twig -> render('login/index.twig', [
                        'loginError' => $loginError,
                    ]);
                 }
             }
             }
        }
        


    }
	
}
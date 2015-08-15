<?php
/**
 * Created by PhpStorm.
 * User: Aztyu
 * Date: 05/08/2015
 * Time: 19:54
 */

class User {
    private $id;
    private $pseudo;
    private $email;
    private $firstname;
    private $lastname;
    private $car_id;

    private $password;
    private $salt;
    private $hash;
    private $token;

    public static function isPseudoTaken($bdd, $pseudo){
        $request = $bdd->prepare('SELECT pseudo FROM driver WHERE pseudo = ?');
        $request->execute(array($pseudo));

        if($donnees = $request->fetch()){
            return True;
        }else{
            return False;
        }
    }

    private static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function isEmailTaken($bdd, $email){
        $request = $bdd->prepare('SELECT pseudo FROM driver WHERE email = ?');
        $request->execute(array($email));

        if($donnees = $request->fetch()){
            return True;
        }else{
            return False;
        }
    }

    public static function getUser($bdd, $hash, $pseudo = NULL, $email = NULL){
        if($pseudo){
            $request = $bdd->prepare('SELECT * FROM driver WHERE pseudo = ?');
            $request->execute(array($pseudo));

            if($donnees = $request->fetch()){
                $salt = $donnees["salt"];

                if($donnees["hash"] == hash('sha512', $salt + $hash, false)){
                    $user = new User($pseudo, $donnees["hash"], $donnees["email"]);
                    $user->setId($donnees["driver_id"]);
                    $user->setCarId($donnees["car_id"]);
                    $user->setPseudo($donnees["pseudo"]);
                    $user->setFirstname($donnees["first_name"]);
                    $user->setLastname($donnees["last_name"]);
                    $user->setToken(User::generateRandomString(200));

                    $request = $bdd->prepare("UPDATE driver SET token=:token WHERE driver_id = :driver");
                    $request->execute(array(
                        'token' => $user->getToken(),
                        'driver' => $user->getId()
                    ));

                    return $user;
                }
            }else{
                return null;
            }
        }else if($email){
            $request = $bdd->prepare('SELECT * FROM driver WHERE email = ?');
            $request->execute(array($email));

            if($donnees = $request->fetch()){
                $salt = $donnees["salt"];

                if($donnees["hash"] == hash('sha512', $salt + $hash, false)){
                    $user = new User($donnees["pseudo"], $donnees["hash"], $email);
                    $user->setId($donnees["id"]);
                    $user->setCarId($donnees["car_id"]);
                    $user->setFirstname($donnees["first_name"]);
                    $user->setLastname($donnees["last_name"]);
                    $user->setToken(generateRandomString(200));

                    $request = $bdd->prepare("UPDATE driver SET token=:token WHERE driver_id = :driver");
                    $request->execute(array(
                        'token' => $user->getToken(),
                        'driver' => $user->getId()
                    ));

                    return $user;
                }
            }else{
                return null;
            }
        }else{
            return null;
        }
    }

    public function __construct($pseudo, $pwd, $email) {
        $this->pseudo = $pseudo;
        $this->password = $pwd;
        $this->email = $email;
    }

    public function copyObject($user){
        $this->pseudo = $user->pseudo;
        $this->password = $user->password;
        $this->email = $user->email;
    }

    public function addToDatabase()
    {
        $bdd = ConnectToMySQL();
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($this->isPseudoTaken($bdd, $this->pseudo)) {
            $result = 'Le pseudo ' . $this->pseudo . ' est déjà pris';
            Message::sendJSONMessage(true, $result);
        }else if($this->isEmailTaken($bdd, $this->email)){
            $result = 'Le mail ' . $this->email . ' est déjà pris';
            Message::sendJSONMessage(true, $result);
        }else{
            try {
                $this->hashPassword();

                $request = $bdd->prepare("INSERT INTO driver (pseudo, hash, salt, email) VALUES(:pseudo, :hash, :salt, :email)");
                $request->execute(array(
                    'pseudo' => $this->pseudo,
                    'hash' => $this->hash,
                    'salt' => $this->salt,
                    'email' => $this->email
                ));

                $request->closeCursor();

                $result = "Utilisateur rajouté";
                Message::sendJSONMessage(false, $result);
            }catch(Exception $e){
                die('Erreur : '.$e->getMessage());
                $result = "Erreur interne";
                Message::sendJSONMessage(true, $result);
            }
        }
    }

    public function updateToDatabase(){

    }

    private function hashPassword(){
        $this->salt = hash('sha512', $this->generateRandomString(), false);
        $this->hash = hash('sha512', $this->salt + $this->password, false);
    }

    public function asArray(){
        return array("id" => $this->id,
            "pseudo" => $this->pseudo,
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "email" => $this->email,
            "car_id" => $this->car_id,
            "token" => $this->token);
    }

    /**
     * @return mixed
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * @param mixed $pseudo
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;
    }



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getCarId()
    {
        return $this->car_id;
    }

    /**
     * @param mixed $car_id
     */
    public function setCarId($car_id)
    {
        $this->car_id = $car_id;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }


}

?>
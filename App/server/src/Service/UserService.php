<?php namespace App\Service;


use App\Exceptions\ConflictException;
use App\Exceptions\InternalErrorException;
use App\Exceptions\NoContentException;
use App\Exceptions\NotFoundException;
use App\Exceptions\RequestException;

use App\Model\DataResult;
use App\Model\Student;
use App\Persistence\UsersPersistence;
use App\Model\User;
use App\Utils;

class UserService{

    private $userPer;

    public function __construct(){
        $this->userPer = new UsersPersistence();
    }


    /**
     * Obtiene todos los usuarios registrados
     * @return \mysqli_result
     * @throws InternalErrorException
     * @throws NoContentException
     */
    public function getUsers(){
        $result = $this->userPer->getUsers();

        if( Utils::isError($result->getOperation()) )
            throw new InternalErrorException(static::class."getUsers", "Ocurrio un error al obtener usuarios", $result->getErrorMessage());
        else if( Utils::isEmpty($result->getOperation()) )
            throw new NoContentException("No hay usuarios");
        else
            return $result->getData();
    }

    public function getUsersByStatus($status)
    {
        if( $status == Utils::$STATUS_ENABLE ){
            $result = $this->userPer->getEnableUsers();

            if( Utils::isError($result->getOperation()) )
                throw new InternalErrorException(static::class."getUsersByStatus","Ocurrio un error al obtener usuarios habilitados", $result->getErrorMessage());
            else if( Utils::isEmpty($result->getOperation()) )
                throw new NoContentException("No hay usuarios");

            return $result->getData();
        }
        else if( $status == Utils::$STATUS_DISABLE ){
            $result = $this->userPer->getDisabledUsers();

            if( Utils::isError($result->getOperation()) )
                throw new InternalErrorException(static::class."getUsersByStatus","Ocurrio un error al obtener usuarios deshabilitados", $result->getErrorMessage());
            else if( Utils::isEmpty($result->getOperation()) )
                throw new NoContentException("No hay usuarios");

            return $result->getData();
        }
        else{
            $result = $this->userPer->getNoconfirmUsers();

            if( Utils::isError($result->getOperation()) )
                throw new InternalErrorException(static::class."getUsersByStatus","Ocurrio un error al obtener usuarios no confirmados", $result->getErrorMessage());
            else if( Utils::isEmpty($result->getOperation()) )
                throw new NoContentException("No hay usuarios");

            return $result->getData();
        }


    }



    /**
     * @param $email string
     * @param $pass string
     * @return array
     * @throws InternalErrorException
     * @throws NotFoundException
     * TODO: solo debe funcionar si usuario esta activo
     */
    public function signIn($email, $pass){
        $result = $this->userPer->getUser_BySignIn($email, $pass);

        if( Utils::isError($result->getOperation()) )
            throw new InternalErrorException(static::class."signIn","Ocurrio un error al authenticar", $result->getErrorMessage());
        else if( Utils::isEmpty($result->getOperation()) )
            throw new NotFoundException("email o contraseña incorrectos");

        //Si se encontró, se crea token y se retorna
        else{
            $user = self::makeUserModel( $result->getData()[0] );

            //Se envia array con datos: id y email y retorna token
            //TODO: no usar id de BD
//            $token = Auth::getToken([
//                'id' => $user->getId(),
//                'email' => $user->getEmail()
//            ]);
            $token = Auth::getToken( $user->getId() );

            return [
                "id" => $user->getId(),
                "token" => $token,
            ];
        }

    }

    /**
     * @param $id
     * @return \mysqli_result|null
     * @throws InternalErrorException
     * @throws NotFoundException
     */
    public function getUser_ById($id){
        $result = $this->userPer->getUser_ById( $id );

        if( Utils::isError($result->getOperation()) )
            throw new InternalErrorException(static::class.":getUserById","Ocurrio un error al obtener usuario", $result->getErrorMessage());
        else if( Utils::isEmpty($result->getOperation()) )
            throw new NotFoundException("No se encontro usuario");
        else
            return $result->getData();
    }

    /**
     * @throws InternalErrorException
     * @throws NoContentException
     * @return \mysqli_result
     */
    private function getLastUser(){
        $result = $this->userPer->getUser_Last();

        if( Utils::isError($result->getOperation()) )
            throw new InternalErrorException(static::class."getLastUser", "Ocurrio un error al obtener usuario", $result->getErrorMessage());
        else if( Utils::isEmpty($result->getOperation()) )
            throw new NoContentException("No hay usuarios");
        else
            return $result->getData();
    }




    /**
     * @param $id
     * @return bool|DataResult
     */
    private function isUserExist($id){
        $result = $this->userPer->getUser_ById($id);

        if( Utils::isSuccessWithResult($result->getOperation()) )
            $result->setOperation(true);
        else if( Utils::isEmpty($result->getOperation()) )
            $result->setOperation(false);

        return $result;
    }


    /**
     * @param $id
     * @return \mysqli_result|null
     * @throws InternalErrorException
     * @throws NoContentException
     */
    public function getRoleUser($id){

        $result = $this->userPer->getRoleUser( $id );

        if( Utils::isError($result->getOperation()) )
            throw new InternalErrorException(static::class.":getRoleUser", "Ocurrio un error al obtener rol de usuario", $result->getErrorMessage());
        else if( Utils::isEmpty($result->getOperation()) )
            throw new NoContentException("No se obtuvo rol");
        else
            return $result->getData();
    }


    /**
     * @param $email
     * @return \mysqli_result|null
     * @throws InternalErrorException
     * @throws NoContentException
     */
    public function getUser_ByEmail($email){
        $result = $this->userPer->getUser_ByEmail( $email );

        if( Utils::isError($result->getOperation()) )
            throw new InternalErrorException(static::class."getUserByEmail","Ocurrio un error al obtener usuario por email", $result->getErrorMessage());
        else if( Utils::isEmpty($result->getOperation()) )
            throw new NoContentException("No se encontro usuario");
        else
            return $result->getData();
    }


    /**
     * @param $email
     *
     * @return \mysqli_result|null
     * @throws InternalErrorException
     * @throws NoContentException
     */
    public function searchUserByEmail($email)
    {
        $result = $this->userPer->searchUsersByEmail( $email );

        if( Utils::isError($result->getOperation()) )
            throw new InternalErrorException(static::class."searchUsersByEmail","Ocurrio un error al obtener usuarios por email", $result->getErrorMessage());
        else if( Utils::isEmpty($result->getOperation()) )
            throw new NoContentException("No se encontraron usuarios");

        return $result->getData();
    }


    private function isEmailUsed($email){
        $result = $this->userPer->getUser_ByEmail( $email );

        if( Utils::isSuccessWithResult($result->getOperation()) )
            $result->setOperation(true);
        else if( Utils::isEmpty($result->getOperation()) )
            $result->setOperation(false);

        return $result;
    }


    public function isRoleExists($role){
        $result = $this->userPer->getRole_ByName( $role );

        if( Utils::isSuccessWithResult($result->getOperation()) )
            $result->setOperation(true);
        else if( Utils::isEmpty($result->getOperation()) )
            $result->setOperation(false);

        return $result;
    }

    //------------------REGISTRAR USUARIO


    /**
     * @param $user User
     * @throws ConflictException
     * @throws InternalErrorException
     * @throws NotFoundException
     */
    public function insertUser($user){

        //TODO: debe enviar correo para que el usuario sea confirmado
        //TODO: cron para eliminar usuario si este no se confirma en una semana
        //TODO: puede solicitarse un correo para confirmar

        //Verifica que email no exista
        $result = $this->isEmailUsed( $user->getEmail() );
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException( static::class."insertUser","Ocurrio un error al verificar email de usuario");
        else if( $result->getOperation() == true )
            throw new ConflictException( "Email ya existe" );

        //Se verifica rol
        $result = $this->isRoleExists( $user->getRole() );
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException( static::class.":insertUser","Ocurrio un error al verificar rol");
        else if( $result->getOperation() == false )
            throw new NotFoundException( "No existe rol asignado" );

        //Se registra usuario
        $result = $this->userPer->insertUser( $user );
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException( static::class.":insertUser","Ocurrio un error al registrar usuario");
    }


    //------------------REGISTRAR USUARIO Y ESTUDIANTE

    /**
     * @param $student Student
     * @throws InternalErrorException
     * @throws RequestException
     */
    public function insertUserAndStudent($student){

        //Inicia transaccion
        $trans = UsersPersistence::initTransaction();
        if( !$trans )
            throw new InternalErrorException(static::class.":insertUserAndStudent","Error al iniciar transaccion");


        //------------Verificacion de datos de usuario (excepciones incluidas)
        try {
            //Registramos usuario
            $this->insertUser( $student->getUser() );
            //Obtenemos ultimo registrado
            $result = $this->getLastUser();
            $user = self::makeUserModel( $result[0] );
            //Se agrega al Modelo de estudiante
            $student->setUser( $user );

        } catch (RequestException $e) {
            //Se termina transaccion
            UsersPersistence::rollbackTransaction();
            throw new RequestException( $e->getMessage(), $e->getStatusCode() );
        }


        //--------------CARRERA

        try {
            $careerService = new CareerService();
            $result = $careerService->getCareer_ById( $student->getCareer() );
            $career = CareerService::makeObject_career( $result[0] );
            //Se asigna carrera (model) a student
            $student->setCareer( $career );

        } catch (RequestException $e) {
            //Se termina transaccion
            UsersPersistence::rollbackTransaction();
            throw new RequestException( $e->getMessage(), $e->getStatusCode() );
        }

        //------------Iniciamos registro de estudiante
        try{
            $studentService = new StudentService();
            $studentService->insertStudent( $student );
        }catch (RequestException $e){
            //Se termina transaccion
            UsersPersistence::rollbackTransaction();
            throw new RequestException( $e->getMessage(), $e->getStatusCode() );
        }

        //Si marcha bien, se registra commit
        $trans = UsersPersistence::commitTransaction();
        if( !$trans )
            throw new InternalErrorException(static::class.":insertUserAndStudent","Error al realizar commit de transaccion");
    }


    /**
     * @param $user User
     * @throws ConflictException
     * @throws InternalErrorException
     * @throws NotFoundException
     */
    public function updateUser($user){
        $result = $this->userPer->getUser_ById( $user->getId() );

        //TODO: Cuando se haga update del correo, debe cambiarse status para confirmar
        //TODO: no debe eliminarse usuario con cron

        //Verificacion de usuario
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException( static::class.":insertUserAndStudent","Ocurrio un error al obtener usuario");
        else if( Utils::isEmpty( $result->getOperation() ) )
            throw new NotFoundException("No existe usuario");

        //Verifica que email
        $user_db = self::makeUserModel( $result->getData()[0] );

        //Si cambio el email
        if( $user_db !== $user->getEmail() ){
            //Se obtiene
            $result = $this->isEmailUsed( $user->getEmail() );
            //Operacion
            if( Utils::isError( $result->getOperation() ) )
                throw new InternalErrorException( static::class.":updateUser","Ocurrio un error al verificar email de usuario");
            else if( $result->getOperation() == true )
                throw new ConflictException( "Email ya existe" );
        }

        //Si cambio el rol
        if( $user_db !== $user->getRole() ){
            //Se verifica rol
            $result = $this->isRoleExists( $user->getRole() );
            if( Utils::isError( $result->getOperation() ) )
                throw new InternalErrorException( static::class.":updateUser","Ocurrio un error al verificar rol");
            else if( $result->getOperation() == false )
                throw new NotFoundException( "No existe rol asignado" );
        }


        //Se actualiza usuario
        $result = $this->userPer->updateUser( $user );
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException( static::class.":updateUser","Ocurrio un error al actualizar usuario");

    }

    /**
     * @param $user_id int
     * @param $status int
     *
     * @throws InternalErrorException
     * @throws NotFoundException
     */
    public function changeStatus($user_id, $status ){

        //Verificando si existe usuario
        $this->getUser_ById( $user_id );

        //Eliminando usuario (cambiando status)
        if( $status == Utils::$STATUS_DISABLE ){
            $result = $this->userPer->changeStatusToDisable( $user_id );
            if( Utils::isError( $result->getOperation() ) )
                throw new InternalErrorException( static::class.":changeStatus","Ocurrio un error al deshabilitar usuario");
        }
        else if( $status == Utils::$STATUS_ENABLE ){
            $result = $this->userPer->changeStatusToEnable( $user_id );
            if( Utils::isError( $result->getOperation() ) )
                throw new InternalErrorException( static::class.":changeStatus","Ocurrio un error al habilitar usuario");
        }

    }

    /**
     * @param $id
     * @throws InternalErrorException
     * @throws NotFoundException
     */
    public function deleteUser($id)
    {
        //Verificando si existe usuario
        $result = $this->userPer->getUser_ById( $id );
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException( static::class.":deleteUser","Ocurrio un error al obtener usuario");
        else if( Utils::isEmpty( $result->getOperation() ) )
            throw new NotFoundException("No existe usuario");

        //Eliminando usuario (cambiando status)
        $result = $this->userPer->deleteUser_ById( $id );
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException( static::class.":deleteUser","Ocurrio un error al eliminar usuario");
    }


//    public function searchUser($search_by, $data_search)
//    {
//        if ($search_by === "id") {
//            $result = $this->getUser_ById($data_search);
//            if ($result == false)
//                throw new ConflictException("Usuario no existe por id");
//        } else if ($search_by === "email") {
//            $result = $this->getUser_ByEmail($data_search);
//            if ($result == false)
//                throw new ConflictException("Usuario no existe por email");
//        } else if($search_by === "users"){
//            $result = $this->getUsers();
//            if ($result == false)
//                throw new ConflictException("Usuarios no existen");
//        } else{
//            $response = [
//                "result" => "Error en los datos",
//                "message" => "Verifique los datos"
//            ];
//            return $response;
//        }
//
//        if( $result['operation'] == Utils::$OPERATION_ERROR )
//            throw new InternalErrorException("Ocurrio un error al registrar usuario", $result['error']);
//        else
//            return Utils::makeArrayResponse(
//                "Busqueda exitosa",
//                $result
//            );
//    }







    //-----------------------
    // EXTRAS
    //-----------------------
    /**
     * array['field']
     *
     * @param $data \mysqli_result
     * @return User
     */
    public static function makeUserModel($data ){
        $user = new User();
        //setting data
        $user->setId( $data['id'] );
        $user->setEmail( $data['email'] );
//        $user->setPassword( $data['password'] );
        $user->setRegister_Date( $data['register_date'] );
        $user->setStatus( $data['status'] );
        $user->setRole( $data['role'] );
        //Returning object
        return $user;
    }



}
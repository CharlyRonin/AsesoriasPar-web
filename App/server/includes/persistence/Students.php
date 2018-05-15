<?php namespace Persistence;

use Objects\Career;
use Objects\Student;
use Utils;

class Students extends Persistence{

    public function __construct(){}

    private $SELECT = "SELECT
                        s.student_id as 'id', 
                        s.itson_id as 'itson_id', 
                        s.first_name as 'first_name', 
                        s.last_name as 'last_name', 
                        s.phone as 'phone', 
                        s.facebook as 'facebook', 
                        s.avatar as 'avatar', 
                        s.date_register as 'date_register',
                        s.status as 'status',
                        s.fk_user as 'user_id', 
                        c.fk_career as 'career_id'
                        FROM student s";


    /**
     * @param $id int
     * @return \Objects\DataResult
     */
    public function getStudent_ById($id){
        $query =    $this->SELECT."
                        INNER JOIN career c ON c.pk_id = s.fk_career
                        WHERE s.student_id = ".$id;
        //Obteniendo resultados
        return $this->executeQuery($query);
    }

    /**
     * @return \Objects\DataResult
     */
    public function getStudents(){
        $query =    $this->SELECT."
                        INNER JOIN career c ON c.pk_id = s.fk_career";
        //Obteniendo resultados
        return $this->executeQuery($query);
    }

    /**
     * @param $id int
     * @return \Objects\DataResult
     */
    public function getStudent_ByUserId( $id ){
        $query = $this->SELECT."
                        INNER JOIN career c ON c.pk_id = s.fk_career
                        WHERE s.fk_user =".$id;
        //Obteniendo resultados
        return $this->executeQuery($query);
    }

//    /**
//     * @param $name String
//     * @return \Objects\DataResult
//     */
//    public function getStudent_LikeName($name ){
//        $query = $this->SELECT."
//                        INNER JOIN career c ON c.pk_id = s.fk_career
//                        WHERE s.first_name LIKE '%$name%' OR s.last_name LIKE '%$name%' ";
//        //Obteniendo resultados
//        return $this->executeQuery($query);
//    }

//    public function ifExistCareer( $career ){
//        $query = " SELECT c.name FROM career c
//                   WHERE c.career_id =".$career;
//        //Obteniendo resultados
//        return $this->executeQuery($query);
//    }


//    public function ifUserExist( $student ){
//        $query = " SELECT s.student_id FROM student s
//                   WHERE s.fk_user =".$student->getUser();
//        //Obteniendo resultados
//        return $this->executeQuery($query);
//    }


//    public function getStudent_byCareer ( $career ){
//        $query = $this->SELECT."
//                        INNER JOIN career c ON c.pk_id = s.fk_career
//                        WHERE c.name LIKE '%$career%'";
//        //Obteniendo resultados
//        return $this->executeQuery($query);
//    }


//    public function getStudent_bySubject ( $subject ){
//        $query = $this->SELECT."
//                        INNER JOIN career c ON c.pk_id = s.fk_career
//                        INNER JOIN subject s ON c.pk_id = s.fk_career
//                        WHERE s.name LIKE '%$subject%'";
//        //Obteniendo resultados
//        return $this->executeQuery($query);
//    }

//    public function getStudent_byItsonId( $itsonId ){
//        $query = $this->SELECT."
//                        INNER JOIN career c ON c.pk_id = s.fk_career
//                        WHERE s.itson_id LIKE '%$itsonId%'";
//        //Obteniendo resultados
//        return $this->executeQuery($query);
//    }

//    public function getStudent_byAdvisor( $advisor ){
//        $query = $this->SELECT."
//                        INNER JOIN career c ON c.pk_id = s.fk_career
//                        WHERE s.itson_id LIKE '%$advisor%'";
//        //Obteniendo resultados
//        return $this->executeQuery($query);
//    }

    //------------------REGISTROS


    /**
     * @param $student Student
     * @return \Objects\DataResult
     */
    public function insertStudent( $student ){
        $query = "INSERT INTO student(itson_id, first_name, last_name, fk_user , fk_career)
                  VALUES(
                      '".$student->getItsonId()."',
                      '".$student->getFirstName()."', 
                      '".$student->getLastname()."', 
                      ".$student->getUser().", 
                      ".$student->getCareer().")";
        return  self::executeQuery($query);
    }

    /**
     * @param $student Student
     * @return \Objects\DataResult
     */
    public function updateStudent( $student ){
        $query = "UPDATE  student s 
                          SET s.itson_id = '".$student->getItsonId()."', s.first_name = '".$student->getFirstName()."', s.last_name = '".$student->getLastName()."',
                          s.status = '".$student->getStatus()."', s.fk_user = '".$student->getUser()."', s.fk_career = '".$student->getCareer()."'
                          WHERE s.student_id = ".$student->getId();
        return  self::executeQuery($query);
    }

    /**
     * @param $idStudent
     * @return \Objects\DataResult
     * TODO: change status
     */
    public function changeStatusToDeleted($idStudent ){
        $query = "UPDATE student s
                         SET s.status = ". Utils::$STATUS_DELETED ."    
                         WHERE s.student_id = " .$idStudent ;
        return  self::executeQuery($query);
    }
}
?>
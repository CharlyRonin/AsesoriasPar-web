<?php namespace App\Service;

use App\Exceptions\ConflictException;
use App\Exceptions\InternalErrorException;
use App\Exceptions\NoContentException;
use App\Exceptions\NotFoundException;
use App\Exceptions\RequestException;
use App\Persistence\SubjectsPersistence;
use App\Model\Subject;
use App\Utils;

class SubjectService{

    private $perSubjects;

    public function __construct(){
        $this->perSubjects = new SubjectsPersistence();
    }

    /**
     * @return array|null|string
     * @throws NoContentException
     * @throws InternalErrorException
     */
    public function getSubjects(){
        $result = $this->perSubjects->getSubjects();

        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException(static::class.":getSubjects","Ocurrio un error al obtener materias", $result->getErrorMessage());
        else if( Utils::isEmpty( $result->getOperation() ) )
            throw new NoContentException("No se encontraron materias reistrados");
        else
            return $result->getData();
    }

    /**
     * @param $subject_id
     * @return \mysqli_result
     * @throws InternalErrorException
     * @throws NotFoundException
     *///TODO: Regresar materias relacionadas
    public function getSubject_ById( $subject_id ){
        $result = $this->perSubjects->getSubject_ById( $subject_id );

        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException(static::class.":getSubjectById","Ocurrio un error al obtener la materia por ID", $result->getErrorMessage());
        else if( Utils::isEmpty( $result->getOperation() ) )
            throw new NotFoundException("No existe materia");
        else
            return $result->getData()[0];
    }

    /**
     * @param $name
     * @return array|bool|string
     * @throws NoContentException
     * @throws InternalErrorException
     */
    public function searchSubjects_ByName($name )
    {
        $result = $this->perSubjects->searchSubjects_ByName( $name );

        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException(static::class.":searchSubjects_ByName",
                "Ocurrio un error al obtener materia por nombre", $result->getErrorMessage());
        else if( Utils::isEmpty( $result->getOperation() ) )
            throw new NoContentException("");
        else
            return $result->getData();
    }

    /**
     * @param $careerID
     * @return \mysqli_result
     * @throws InternalErrorException
     * @throws NoContentException
     */
    public function getSubjects_ByCareer($careerID ){
        $result = $this->perSubjects->getSubjects_ByCareer( $careerID );

        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException(static::class.":getCareerSubject","Ocurrio un error al obtener materias", $result->getErrorMessage());
        else if( Utils::isEmpty( $result->getOperation() ) )
            throw new NoContentException("No se encontraron materias reistrados");
        else
            return $result->getData();
    }


    /**
     * @param $plan
     * @return \mysqli_result
     * @throws NoContentException
     * @throws InternalErrorException
     * TODO: mover a plan
     */
    public function getPlanSubjects($plan )
    {
        $result = $this->perSubjects->getSubjects_ByPlan( $plan  );

        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException(static::class.":getPlanSubjects","Ocurrio un error al obtener materias", $result->getErrorMessage());
        else if( Utils::isEmpty( $result->getOperation() ) )
            throw new NoContentException("No se encontraron materias reistrados");
        else
            return $result->getData();
    }

    /**
     * @param $semester
     * @return \mysqli_result
     * @throws InternalErrorException
     * @throws NoContentException
     */
    public function getSubject_BySemester( $semester )
    {
        $result = $this->perSubjects->getSubjects_BySemester( $semester );

        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException(static::class.":getSubjectBySemester","Ocurrio un error al obtener materias", $result->getErrorMessage());
        else if( Utils::isEmpty( $result->getOperation() ) )
            throw new NoContentException("No se encontraron materias reistrados");
        else
            return $result->getData();
    }


//    /**
//     * @param $id
//     * @return array
//     * @throws InternalErrorException
//     * @throws NoContentException
//     */
//    public function getScheduleSubjects_ByScheduleId($id ) {
//        $result = $this->perSubjects->getSubjects_ByScheduleId( $id );
//
//        if( Utils::isError( $result->getOperation() ) )
//            throw new InternalErrorException("Ocurrio un error al obtener materias");
//        else if( Utils::isEmpty( $result->getOperation() ) )
//            throw new NoContentException("No se encontraron materias reistrados");
//        else
//            return Utils::makeArrayResponse(
//                "Materias registradas",
//                $result['data']
//            );
//    }



    //-----------------
    // ASESORIAS
    //-----------------

//    public function getCurrAvailScheduleSubs_SkipSutdent( $idStudent ){
//        $conSchedule = new ScheduleControl();
//        $cycle = $conSchedule->getCurrentPeriod();
//        if( !is_array($cycle) )
//            return $cycle;
//        else{
//            $result = $this->perSubjects->getAvailScheduleSubs_SkipStudent( $idStudent, $cycle['id'] );
//            if( $result === false )
//                return 'error';
//            else if( $result === null )
//                return null;
//            else{
//                return $result;
//            }
//        }
//    }



    /*--------------------------/Nuevo------------------------------------------- /*/







    /**------------------------INSERT , UPDATE , DELETE , SEARCH --------------------------------- **/

    /**
     * @param $subject Subject objeto de materia
     * @throws InternalErrorException
     * @throws RequestException
     */
    public function insertSubject( $subject ){

        //------------Verificamos que la materia no exista
        //TODO: verificar que no sea el mismo nombre dentro de la misma carrera/plan, se puede repetir en otros...

        try{
            //Verifica que no exista el nombre
            $this->getSubject_ByName( $subject->getName(), $subject->getPlan(), $subject->getCareer() );
            //Verifica que no exista la abreviacion
            $this->getSubject_ByName( $subject->getShortName(), $subject->getPlan(), $subject->getCareer() );

            //Si no se encuentra nada, no hay problema
        }catch (NoContentException $e){}



        //------------Verificamos que la carrera exista
        $careerService =  new CareerService();
        $careerService->getCareer_ById( $subject->getCareer() );

        //------------Verificamos que el plan exista
        $planService =  new PlanService();
        $planService->getPlan_ById( $subject->getPlan() );


        //-------------Registrando materia
        $result = $this->perSubjects->insertSubject( $subject );
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException(static::class.":insertSubject",
                "Ocurrio un error al registrar la materia", $result->getErrorMessage());
    }

    //--------------------UPDATE SUBJECT--------------------

    /**
     * @param $subject Subject
     * @throws RequestException
     */
    public function updateSubject( $subject ){

        //------------Verificamos que la materia exista
        $subject_aux = $this->getSubject_ById( $subject->getId() );


        //------------DATOS QUE CAMBIARON
        try{
            //Si cambio nombre, se verifica
            if( $subject_aux['name'] != $subject->getName() ) {
                //Debe lanzar exception para que sea correcto
                $this->getSubject_ByName($subject->getName(), $subject->getPlan(), $subject->getCareer());
                throw new ConflictException("Nombre ya existe");
            }
            //Si no encuentra nada, no hay problema
        }catch (NoContentException $e){}


        try{
            //Si cambio abreviacion, se verifica
            if( $subject_aux['short_name'] != $subject->getShortName() ) {
                //Debe lanzar exception para que sea correcto
                $this->getSubject_ByName($subject->getShortName(), $subject->getPlan(), $subject->getCareer());
                throw new ConflictException("Abreviacion ya existe");
            }
            //Si no encuentra nada, no hay problema
        }catch (NoContentException $e){}




        //------------Verificamos que la carrera exista
        $careerService =  new CareerService();
        $careerService->getCareer_ById( $subject->getCareer() );

        //------------Verificamos que el plan exista
        $planService =  new PlanService();
        $planService->getPlan_ById( $subject->getPlan() );

        //-------------Registrando materia
        $result = $this->perSubjects->updateSubject( $subject );
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException(static::class.":updateSubject","Ocurrio un error al actualizar la materia", $result->getErrorMessage());
    }


    //---------------------DELETE SUBJECT--------------------

    /**
     * @param $subjectID
     * @param $new_status
     *
     * @return void
     * @throws InternalErrorException
     * @throws NotFoundException
     */
    public function changeStatus($subjectID, $new_status ){

        $result = $this->isSubjectExist_ById( $subjectID );
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException(static::class.":changeStatus","Error al obtener materia por ID", $result->getErrorMessage());
        else if( $result->getOperation() == false )
            throw new NotFoundException("No existe materia");

        if( $new_status == Utils::$STATUS_DISABLE ){
            $result = $this->perSubjects->changeStatusToDeleted( $subjectID );
            if( Utils::isError( $result->getOperation() ) )
                throw new InternalErrorException(static::class.":changeStatus","Error al deshabilitar materia", $result->getErrorMessage());
        }
        else if( $new_status == Utils::$STATUS_ENABLE ){
            $result = $this->perSubjects->changeStatusToEnable( $subjectID );
            if( Utils::isError( $result->getOperation() ) )
                throw new InternalErrorException(static::class.":changeStatus","Error al habilitar materia", $result->getErrorMessage());
        }
    }

    /**
     * @param $subjectID
     * @throws InternalErrorException
     * @throws NotFoundException
     */
    public function deleteSubject($subjectID ){

        $result = $this->isSubjectExist_ById( $subjectID );
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException(static::class.":deleteSubject","Error al obtener materia por ID", $result->getErrorMessage());
        else if( $result->getOperation() == false )
            throw new NotFoundException("No existe materia");

        $result = $this->perSubjects->deleteSubject( $subjectID );
        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException(static::class.":deleteSubject","Error al eliminar materia", $result->getErrorMessage());
    }


    /**
     * @param $name String Career name/short_name
     * @param $plan int Plan Id
     * @param $career int Career id
     * @return \mysqli_result|null
     * @throws InternalErrorException
     * @throws NoContentException
     */
    public function getSubject_ByName($name, $plan, $career )
    {
        $result = $this->perSubjects->getSubject_ByName_ShortName( $name, $plan, $career );

        if( Utils::isError( $result->getOperation() ) )
            throw new InternalErrorException(static::class.":isSubjectExist_ByName_ShortName",
                "Ocurrio un error obtener materia por nombre/abreviacion");

        else if( Utils::isEmpty( $result->getOperation() ) )
            throw new NoContentException("");

        return $result->getData()[0];
    }


    /**
     * @param $id
     * @return \App\Model\DataResult
     */
    public function isSubjectExist_ById( $id )
    {
        $result = $this->perSubjects->getSubject_ById( $id );

        if( Utils::isSuccessWithResult( $result->getOperation() ) )
            $result->setOperation(true);
        else if( Utils::isEmpty( $result->getOperation() ) )
            $result->setOperation(false);

        return $result;
    }

    /**
     * @param $subject_id int
     *
     * @return \mysqli_result
     * @throws InternalErrorException
     * @throws NotFoundException
     * @throws NoContentException
     */
    public function getCurrentAdvisers_BySubject($subject_id)
    {
        //Comprobando que existe materia
        $this->getSubject_ById( $subject_id );

        $scheduleServ = new ScheduleService();
        return $scheduleServ->getCurrentAdvisers_BySubject( $subject_id );
    }

    //----------------------
    // MATERIAS RELACIONADAS
    //----------------------

//    /**
//     * @param $mainSubID int ID de la materia principal
//     * @param $subjectsArray array array de materias relacionadas
//     * @return array
//     * @throws NotFoundException
//     * @throws InternalErrorException
//     */
//    public function addSimilarySubjetcs($mainSubID, $subjectsArray){
//
//        //Verificando que materia principal exista
//        $result = $this->isSubjectExist_ById( $mainSubID );
//        //Comprobando errores
//        if( Utils::isError( $result->getOperation() ) )
//            throw new InternalErrorException("No se pudo obtener materia", $result->getErrorMessage());
//        else if( Utils::isEmpty( $result->getOperation() ) )
//            throw new NotFoundException("No existe materia principal");
//
//        //TODO: verificar que no esten ya relacionadas
//        //TODO: Verificar que materia no sea la misma que principal
//        //TODO: Verificar que materia no se haya relacionado anteriomente (durante registros)
//
//
//        //TODO: Usar transacciones
//        foreach ( $subjectsArray as $subID ){
//            if( Utils::isError( $result->getOperation() ) )
//                throw new InternalErrorException("No se pudo obtener materia", $result->getErrorMessage());
//            else if( Utils::isEmpty( $result->getOperation() ) )
//                throw new NotFoundException("No existe materia principal");
//
//            //Se registra
//            $result = $this->perSubjects->setSubjectRelation( $mainSubID, $subID );
//
//            if( Utils::isError( $result->getOperation() ) )
//                throw new InternalErrorException("No se pudo relacionar materia", $result->getErrorMessage());
//        }
//
//        return Utils::makeArrayResponse("Materias relacionadas con éxito");
//
//    }


}

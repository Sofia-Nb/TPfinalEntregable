<?php
include_once "BaseDatos.php";

include_once "Persona.php";

class Responsable extends Persona {
    private $numeroEmpleado;
    private $numeroLicencia;

    public function __construct(){
        parent::__construct(); // Llama al constructor de la clase padre (Persona)
        $this->numeroEmpleado = "";
        $this->numeroLicencia = "";
    }

    public function cargar($param){
    //  parent::cargar($nrodoc, $nomb, $ape, $tel);
        parent::cargar($param);
        $this->setNumeroEmpleado($param['numEmpleado']);
        $this->setNumeroLicencia($param['numLicencia']);
    }

    public function setNumeroEmpleado($rnumemp){
        $this->numeroEmpleado = $rnumemp;
    }

    public function setNumeroLicencia($rnumlic){
        $this->numeroLicencia = $rnumlic;
    }

    public function getNumeroEmpleado(){
        return $this->numeroEmpleado;
    }

    public function getNumeroLicencia(){
        return $this->numeroLicencia;
    }


    public function listar($condicion = ""){
        $arregloResponsable = null;
        $base = new BaseDatos();
        $consultaResponsable = "SELECT * FROM responsable ";
        if ($condicion != "") {
            $consultaResponsable = $consultaResponsable . ' WHERE ' . $condicion;
        }
        $consultaResponsable .= " ORDER BY numeroDocumentoRes ";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaResponsable)) {
                $arregloResponsable = array();
                while ($row2 = $base->Registro()) {
                    $NroDoc = $row2['numeroDocumentoRes'];
                    $numeroEmpleado = $row2['numeroEmpleado'];
                    $numeroLicencia = $row2['numeroLicencia'];
                    
                    $Responsable = new Responsable(); 
                    
                    parent::buscar($NroDoc);

                    $Responsable->cargar(['nrodoc' => $NroDoc, 
                    'nomb'=> parent::getNombre(), 
                    'ape'=> parent::getApellido(), 
                    'tel' => parent::getTelefono(), 
                    'numEmpleado' => $numeroEmpleado, 
                    'numLicencia' => $numeroLicencia  ]);   
                    array_push($arregloResponsable, $Responsable);
                }
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $arregloResponsable;
    }

    public function buscar($nrodoc){
        $base = new BaseDatos();
        $consultaResponsable = "SELECT * FROM responsable WHERE numeroDocumentoRes = " .$nrodoc;
        $resp = false;
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaResponsable)) {
                if ($row = $base->Registro()){

                    parent::buscar($row['numeroDocumentoRes']);
                    $this->setNrodoc($row['numeroDocumentoRes']);
                    $this->setNumeroEmpleado($row['numeroEmpleado']);
                    $this->setNumeroLicencia($row['numeroLicencia']);
                    $resp = true;
                }
            } else {
                $this->setMensajeOperacion($base->getError());
            }
        } else {
            $this->setMensajeOperacion($base->getError());
        }
        return $resp;
    }

    public function insertar(){
        parent::insertar(); // Asegura que la inserción en Persona se realice primero
        $base = new BaseDatos();
        $resp = false;
        $consultaInsertar = "INSERT INTO responsable (numeroDocumentoRes, numeroEmpleado, numeroLicencia) VALUES (" . $this->getNrodoc() . "," . $this->getNumeroEmpleado() . "," . $this->getNumeroLicencia() . ")";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaInsertar)) {
                $resp = true;
            } else {
                $this->setMensajeOperacion($base->getError());
            }
        } else {
            $this->setMensajeOperacion($base->getError());
        }
        return $resp;
    }

    public function modificar(){
        parent::modificar(); // Asegura que la modificación en Persona se realice primero
        $resp = false;
        $base = new BaseDatos();
        $consultaModifica = "UPDATE responsable SET numeroEmpleado='" . $this->getNumeroEmpleado() . "', numeroLicencia='" . $this->getNumeroLicencia() . "' WHERE numeroDocumentoRes=" . $this->getNrodoc();
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaModifica)) {
                $resp = true;
            } else {
                $this->setMensajeOperacion($base->getError());
            }
        } else {
            $this->setMensajeOperacion($base->getError());
        }
        return $resp;
    }

    public function eliminar(){
        $base = new BaseDatos();
        $resp = false;
        $consultaEliminar = "DELETE FROM responsable WHERE numeroDocumentoRes=" . $this->getNrodoc();
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaEliminar)) {
                if(parent::eliminar()){
                    $resp = true;
                    }
            } else {
                $this->setMensajeOperacion($base->getError());
            }
        } else {
            $this->setMensajeOperacion($base->getError());
        }
        return $resp;
    }

    public function __toString(){
        return "Número de Documento: " . $this->getNrodoc() . "\n" .
               "Número de Empleado: " . $this->getNumeroEmpleado() . "\n" .
               "Número de Licencia: " . $this->getNumeroLicencia() . "\n";
    }
}
?>

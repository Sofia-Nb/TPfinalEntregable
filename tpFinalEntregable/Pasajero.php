<?php
include_once 'BaseDatos.php';

class Pasajero extends Persona
{
    private $objViaje;

    public function __construct()
    {
        parent::__construct(); // Llama al constructor de la clase padre (Persona)
        $this->objViaje = '';
    }

    public function setObjViaje($objViaje)
    {
        $this->objViaje = $objViaje;
    }
    public function getObjViaje()
    {
        return $this->objViaje;
    }

    public function cargar($param)
    {
        parent::cargar($param);
        $this->setObjViaje($param['objViaje']);
    }

    public function buscar($nrodoc)
    {
        $base = new BaseDatos();
        $consultaPasajero =
            'SELECT * FROM pasajero WHERE numdocPasajero = ' . $nrodoc;
        $resp = false;
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaPasajero)) {
                if ($row = $base->Registro()) {
                    $this->setNrodoc($row['numdocPasajero']);

                    $idViaje = $row['idViajePas'];
                    $viaje = new Viaje();
                    $viaje->buscar($idViaje);

                    $this->setObjViaje($viaje);

                    parent::buscar($row['numdocPasajero']);

                    parent::cargar([
                        'nrodoc' => $row['numdocPasajero'],
                        'nomb' => parent::getNombre(),
                        'ape' => parent::getApellido(),
                        'tel' => parent::getTelefono(),
                    ]);

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

    public function listar($condicion = '')
    {
        $arregloPasajero = null;
        $base = new BaseDatos();
        $consultaPasajero = 'SELECT * FROM pasajero ';
        if ($condicion != '') {
            $consultaPasajero = $consultaPasajero . ' WHERE ' . $condicion;
        }
        $consultaPasajero .= ' ORDER BY numdocPasajero ';
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaPasajero)) {
                $arregloPasajero = [];
                while ($row2 = $base->Registro()) {
                    $NroDoc = $row2['numdocPasajero'];
                    $idViaje = $row2['idViajePas'];

                    $pasajero = new Pasajero();
                    $persona = new Persona();
                    $viaje = new Viaje();

                    $viaje->buscar($idViaje);
                    parent::buscar($NroDoc);

                    $pasajero->cargar([
                        'nomb' => parent::getNombre(),
                        'nrodoc' => $NroDoc,
                        'ape' => parent::getApellido(),
                        'tel' => parent::getTelefono(),
                        'objViaje' => $viaje,
                    ]);

                    array_push($arregloPasajero, $pasajero);
                }
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $arregloPasajero;
    }

    public function insertar()
    {
        //Inserta los datos a la tabla persona
        parent::insertar();  // AGREGAR UN IF 

        $base = new BaseDatos();
        $resp = false;
        // Se insertan los datos del pasajero en la tabla pasajero
        $consultaInsertar =
            "INSERT INTO pasajero (numdocPasajero, idViajePas) VALUES ('" .
            parent::getNrodoc() .
            "','" .
            $this->getObjViaje()->getIdviaje() .
            "')";
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

    public function modificar()
    {
        parent::modificar();
        $resp = false;
        $base = new BaseDatos();
        // Se actualizan los datos del pasajero en la tabla pasajero
        $consultaModifica =
            'UPDATE pasajero SET numdocPasajero= ' .
            $this->getNrodoc() .
            ', idViajePas= ' .
            $this->getObjViaje()->getIdviaje() .
            ' WHERE numdocPasajero=' .
            $this->getNrodoc() .
            ' ';
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

    public function eliminar()
    {
        $base = new BaseDatos();
        $resp = false;
        // Se elimina el pasajero de la tabla pasajero
        $consultaEliminar =
            "DELETE FROM pasajero WHERE numdocPasajero='" .
            $this->getNrodoc() .
            "'";
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

    public function __toString()
    {
        $resultado = parent::__toString(); // Obtener la representación de la clase padre (Persona)
        $resultado .= "Viaje: \n" . $this->getObjViaje();

        return $resultado;
    }
}

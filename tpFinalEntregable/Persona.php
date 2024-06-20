<?php
include_once 'BaseDatos.php';

class Persona
{
    private $nrodoc;
    private $nombre;
    private $apellido;
    private $telefono;
    private $mensajeoperacion;

    public function __construct()
    {
        $this->nrodoc = '';
        $this->nombre = '';
        $this->apellido = '';
        $this->telefono = '';
    }

    public function cargar($param)
    {
        $this->setNrodoc($param['nrodoc']);
        $this->setNombre($param['nomb']);
        $this->setApellido($param['ape']);
        $this->setTelefono($param['tel']);
    }

    public function setNrodoc($NroDNI)
    {
        $this->nrodoc = $NroDNI;
    }

    public function setNombre($Nom)
    {
        $this->nombre = $Nom;
    }

    public function setApellido($Ape)
    {
        $this->apellido = $Ape;
    }

    public function setTelefono($tel)
    {
        $this->telefono = $tel;
    }

    public function setmensajeoperacion($mensajeoperacion)
    {
        $this->mensajeoperacion = $mensajeoperacion;
    }

    public function getNrodoc()
    {
        return $this->nrodoc;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getApellido()
    {
        return $this->apellido;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function getmensajeoperacion()
    {
        return $this->mensajeoperacion;
    }

    public function Buscar($dni)
    {
        $base = new BaseDatos();
        $consultaPersona = 'SELECT * FROM persona WHERE nrodoc = ' . $dni;
        $resp = false;
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaPersona)) {
                if ($row2 = $base->Registro()) {
                    $this->setNrodoc($dni);
                    $this->setNombre($row2['nombre']);
                    $this->setApellido($row2['apellido']);
                    $this->setTelefono($row2['telefono']);
                    $resp = true;
                }
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }

    //a este metodo le sacamos la palabra static
    public function listar($condicion = '')
    {
        $arregloPersona = null;
        $base = new BaseDatos();
        $consultaPersonas = 'SELECT * FROM persona ';
        if ($condicion != '') {
            $consultaPersonas = $consultaPersonas . ' WHERE ' . $condicion;
        }
        $consultaPersonas .= ' ORDER BY apellido ';
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaPersonas)) {
                $arregloPersona = [];
                while ($row2 = $base->Registro()) {
                    $NroDoc = $row2['nrodoc'];
                    $Nombre = $row2['nombre'];
                    $Apellido = $row2['apellido'];
                    $Telefono = $row2['telefono'];
                    $perso = new Persona();
                    $perso->cargar([
                        'nomb' => $Nombre,
                        'nrodoc' => $NroDoc,
                        'ape' => $Apellido,
                        'tel' => $Telefono,
                    ]);
                    array_push($arregloPersona, $perso);
                }
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $arregloPersona;
    }

    public function insertar()
    {
        $base = new BaseDatos();
        $resp = false;
        $consultaInsertar =
            'INSERT INTO persona (nrodoc, apellido, nombre, telefono) VALUES (' .
            $this->getNrodoc() .
            ",'" .
            $this->getApellido() .
            "','" .
            $this->getNombre() .
            "','" .
            $this->getTelefono() .
            "')";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaInsertar)) {
                $resp = true;
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }

    public function modificar()
    {
        $resp = false;
        $base = new BaseDatos();
        $consultaModifica =
            "UPDATE persona SET apellido='" .
            $this->getApellido() .
            "', nombre='" .
            $this->getNombre() .
            "', telefono='" .
            $this->getTelefono() .
            "' WHERE nrodoc=" .
            $this->getNrodoc();
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaModifica)) {
                $resp = true;
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }

    public function eliminar()
    {
        $base = new BaseDatos();
        $resp = false;
        if ($base->Iniciar()) {
            $consultaBorra =
                'DELETE FROM persona WHERE nrodoc=' . $this->getNrodoc();
            if ($base->Ejecutar($consultaBorra)) {
                $resp = true;
            } else {
                $this->setmensajeoperacion($base->getError());
            }
        } else {
            $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }

    public function __toString()
    {
        return 'DNI: ' .
            $this->getNrodoc() .
            "\nNombre: " .
            $this->getNombre() .
            "\nApellido: " .
            $this->getApellido() .
            "\ntelefono: " .
            $this->getTelefono() .
            "\n";
    }
}
?>

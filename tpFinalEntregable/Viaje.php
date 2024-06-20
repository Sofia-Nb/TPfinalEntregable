
<?php class Viaje
{
    private $idviaje;
    private $vdestino;
    private $vcantmaxpasajeros;
    private $objEmpresa; 
    private $objResponsaje;
    private $vimporte;
    private $colObjPasajero;
    private $mensajeoperacion;

    public function __construct()
    {
        $this->idviaje = '';
        $this->vdestino = '';
        $this->vcantmaxpasajeros = '';
        $this->objEmpresa = '';
        $this->objResponsaje = '';
        $this->vimporte = '';
        $this->colObjPasajero = [];
    }

    public function setIdviaje($idv)
    {
        $this->idviaje = $idv;
    }

    public function setVdestino($dest)
    {
        $this->vdestino = $dest;
    }

    public function setVcantmaxpasajeros($cantmax)
    {
        $this->vcantmaxpasajeros = $cantmax;
    }

    public function setobjEmpresa($objEmpresa)
    {
        $this->objEmpresa = $objEmpresa;
    }

    public function setObjResponsaje($objRes)
    {
        $this->objResponsaje = $objRes;
    }

    public function setVimporte($importe)
    {
        $this->vimporte = $importe;
    }

    public function setMensajeOperacion($mensajeoperacion)
    {
        $this->mensajeoperacion = $mensajeoperacion;
    }
    public function setColObjPasajero($colObjPasajero)
    {
        $this->colObjPasajero = $colObjPasajero;
    }

    public function getIdviaje()
    {
        return $this->idviaje;
    }

    public function getVdestino()
    {
        return $this->vdestino;
    }

    public function getVcantmaxpasajeros()
    {
        return $this->vcantmaxpasajeros;
    }

    public function getobjEmpresa()
    {
        return $this->objEmpresa;
    }

    public function getObjResponsaje()
    {
        return $this->objResponsaje;
    }

    public function getVimporte()
    {
        return $this->vimporte;
    }

    public function getMensajeOperacion()
    {
        return $this->mensajeoperacion;
    }
    public function getColObjPasajero()
    {
        return $this->colObjPasajero;
    }

    public function cargar(
        $idv,
        $dest,
        $cantmax,
        $objEmpresa,
        $objRes,
        $importe,
        $colObjPasajero
    ) {
        $this->setIdviaje($idv);
        $this->setVdestino($dest);
        $this->setVcantmaxpasajeros($cantmax);
        $this->setobjEmpresa($objEmpresa);
        $this->setObjResponsaje($objRes);
        $this->setVimporte($importe);
        $this->setColObjPasajero($colObjPasajero);
    }

    public function buscar($idv)
    {
        $base = new BaseDatos();
        $consultaViaje = 'SELECT * FROM viaje WHERE idviaje = ' . $idv;
        $resp = false;
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaViaje)) {
                if ($row = $base->Registro()) {
                    $this->setIdviaje($row['idviaje']);
                    $this->setVdestino($row['vdestino']);
                    $this->setVcantmaxpasajeros($row['vcantmaxpasajeros']);
                    $nroDocResponsable = $row['rnumeroempleado'];

                    $responsable = new Responsable();
                    $empresa = new Empresa();
                    $empresa->buscar($row['idempresa']);
                    $this->setobjEmpresa($empresa);

                    if ($responsable->buscar($nroDocResponsable)) {
                        $this->setObjResponsaje($responsable);
                        $this->getObjResponsaje()->setNrodoc(
                            $row['rnumeroempleado']
                        );
                    }

                    $this->setVimporte($row['vimporte']);

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
        $arregloViaje = null;
        $base = new BaseDatos();
        $consultaViajes = 'SELECT * FROM viaje ';
        if ($condicion != '') {
            $consultaViajes = $consultaViajes . ' WHERE ' . $condicion;
        }
        $consultaViajes .= ' ORDER BY idviaje ';
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaViajes)) {
                $arregloViaje = [];
                while ($row = $base->Registro()) {
                    $idv = $row['idviaje'];
                    $dest = $row['vdestino'];
                    $cantmax = $row['vcantmaxpasajeros'];
                    $id = $row['idempresa'];
                    $objRes = $row['rnumeroempleado'];
                    $importe = $row['vimporte'];
                    // USAR EL BUSCAR
                    //coleccion de pasajeros
                    $pasajero = new Pasajero();
                    $coleccionPasajero = $pasajero->listar('idViajePas ='.$idv);
                

                    $empresa = new Empresa();
                    $empresa->buscar($id);
                    $viaje = new Viaje();

                    $viaje->cargar(
                        $idv,
                        $dest, 
                        $cantmax,
                        $empresa,
                        $objRes,
                        $importe,
                        $coleccionPasajero
                    );
                    array_push($arregloViaje, $viaje);
                }
            } else {
                $this->setMensajeOperacion($base->getError());
            }
        } else {
            $this->setMensajeOperacion($base->getError());
        }
        return $arregloViaje;
    }

    public function insertar()
    {
        $base = new BaseDatos();
        $resp = false;
        // Insertar el nuevo viaje en la tabla viaje
        $consultaInsertar =
            "INSERT INTO viaje (vdestino, vcantmaxpasajeros, idempresa, rnumeroempleado, vimporte) VALUES ('" .
            $this->getVdestino() .
            "','" .
            $this->getVcantmaxpasajeros() .
            "'," .
            $this->getobjEmpresa() .
            ",'" .
            $this->getObjResponsaje()->getNrodoc() .
            "','" .
            $this->getVimporte() .
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
        $resp = false;
        $base = new BaseDatos();
        // Modificar los datos del viaje en la tabla viaje
        $consultaModifica =
            "UPDATE viaje SET vdestino='" .
            $this->getVdestino() .
            "', vcantmaxpasajeros=" .
            $this->getVcantmaxpasajeros() .
            ', idempresa=' .
            $this->getobjEmpresa() .
            ', rnumeroempleado=' .
            $this->getObjResponsaje()->getNrodoc() .
            ', vimporte=' .
            $this->getVimporte() .
            ' WHERE idviaje=' .
            $this->getIdviaje();
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
        // Eliminar el viaje de la tabla viaje
        $consultaEliminar =
            'DELETE FROM viaje WHERE idviaje=' . $this->getIdviaje();
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consultaEliminar)) {
                $resp = true;
            } else {
                $this->setMensajeOperacion($base->getError());
            }
        } else {
            $this->setMensajeOperacion($base->getError());
        }
        return $resp;
    }

    public function cadenaPasajeros()
    {
        $cadena = '';
        if (count($this->getColObjPasajero()) > 0) {
            foreach ($this->getColObjPasajero() as $pasajero) {
                $cadena .= $pasajero . "\n";
            }
        } else {
            $cadena = "No se encuentran Pasajeros en el viaje\n";
        }
    }

    public function __toString()
    {
        $cadena = 'ID Viaje: ' . $this->getIdviaje() . "\n";
        $cadena .= 'Destino: ' . $this->getVdestino() . "\n";
        $cadena .=
            'Cantidad Máxima de Pasajeros: ' .
            $this->getVcantmaxpasajeros() .
            "\n";
        $cadena .= 'Id de la Empresa = ' . $this->getobjEmpresa() . "\n";
        $cadena .=
            'Número de Empleado Responsable: ' .
            $this->getObjResponsaje() .
            "\n";
        $cadena .= 'Importe: ' . $this->getVimporte() . "\n";
        $cadena .= "Pasajeros:\n" . $this->cadenaPasajeros();
        return $cadena;
    }
}

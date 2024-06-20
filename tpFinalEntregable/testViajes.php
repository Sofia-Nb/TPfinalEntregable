<?php
include_once 'Empresa.php';
include_once 'BaseDatos.php';
include_once 'Responsable.php';

include_once 'Viaje.php';
include_once 'Pasajero.php';

// Creas un objeto y estableces la conexión a la base de datos
$objEmpresa = new Empresa();
$base = new BaseDatos();
$objResponsable = new Responsable();
$objViaje = new Viaje();
$objPasajero = new Pasajero();
$colecccionPasajeros = [];

echo "**********************\nBienvenido al sistema\n**********************\n";

// Función para mostrar el menú y recibir la opción del usuario
function mostrarMenu()
{
    echo "***************\nMenú de Opciones\n***************\n";
    echo "1. Cargar una Empresa\n";
    echo "2. Modificar una Empresa\n";
    echo "3. Eliminar una Empresa\n";
    echo "4. Listar Empresas\n"; // Esto muestra la lista de empresas, aunque originalmente no lo pedías.
    echo "5. Cargar Responsable\n";
    echo "6. Viaje\n";
    echo "7. Pasajero\n";
    echo "0. Salir\n";
    echo 'Seleccione una opción: ';
    return trim(fgets(STDIN));
}

// Función para cargar la primera empresa si no hay ninguna cargada
function cargarPrimeraEmpresa($objEmpresa)
{
    $colEmpresas = $objEmpresa->listar(); // Obtener la lista de empresas existentes

    if (empty($colEmpresas)) {
        // verifica que el array está vacio.
        echo "No hay empresas cargadas. Vamos a cargar la primera:\n";
        echo "Ingrese el nombre de la empresa: \n";
        $nombre = trim(fgets(STDIN));
        echo "Ingrese la dirección de la empresa: \n";
        $direccion = trim(fgets(STDIN));

        $objEmpresa->cargar(
            1, // Utilizamos 1 como ID para la primera empresa
            $nombre,
            $direccion,
            []
        );

        // Insertar el objeto Empresa en la base de datos
        $respuesta = $objEmpresa->insertar();
        if ($respuesta == true) {
            echo "\nOperación Exitosa: La Empresa fue ingresada en la BD\n";
            echo $objEmpresa . "\n"; // Muestra los datos de la empresa ingresada
            return true; // Indica que se cargó una empresa correctamente
        } else {
            echo 'Error: ' . $objEmpresa->getMensajeOperacion() . "\n";
            return false; // Indica que hubo un error al cargar la empresa
        }
    } else {
        echo "Empresas cargadas:\n";
        foreach ($colEmpresas as $empresa) {
            echo $empresa . "\n"; // Muestra los datos de cada empresa
        }
        return true; // Indica que ya hay empresas cargadas
    }
}

// Variable para controlar si se ha cargado una empresa o no
$empresaCargada = cargarPrimeraEmpresa($objEmpresa);

// Bucle principal del programa (mostrar menú completo solo si hay una empresa cargada)
if ($empresaCargada) {
    do {
        $opcion = mostrarMenu();

        switch ($opcion) {
            case 1:
                // Cargar una empresa
                echo "***************\nCargar una Empresa\n***************\n";
                echo "Ingrese el nombre de la empresa: \n";
                $nombre = trim(fgets(STDIN));
                echo "Ingrese la dirección de la empresa: \n";
                $direccion = trim(fgets(STDIN));

                // Obtener el último ID para la nueva empresa
                $ultimoID = count($objEmpresa->listar()) + 1;

                $objEmpresa->cargar($ultimoID, $nombre, $direccion, []);

                // Insertar el objeto Empresa en la base de datos
                $respuesta = $objEmpresa->insertar();
                if ($respuesta == true) {
                    echo "\nOperación Exitosa: La Empresa fue ingresada en la BD\n";
                    echo $objEmpresa . "\n"; // Muestra los datos de la empresa ingresada
                } else {
                    echo 'Error: ' . $objEmpresa->getMensajeOperacion() . "\n";
                }
                break;

            case 2:
                // Modificar una empresa
                echo "***************\nModificar una Empresa\n***************\n";
                echo "Ingrese el ID de la empresa que desea modificar: \n";
                $idModificar = trim(fgets(STDIN));

                // Buscar la empresa por ID
                if ($objEmpresa->buscar($idModificar)) {
                    echo "Empresa encontrada:\n";
                    echo $objEmpresa . "\n"; // Muestra los datos actuales de la empresa

                    // Solicitar nuevos datos
                    echo "Ingrese el nuevo nombre de la empresa: \n";
                    $nuevoNombre = trim(fgets(STDIN));
                    echo "Ingrese la nueva dirección de la empresa: \n";
                    $nuevaDireccion = trim(fgets(STDIN));

                    // Actualizar los datos en el objeto Empresa
                    $objEmpresa->setEnombre($nuevoNombre);
                    $objEmpresa->setEdireccion($nuevaDireccion);

                    // Ejecutar la modificación en la base de datos
                    $respuestaModificacion = $objEmpresa->modificar();
                    if ($respuestaModificacion) {
                        echo "Empresa modificada exitosamente.\n";
                        echo $objEmpresa . "\n"; // Muestra los datos actualizados de la empresa
                    } else {
                        echo 'Error al modificar la empresa: ' .
                            $objEmpresa->getMensajeOperacion() .
                            "\n";
                    }
                } else {
                    echo "No se encontró ninguna empresa con ID: $idModificar\n";
                }
                break;

            case 3:
                // Eliminar una empresa
                echo "***************\nEliminar una Empresa\n***************\n";
                echo "Ingrese el ID de la empresa que desea eliminar: \n";
                $idEliminar = trim(fgets(STDIN));

                // Buscar la empresa por ID
                if ($objEmpresa->buscar($idEliminar)) {
                    echo "Empresa encontrada:\n";
                    echo $objEmpresa . "\n"; // Muestra los datos de la empresa antes de eliminar

                    // Confirmar eliminación
                    echo "¿Está seguro que desea eliminar la empresa? (S/N): \n";
                    $confirmacion = strtoupper(trim(fgets(STDIN)));

                    if ($confirmacion === 'S') {
                        // Ejecutar la eliminación en la base de datos
                        $respuestaEliminacion = $objEmpresa->eliminar();
                        if ($respuestaEliminacion) {
                            echo "Empresa eliminada correctamente.\n";
                            // Si eliminamos la empresa cargada, marcamos que no hay ninguna empresa cargada
                        } else {
                            echo 'Error al eliminar la empresa: ' .
                                $objEmpresa->getMensajeOperacion() .
                                "\n";
                        }
                    } else {
                        echo "Operación cancelada.\n";
                    }
                } else {
                    echo "No se encontró ninguna empresa con ID: $idEliminar\n";
                }
                break;

            case 4:
                // Listar empresas
                echo "***************\nListado de Empresas\n***************\n";
                $colEmpresas = $objEmpresa->listar();
                foreach ($colEmpresas as $empresa) {
                    echo $empresa . "\n"; // Muestra cada empresa en la lista
                    echo "-------------------------------------------------------\n";
                }
                break;
            // Caso 5: Cargar Responsable
            case 5:
                echo "***************\nCargar Responsable\n***************\n";
                if (count($objEmpresa->listar()) > 0) {
                    // Solicitar datos del responsable
                    echo "Ingrese el número de documento del responsable: \n";
                    $nrodoc = trim(fgets(STDIN));

                    $existe = $objResponsable->buscar($nrodoc);
                    if (!$existe) {
                        echo "Ingrese el nombre del responsable: \n";
                        $nombre = trim(fgets(STDIN));
                        echo "Ingrese el apellido del responsable: \n";
                        $apellido = trim(fgets(STDIN));
                        echo "Ingrese el teléfono del responsable: \n";
                        $telefono = trim(fgets(STDIN));
                        echo "Ingrese el número de empleado del responsable: \n";
                        $numeroEmpleado = trim(fgets(STDIN));
                        echo "Ingrese el número de licencia del responsable: \n";
                        $numeroLicencia = trim(fgets(STDIN));

                        // Crear un objeto Responsable y cargar los datos

                        $objResponsable->cargar([
                            'nrodoc' => $nrodoc,
                            'nomb' => $nombre,
                            'ape' => $apellido,
                            'tel' => $telefono,
                            'numEmpleado' => $numeroEmpleado,
                            'numLicencia' => $numeroLicencia,
                        ]);

                        // Insertar el responsable en la base de datos
                        $respuestaResp = $objResponsable->insertar();
                        if ($respuestaResp) {
                            echo "\nOperación Exitosa: El Responsable fue ingresado en la BD\n";
                            echo $objResponsable . "\n";
                        } else {
                            echo 'Error: ' .
                                $objResponsable->getMensajeOperacion() .
                                "\n";
                        }
                    } else {
                        echo "Ya existe un Responsable con el nro de documento ingresado.\n";
                    }
                } else {
                    echo "No hay empresas registradas\n";
                }

                break;

            case 6:
                echo "\n***************\nViaje\n***************\n";

                do {
                    echo "\n***************\nMenú de Opciones\n***************\n";
                    echo "1. Cargar un Viaje\n";
                    echo "2. Modificar un Viaje\n";
                    echo "3. Eliminar un Viaje\n";
                    echo "4. Listar Viajes\n";
                    echo "0. Salir\n";
                    echo 'Seleccione una opción: ';
                    $opcionViaje = trim(fgets(STDIN));

                    switch ($opcionViaje) {
                        case 1:
                            echo "\n***************\nCargar Viaje\n***************\n";
                            echo "Ingrese el ID de la empresa para la que desea cargar el Viaje: \n";
                            $idEmpresaResp = trim(fgets(STDIN));

                            if ($objEmpresa->buscar($idEmpresaResp)) {
                                // Empresa encontrada, proceder a cargar responsable
                                echo "Empresa encontrada:\n";
                                echo $objEmpresa . "\n";

                                // Solicitar datos del viaje
                                echo "Ingrese el destino: \n";
                                $destino = trim(fgets(STDIN));
                                echo "Ingrese el dni del resonsable: \n";
                                $dniResp = trim(fgets(STDIN));
                                if ($objResponsable->buscar($dniResp)) {
                                    // Responsable encontrado, proceder con el resto del proceso
                                    echo "Ingrese la cant max de pasajeros : \n";
                                    $cantMax = trim(fgets(STDIN));
                                    echo "Ingrese el importe $: \n";
                                    $importe = trim(fgets(STDIN));

                                    //Buscamos el responsable

                                    $objResponsable = $objResponsable->listar(
                                        'numeroDocumentoRes =' . $dniResp
                                    )[0];

                                    // Crear un objeto viaje y cargar los datos

                                    $objViaje->cargar(
                                        count(
                                            $objViaje->listar(
                                                'idempresa = ' . $idEmpresaResp
                                            )
                                        ) + 1,
                                        $destino,
                                        $cantMax,
                                        $objEmpresa->getidempresa(),
                                        $objResponsable,
                                        $importe,
                                        []
                                    );

                                    // Insertar el Viaje en la base de datos
                                    $respuesta = $objViaje->insertar();
                                    if ($respuesta) {
                                        echo "\nOperación Exitosa: El Viaje fue ingresado en la BD\n";
                                        echo $objViaje . "\n";
                                    } else {
                                        echo 'Error: ' .
                                            $objViaje->getMensajeOperacion() .
                                            "\n";
                                    }
                                } else {
                                    echo 'No se encotro el responsable, ingrese un numero valido.';
                                }
                            } else {
                                echo "No se encontró ninguna empresa con ID: $idEmpresaResp\n";
                            }
                            break;

                        case 2:
                            echo "\n***************\Modificar Viaje\n***************\n";

                            echo "Ingrese el ID del Viaje\n";
                            $idViaje = trim(fgets(STDIN));

                            if ($objViaje->buscar($idViaje)) {
                                // Solicitar nuevos datos

                                do {
                                    echo "Ingrese el ID de la nueva Empresa:\n";
                                    $idEmpresa = trim(fgets(STDIN));
                                    $respuesta = 's';
                                    $valor = false;
                                    if ($objEmpresa->buscar($idEmpresa)) {
                                        $valor = true;
                                        $objViaje->setobjEmpresa(
                                            $objEmpresa->getidempresa()
                                        );
                                    } else {
                                        echo "No se encontro una Empresa con el ID ingresado\n ¿Desea volver a intentarlo? S/N\n";
                                        $respuesta = trim(fgets(STDIN));
                                    }
                                } while (
                                    !$valor &&
                                    ($respuesta == 'S' || $respuesta == 's')
                                );
                                if ($valor) {
                                    do {
                                        echo "Ingrese el numero de documento del nuevo responsable:\n";
                                        $nrodoc = trim(fgets(STDIN));
                                        $respuesta = 's';
                                        $valor = false;
                                        if ($objResponsable->buscar($nrodoc)) {
                                            $valor = true;
                                            $objViaje->setObjResponsaje(
                                                $objResponsable
                                            );
                                        } else {
                                            echo "No se encontro un responsable con el ID ingresado\n ¿Desea volver a intentarlo? S/N\n";
                                            $respuesta = trim(fgets(STDIN));
                                        }
                                    } while (
                                        !$valor &&
                                        ($respuesta == 'S' || $respuesta == 's')
                                    );
                                }
                                if ($valor) {
                                    echo "Ingrese el nuevo importe: \n";
                                    $objViaje->setVimporte(trim(fgets(STDIN)));

                                    echo "Ingrese el nuevo destino del viaje: \n";
                                    $objViaje->setVdestino(trim(fgets(STDIN)));

                                    echo "Ingrese la nueva cantidad maxima de pasajeros: \n";
                                    $objViaje->setVcantmaxpasajeros(
                                        trim(fgets(STDIN))
                                    );

                                    // Ejecutar la modificación en la base de datos
                                    $respuesta = $objViaje->modificar();
                                    if ($respuesta) {
                                        echo "Viaje modificado exitosamente.\n";
                                        echo $objViaje . "\n"; // Muestra los datos actualizados de la empresa
                                    } else {
                                        echo 'Error al modificar el viaje: ' .
                                            $objViaje->getMensajeOperacion() .
                                            "\n";
                                    }
                                }
                            } else {
                                echo 'No se encontro ningun viaje con ID: ' .
                                    $idViaje .
                                    "\n";
                            }

                            break;
                        case 3:
                            // Eliminar un viaje
                            echo "*****\nEliminar Viaje\n*******\n";
                            echo "Ingrese el ID del viaje que desea eliminar: \n";
                            $idEliminarViaje = trim(fgets(STDIN));

                            // Buscar el viaje por ID
                            if ($objViaje->buscar($idEliminarViaje)) {
                                echo "Viaje encontrado:\n";
                                echo $objViaje . "\n"; // Muestra los datos del viaje antes de eliminar

                                $colecccionPasajeros = $objViaje->getColObjPasajero();
                                if (count($colecccionPasajeros) > 0) {
                                    echo "¿Está seguro que desea eliminar el viaje? (S/N). Tenga en cuenta que se eliminarán todos sus pasajeros: \n";
                                    $confirmacionViaje = strtoupper(
                                        trim(fgets(STDIN))
                                    );

                                    if ($confirmacionViaje === 'S') {
                                        // Ejecutar la eliminación en la base de datos
                                        $respuestaEliminacionViaje = $objViaje->eliminar();
                                        if ($respuestaEliminacionViaje) {
                                            echo "Viaje eliminado correctamente.\n";
                                        } else {
                                            echo 'Error al eliminar el viaje: ' .
                                                $objViaje->getMensajeOperacion() .
                                                "\n";
                                        }
                                    } else {
                                        echo "Operación cancelada.\n";
                                    }
                                } else {
                                    $respuestaEliminacionViaje = $objViaje->eliminar();
                                    if ($respuestaEliminacionViaje) {
                                        echo "Viaje eliminado correctamente.\n";
                                    } else {
                                        echo 'Error al eliminar el viaje: ' .
                                            $objViaje->getMensajeOperacion() .
                                            "\n";
                                    }
                                }
                            } else {
                                echo "No se encontró ningún viaje con ID: $idEliminarViaje\n";
                            }
                            break;
                        case 4:
                            // Listar empresas
                            echo "***************\nListado de Viajes\n***************\n";
                            echo "Ingrese el Id de la Empresa:\n";
                            $idEmpresa = trim(fgets(STDIN));
                            $colecccionViajes = $objViaje->listar(
                                'idempresa =' . $idEmpresa
                            );
                            if (count($colecccionViajes) > 0) {
                                foreach ($colecccionViajes as $viaje) {
                                    echo $viaje . "\n"; // Muestra cada viaje en la lista
                                    echo "-------------------------------------------------------\n";
                                }
                            } else {
                                echo "No se encuentran viajes en la Empresa o se ingreso un Id erroneo\n";
                            }
                            break;
                        default:
                            echo "Opción inválida. Por favor, seleccione una opción válida.\n";
                            break;
                    }
                } while ($opcionViaje != 0);
                break;

            case 7:
                do {
                    echo "\n**********\nPasajero\n**********\n";
                    echo "\n***************\nMenú de Opciones\n***************\n";
                    echo "1. Cargar un Pasajero\n";
                    echo "2. Modificar un Pasajero\n";
                    echo "3. Eliminar un Pasajero\n";
                    echo "0. Salir\n";
                    echo 'Seleccione una opción: ';
                    $opcionPasajero = trim(fgets(STDIN));

                    switch ($opcionPasajero) {
                        case 1:
                            echo "\n***************\nCargar Pasajero\n***************\n";
                            echo "Ingrese el ID del Viaje para la que desea cargar el Pasajero: \n";
                            $idViaje = trim(fgets(STDIN));
                            $coleccionPasajeros = $objViaje->listar(
                                'idviaje =' . $idViaje
                            );

                            if (
                                $objViaje->buscar($idViaje) &&
                                count($coleccionPasajeros) <
                                    $objViaje->getVcantmaxpasajeros()
                            ) {
                                // Viaje encontrado, proceder a cargar Pasajero
                                echo "Viaje encontrado:\n";

                                //Cargamos la coleccion de pasajeros
                                //Mostramos los datos del viaje actual
                                echo $objViaje . "\n";

                                // Solicitar datos del pasajero
                                echo "Ingrese el dni del pasajero: \n";
                                $nrodoc = trim(fgets(STDIN));

                                $existe = $objPasajero->buscar($nrodoc);

                                if (
                                    !$existe &&
                                    !$objResponsable->buscar($nrodoc)
                                ) {
                                    echo "Ingrese el nombre del pasajero:\n";
                                    $nombre = trim(fgets(STDIN));

                                    echo "Ingrese el apellido del pasajero:\n";
                                    $apellido = trim(fgets(STDIN));

                                    echo "Ingrese el telefono del pasajero:\n";
                                    $telefono = trim(fgets(STDIN));

                                    $objPasajero->cargar([
                                        'nrodoc' => $nrodoc,
                                        'nomb' => $nombre,
                                        'ape' => $apellido,
                                        'tel' => $telefono,
                                        'objViaje' => $objViaje,
                                    ]);
                                    $respuesta = $objPasajero->insertar();
                                    if ($respuesta == true) {
                                        echo "\nOperación Exitosa: El Pasajero fue ingresado en la BD\n";
                                        echo $objPasajero . "\n";
                                    } else {
                                        echo 'Error: ' .
                                            $objPasajero->getMensajeOperacion() .
                                            "\n";
                                    }
                                } else {
                                    echo "El pasajero ya se encuentra en el Viaje o es Responsable\n";
                                }
                            } else {
                                echo 'No se encontró ningun viaje con ID: ' .
                                    $idViaje .
                                    " o se excede el limite de pasajeros. \n";
                            }
                            break;
                        case 2:
                            echo "\n***************\nModificar Pasajero\n***************\n";
                            echo "Ingrese el nro de documento del Pasajero a modificar: \n";
                            $nrodoc = trim(fgets(STDIN));

                            if ($objPasajero->buscar($nrodoc)) {
                                echo "Ingrese el nombre del pasajero:\n";
                                $nombre = trim(fgets(STDIN));

                                echo "Ingrese el apellido del pasajero:\n";
                                $apellido = trim(fgets(STDIN));

                                echo "Ingrese el telefono del pasajero:\n";
                                $telefono = trim(fgets(STDIN));

                                do {
                                    $respuesta = 'N';
                                    $valor = false;
                                    echo "Ingrese el ID del viaje:\n";
                                    $idViaje = trim(fgets(STDIN));

                                    if ($objViaje->buscar($idViaje)) {
                                        $objPasajero->cargar([
                                            'nrodoc' => $nrodoc,
                                            'nomb' => $nombre,
                                            'ape' => $apellido,
                                            'tel' => $telefono,
                                            'objViaje' => $objViaje,
                                        ]);
                                        $valor = true;
                                        $objPasajero->modificar();
                                        echo "Se ha modificado el pasajero exitosamente.\n";
                                    } else {
                                        echo "No se encontraron viajes con el ID ingresado\n¿Desea ingresar otro? S/N\n";
                                        $respuesta = trim(fgets(STDIN));
                                    }
                                } while (
                                    !$valor &&
                                    ($respuesta == 'S' || $respuesta == 's')
                                );
                            } else {
                                echo "El numero de documento ingresado no ha sido encontrado.\n";
                            }
                            break;
                        case 3:
                            echo "\n***************\nEliminar Pasajero\n***************\n";
                            echo "Ingrese el nro de documento del Pasajero a eliminar\n";
                            $nrodoc = trim(fgets(STDIN));

                            if ($objPasajero->buscar($nrodoc)) {
                                echo 'Está seguro que desea eliminar este pasajero? (S/N): ';
                                $confirmacion = strtoupper(trim(fgets(STDIN)));

                                if ($confirmacion === 'S') {
                                    // Eliminar el pasajero de la base de datos
                                    $respuestaElim = $objPasajero->eliminar();
                                    if ($respuestaElim) {
                                        echo "\nOperación Exitosa: El Pasajero fue eliminado\n";
                                    } else {
                                        echo 'Error: ' .
                                            $objPasajero->getMensajeOperacion() .
                                            "\n";
                                    }
                                } else {
                                    echo "Operación cancelada\n";
                                }
                            } else {
                                echo "El pasajero ingresado no existe\n";
                            }

                            break;
                        case 0:
                            echo 'Volviendo al menu previo';
                            break;

                        default:
                            echo "Opción inválida. Por favor, seleccione una opción válida.\n";

                            break;
                    }
                } while ($opcionPasajero != 0);

                break;

            case 0:
                // Salir del programa
                echo "Saliendo del sistema.\n";
                break;

            default:
                echo "Opción inválida. Por favor, seleccione una opción válida.\n";
                break;
        }
    } while ($opcion != 0);
} else {
    echo "No se pudo cargar la empresa. Saliendo del sistema.\n";
}
?>

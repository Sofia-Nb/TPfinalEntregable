<?php

/**Encapsulamiento: Es cierto que podrías usar métodos get y set para acceder a los atributos privados. Sin embargo, en este caso, no es estrictamente necesario porque la clase BaseDatos es interna y solo la usan otras clases que dependen de ella. */

class BaseDatos
{
    private $HOSTNAME; // La dirección del servidor de la base de datos. En este caso, es 127.0.0.1 (localhost).
    private $BASEDATOS; // El nombre de la base de datos que se va a utilizar. Aquí es bd_prueba.
    private $USUARIO; //  El nombre de usuario para conectarse a la base de datos, que es root.
    private $CLAVE; //La contraseña para el usuario de la base de datos. Aquí está vacía ("")
    private $CONEXION; //Almacena la conexión a la base de datos.
    private $QUERY; // Almacena la consulta SQL que se ejecutará.
    private $RESULT; //Almacena el resultado de una consulta.
    private $ERROR; //Almacena los mensajes de error si ocurre algún problema.

    public function __construct()
    {
        $this->HOSTNAME = '127.0.0.1';
        $this->BASEDATOS = 'bdviajes';
        $this->USUARIO = 'root';
        $this->CLAVE = '';
        $this->RESULT = null;
        $this->QUERY = '';
        $this->ERROR = '';
    }

    //Este método devuelve cualquier mensaje de error almacenado en el atributo $ERROR.
    public function getError()
    {
        return "\n" . $this->ERROR; //la unica diferencia es que tiene un salto de linea
    }

    /**Este método intenta establecer una conexión con la base de datos utilizando los atributos de conexión. Si la conexión es exitosa, devuelve true; de lo contrario, almacena el mensaje de error en $ERROR y devuelve false. */
    public function Iniciar()
    {
        $resp = false;
        $this->CONEXION = mysqli_connect(
            $this->HOSTNAME,
            $this->USUARIO,
            $this->CLAVE,
            $this->BASEDATOS
        );
        if ($this->CONEXION) {
            $resp = true;
        } else {
            $this->ERROR =
                mysqli_connect_errno() . ': ' . mysqli_connect_error();
        }
        return $resp;
    }

    public function Ejecutar($consulta)
    {
        $resp = false;
        unset($this->ERROR);
        $this->QUERY = $consulta;
        if ($this->RESULT = mysqli_query($this->CONEXION, $consulta)) {
            $resp = true;
        } else {
            $this->ERROR =
                mysqli_errno($this->CONEXION) .
                ': ' .
                mysqli_error($this->CONEXION);
        }
        return $resp;
    }

       // Se optienen resultados de la consulta.

    public function Registro()
    {
        $resp = null;
        if ($this->RESULT) {
            unset($this->ERROR);
            if ($temp = mysqli_fetch_assoc($this->RESULT)) {
                $resp = $temp;
            } else {
                mysqli_free_result($this->RESULT);
            }
        } else {
            $this->ERROR =
                mysqli_errno($this->CONEXION) .
                ': ' .
                mysqli_error($this->CONEXION);
        }
        return $resp;
    }

    public function devuelveIDInsercion($consulta)
    {
        $resp = null;
        unset($this->ERROR);
        $this->QUERY = $consulta;
        if ($this->RESULT = mysqli_query($this->CONEXION, $consulta)) {
            $resp = mysqli_insert_id($this->CONEXION);
        } else {
            $this->ERROR =
                mysqli_errno($this->CONEXION) .
                ': ' .
                mysqli_error($this->CONEXION);
        }
        return $resp;
    }
}

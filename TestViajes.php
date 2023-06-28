<?php

include "BaseDatos.php";
include "ResponsableV.php";
include "Empresa.php";
include "Viaje.php";
include "Pasajero.php";
 
function cargarDatos(){
    do{
        echo "¿Qué datos desea cargar?\n
        1) Datos de una empresa \n
        2) Datos de un empleado \n
        3) Datos de un viaje \n
        4) Datos de uno o más pasajeros \n
        5) Volver al menú principal. \n";
        $opcion = trim(fgets(STDIN));
        
        if($opcion <= 5 && $opcion >= 1)
        {
            switch($opcion){
                case 1: // Datos de una empresa
                    cargarEmpresa();
                break;
                case 2://Datos de un empleado
                    cargarResponsableV();
                break;
                case 3: //Datos de un viaje
                    cargarViaje();
                break;
                case 4: //Datos de uno o más pasajeros
                    agregarPasajeros();
                break;
                case 5:
                break;
            }
        } else {
            echo "El número ingresado no está dentro del rango de opciones, por favor vuelva a intentarlo.\n";
        }
        
    } while($opcion <> 5);

}

/**
 * función cargarEmpresa
 * carga los datos de un objetpo empresa en la base de datos.
 */
function cargarEmpresa()
{
    echo "Ingrese el nombre de la empresa.\n";
    $nombre = trim(fgets(STDIN));
    echo "Ingrese la dirección de la empresa.\n";
    $direccion = trim(fgets(STDIN));
    $objEmpresa = new Empresa();
    $objEmpresa->cargar($nombre, $direccion);
    $resp = $objEmpresa->insertar();
    if($resp)
    {
        echo "Empresa ingresada correctamente a la base de datos.\n";
    } else {
        echo "La empresa no pudo ser cargada a la base de datos.\n";
        echo $objEmpresa->getMsjBaseDatos()."\n";
    }
}

/**
 * función cargarResponsableV 
 * carga los datos de un objeto responsableV en caso de que no exista ya en la bbdd.
 * @return object
 */
function cargarResponsableV()
{
    $objBaseDatos = new BaseDatos();
    $objResponsable = new ResponsableV();
    echo "Ingrese el número de licencia.\n";
    $numLicencia = trim(fgets(STDIN));
    $consulta = "SELECT * FROM resposable WHERE rnumerolicencia=$numLicencia";
    
    if($objBaseDatos->Iniciar())
    {
        if($objBaseDatos->Ejecutar($consulta))
        {
             echo "El responsable ingresado ya existe\n"; 
        } else {
            echo "Ingrese el nombre del responsable del viaje.\n";
            $nombre_responsable = trim(fgets(STDIN));
            echo  "Ingrese el apellido del resposable del viaje.\n";
            $apellido_responsable = trim(fgets(STDIN));
            $objResponsable -> cargar ("", $numLicencia, $nombre_responsable, $apellido_responsable);
            $resp = $objResponsable -> insertar();
            if($resp)
            {
                echo "Responsable ingresado correctamente a la base de datos.\n";
            } else {
                echo "El responsable no pudo ser cargado a la base de datos.\n";
                echo $objResponsable -> getMsjBaseDatos()."\n";
            }
        }
    }
}

/**
 * función cargarViaje
 * carga los datos de un obj viaje en caso de que no exista ya en la bbdd.
 * @return object 
 */
function cargarViaje(){
    $objViaje = new Viaje();
    echo "Ingrese el id de la empresa a la que pertenece el viaje.\n";
    $idempresa = trim(Fgets(STDIN));
    $empresa = new Empresa();
    
    if($empresa->Buscar($idempresa))
    {
        echo "Ingrese el número de empleado del responsable del viaje. \n";
        $numEmpleado =trim(fgets(STDIN));
        $responsable = new ResponsableV();
        
        if($responsable->Buscar($numEmpleado))
        {
            do{
                echo "Ingrese el destino del viaje.\n";
                $destino = trim(fgets(STDIN));
                $existe = existeDestino($destino);
                if($existe)
                {
                    echo "El destino ya existe, inténtelo de nuevo.\n";
                }
            } while($existe);

            echo "Ingrese el máximo de pasajeros.\n";
            $maxPasajerosV = trim(fgets(STDIN));
            echo "Ingrese el importe del viaje.\n";
            $importe = trim(fgets(STDIN));
           
            $objViaje->cargar("",$destino, $maxPasajerosV, $empresa, $responsable, $importe);
            $resp = $objViaje->insertar();
            if($resp)
            {
                echo "El viaje fue ingresado correctamente a la base de datos.\n";
            } else {
                echo "El viaje no pudo ser cargado a la base de datos.\n";
                echo $objViaje->getMsjBaseDatos()."\n";
            }
        } else {
            echo "El responsable ingresado no existe, por favor ingrese un responsable existente para registrar el viaje.\n";
        }
    } else {
        echo "La empresa ingresada no existe, por favor ingrese una empresa existente para registrar el viaje.\n";
    }
}

function existeDestino($destino)
{
    $objViaje = new Viaje();
    $listaViajes = $objViaje->Listar("vdestino='$destino'");
    $existe=false;
    $i  = 0;
    if($i < sizeof($listaViajes) && !$existe)
    {
        $existe = true;
    } else {
        $i = $i + 1;
    }
    return $existe;
}

function agregarPasajeros()
{
    $objEmpresa = new Empresa();
    $objViaje = new Viaje();
    $i=1;
    echo "Ingrese el destino al que desea viajar, ingrese VOLVER si desea volver atrás.\n";
    $destinoElegido = trim(fgets(STDIN));
    if($destinoElegido <> "VOLVER" || $destinoElegido <> "volver")
    {
        $arrayViajes = $objViaje->listar(" vdestino= '$destinoElegido'");
        if($arrayViajes == null)
        {
            echo "No hay viajes disponibles para este destino.\n";
        } else {
            echo "Los viajes disponibles son los siguientes: \n";
            foreach($arrayViajes as $key => $viaje) 
            {
                $cantMaxPasajeros = $viaje->getVcantmaxpasajeros();
                $arrayPasajeros = $viaje->arregloPasajeros();
                if (count ($arrayPasajeros) < $cantMaxPasajeros){
                    $objEmpresa = $viaje->getObjEmpresa(); 
                    $nombreEmpresa = $objEmpresa->getEnombre();
                    $importe = $viaje->getVimporte();
                    $idviaje = $viaje->getIdviaje();
                    echo "---------------------------------------------\n
                    VIAJE  $key
                    \n Id del viaje: $idviaje 
                    \n Empresa:  $nombreEmpresa
                    \n Importe: $.$importe
                    \n
                    ---------------------------------------------\n";
                } else {
                    $idviaje = $viaje->getIdviaje();
                    echo "VIAJE $key \n No hay asientos disponibles en este viaje.\n";
                }
            }
            echo "¿En cuál viaje desea registrar pasajeros? Ingrese el número que ve al lado de VIAJE, ingrese -1 si no desea ninguno. \n";
            $opcion = trim(fgets(STDIN));
            
            if($opcion > -1 && $opcion < count($arrayViajes))
            {
                $objViaje = new Viaje();
                $viajeElegido = $arrayViajes[$opcion];
                $idViajeElegido = $viajeElegido->getIdviaje();
                
                if($objViaje->Buscar($idViajeElegido))
                {
                    echo "Viaje seleccionado.\n";
                    generarRegistroPasajeros($objViaje);
                } else {
                    echo "No se pudo seleccionar el viaje.\n";
                    echo $objViaje->getMsjBaseDatos();
                }
                
            } elseif ($opcion == 0) {
                echo "VOLVIENDO AL MENU ANTERIOR.\n";
            } else {
                echo "El número ingresado no está dentro del rango de opciones. \n";
            }
        }
    }
}

/**
 * función generarRegistroPasajeros
 * genera objetos pasajeros con la función cargarPasajero(), que los ingresa a la 
 * base de datos.
 * @param int $maxPasajerosV, $idViaje
 */

function generarRegistroPasajeros($objViaje)
{
    $objPasajero = new Pasajero();
    $maxPasajerosV = $objViaje->getVcantmaxpasajeros();
    $idviaje = $objViaje->getIdviaje();
    $arrayPasajeros = $objPasajero->Listar("idviaje=$idviaje");
    $i = count($arrayPasajeros);
    $seguir = "N";
    
    do {
        if($i >= $maxPasajerosV)
        {
            echo "**********\nYa llegó al límite de pasajeros, el pasajero no entra en este viaje.\n**********\n";
            $seguir = "N";
        }else {
            $existe = generarPasajero($objViaje);
            if($existe)
            {
                echo "¿Desea cargar otro pasajero? S/N \n";
                $seguir = trim(fgets(STDIN));
            } else {
                echo "¿Desea cargar otro pasajero? S/N \n";
                $seguir = trim(fgets(STDIN));
                $i = $i+1;
            }

        }
    }while($seguir == "S" || $seguir == "s" && $i <= $maxPasajerosV);
}


/**
 * función generarPasajero
 * genera un solo objeto pasajero y lo carga en la base de datos.
 * @return object
 */
function generarPasajero ($objViaje)
{
    $objPasajero = new Pasajero ();
    $existe = false;
    echo "Ingrese sin puntos ni espacios el numero de documento del pasajero.\n";
    $dni_pasajero = trim(fgets(STDIN));
    
    if($objPasajero->Buscar($dni_pasajero))
    {
        echo "Ese pasajero ya existe.\n";
        $existe = true;
    } else {
        echo "Ingrese el nombre del pasajero.\n";
        $nombre_pasajero = trim(fgets(STDIN));
        echo "Ingrese el apellido del pasajero.\n";
        $apellido_pasajero = trim(fgets(STDIN));
        echo "Ingrese sin espacios el número de teléfono del pasajero.\n";
        $telefono_pasajero = trim(fgets(STDIN));
        $objPasajero->cargar ($dni_pasajero, $nombre_pasajero, $apellido_pasajero, $telefono_pasajero, $objViaje);
        $resp = $objPasajero->insertar();
        if($resp)
        {
            echo "Pasajero ingresado correctamente a la base de datos.\n";
        } else {
            echo "El pasajero no pudo ser cargado a la base de datos.\n";
            echo $objPasajero->getMsjBaseDatos()."\n";
        }
    }
    return $existe;
}


/**
 * función modificarDatos
 * menu que da opciones para modificar datos, usa funciones para modificar el viaje, responsable y pasajero
 * @param object $objViaje
 */
function modificarDatos ()
{
    do{
     echo "********* MENU DE CAMBIOS *********\n
     1) Modificar o eliminar una Empresa \n
     2) Modificar o eliminar un Viaje \n
     3) Modificar o eliminar un Responsable del viaje \n
     4) Modificar o eliminar un Pasajero \n
     5) Volver al menú principal. \n";
     $opcion_modificarDatos = trim(fgets(STDIN));
     
     switch($opcion_modificarDatos)
     {
        case 1:
               echo "Ingrese el id de la empresa que desea modificar o eliminar.\n";
               $idempresa = trim(fgets(STDIN));
               modificarEmpresa($idempresa);
        break; 
        case 2: 
               echo "Ingrese el id del viaje que desea modificar o eliminar.\n";
               $idviaje = trim(fgets(STDIN));
               modificarViaje($idviaje);
        break;
        case 3:
               echo "Ingrese el numero de empleado del responsable que desea modificar o eliminar.\n";
               $numEmpleado = trim(fgets(STDIN));
               modificarResponsable($numEmpleado);
        break;
        case 4: 
               echo "Ingrese el numero de documento del pasajero a modificar o eliminar.\n";
               $documento = trim(fgets(STDIN));
               modificarPasajeros($documento);
        break;
        case 5:
        break;
        default:
           echo "Su número no entra en el rango de opciones, vuelva a intentar.\n";
        break;
    } 
    }while ($opcion_modificarDatos <> 5);
     echo "Datos modificados exitosamente.\n";
 }
 

/**
 * función modificarEmpresa
 * modifica todos los atributos del objeto empresa
 * (menos la clave primaria) o lo borra por completo
 * @param object $idempresa
 */
function modificarEmpresa($idempresa){
    $objEmpresa = new Empresa ();
    
    if($objEmpresa->buscar($idempresa))
    {
        do {
            echo "**** MODIFICAR EMPRESA ****\n
            1) Modificar el nombre de la empresa.\n
            2) Modificar la dirección de la empresa \n
            3) Borrar datos de la empresa \n
            4) Volver atrás\n";
            $opcion = trim(fgets(STDIN));
            
            switch($opcion){ 
                case 1: //modificar nombre de la empresa.
                    echo "Ingrese el nuevo nombre de la empresa.\n";
                    $new_nombre = trim(fgets(STDIN));
                    $objEmpresa->setEnombre ($new_nombre);
                    $resp = $objEmpresa->modificar();
                    
                    if($resp)
                    {
                        echo "los datos fueron actualizados correctamente. \n";
                    } else {
                        echo "No se pudo realizar el cambio.\n";
                        echo $objEmpresa->getMsjBaseDatos();
                    }
                break;
                case 2: //modificar dirección de la empresa.
                echo "Ingrese la nueva dirección de la empresa.\n";
                $new_direccion = trim(fgets(STDIN));
                $objEmpresa->setEnombre ($new_direccion);
                $resp = $objEmpresa->modificar();
                
                if($resp)
                {
                    echo "los datos fueron actualizados correctamente. \n";
                } else {
                    echo "No se pudo realizar el cambio.\n";
                    echo $objEmpresa->getMsjBaseDatos();
                }
                break;
                case 3: //eliminar datos de la empresa                 
                echo "Va a eliminar la siguiente empresa: \n". $objEmpresa->__toString(). "\nADVERTENCIA: SE BORRARAN LOS DATOS DE LOS VIAJES QUE TENGAN REGISTRADA ESTA EMPRESA\n ¿Está seguro de hacerlo? S/N.\n";
                $eliminar = trim(fgets(STDIN));
                if($eliminar == "S" || $eliminar == "s")
                {
                    if($objEmpresa -> eliminar())
                    {
                        echo "Empresa eliminada correctamente.\n";
                        $opcion = 4;
                    } else {
                        echo "No se pudieron borrar los datos.\n";
                        echo $objEmpresa -> getMsjBaseDatos();
 
                    }
                } else {
                    echo "Se canceló la eliminación de la empresa.\n";
                }
                break;
                case 4: //volver atrás
                break;
                default:
                    echo "El número ingresado no está dentro del rango de opciones, por favor vuelva a intentar.\n";
                break;
            }
        } while($opcion <> 4);
        echo "Cambios en la empresa realizados exitosamente.\n";
    } else {
        echo "El registro ingresado no existe.\n";
    }
    
}

/**
 * función modificarResponsable
 * modifica todos los atributos del objeto responsable
 * (menos la clave primaria) o lo borra por completo
 * @param int $numEmpleado
 */
function modificarResponsable($numEmpleado)
{
    $objResponsable = new ResponsableV ();
    $existe = $objResponsable -> buscar($numEmpleado);
    
    if($existe)
    {
        do {
            echo "**** MODIFICAR RESPONSABLE DEL VIAJE ****\n
            1) Modificar el número de licencia.\n
            2) Modificar el nombre del responsable del viaje \n
            3) Modificar el apellido del responsable del viaje \n
            4) Eliminar responsable del viaje.
            5) Volver atrás\n";
            $opcion = trim(fgets(STDIN));
            
            switch($opcion)
            {
            case 1: //modificar número de licencia.
                echo "Ingrese el nuevo número de licencia.\n";
                $new_numLicencia = trim(fgets(STDIN));
                $objResponsable -> setRnumerolicencia ($new_numLicencia);
                $resp = $objResponsable -> modificar();
                
                if($resp)
                {
                    echo "los datos fueron actualizados correctamente. \n";
                } else {
                    echo "No se pudo realizar el cambio.\n";
                    echo $objResponsable -> getMsjBaseDatos();
                }
            break;
            case 2: //modificar nombre del responsable.
                echo "Ingrese el nuevo nombre.\n";
                $new_nombreResponsable = trim(fgets(STDIN));
                $objResponsable -> setRnombre ($new_nombreResponsable);
                $resp = $objResponsable -> modificar();
                
                if($resp)
                {
                    echo "los datos fueron actualizados correctamente. \n";
                } else {
                    echo "No se pudo realizar el cambio.\n";
                    echo $objResponsable -> getMsjBaseDatos();
                }
            break;
            case 3: // modificar apellido del responsable. 
                echo "Ingrese el nuevo apellido.\n";
                $new_apellidoResponsable = trim(fgets(STDIN));
                $objResponsable -> setRapellido ($new_apellidoResponsable);
                $resp = $objResponsable -> modificar();
                
                if($resp)
                {
                    echo "los datos fueron actualizados correctamente. \n";
                } else {
                    echo "No se pudo realizar el cambio.\n";
                    echo $objResponsable -> getMsjBaseDatos();
                }
            break;
            case 4: //eliminar registro del responsable
                echo "Va a eliminar al siguiente responsable: \n". $objResponsable->__toString(). "\nADVERTENCIA: SE BORRARAN LOS DATOS DE LOS VIAJES EN LOS QUE ESTÉ REGISTRADO\n ¿Está seguro de hacerlo? S/N.\n";
                $eliminar = trim(fgets(STDIN));
                
                if($eliminar == "S" || $eliminar == "s")
                {
                    if ($objResponsable -> eliminar())
                    {
                        echo "Responsable eliminado correctamente.\n";
                        $opcion = 5;
                    } else {
                        echo "No se pudieron borrar los datos.\n";
                        echo $objResponsable -> getMsjBaseDatos();
                    }
                } else {
                    echo "Se canceló la eliminación del responsable.\n";
                }

            break;
            case 5: //volver atrás
            break;
            default:
                echo "El número ingresado no está dentro del rango de opciones, por favor vuelva a intentar.\n";
            break;
        }
        } while ($opcion <> 5);
        echo "Cambios en el responsable del viaje realizados exitosamente.\n";
    } else {
        echo "El registro ingresado no existe.\n";
    }
    
}


/**
 * función modificarViaje
 * permite modificar todos los atributos del objeto viaje (menos la clave primaria)
 * o eliminarlo por completo
 * @param int $idviaje
 */
function modificarViaje($idviaje)
{
    $objViaje = new Viaje();
    
    if ($objViaje->buscar ($idviaje))
    {
        do{
            echo "**** MODIFICAR VIAJE ****\n
            1) Modificar destino del viaje \n
            2) Modificar el máximo de pasajeros del viaje \n
            3) Modificar empresa registrada \n
            4) Modificar responsable a cargo \n 
            5) Modificar importe del viaje \n
            6) Eliminar viaje \n
            7) Volver atrás.\n";
            $opcion_modificarViaje = trim(fgets(STDIN));
            switch($opcion_modificarViaje){
                case 1: //modificar destino del viaje.
                    do{
                        echo "Ingrese el nuevo destino del viaje.\n";
                        $destino_nuevo = trim(fgets(STDIN));
                        $existe = existeDestino($destino_nuevo);
                        if ($existe)
                        {
                            echo "El destino ya existe, inténtelo de nuevo.\n";
                        }
                    } while($existe);
                    $objViaje -> setVdestino ($destino_nuevo);
                    $resp = $objViaje -> modificar();
                    
                    if($resp){
                        echo "Cambio realizado correctamente.\n";
                    } else {
                        echo "No se pudo realizar el cambio.\n";
                        echo $objViaje -> getMsjBaseDatos();
                    }
                break;
                case 2:
                    //modificar el máximo de pasajeros.
                    echo "Escriba el nuevo máximo de pasajeros. \n";
                    $maximoPasajeros_nuevo = trim(fgets(STDIN));
                    if($maximoPasajeros_nuevo >= count($objViaje -> getArrayObjPasajeros()))
                    {
                        $objViaje -> setVcantmaxpasajeros ($maximoPasajeros_nuevo);
                        $resp = $objViaje -> modificar();
                        if($resp)
                        {
                            echo "Cambio realizado correctamente.\n";
                        } else {
                            echo "No se pudo realizar el cambio.\n";
                            echo $objViaje -> getMsjBaseDatos();
                        }
                    } else {
                        echo "El número ingresado es igual al máximo actual o menor a la cantidad de pasajeros registrados.\n";
                    }

                break; 
                case 3: //Modificar empresa registrada 
                    echo "Ingrese el id de la nueva empresa.\n";
                    $id_nuevo = trim(fgets(STDIN));
                    $objEmpresa = new Empresa();
                    
                    if($objEmpresa->Buscar($id_nuevo))
                    {
                        $objViaje->setObjEmpresa ($objEmpresa);
                        $resp = $objViaje->modificar();
                        if($resp)
                        {
                            echo "Cambio realizado correctamente.\n";
                        } else {
                            echo "No se pudo realizar el cambio.\n";
                            echo $objViaje->getMsjBaseDatos();
                        }
                    } else {
                        echo "La empresa ingresada no existe.\n";
                    }
                   
                break;
                case 4: // Modificar responsable a cargo
                    echo "Ingrese el número de empleado del nuevo responsable.\n";
                    $numEmpleado_nuevo =trim(fgets(STDIN));
                    $objResponsable = new ResponsableV();
                    
                    if($objResponsable->Buscar($numEmpleado_nuevo))
                    {
                        $objViaje->setObjResponsable ($objResponsable);
                        $resp = $objViaje->modificar();
                        if($resp)
                        {
                            echo "Cambio realizado correctamente.\n";
                        } else {
                            echo "No se pudo realizar el cambio.\n";
                            echo $objViaje -> getMsjBaseDatos();
                        }
                    } else {
                        echo "El responsable ingresado no existe.\n";
                    }
         
                break;
                case 5: //Modificar importe del viaje
                    echo "Ingrese el nuevo importe del viaje.\n";
                    $importe_nuevo = trim(fgets(STDIN));
                    $objViaje->setVimporte ($importe_nuevo);
                    $resp = $objViaje->modificar($idviaje);
                    
                    if($resp)
                    {
                        echo "Cambio realizado correctamente.\n";
                    } else {
                        echo "No se pudo realizar el cambio.\n";
                        echo $objViaje->getMsjBaseDatos();
                    }
                break;
                case 6: 
                    $idviaje = $objViaje->getIdviaje();
                    echo "Va a eliminar al siguiente viaje: \n {$objViaje->__toString()}\n\nADVERTENCIA: SE BORRARAN LOS DATOS DE LOS PASAJEROS REGISTRADOS EN ESTE VIAJE\n ¿Está seguro de hacerlo? S/N.\n";
                    $eliminar = trim(fgets(STDIN));
                    
                    if($eliminar == "S" || $eliminar == "s")
                    {
                        if($objViaje->eliminar())
                        {
                            echo "Viaje eliminado correctamente.\n";
                            $opcion_modificarViaje=7;
                        } else {
                            echo "No se pudieron borrar los datos.\n";
                            echo $objViaje -> getMsjBaseDatos();
                        }
                    } else {
                        echo "Se canceló la eliminación del viaje.\n";
                    }
                break;
                case 7: //volver atrás
                break;
                default: 
                echo "El número ingresado no está dentro del rango de opciones, por favor, vuelva a intentar.\n";
                break;
            }
        } while($opcion_modificarViaje <> 7);
        echo "Datos del VIAJE modificados correctamente.\n";
    } else {
        echo "El registro ingresado no existe.\n";
    }
} 


/**
 * función modificarPasajeros
 * permite modificar todos los atributos de un objeto pasajero 
 * (menos la clave primaria) o eliminarlo por completo
 * @param int $pdocumento
 */
function modificarPasajeros($pdocumento)
{
    $objPasajero = new Pasajero();
    if($objPasajero->Buscar($pdocumento))
    {
        do {
            echo "* MODIFICAR PASAJERO *\n
            1) Modificar nombre \n
            2) Modificar apellido \n
            3) Modificar número de teléfono \n
            4) Cambiar de viaje \n
            5) Eliminar pasajero.\n
            6) Volver atrás \n";
            $opcion = trim(fgets(STDIN));
            
            switch ($opcion)
            {
                case 1: //modificar nombre
                    echo "Ingrese el nombre nuevo.\n";
                    $nuevo_nombrePasajero = trim(fgets(STDIN));
                    $objPasajero->setPnombre ($nuevo_nombrePasajero);
                    $resp = $objPasajero->modificar();
                    
                    if($resp)
                    {
                        echo "Datos actualizados correctamente.\n";
                    } else {
                        echo "No se pudo realizar el cambio.\n";
                        echo $objPasajero->getMsjBaseDatos();
                    }
                break;
                case 2://modificar apellido
                    echo "Ingrese el apellido nuevo.\n";
                    $nuevo_apellidoPasajero = trim(fgets(STDIN));
                    $objPasajero->setPapellido($nuevo_apellidoPasajero);
                    $resp = $objPasajero->modificar();
                        if($resp)
                        {
                            echo "Datos actualizados correctamente.\n";
                        } else {
                            echo "No se pudo realizar el cambio.\n";
                            echo $objPasajero->getMsjBaseDatos();
                        }
                break;
                case 3: //modificar número de teléfono
                    echo "Ingrese el número de teléfono nuevo.\n";
                    $nuevo_telefonoPasajero = trim(fgets(STDIN));
                    $objPasajero->setPtelefono ($nuevo_telefonoPasajero);
                    
                    if($objPasajero->modificar())
                    {
                        echo "Datos actualizados correctamente.\n";
                    } else {
                        echo "No se pudo realizar el cambio.\n";
                        echo $objPasajero->getMsjBaseDatos();
                    }
                break;
                case 4: //modificar viaje.
                    echo "Ingrese el id del nuevo viaje.\n";
                    $nuevo_idviaje = trim(fgets(STDIN));
                    $viaje = new Viaje();
                    
                    if($viaje->Buscar($nuevo_idviaje))
                    {
                        $objPasajero->setObjViaje($viaje);
                        if ($objPasajero->modificar())
                        {
                            echo "Datos actulizados correctamente.\n";
                        } else {
                            echo "No se pudo realizar el cambio.\n";
                            echo $objPasajero -> getMsjBaseDatos();
                        }
                    } else {
                        echo "El viaje ingresado no existe.\n";
                    }
                case 5: //eliminar pasajero.
                    echo "Va a eliminar al siguiente pasajero: \n". $objPasajero -> __toString(). "\n ¿Está seguro de hacerlo? S/N.\n";
                    $eliminar = trim(fgets(STDIN));
                    if($eliminar == "S" || $eliminar == "s")
                    {
                        if($objPasajero->eliminar())
                        {
                            echo "Pasajero eliminado correctamente.\n";
                            $opcion = 6;
                        } else {
                            echo "No se pudieron borrar los datos.\n";
                            echo $objPasajero -> getMsjBaseDatos();
                        }
                    } else {
                        echo "Se canceló la eliminación del pasajero.\n";
                    }
                break;
                case 6://volver atrás.
                break;
                default: 
                    echo "El número ingresado no está dentro del rango de opciones, por favor inténtelo de nuevo. \n";
                break;
                }
        
        } while($opcion <> 6);
    } else {
        echo "El pasajero ingresado no existe en la base de datos.\n";
    }
}


function mostrar(){
    do {
        echo "********* MOSTRAR DATOS *********\n
        Ingrese cuáles son los datos que desea ver:\n
        1) Listado de empresas.\n
        2) Listado de responsables.\n
        3) Listado de viajes.\n
        4) Listado de pasajeros.\n
        5) Volver atrás. \n";
        $opcion = trim(fgets(STDIN));
        if($opcion>=1 && $opcion<=5)
        {
            switch ($opcion){
                case 1://Listado de  empresas.
                    $objEmpresa = new Empresa;
                    $listaEmpresas = $objEmpresa->listar();
                    foreach($listaEmpresas as $key => $empresa) 
                    {
                        echo "\n-----------------------------------------\n{$empresa->__toString()}";
                    }
                break;
                case 2://Listado de responsables.
                    $objResponsable = new ResponsableV();
                    $listaResponsables = $objResponsable->listar();
                    foreach($listaResponsables as $key => $responsable)
                    {
                        echo "\n-----------------------------------------\n{$responsable->__toString()}";
                    }
                break;
                case 3://Listado de viajes
                    $objViaje = new Viaje();
                    $listaViajes = $objViaje->listar();
                    foreach($listaViajes as $key => $viaje)
                    {
                        echo "\n-----------------------------------------\n{$viaje->__toString()}";
                    }
                break;
                case 4://Ver listado de pasajeros.
                    $objPasajero = new Pasajero();
                    $listaPasajeros = $objPasajero->listar();
                    foreach($listaPasajeros as $key=>$pasajero)
                    {
                        $str = $pasajero->__toString();
                        echo "\n-----------------------------------------\n $str";
                    }
                break;
                case 5://Volver atrás
                break;
            }
        }
       
        
    } while($opcion <> 5);
    
}

// PROGRAMA PRINCIPAL
do {
    echo "\nBienvenidos al meú de gestión de viajes\n";
    echo "\n******* MENU PRINCIPAL *******\n
    1) Cargar datos\n
    2) Modificar datos \n
    3) Ver datos \n
    4) Salir. \n";
    $opcionMenu = trim(fgets(STDIN));
    if($opcionMenu >= 1 && $opcionMenu <= 4)
    {
        switch ($opcionMenu)
        {
            case 1:
                cargarDatos();
            break;
            case 2:
                modificarDatos();
            break;
            case 3:
                mostrar();
            break;
            case 4:
            break;
        }
    } else {
        echo "El número ingresado no está dentro del rango de opciones, por favor inténtelo de nuevo.\n";
    }
} while($opcionMenu <> 4);
echo "¡Hasta pronto!\n";

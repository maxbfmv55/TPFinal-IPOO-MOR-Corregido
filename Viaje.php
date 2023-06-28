<?php
class Viaje{
    //Atributos de la clase Viaje
    private $idviaje; //clave primaria
    private $vdestino;
    private $vcantmaxpasajeros;
    private $objEmpresa; //clave foránea, referencia a la clase Empresa
    private $objResponsable; //clave foránea, referencia 
    private $vimporte;
    private $arrayObjPasajeros;
    private $msjBaseDatos;

    //se implementa el método constructor de la clase Viaje
    public function __construct()
    {
        $this -> idviaje = 0;
        $this -> vdestino = "";
        $this -> vcantmaxpasajeros = 0;
        $this -> vimporte = 0;
        $this -> arrayObjPasajeros = array();
        $this -> msjBaseDatos = '';
    }
    
    //se implementan los métodos de acceso

    //get y set del ID del viaje
    public function getIdviaje()
    {
        return $this->idviaje;
    }

    public function setIdviaje($idviaje)
    {
        $this->idviaje = $idviaje;
    }

    //get y set del destino del viaje
    public function getVdestino()
    {
        return $this->vdestino;
    }
 
    public function setVdestino($vdestino)
    {
        $this->vdestino = $vdestino;
    }

    //get y set de la cantidad maxima de pasajeros
    public function getVcantmaxpasajeros()
    {
        return $this->vcantmaxpasajeros;
    }
 
    public function setVcantmaxpasajeros($vcantmaxpasajeros)
    {
        $this->vcantmaxpasajeros = $vcantmaxpasajeros;
    }

    //get y set de la colección de empresas
    public function getObjEmpresa()
    {
        return $this->objEmpresa;
    }

    public function setObjEmpresa($empresa)
    {
        $this->objEmpresa = $empresa;
    }

    //get y set de la colección de responsables
    public function getObjResponsable()
    {
        return $this->objResponsable;
    }

    public function setObjResponsable($responsable)
    {
        $this->objResponsable = $responsable;
    }

    //get y set del Importe del viaje
    public function getVimporte()
    {
        return $this->vimporte;
    }

    public function setVimporte($vimporte)
    {
        $this->vimporte = $vimporte;
    }

    //get y set de la colección de los pasajeros
    public function getArrayObjPasajeros()
    {
        return $this->arrayObjPasajeros;
    }
 
    public function setArrayObjPasajeros($arrayObjPasajeros)
    {
        $this->arrayObjPasajeros = $arrayObjPasajeros;
    }
    
    //get y set del manejo de los mensajes
    public function getMsjBaseDatos()
    {
        return $this->msjBaseDatos;
    }

    public function setMsjBaseDatos($msjBaseDatos)
    {
        $this->msjBaseDatos = $msjBaseDatos;
    }

    //se implementa el método __toString

    public function __toString()
    {
        $responsable = $this->getObjResponsable();
        $infoResponsable = $responsable->__toString();
        $infoViaje= "**** VIAJE  {$this->getIdviaje()} ****\n 
        Destino: {$this->getVdestino()} \nCantidad máxima de pasajeros: {$this->getVcantmaxpasajeros()}
        \nImporte: $  {$this->getVimporte()} \nResponsable del viaje:\n $infoResponsable
        \n** INFO PASAJEROS ** {$this->infoPasajero()}  " ; 
        return $infoViaje;
    }

    /*
		- Método que recibe el ID del viaje, el destino, la cantidad maxima
        de pasajeros, la empresa, el responsable, y el importe del viaje
        y los asigna a las propiedades correspondientes.
	*/
    public function cargar ( $idviaje,$vdestino, $maxPasajeros, $empresa, $responsable, $vimporte){  
        $this->setIdviaje ($idviaje); 
        $this->setVdestino ($vdestino);
        $this->setVcantmaxpasajeros ($maxPasajeros);
        $this->setObjEmpresa ($empresa);
        $this->setObjResponsable ($responsable);
        $this->setVimporte ($vimporte);       
    }

    /**
	 * Recupera los datos de un viaje por ID 
     * @param int $idviaje
	 * @return true $resp en caso de encontrar los datos, false en caso contrario 
	 */		
    public function Buscar($idviaje){ 
		$base=new BaseDatos(); 
        $resp = false; 
		$consultaViaje="Select * from viaje where idviaje=".$idviaje;
		
        if($base->Iniciar()){ 
			if($base->Ejecutar($consultaViaje)){
				if($row2=$base->Registro()){					 
				    /*$this->setIdviaje($idviaje);
					$this->setVdestino($row2["vdestino"]);
					$this->setVcantmaxpasajeros($row2["vcantmaxpasajeros"]);
					$idempresa=($row2["idempresa"]);
                    $numEmpleado=($row2["rnumeroempleado"]);
                    $this->setVimporte($row2["vimporte"]);
                    $empresa = new Empresa();
                    $empresa->Buscar($idempresa);
                    $this->setObjEmpresa($empresa);
                    $responsable = new ResponsableV();
                    $responsable->Buscar($numEmpleado);
                    $this->setObjResponsable($responsable); */
                   
                    $empresa = new Empresa();
                    $empresa->Buscar($row2['idempresa']);
                    
                    $responsable = new ResponsableV();
                    $responsable->Buscar($row2['rnumeroempleado']);
        
                    $this->cargar($row2['idviaje'],$row2['vdestino'], $row2['vcantmaxpasajeros'], $empresa, $responsable, $row2['vimporte']);
                    $resp= true;
				}				
		 	}	else {
		 			$this->setMsjBaseDatos($base->getError());		
			}
		 }	else {
		 		$this->setMsjBaseDatos($base->getError());		 	
		 }		
		 return $resp;
	}	
    
    /** 
	 * Lista los viajes, se le puede pasar una condición para filtrar la lista.
	 * @param string $condicion
	 * @return array $arregloViajes
	 */
	public function listar($condicion="")
    {
	    $arregloViajes = null;
		$base=new BaseDatos(); 
		$consultaViaje="Select * from viaje ";
		
        if ($condicion!="")
        {
		    $consultaViaje=$consultaViaje.' where '.$condicion;
		}
		$consultaViaje=$consultaViaje." order by vdestino ";

        if($base->Iniciar())
        {
			if($base->Ejecutar($consultaViaje))
            {				
				$arregloViajes= array();
				while($row2=$base->Registro())
                {
					$idviaje=$row2['idviaje'];
                    $viaje=new Viaje();
                    $viaje->Buscar($idviaje);
					array_push($arregloViajes,$viaje);
				}							
		 	}	else {
		 			$this->setMsjBaseDatos($base->getError());		 		
			}
		 }	else {
		 		$this->setMsjBaseDatos($base->getError());		 	
		 }	
		 return $arregloViajes;
	}	

    /**  
	 * Metodo para ingresar una nueva tupla a la tabla viaje.
	 * @return boolean $resp
	 */
    public function insertar()
    {
		$base=new BaseDatos();
		$resp= false;
        $empresa = $this->getObjEmpresa();
        $idempresa = $empresa->getIdempresa();
        $responsable = $this->getObjResponsable();
        $numEmpleado = $responsable->getRnumeroempleado();  
		$consultaInsertar="INSERT INTO viaje(vdestino, vcantmaxpasajeros, idempresa, rnumeroempleado, vimporte) 
				VALUES ('{$this->getVdestino()}',{$this->getVcantmaxpasajeros()},
                $idempresa,$numEmpleado,{$this ->getVimporte()})";
		
		if($base->Iniciar())
        { 
			if($idviaje = $base->devuelveIDInsercion($consultaInsertar))
            {
                $this->setIdviaje($idviaje);
			    $resp=  true;
			} else {
					$this->setMsjBaseDatos($base->getError()); 					
			}
		} else {
				$this->setMsjBaseDatos($base->getError());			
		}
		return $resp;
	}
	
    /** 
	 * Metodo para modificar una tupla de la tabla viaje.
	 * @return boolean $resp
	 */
	public function modificar()
    { 
	    $resp =false; 
	    $base=new BaseDatos();
        $empresa = $this->getObjEmpresa();
        $idempresa = $empresa->getIdempresa();
        $responsable = $this->getObjResponsable();
        $numEmpleado = $responsable->getRnumeroempleado();
		$consultaModifica="UPDATE viaje SET vdestino='{$this->getVdestino()}',
        vcantmaxpasajeros={$this->getVcantmaxpasajeros()} ,idempresa=$idempresa,
        rnumeroempleado=$numEmpleado,vimporte={$this->getVimporte()} WHERE idviaje={$this->getIdviaje()}";
		
        if($base->Iniciar())
        {
			if($base->Ejecutar($consultaModifica))
            {
			    $resp=  true;
			} else{
				$this->setMsjBaseDatos($base->getError());				
			}
		} else{
				$this->setMsjBaseDatos($base->getError());			
		}
		return $resp;
	}

    /*
      - Metodo para eliminar una tupla de la tabla viaje.
      - @return boolean $resp
    */
    public function eliminar()
    {
		$base=new BaseDatos();
		$resp=false;

		if($base->Iniciar())
        {
				$consultaBorra="DELETE FROM viaje WHERE idviaje=".$this->getIdviaje();
				if($base->Ejecutar($consultaBorra))
                {
				    $resp=  true;
				} else{
						$this->setMsjBaseDatos($base->getError());					
				}
		} else{
				$this->setMsjBaseDatos($base->getError());			
		}
		return $resp; 
	}





    
    //Metodos Extras

    //metodo que concatena la información acerca de los pasajeros de un viaje
    public function infoPasajero()
    {
        $listaPasajeros = " ";
        $pasajeros = $this -> arregloPasajeros ();
        
        for ($i = 0; $i < count($pasajeros); $i++)
        {
           $listaPasajeros = $listaPasajeros. "\n".$pasajeros[$i]->__toString();
        }
        return $listaPasajeros;
    } 

    //método para extraer la información de los pasajeros y la devuelve como array
    public function arregloPasajeros()
    {
        $pasajero = new Pasajero();
        $condicion = "idviaje={$this->getIdviaje()}";
        $arrayPasajeros = $pasajero -> listar($condicion);
        $this -> setArrayObjPasajeros($arrayPasajeros);
        return $arrayPasajeros;
    }

}

<?php

class Empresa {
	//Atributos de la clase Empresa
    private $idempresa; //clave primaria
    private $enombre;
    private $edireccion;
    private $msjBaseDatos;
	private $arrayObjViajes;
    
    //se implementa el constructor de la clase Empresa

    public function __construct()
    {
        $this -> idempresa = "";
        $this -> enombre = "";
        $this -> edireccion = "";
    }

    //se implementan los métodos de acceso

	//get y set de ID de la empresa
    public function getIdempresa()
    {
        return $this->idempresa;
    }

    public function setIdempresa($idempresa)
    {
        $this->idempresa = $idempresa;
    }

	//get y set del nombre de la empresa
    public function getEnombre()
    {
        return $this->enombre;
    }

    public function setEnombre($enombre)
    {
        $this->enombre = $enombre;
    }

	//get y set de dirección de la empresa
    public function getEdireccion()
    {
        return $this->edireccion;
    }

    public function setEdireccion($edireccion)
    {
        $this->edireccion = $edireccion;
    }

	//get y set del manejo de mensajes
    public function getMsjBaseDatos()
    {
        return $this->msjBaseDatos;
    }

    public function setMsjBaseDatos($msjBaseDatos)
    {
        $this->msjBaseDatos = $msjBaseDatos;
    }

	//get y set de la colección de viajes
	public function getArrayObjViajes()
	{
		return $this->arrayObjViajes;
	}

	public function setArrayObjViajes($arrayObjViajes)
	{
		$this->arrayObjViajes = $arrayObjViajes;
	}

	/*
		- Método que recibe el nombre y la dirección de la empresa y los 
		asigna a las propiedades correspondientes.
	*/

	public function cargar ( $enombre, $edireccion)
	{
        $this->setEnombre ($enombre);
        $this->setEdireccion ($edireccion);
    }
    
	public function __toString()
    {
        $idempresa = $this->getIdempresa();
        $enombre = $this->getEnombre();
        $edireccion = $this->getEdireccion();

        $infoEmpresa = "Empresa $enombre \nID: $idempresa \nDirección: $edireccion \nViajes registrados: \n{$this->infoViajes()}";
        return $infoEmpresa; 
    }
    
	/**
	 * Método que busca y recupera los datos de una empresa en la base de datos 
	 * según su ID. Establece los valores correspondientes en las propiedades de la empresa.
	 * @param int $idempresa
	 * @return true en caso de encontrar los datos, false en caso contrario 
	*/

    public function Buscar($idempresa)
	{ 
		$base = new BaseDatos();  
		$consultaEmpresa = "Select * from empresa where idempresa=".$idempresa;
		$resp = false; 
		
		if($base->Iniciar())
		{ 
			if($base->Ejecutar($consultaEmpresa))
			{
				if($row2 = $base->Registro())
				{					 
				    /*$this->setIdempresa($idempresa);
					$this->setEnombre($row2["enombre"]);
					$this->setEdireccion($row2["edireccion"]);*/
					$this->cargar($row2['idempresa'], $row2['enombre'], $row2['edireccion']);
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
		 * Lista a los pasajeros, se le puede pasar una condición para filtrar la lista.
		 * @param string $condicion
		 * @return array $arregloEmpresas
 	*/ 

	public function listar($condicion="")
	{
	    $arregloEmpresas = null;
		$base = new BaseDatos(); 
		$consultaEmpresa = "Select * from empresa ";
		
		if ($condicion != "")
		{
		    $consultaEmpresa=$consultaEmpresa.' where '.$condicion;
		}
		$consultaEmpresa.=" order by enombre ";

		if($base->Iniciar())
		{
			if($base->Ejecutar($consultaEmpresa))
			{				
				$arregloEmpresas = array();
				while($row2 = $base->Registro())
				{					
					$idempresa = $row2['idempresa'];
					$empresa = new Empresa();
					$empresa->Buscar($idempresa);
					array_push($arregloEmpresas,$empresa);
				}				
		 	} else {
		 			$this->setMsjBaseDatos($base->getError());
			}
		 } else {
		 		$this->setMsjBaseDatos($base->getError());
		 }	
		 return $arregloEmpresas;
	}	

	/**  
		* Metodo para ingresar una nueva tupla a la tabla Empresa
    	* @return boolean $resp
    */

    public function insertar()
	{
		$base=new BaseDatos();
		$resp= false;
		$consultaInsertar="INSERT INTO empresa(enombre, edireccion) VALUES ('{$this->getEnombre()}','{$this->getEdireccion()}')";//idempresa, ".$this->getIdempresa().",'
		
		if($base->Iniciar()){ 
			if($id = $base->devuelveIDInsercion($consultaInsertar))
			{	
				$this->setIdempresa($id);
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
      * Metodo para modificar una tupla de la tabla de Empresa
      * @return boolean $resp
    */
	public function modificar()
	{
	    $resp =false; 
	    $base=new BaseDatos();
		$consultaModifica="UPDATE empresa SET enombre='".$this->getEnombre()."',edireccion='".$this->getEdireccion()."' WHERE idempresa=". $this->getIdempresa();
		
		if($base->Iniciar())
		{
			if($base->Ejecutar($consultaModifica))
			{
			    $resp=  true;
			}else{
				$this->setMsjBaseDatos($base->getError());		
			}
		} else{
				$this->setMsjBaseDatos($base->getError());
			
		}
		return $resp;
	}

	/*
      - Metodo para eliminar una tupla de la tabla de Empresa
      - @return boolean $resp
    */
    public function eliminar()
	{
		$base=new BaseDatos();
		$resp=false;
		
		if($base->Iniciar())
		{
				$consultaBorra="DELETE FROM empresa WHERE idempresa=".$this->getIdempresa();
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




	// Metodos Extras //

	/* 
		- Método que retorna un array de objetos de la clase "Viaje" asociados 
		a la empresa actual. Utiliza el método listar() de la clase "Viaje" 
		con una condición para obtener los viajes específicos de la empresa.
	*/

	public function arregloViajes ()
	{
		$objViaje = new Viaje (); 
		$arrayViajes = $objViaje->listar("idempresa='{$this->getIdempresa()}'");
		$this -> setArrayObjViajes($arrayViajes);
		return $arrayViajes;
	}

	/* 
		- Método que devuelve una representación en forma de cadena de los 
		viajes asociados a la empresa actual. Utiliza el método __toString() 
		de la clase "Viaje" para obtener la información de cada viaje.
	*/

	public function infoViajes ()
	{
		$arrayViajes = $this->arregloViajes();
		$infoViajes = "";
		if (count($arrayViajes)<=0)
		{
			$infoViajes = $infoViajes. "No hay viajes registrados en esta empresa.\n";
		} else {
			for ($i=0; $i<count($arrayViajes); $i++)
			{ 
				$infoViajes=$infoViajes."---------------------------------------------------------------\n".$arrayViajes[$i]->__toString(). "\n";	
			}
		}
		return $infoViajes;
	}

}
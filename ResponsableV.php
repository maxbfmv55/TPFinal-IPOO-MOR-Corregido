<?php

class ResponsableV 
{
	//Atributos de la clase Responsable
    private $rnumeroempleado; //clave primaria
    private $rnumerolicencia;
    private $rnombre;
    private $rapellido;
    private $msjBaseDatos;

    public function __construct()
    {
        $this -> rnumeroempleado = "";
        $this -> rnumerolicencia = "";
        $this -> rnombre = "";
        $this -> rapellido = "";
    }
 
    //se implementan los métodos de acceso

	//get y set del número de responsable
    public function getRnumeroempleado()
    {
        return $this->rnumeroempleado;
    }

    public function setRnumeroempleado($rnumeroempleado)
    {
        $this->rnumeroempleado = $rnumeroempleado;
    }

	//get y set del numero de licencia del responsable
    public function getRnumerolicencia()
    {
        return $this->rnumerolicencia;
    }

    public function setRnumerolicencia($rnumerolicencia)
    {
        $this->rnumerolicencia = $rnumerolicencia;
    }

	//get y set del nombre del responsable
    public function getRnombre()
    {
        return $this->rnombre;
    }

    public function setRnombre($rnombre)
    {
        $this->rnombre = $rnombre;
    }
	
	//get y set del apellido del responsable
    public function getRapellido()
    {
        return $this->rapellido;
    }

    public function setRapellido($rapellido)
    {
        $this->rapellido = $rapellido;
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

    public function __toString()
    {
        $numEmpleado = $this->getRnumeroempleado();
        $numLic = $this ->getRnumerolicencia();
        $nombre = $this -> getRnombre();
        $apellido = $this -> getRapellido();
        $infoResponsable = "\nNombre:  $apellido \nApellido: $nombre
        \nNúmero de empleado:  $numEmpleado\nNúmero de licencia:  $numLic \n";
        return $infoResponsable;
    }

	/*
		- Método que recibe el número de empleado, número de licencia,
		nombre y appelido del responsable y los asigna a las 
		propiedades correspondientes.
	*/
	public function cargar ( $numEmpleado, $numLic, $nombre, $apellido){
	$this -> setRnumeroempleado ($numEmpleado);
	$this -> setRnumerolicencia  ($numLic);
	$this -> setRnombre($nombre);
	$this -> setRapellido ($apellido);
 }
    /**
	 * Recupera los datos de un responsable por medio del número de empleado
	 * @param int $numEmpleado
	 * @return true $resp en caso de encontrar los datos, false en caso contrario 
	 */		
    public function Buscar($numEmpleado)
	{	 
		$base=new BaseDatos();  
		$consultaResponsable="Select * from responsable where rnumeroempleado=".$numEmpleado;
		$resp= false; 
		if($base->Iniciar())
		{ 
			if($base->Ejecutar($consultaResponsable))
			{
				if($row2 = $base->Registro())
				{					 
				    /*$this->setRnumeroempleado($numEmpleado);
					$this->setRnumerolicencia($row2["rnumerolicencia"]);
					$this->setRnombre($row2["rnombre"]);
					$this->setRapellido($row2["rapellido"]);*/
					$this->cargar($row2['rnumeroempleado'], $row2['rnumerolicencia'], $row2['rnombre'], $row2['rapellido']);
					$resp= true;
				}				
			
		 	} else {
		 			$this->setMsjBaseDatos($base->getError());	 		
			}
		 } else {
		 		$this->setMsjBaseDatos($base->getError());
		 	
		 }		
		 return $resp;
	}	
    
	/** 
	 * Lista al responsable, se le puede pasar una condición para filtrar la lista.
	 * @param string $condicion
	 * @return array $arregloResponsables
	 */
	public function listar($condicion="")
	{
	    $arregloResponsables = null;
		$base=new BaseDatos(); 
		$consultaResponsable="Select * from responsable ";
		
		if ($condicion!="")
		{
		    $consultaResponsable=$consultaResponsable.' where '.$condicion;
		}
		$consultaResponsable.=" order by rapellido ";

		if($base->Iniciar())
		{
			if($base->Ejecutar($consultaResponsable))
			{				
				$arregloResponsables= array();
				while($row2=$base->Registro()){
					$numEmpleado=$row2['rnumeroempleado'];
					$responsable=new ResponsableV();
					$responsable->Buscar($numEmpleado);
					array_push($arregloResponsables,$responsable);			
				}				
		 	} else {
		 			$this->setMsjBaseDatos($base->getError());
			}
		 } else {
		 		$this->setMsjBaseDatos($base->getError());
		 }	
		 return $arregloResponsables;
	}	

	/**  
	 * Metodo para ingresar una nueva tupla a la tabla responsable.
	 * @return boolean $resp
	 */
    public function insertar()
	{
		$base=new BaseDatos();
		$resp= false;
		$consultaInsertar="INSERT INTO responsable( rnumerolicencia, rnombre, rapellido) 
				VALUES ('{$this->getRnumerolicencia()}','{$this->getRnombre()}','{$this->getRapellido()}')";
		
		if($base->Iniciar())
		{ 
			if($numeroEmpleado = $base->devuelveIDInsercion($consultaInsertar))
			{
				$this->setRnumeroempleado($numeroEmpleado);
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
	 * Metodo para modificar una tupla de la tabla del responsable.
	 * @return boolean $resp
	 */
	public function modificar()
	{
	    $resp =false; 
	    $base=new BaseDatos();
		$consultaModifica="UPDATE responsable SET rapellido='".$this->getRapellido()."',rnombre='".$this->getRnombre()."'
                           ,rnumerolicencia='".$this->getRnumerolicencia()."' WHERE rnumeroempleado=". $this->getRnumeroempleado();
	
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
      - Metodo para eliminar una tupla de la tabla del responsable.
      - @return boolean $resp
    */
    public function eliminar()
	{
		$base=new BaseDatos();
		$resp=false;
		
		if($base->Iniciar())
		{
				$consultaBorra="DELETE FROM responsable WHERE rnumeroempleado=".$this->getRnumeroempleado();
				if($base->Ejecutar($consultaBorra))
				{
				    $resp=  true;
				} else {
						$this->setMsjBaseDatos($base->getError());				
				}
		} else {
				$this->setMsjBaseDatos($base->getError());		
		}
		return $resp; 
	}



}
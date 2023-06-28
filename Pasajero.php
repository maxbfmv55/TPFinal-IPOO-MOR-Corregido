<?php
class Pasajero
{
	//Atributos de la clase Pasajero
	private $pdocumento; //clave primaria
	private $pnombre;
	private $papellido;
	private $ptelefono;
	private $objViaje;
	private $msjBaseDatos;

	public function __construct()
	{
		$this->pdocumento = "";
		$this->pnombre = "";
		$this->papellido = "";
		$this->ptelefono = "";
	}

	//se implementan los métodos de acceso

	//get y set del Documento del pasajero
	public function getPdocumento()
	{
		return $this->pdocumento;
	}

	public function setPdocumento($pdocumento)
	{
		$this->pdocumento = $pdocumento;
	}

	//get y set del Nombre del pasajero
	public function getPnombre()
	{
		return $this->pnombre;
	}

	public function setPnombre($pnombre)
	{
		$this->pnombre = $pnombre;
	}

	//get y set del Apellido del pasajero
	public function getPapellido()
	{
		return $this->papellido;
	}

	public function setPapellido($papellido)
	{
		$this->papellido = $papellido;
	}

	//get y set del telefono del pasajero
	public function getPtelefono()
	{
		return $this->ptelefono;
	}

	public function setPtelefono($ptelefono)
	{
		$this->ptelefono = $ptelefono;
	}

	//Get y set de la colección del viaje

	public function getObjViaje()
	{
		return $this->objViaje;
	}

	public function setObjViaje($objViaje)
	{
		$this->objViaje = $objViaje;
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

	public function __toString()
	{
		$objViaje = $this->getObjViaje();
		$idViaje = $objViaje->getIdviaje();
		$infoPasajero = "\n----------------------------------------\nNro documento: {$this->getPdocumento()}\nNombre: {$this->getPnombre()}
        \nApellido:  {$this->getPapellido()} \nTeléfono: {$this->getPtelefono()}\nId del viaje: $idViaje\n";
		return $infoPasajero;
	}

	/*
		- Método que recibe el documento, nombre, apellido, telefono y
		la colección de viajes y los asigna a las propiedades correspondientes.
	*/

	public function cargar($dni, $nombre, $apellido, $telefono, $objViaje)
	{
		$this->setPdocumento($dni);
		$this->setPnombre($nombre);
		$this->setPapellido($apellido);
		$this->setPtelefono($telefono);;
		$this->setObjViaje($objViaje);
	}

	/**
	 * Recupera los datos de un pasajero por dni
	 * @param int $dni
	 * @return true $resp en caso de encontrar los datos, false en caso contrario 
	 */
	public function Buscar($dni)
	{
		$base = new BaseDatos();
		$consultaPasajero = "Select * from pasajero where pdocumento=" . $dni;
		$resp = false;
		if ($base->Iniciar()) {
			if ($base->Ejecutar($consultaPasajero)) 
			{
				if ($row2 = $base->Registro()) 
				{
					/*$this->setPdocumento($dni);
					$this->setPnombre($row2["pnombre"]);
					$this->setPapellido($row2["papellido"]);
					$this->setPtelefono($row2["ptelefono"]);
					$idviaje = ($row2["idviaje"]);
					$resp = true;
					$objViaje = new Viaje();
					$objViaje->Buscar($idviaje);
					$this->setObjViaje($objViaje);*/
					$viaje = new Viaje();
					$viaje->Buscar($row2['idviaje']);

					$this->cargar($row2['pdocumento'], $row2['pnombre'], $row2['papellido'], $row2['ptelefono'], $viaje);
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
	 * Lista a los pasajeros, se le puede pasar una condición para filtrar la lista.
	 * @param string $condicion
	 * @return array $arregloPasajeros
	 */
	public function listar($condicion = "")
	{
		$arregloPasajeros = null;
		$base = new BaseDatos();
		$consultaPasajero = "Select * from pasajero ";

		if ($condicion != "") 
		{
			$consultaPasajero = $consultaPasajero . ' where ' . $condicion;
		}
		$consultaPasajero .= " order by papellido ";

		if ($base->Iniciar()) 
		{
			if ($base->Ejecutar($consultaPasajero)) 
			{
				$arregloPasajeros = array();
				while ($row2 = $base->Registro()) 
				{
					$NroDoc = $row2['pdocumento'];
					$pasajero = new Pasajero();
					$pasajero->Buscar($NroDoc);
					array_push($arregloPasajeros, $pasajero);
				}
			} else {
				$this->setMsjBaseDatos($base->getError());
			}
		} else {
			$this->setMsjBaseDatos($base->getError());
		}
		return $arregloPasajeros;
	}

	/**  
	 * Metodo para ingresar una nueva tupla a la tabla pasajero
	 * @return boolean $resp
	 * NOTA: Agregue la varible $idviaje para resolver un error que me tiraba el programa 
	 * si intentaba recuperar el id directamente desde la clase viaje.
	 */

	public function insertar()
	{
		$base = new BaseDatos();
		$resp = false;
		$objViaje = $this->getObjViaje();
		$idviaje = $objViaje->getIdviaje();
		$consultaInsertar = "INSERT INTO pasajero	VALUES ({$this->getPdocumento()},'{$this->getPnombre()}','{$this->getPapellido()}',{$this->getPtelefono()}, $idviaje)";

		if ($base->Iniciar()) 
		{
			if ($base->Ejecutar($consultaInsertar)) 
			{
				$resp =  true;
			} else {
				$this->setMsjBaseDatos($base->getError());
			}
		} else {
			$this->setMsjBaseDatos($base->getError());
		}
		return $resp;
	}

	/** 
	 * Metodo para modificar una tupla de la tabla del pasajero
	 * @return boolean $resp
	 */
	public function modificar()
	{
		$resp = false;
		$base = new BaseDatos();
		$viaje = $this->getObjViaje();
		$idViaje = $viaje->getIdviaje();
		$consultaModifica = "UPDATE pasajero SET papellido='{$this->getPapellido()}',pnombre='{$this->getPnombre()}'
                           ,ptelefono={$this->getPtelefono()}, idviaje=$idViaje WHERE pdocumento={$this->getPdocumento()}";

		if ($base->Iniciar()) 
		{
			if ($base->Ejecutar($consultaModifica)) 
			{
				$resp =  true;
			} else {
				$this->setMsjBaseDatos($base->getError());
			}
		} else {
			$this->setMsjBaseDatos($base->getError());
		}
		return $resp;
	}

	/*
      - Metodo para eliminar una tupla de la tabla de pasajeros
      - @return boolean $resp
    */
	public function eliminar()
	{
		$base = new BaseDatos();
		$resp = false;
		if ($base->Iniciar()) {
			$consultaBorra = "DELETE FROM pasajero WHERE pdocumento=" . $this->getPdocumento();

			if ($base->Ejecutar($consultaBorra)) 
			{
				$resp = true;
			} else {
				$this->setMsjBaseDatos($base->getError());
			}
		} else {
			$this->setMsjBaseDatos($base->getError());
		}
		return $resp;
	}
}

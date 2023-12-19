<?php

class Medico
{
	private $IdMedico;
	private $nombre;
	private $especialidad;
	private $IdUsuario;
	private $foto;

	private $con;
	private $PATH_usuarios = "../imagenes/sellos/";
	function __construct($cn)
	{
		$this->con = $cn;
	}


	//*********************** 3.1 METODO update_consulta() **************************************************	

	public function update_medico()
	{
		$this->IdMedico = $_POST['IdMedico'];
		$this->nombre = $_POST['nombre'];
		$this->especialidad = $_POST['especialidadCMB'];
		$this->IdUsuario = $_POST['IdUsuarioCMB'];

		$this->foto = $this->_get_name_file($_FILES['foto']['name'], 12);

		$path = $this->PATH_usuarios . $this->foto;

		if (!move_uploaded_file($_FILES['foto']['tmp_name'], $path)) {
			$mensaje = "Cargar la imagen";
			echo $this->_message_error($mensaje);
			exit;
		}

		$sql = "UPDATE medicos SET nombre='$this->nombre',
									especialidad='$this->especialidad',
									idUsuario='$this->IdUsuario',
									foto='$this->foto'
				WHERE IdMedico=$this->IdMedico;";
		//echo $sql;
		//exit;
		if ($this->con->query($sql)) {
			echo $this->_message_ok("modificó");
		} else {
			echo $this->_message_error("al modificar");
		}
	}


	//*********************** 3.2 METODO save_consulta() **************************************************	

	public function save_medico()
	{
		$this->nombre = $_POST['nombre'];
		$this->especialidad = $_POST['especialidadCMB'];
		$this->IdUsuario = $_POST['IdUsuarioCMB'];

		$this->foto = $this->_get_name_file($_FILES['foto']['name'], 12);

		$path = $this->PATH_usuarios . $this->foto;

		//exit;
		if (!move_uploaded_file($_FILES['foto']['tmp_name'], $path)) {
			$mensaje = "Cargar la imagen";
			echo $this->_message_error($mensaje);
			exit;
		}

		/*PRUEBA DE ESCRITORIO*/ /*MANIPULACION DE LA FOTO*/
		/*echo "<br> FILES <br>";
		echo "<pre>";
		print_r($_FILES);
		echo "</pre>";*/

		$sql = "INSERT INTO medicos VALUES(NULL,
											'$this->nombre',
											'$this->especialidad',
											'$this->IdUsuario',
											'$this->foto');";
		//echo $sql;
		//exit;
		if ($this->con->query($sql)) {
			echo $this->_message_ok("guardó");
		} else {
			echo $this->_message_error("guardar");
		}
	}


	//*********************** 3.3 METODO _get_name_File() **************************************************	

	private function _get_name_file($nombre_original, $tamanio)
	{
		$tmp = explode(".", $nombre_original); //DivIdMedicoo el nombre por el punto y guardo en un arreglo
		$numElm = count($tmp); //cuento el número de elemetos del arreglo
		$ext = $tmp[$numElm - 1]; //Extraer la última posición del arreglo.
		$cadena = "";
		for ($i = 1; $i <= $tamanio; $i++) {
			$c = rand(65, 122);
			if (($c >= 91) && ($c <= 96)) {
				$c = NULL;
				$i--;
			} else {
				$cadena .= chr($c);
			}
		}
		return $cadena . "." . $ext;
	}


	//*************************************** PARTE I ************************************************************


	/*Aquí se agregó el parámetro:  $defecto*/
	private function _get_combo_db($tabla, $valor, $etiqueta, $nombre, $defecto)
	{
		$html = '<select name="' . $nombre . '">';
		$sql = "SELECT $valor,$etiqueta FROM $tabla;";
		$res = $this->con->query($sql);
		while ($row = $res->fetch_assoc()) {
			//ImpResultQuery($row);
			$html .= ($defecto == $row[$valor]) ? '<option value="' . $row[$valor] . '" selected>' . $row[$etiqueta] . '</option>' . "\n" : '<option value="' . $row[$valor] . '">' . $row[$etiqueta] . '</option>' . "\n";
		}
		$html .= '</select>';
		return $html;
	}

	/*Aquí se agregó el parámetro:  $defecto*/
	private function _get_combo_anio($nombre, $anio_inicial, $defecto)
	{
		$html = '<select name="' . $nombre . '">';
		$anio_actual = date('Y');
		for ($i = $anio_inicial; $i <= $anio_actual; $i++) {
			$html .= ($i == $defecto) ? '<option value="' . $i . '" selected>' . $i . '</option>' . "\n" : '<option value="' . $i . '">' . $i . '</option>' . "\n";
		}
		$html .= '</select>';
		return $html;
	}

	/*Aquí se agregó el parámetro:  $defecto*/
	private function _get_radio($arreglo, $nombre, $defecto)
	{

		$html = '
		<table border=0 align="left">';

		//CODIGO NECESARIO EN CASO QUE EL USUARIO NO SE ESCOJA UNA OPCION

		foreach ($arreglo as $etiqueta) {
			$html .= '
			<tr>
				<td>' . $etiqueta . '</td>
				<td>';

			if ($defecto == NULL) {
				// OPCION PARA GRABAR UN NUEVO CONSULTA (IdMedico=0)
				$html .= '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '" checked/></td>';
			} else {
				// OPCION PARA MODIFICAR UN CONSULTA EXISTENTE
				$html .= ($defecto == $etiqueta) ? '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '" checked/></td>' : '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '"/></td>';
			}

			$html .= '</tr>';
		}
		$html .= '
		</table>';
		return $html;
	}


	//************************************* PARTE II ****************************************************	

	public function get_form($IdMedico = NULL)
	{

		if ($IdMedico == NULL) {
			$this->nombre = NULL;
			$this->especialidad = NULL;
			$this->IdUsuario = NULL;
			$this->foto = NULL;
			
			$flag = NULL;
			$op = "newmedico";
		} else {
			$sql = "SELECT me.IdMedico, me.Nombre as nombre, e.Descripcion as especialidad, u.Nombre as IdUsuario, me.Foto as foto  
					FROM medicos me, especialidades e, usuarios u 
					WHERE me.Especialidad = e.IdEsp AND u.IdUsuario = me.IdUsuario AND IdMedico=$IdMedico;";
			$res = $this->con->query($sql);
			$row = $res->fetch_assoc();

			$num = $res->num_rows;
			if ($num == 0) {
				$mensaje = "tratar de actualizar el consulta con IdMedico= " . $IdMedico;
				echo $this->_message_error($mensaje);
			} else {

				// ***** TUPLA ENCONTRADA *****
				/*echo "<br>TUPLA <br>";
				echo "<pre>";
				print_r($row);
				echo "</pre>";*/

				$this->nombre = $row['nombre'];
				$this->especialidad = $row['especialidad'];
				$this->IdUsuario = $row['IdUsuario'];
				$this->foto = $row['foto'];
				
				$flag = "disabled";
				$op = "updatemedico";
			}
		}

		$especialidad = [
			"Cardiología",
			"Pediatría",
			"Dermatología",
			"Ginecología",
			"Oftalmología"
		];

		$html = '
<form name="medico" method="POST" action="unido.php" enctype="multipart/form-data">

    <input type="hidden" name="IdMedico" value="' . $IdMedico  . '">
    <input type="hidden" name="op" value="' . $op  . '">

    <div class="container">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="thead-dark">
                    <th style="color:white" colspan="2">REGISTRO MEDICOS</th>
                </thead>

                <tbody>
                    <tr>
                        <td>Nombre:</td>
                        <td><input type="text" class="form-control" name="nombre" value="' . $this->nombre . '" required></td>
                    </tr>
                    <tr>
                        <td>Especialidad:</td>
                        <td>' . $this->_get_radio($especialidad, "especialidadCMB", $this->especialidad) . '</td>
                    </tr>
                    <tr>
                        <td>Usuario:</td>
                        <td>' . $this->_get_combo_db("usuarios", "IdUsuario", "Nombre", "IdUsuarioCMB", $this->IdUsuario) . '</td>
                    </tr>
                    <tr>
                        <td>Foto:</td>
                        <td><input type="file" class="form-control-file" name="foto" ' . $flag . '></td>
                    </tr>
                    <tr>
                        <th colspan="2"><input type="submit" name="Guardar" value="GUARDAR" class="btn btn-info col-12"></th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</form>';
		return $html;
	}



	public function get_list($rol)
	{
		$d_new = "newmedico/0";
		$d_new_final = base64_encode($d_new);
		$html = '
<div class="container">
    <div class="tab" role="tabpanel">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="thead-dark">
                    <th colspan="8" class="text-center" style="color: white;">DATOS DE LOS MEDICOS</th>
                </thead>
                <tr>
                    <th colspan="8" class="text-center"><a class="btn btn-info" href="unido.php?d=' . $d_new_final . '" class="align-middle">Nuevo</a></th>
                </tr>
                <tr>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Especialidad</th>
                    <th colspan="3" class="text-center">Acciones</th>
                </tr>';
		$sql = "SELECT me.IdMedico, me.Nombre as nombre, e.Descripcion as especialidad, u.Nombre as IdUsuario, me.Foto as foto  
		FROM medicos me, especialidades e, usuarios u 
		WHERE me.Especialidad = e.IdEsp AND u.IdUsuario = me.IdUsuario;";

		$res = $this->con->query($sql);
		// Sin codificar <td><a href="unido.php?op=del&IdMedico=' . $row['IdMedico'] . '">Borrar</a></td>
		while ($row = $res->fetch_assoc()) {
			$d_del = "delmedico/" . $row['IdMedico'];
			$d_del_final = base64_encode($d_del);
			$d_act = "actmedico/" . $row['IdMedico'];
			$d_act_final = base64_encode($d_act);
			$d_det = "detmedico/" . $row['IdMedico'];
			$d_det_final = base64_encode($d_det);
			$html .= '
				<tr>
					<td>' . $row['nombre'] . '</td>
					<td>' . $row['especialidad'] . '</td>';
					if($rol == 1){
						$html .= '
					<td><a class="btn btn-primary " href="unido.php?d=' . $d_del_final . '">Borrar</a></td>
					<td><a class="btn btn-primary " href="unido.php?d=' . $d_act_final . '">Actualizar</a></td>
					<td><a class="btn btn-primary" href="unido.php?d=' . $d_det_final . '">Detalle</a></td>';
					}elseif($rol == 2){
						$html .= '
						<td><a class="btn btn-primary " href="unido.php?d=' . $d_act_final . '">Actualizar</a></td>
						<td><a class="btn btn-primary" href="unido.php?d=' . $d_det_final . '">Detalle</a></td>';
					}elseif($rol == 3){
						$html .= '
					<td><a class="btn btn-primary" href="unido.php?d=' . $d_det_final . '">Detalle</a></td>';
					}elseif($rol == 4){
						$html .= '';
					}else{
						$html .= '';
					}
					$html .='</tr>';
		}
		$html .= '  
	
		</table>
</div>

</div>';

		return $html;
	}


	public function get_detail_medico($IdMedico)
	{
		$sql = "SELECT me.IdMedico, me.Nombre as nombre, e.Descripcion as especialidad, u.Nombre as IdUsuario, me.Foto as foto  
		FROM medicos me, especialidades e, usuarios u 
		WHERE me.Especialidad = e.IdEsp AND u.IdUsuario = me.IdUsuario AND IdMedico=$IdMedico;";
		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();

		$num = $res->num_rows;

		//Si es que no existiese ningun registro debe desplegar un mensaje 
		//$mensaje = "tratar de eliminar el consulta con IdMedico= ".$IdMedico;
		//echo $this->_message_error($mensaje);
		//y no debe desplegarse la tablas

		if ($num == 0) {
			$mensaje = "tratar de editar el consulta con IdMedico= " . $IdMedico;
			echo $this->_message_error($mensaje);
		} else {
			$html = '
<div class="container">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead class="thead-dark">
                <th colspan="4" class="text-center" style="color:white;">MEDICOS</th>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">Nombre: </td>
                    <td class="text-center">' . $row['nombre'] . '</td>
                </tr>
                <tr>
                    <td class="text-center">Especialidad: </td>
                    <td class="text-center">' . $row['especialidad'] . '</td>
                </tr>
                <tr>
                    <td class="text-center">Usuario: </td>
                    <td class="text-center">' . $row['IdUsuario'] . '</td>
                </tr>
                
                <tr>
                    <th colspan="2" class="text-center"><a href="unido.php">Regresar</a></th>
                </tr>
            </tbody>
        </table>
    </div>
</div>';

						$html .= '
									</tbody>
								</table>
							</div>
						</div>';
				
							return $html;
		}
	}


	public function delete_medico($IdMedico)
	{
		$sql = "DELETE FROM medicos WHERE IdMedico=$IdMedico;";
		if ($this->con->query($sql)) {
			echo $this->_message_ok("ELIMINÓ");
		} else {
			echo $this->_message_error("eliminar");
		}
	}

	//*************************************************************************

	private function _get_especialidad_medico($nombre)
	{
		$sql = "SELECT Especialidad FROM medicos WHERE nombre = $nombre";
		$result = $this->con->query($sql);

		if ($result) {
			$row = $result->fetch_assoc();
			return $row['especialidad'];
		}

		return null;
	}

	//*************************************************************************	

	private function _message_error($tipo)
	{
		$html = '
		<table border="0" align="center">
			<tr>
				<th>Error al ' . $tipo . '. Favor contactar a .................... </th>
			</tr>
			<tr>
				<th><a href="unido.php">Regresar</a></th>
			</tr>
		</table>';
		return $html;
	}


	private function _message_ok($tipo)
	{
		$html = '
		<table border="0" align="center">
			<tr>
				<th>El registro se  ' . $tipo . ' correctamente</th>
			</tr>
			<tr>
				<th><a href="unido.php">Regresar</a></th>
			</tr>
		</table>';
		return $html;
	}

	//****************************************************************************	

} // FIN SCRPIT

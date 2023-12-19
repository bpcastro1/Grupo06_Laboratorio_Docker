<?php
class Receta
{
	private $IdReceta;
	private $consulta;
	private $medicamento;
	private $cantidad;
	private $con;

	function __construct($cn)
	{
		$this->con = $cn;
	}


	//*********************** 3.1 METODO update_consulta() **************************************************	

	public function update_receta()
	{
		$this->IdReceta = $_POST['IdReceta'];
		$this->consulta = $_POST['consulta'];
		$this->medicamento = $_POST['medicamentoCMB'];

		$this->cantidad = $_POST['cantidad'];

		$sql = "UPDATE recetas SET idConsulta='$this->consulta',
									idMedicamento=$this->medicamento,
									cantidad='$this->cantidad'
				WHERE IdReceta=$this->IdReceta;";
		//echo $sql;
		//exit;
		if ($this->con->query($sql)) {
			echo $this->_message_ok("modificó");
		} else {
			echo $this->_message_error("al modificar");
		}
	}


	//*********************** 3.2 METODO save_consulta() **************************************************	

	public function save_receta()
	{

		$this->consulta = $_POST['consulta'];
		$this->medicamento = $_POST['medicamentoCMB'];
		$this->cantidad = $_POST['cantidad'];

		/*PRUEBA DE ESCRITORIO*/ /*MANIPULACION DE LA FOTO*/
		/*echo "<br> FILES <br>";
		echo "<pre>";
		print_r($_FILES);
		echo "</pre>";*/

		//exit;

		$sql = "INSERT INTO recetas VALUES(NULL,
											'$this->consulta',
											'$this->medicamento',
											'$this->cantidad');";
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
		$tmp = explode(".", $nombre_original); //DivIdRecetao el nombre por el punto y guardo en un arreglo
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
				// OPCION PARA GRABAR UN NUEVO CONSULTA (IdReceta=0)
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

	public function get_form($IdReceta = NULL)
	{

		if ($IdReceta == NULL) {
			$this->consulta = NULL;
			$this->medicamento = NULL;
			$this->cantidad = NULL;

			$flag = NULL;
			$op = "newreceta";
		} else {
			$sql = "SELECT * FROM recetas WHERE IdReceta=$IdReceta;";
			$res = $this->con->query($sql);
			$row = $res->fetch_assoc();

			$num = $res->num_rows;
			if ($num == 0) {
				$mensaje = "tratar de actualizar el consulta con IdReceta= " . $IdReceta;
				echo $this->_message_error($mensaje);
			} else {

				// ***** TUPLA ENCONTRADA *****
				/*echo "<br>TUPLA <br>";
				echo "<pre>";
				print_r($row);
				echo "</pre>";*/

				$this->consulta = $row['IdConsulta'];
				$this->medicamento = $row['IdMedicamento'];
				$this->cantidad = $row['Cantidad'];

				$flag = "disabled";
				$op = "updatereceta";
			}
		}

		$html = '
<form name="receta" method="POST" action="unido.php" enctype="multipart/form-data">

    <input type="hidden" name="IdReceta" value="' . $IdReceta  . '">
    <input type="hidden" name="op" value="' . $op  . '">

    <div class="container">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="thead-dark">
                    <th style="color:white" colspan="2">DATOS DE LA RECETA</th>
                </thead>

                <tbody>
                    <tr>
                        <td>Consulta:</td>
                        <td>' . $this->_get_combo_db("consultas", "idConsulta", "Diagnostico", "consulta", $this->consulta) . '</td>
                    </tr>
                    <tr>
                        <td>Medicamento:</td>
                        <td>' . $this->_get_combo_db("medicamentos", "IdMedicamento", "Nombre", "medicamentoCMB", $this->medicamento) . '</td>
                    </tr>
                    <tr>
                        <td>Cantidad:</td>
                        <td><input type="number" class="form-control" name="cantidad" value="' . $this->cantidad . '" required></td>
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
		$d_new = "newreceta/0";
		$d_new_final = base64_encode($d_new);
		$html = '
<div class="container">
    <div class="tab" role="tabpanel">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="thead-dark">
                    <th colspan="8" class="text-center" style="color: white;">DATOS DE LA RECETA</th>
                </thead>
                <tr>
                    <th colspan="8" class="text-center"><a class="btn btn-info" href="unido.php?d=' . $d_new_final . '" class="align-middle">Nuevo</a></th>
                </tr>
                <tr>
                    <th class="text-center">Consulta</th>
                    <th class="text-center">Medicamento</th>
                    <th class="text-center">Cantidad</th>
                    <th colspan="3" class="text-center">Acciones</th>
                </tr>';
		$sql = "SELECT r.IdReceta, c.Diagnostico as consultas, m.Nombre as medicamentos, r.cantidad  
				FROM recetas r, consultas c, medicamentos m 
				WHERE r.idConsulta=c.idConsulta AND r.idMedicamento=m.idMedicamento;";

		$res = $this->con->query($sql);
		// Sin codificar <td><a href="unido.php?op=del&IdReceta=' . $row['IdReceta'] . '">Borrar</a></td>
		while ($row = $res->fetch_assoc()) {
			$d_del = "delreceta/" . $row['IdReceta'];
			$d_del_final = base64_encode($d_del);
			$d_act = "actreceta/" . $row['IdReceta'];
			$d_act_final = base64_encode($d_act);
			$d_det = "detreceta/" . $row['IdReceta'];
			$d_det_final = base64_encode($d_det);
			$html .= '
				<tr>
					<td>' . $row['consultas'] . '</td>
					<td>' . $row['medicamentos'] . '</td>
					<td>' . $row['cantidad'] . '</td>';
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


	public function get_detail_receta($IdReceta)
	{
		$sql = "SELECT r.IdReceta, c.Diagnostico as consultas, m.Nombre as medicamentos, r.cantidad  
				FROM recetas r, consultas c, medicamentos m 
				WHERE r.idConsulta=c.idConsulta 
				AND r.idMedicamento=m.idMedicamento 
				AND IdReceta=$IdReceta;";
		
		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();

		$num = $res->num_rows;

		//Si es que no existiese ningun registro debe desplegar un mensaje 
		//$mensaje = "tratar de eliminar el consulta con IdReceta= ".$IdReceta;
		//echo $this->_message_error($mensaje);
		//y no debe desplegarse la tablas

		if ($num == 0) {
			$mensaje = "tratar de editar el consulta con IdReceta= " . $IdReceta;
			echo $this->_message_error($mensaje);
		} else {
			$html = '
<div class="container">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead class="thead-dark">
                <th colspan="4" class="text-center" style="color:white;">CONSULTAS MEDICAS</th>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">Consulta: </td>
                    <td class="text-center">' . $row['consultas'] . '</td>
                </tr>
                <tr>
                    <td class="text-center">Medicamento: </td>
                    <td class="text-center">' . $row['medicamentos'] . '</td>
                </tr>
                <tr>
                    <td class="text-center">Cantidad: </td>
                    <td class="text-center">' . $row['cantidad'] . '</td>
                </tr>
                <tr>
                    <th colspan="2" class="text-center"><a class="btn btn-info col-12" href="unido.php">Regresar</a></th>
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


	public function delete_receta($IdReceta)
	{
		$sql = "DELETE FROM recetas WHERE IdReceta=$IdReceta;";
		if ($this->con->query($sql)) {
			echo $this->_message_ok("ELIMINÓ");
		} else {
			echo $this->_message_error("eliminar");
		}
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

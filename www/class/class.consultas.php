<?php
class Consulta
{
	private $IdConsulta;
	private $idPaciente;
	private $idMedico;
	private $fechaConsulta;
	private $HI;
	private $HF;
	private $diagnostico;
	private $foto;
	private $con;

	function __construct($cn)
	{
		$this->con = $cn;
	}


	//*********************** 3.1 METODO update_consulta() **************************************************	

	public function update_consultas()
	{
		$this->IdConsulta = $_POST['IdConsulta'];
		$this->idPaciente = $_POST['idPacienteCMB'];
		$this->idMedico = $_POST['idMedicoCMB'];
		$this->fechaConsulta = $_POST['fechaConsulta'];
		$this->HI = $_POST['HI'];
		$this->HF = $_POST['HF'];

		// Verificar la regla de negocio para pacientes femeninas y ginecólogo
		$generoPaciente = $this->_get_genero_paciente($this->idPaciente);
		if ($generoPaciente == 'Femenino' && $this->idMedico != '4') {
			echo $this->_message_error("Las pacientes femeninas solo pueden tomar consulta con la Dra. Lopez de ginecología");
			exit;
		}

		// Verificar la regla de negocio para pacientes masculinos y ginecólogo
		if ($generoPaciente == 'Masculino' && $this->idMedico == '4') {
			echo $this->_message_error("Los pacientes masculinos no pueden tomar consulta con el Ginecólogo");
			exit;
		}

		$this->diagnostico = $_POST['diagnostico'];

		$sql = "UPDATE consultas SET idPaciente='$this->idPaciente',
									idMedico=$this->idMedico,
									fechaConsulta='$this->fechaConsulta',
									HI='$this->HI',
									HF='$this->HF',
									diagnostico='$this->diagnostico'
				WHERE IdConsulta=$this->IdConsulta;";
		//echo $sql;
		//exit;
		if ($this->con->query($sql)) {
			echo $this->_message_ok("modificó");
		} else {
			echo $this->_message_error("al modificar");
		}
	}


	//*********************** 3.2 METODO save_consulta() **************************************************	

	public function save_consultas()
	{

		$this->fechaConsulta = $_POST['fechaConsulta'];
		$this->diagnostico = $_POST['diagnostico'];
		$this->HI = $_POST['HI'];
		$this->HF = $_POST['HF'];


		$this->idPaciente = $_POST['idPacienteCMB'];
		$this->idMedico = $_POST['idMedicoCMB'];

		// Verificar la regla de negocio para pacientes femeninas y ginecólogo
		$generoPaciente = $this->_get_genero_paciente($this->idPaciente);
		if ($generoPaciente == 'Femenino' && $this->idMedico != '4') {
			echo $this->_message_error("Las pacientes femeninas solo pueden tomar consulta con la Dra. Lopez de ginecología");
			exit;
		}

		// Verificar la regla de negocio para pacientes masculinos y ginecólogo
		if ($generoPaciente == 'Masculino' && $this->idMedico == '4') {
			echo $this->_message_error("Los pacientes masculinos no pueden tomar consulta con el Ginecólogo");
			exit;
		}

		/*PRUEBA DE ESCRITORIO*/ /*MANIPULACION DE LA FOTO*/
		/*echo "<br> FILES <br>";
		echo "<pre>";
		print_r($_FILES);
		echo "</pre>";*/
 
		$sql = "INSERT INTO consultas VALUES(NULL,
											'$this->idPaciente',
											'$this->idMedico',
											'$this->fechaConsulta',
											'$this->HI',
											'$this->HF',
											'$this->diagnostico');";
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
		$tmp = explode(".", $nombre_original); //DivIdConsultao el nombre por el punto y guardo en un arreglo
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
				// OPCION PARA GRABAR UN NUEVO CONSULTA (IdConsulta=0)
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

	public function get_form($IdConsulta = NULL)
	{

		if ($IdConsulta == NULL) {
			$this->idPaciente = NULL;
			$this->idMedico = NULL;
			$this->fechaConsulta = NULL;
			$this->HI = NULL;
			$this->HF = NULL;
			$this->diagnostico = NULL;

			$flag = NULL;
			$op = "new";
		} else {
			$sql = "SELECT * FROM consultas WHERE IdConsulta=$IdConsulta;";
			$res = $this->con->query($sql);
			$row = $res->fetch_assoc();

			$num = $res->num_rows;
			if ($num == 0) {
				$mensaje = "tratar de actualizar el consulta con IdConsulta= " . $IdConsulta;
				echo $this->_message_error($mensaje);
			} else {

				// ***** TUPLA ENCONTRADA *****
				/*echo "<br>TUPLA <br>";
				echo "<pre>";
				print_r($row);
				echo "</pre>";*/

				$this->idPaciente = $row['IdPaciente'];
				$this->idMedico = $row['IdMedico'];
				$this->fechaConsulta = $row['FechaConsulta'];
				$this->HI = $row['HI'];
				$this->HF = $row['HF'];
				$this->diagnostico = $row['Diagnostico'];

				$flag = "enabled";
				$op = "update";
			}
		}

		$html = '
<form name="consultas" method="POST" action="unido.php" enctype="multipart/form-data">

    <input type="hidden" name="IdConsulta" value="' . $IdConsulta  . '">
    <input type="hidden" name="op" value="' . $op  . '">

    <div class="container">
        <div class="table-responsive">
            <table class="table table-striped mb-0">

                <thead class="thead-dark">
                    <th style="color:white" colspan="2">DATOS DE LA CONSULTA</th>
                </thead>

                <tbody>
                    <tr>
                        <td>Paciente:</td>
                        <td>' . $this->_get_combo_db("pacientes", "idPaciente", "Nombre", "idPacienteCMB", $this->idPaciente) . '</td>
                    </tr>
                    <tr>
                        <td>Médico:</td>
                        <td>' . $this->_get_combo_db("medicos", "idMedico", "Nombre", "idMedicoCMB", $this->idMedico) . '</td>
                    </tr>
                    <tr>
                        <td>Fecha Consulta:</td>
                        <td><input type="date" size="15" name="fechaConsulta" value="' . $this->fechaConsulta . '" required></td>
                    </tr>
                    <tr>
                        <td>Hora de Inicio:</td>
                        <td><input type="time" size="15" name="HI" value="' . $this->HI . '" required></td>
                    </tr>
                    <tr>
                        <td>Hora Final:</td>
                        <td><input type="time" size="15" name="HF" value="' . $this->HF . '" required></td>
                    </tr>
                    <tr>
                        <td>Diagnóstico:</td>
                        <td><input type="text" size="15" name="diagnostico" value="' . $this->diagnostico . '" required></td>
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
		$d_new = "new/0";
		$d_new_final = base64_encode($d_new);
		$html = '
	
		<div class="container">
    <div class="tab" role="tabpanel">
        <div class="table-responsive">
            <table class="table table-striped mb-0">

                <thead class="thead-dark">
                    <th colspan="9" class="text-center" style="color: white;">DATOS DE LA CONSULTA</th>
                </thead>
                <tr>
                    <th colspan="9" class="text-center"><a class="btn btn-info" href="unido.php?d=' . $d_new_final . '" class="align-middle">Nuevo</a></th>
                </tr>
                <tr>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Fecha Consulta</th>
                    <th>Hora de Inicio</th>
                    <th>Hora Final</th>
                    <th>Diagnóstico</th>
                    <th colspan="3">Acciones</th>
                </tr>';
		$sql = "SELECT c.IdConsulta, p.Nombre as pacientes, m.Nombre as medicos, c.fechaConsulta, c.HI, c.HF, c.diagnostico FROM consultas c, pacientes p, medicos m WHERE c.idPaciente=p.idPaciente AND c.idMedico=m.idMedico;";

		$res = $this->con->query($sql);
		// Sin codificar <td><a href="unido.php?op=del&IdConsulta=' . $row['IdConsulta'] . '">Borrar</a></td>
		while ($row = $res->fetch_assoc()) {
			$d_del = "del/" . $row['IdConsulta'];
			$d_del_final = base64_encode($d_del);
			$d_act = "act/" . $row['IdConsulta'];
			$d_act_final = base64_encode($d_act);
			$d_det = "det/" . $row['IdConsulta'];
			$d_det_final = base64_encode($d_det);
			$html .= '
				<tr>
					<td>' . $row['pacientes'] . '</td>
					<td>' . $row['medicos'] . '</td>
					<td>' . $row['fechaConsulta'] . '</td>
					<td>' . $row['HI'] . '</td>
					<td>' . $row['HF'] . '</td>
					<td>' . $row['diagnostico'] . '</td>';
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


	public function get_detail_consultas($IdConsulta)
	{
		$sql = "SELECT c.IdConsulta, p.Nombre as pacientes, p.Edad as pacienteEdad, p.Genero as pacientesGenero, m.Nombre as medicos, m.Especialidad as medicosEspecialidad, c.fechaConsulta, c.HI, c.HF, c.diagnostico, p.foto  
				FROM consultas c, pacientes p, medicos m 
				WHERE c.idPaciente=p.idPaciente AND c.idMedico=m.idMedico AND IdConsulta=$IdConsulta;";
		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();

		$num = $res->num_rows;

		//Si es que no existiese ningun registro debe desplegar un mensaje 
		//$mensaje = "tratar de eliminar el consulta con IdConsulta= ".$IdConsulta;
		//echo $this->_message_error($mensaje);
		//y no debe desplegarse la tablas

		if ($num == 0) {
			$mensaje = "tratar de editar el consulta con IdConsulta= " . $IdConsulta;
			echo $this->_message_error($mensaje);
		} else {
			$html = '
			<div class="container">
			<div class="table-responsive">
				<table class="table table-striped mb-0">
					<thead class="thead-dark">
						<th colspan="4" class="text-center" style="color:white;">Consultas medicas </th>
					</thead>
            <tbody>
                <tr>
                    <td>IdPaciente: </td>
                    <td>' . $row['pacientes'] . '</td>
                </tr>
                <tr>
                    <td>Edad: </td>
                    <td>' . $row['pacienteEdad'] . '</td>
                </tr>
                <tr>
                    <td>Género: </td>
                    <td>' . $row['pacientesGenero'] . '</td>
                </tr>
                <tr>
                    <td>IdMédico: </td>
                    <td>' . $row['medicos'] . '</td>
                </tr>
                <tr>
                    <td>Especialidad del Médico: </td>
                    <td>' . $row['medicosEspecialidad'] . '</td>
                </tr>
                <tr>
                    <td>Fecha Consulta: </td>
                    <td>' . $row['fechaConsulta'] . '</td>
                </tr>
                <tr>
                    <td>Hora de Inicio: </td>
                    <td>' . $row['HI'] . '</td>
                </tr>
                <tr>
                    <td>Hora Final: </td>
                    <td>' . $row['HF'] . '</td>
                </tr>
                <tr>
                    <td>Diagnóstico: </td>
                    <td>' . $row['diagnostico'] . '</td>
                </tr>
                
                <tr>
                    <th colspan="2"><a class="btn btn-info col-12" href="unido.php">Regresar</a></th>
                </tr>';

        $html .= '
                    </tbody>
                </table>
            </div>
        </div>';

			return $html;
		}
	}


	public function delete_consultas($IdConsulta)
	{
		$sql = "DELETE FROM consultas WHERE IdConsulta=$IdConsulta;";
		if ($this->con->query($sql)) {
			echo $this->_message_ok("ELIMINÓ");
		} else {
			echo $this->_message_error("eliminar");
		}
	}

	//*************************************************************************

	private function _get_genero_paciente($idPaciente)
	{
		$sql = "SELECT genero FROM pacientes WHERE idPaciente = $idPaciente";
		$result = $this->con->query($sql);

		if ($result) {
			$row = $result->fetch_assoc();
			return $row['genero'];
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

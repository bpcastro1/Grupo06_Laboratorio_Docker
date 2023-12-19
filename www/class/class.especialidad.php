<?php
class Especialidad{
	private $idEsp;
	private $descripcion;
	private $dias;
	private $franja_HI;
	private $franja_HF;
    private $con;
	
	function __construct($cn){
		$this->con = $cn;
/* 		echo "EJECUTANDOSE EL CONSTRUCTOR ESPECIALIDAD<br><br>";
 */	}
	
		
	/*********************** 3.1 METODO update_especialidad() ***********************/	
	public function update_especialidad(){
		$this->idEsp = $_POST['idEsp'];
		$this->descripcion = $_POST['descripcion'];
		$this->franja_HI = $_POST['franja_HI'];
        $this->franja_HF = $_POST['franja_HF'];

        $diasSeleccionados = isset($_POST['diasHabilesRBT']) ? $_POST['diasHabilesRBT'] : array();
        $dias = implode(',', $diasSeleccionados);
        
		$sql = "UPDATE especialidades SET Descripcion='$this->descripcion',
                                        Dias='$dias',
                                        Franja_HI='$this->franja_HI',
                                        Franja_HF='$this->franja_HF'
				WHERE IdEsp=$this->idEsp;";
			echo $sql;
			//exit;
			if($this->con->query($sql)){
				echo $this->_message_ok("modificó");
			}else{
				echo $this->_message_error("al modificar");
			}
										
										
	}

	/*********************** 3.2 METODO save_especialidad() ***********************/
	public function save_especialidad(){

		$this->descripcion = $_POST['descripcion'];
        $this->dias = $_POST['diasHabilesRBT'];
		$this->franja_HI = $_POST['franja_HI'];
        $this->franja_HF = $_POST['franja_HF'];

        // Obtener los días seleccionados del formulario
        $diasSeleccionados = isset($_POST['diasHabilesRBT']) ? $_POST['diasHabilesRBT'] : array();

        // Convertir el array de días a una cadena separada por comas
        $dias = implode(',', $diasSeleccionados);

        $sql = "INSERT INTO especialidades VALUES(NULL,
												'$this->descripcion',
												'$dias',
												'$this->franja_HI',
												'$this->franja_HF');";
		echo $sql;
        //exit;
        if($this->con->query($sql)){
            echo $this->_message_ok("guardó");
        }else{
            echo $this->_message_error("guardar");
        }
		
	}

    private function _get_checkbox($arreglo,$nombre,$defecto){
		
		$html = '
		<table border=0 align="left">';
		
		//CODIGO NECESARIO EN CASO QUE EL USUARIO NO SE ESCOJA UNA OPCION
		
		foreach($arreglo as $etiqueta){
			$html .= '
			<tr>
				<td>' . $etiqueta . '</td>
				<td>';
				
				if ($defecto == NULL) {
                    $html .= '<input type="checkbox" value="' . $etiqueta . '" name="' . $nombre . '[]" checked/></td>';
                } else {
                    $checked = (in_array($etiqueta, $defecto)) ? 'checked' : '';
                    $html .= '<input type="checkbox" value="' . $etiqueta . '" name="' . $nombre . '[]" ' . $checked . '/></td>';
                }
			
			$html .= '</tr>';
		}
		$html .= '
		</table>';
		return $html;
	}
	
	/*********************** 3.5 METODO get_form() ***********************/
	// Metodo para obtener un formulario, que varia si es que la peticion $GET es new o act
	// Si es act, debera tomar el idEsp del resgistro y mostrar la informacion correspondiente al idEsp que se envio por la peticion $GET
	public function get_form($id=NULL){
		
		if($id == NULL){
			$this->idEsp = NULL;
			$this->descripcion = NULL;
			$this->dias = NULL;
			$this->franja_HI = NULL;
			$this->franja_HF = NULL;
			
			$flag = NULL;
			$op = "newespecialidad";
			
		}else{

			$sql = "SELECT e.IdEsp AS idEsp, e.Descripcion, e.Dias, e.Franja_HI, e.Franja_HF
                    FROM especialidades e
                    WHERE IdEsp=$id;";
			$res = $this->con->query($sql);
			$row = $res->fetch_assoc();
			
			$num = $res->num_rows;
            if($num==0){
                $mensaje = "tratar de actualizar el especialidad con idEsp= ".$id;
                echo $this->_message_error($mensaje);
            }else{   
			
              // ***** TUPLA ENCONTRADA *****
				
			
				$this->descripcion = $row['Descripcion'];
				$this->dias = $row['Dias'];
				$this->franja_HI = $row['Franja_HI'];
				$this->franja_HF = $row['Franja_HF'];
				
				$flag = "disabled";
				$op = "updateespecialidad";
			}
		}

        $diasHabiles = ["Lunes",
						 "Martes",
						 "Miercoles",
                         "Jueves",
                         "Viernes"
						 ];

						 $html = '
						 <div class="container text-center">
							 <form name="especialidad" method="POST" action="unido.php" enctype="multipart/form-data">
						 
								 <input type="hidden" name="idEsp" value="' . $id  . '">
								 <input type="hidden" name="op" value="' . $op  . '">
								 <input type="hidden" name="especialidad" value="especialidad">
						 
								 <div class="container">
									 <div class="table-responsive">
										 <table class="table table-striped mb-0">
											 <thead class="thead-dark">
												 <th style="color:white" colspan="2">REGISTRO ESPECIALIDADES</th>
											 </thead>
						 
											 <tbody>
												 <tr>
													 <td>Descripción</td>
													 <td><input type="text" class="form-control" name="descripcion" value="' . $this->descripcion . '" required></td>
												 </tr>
												 <tr>
													 <td>Días Hábiles:</td>
													 <td>' . $this->_get_checkbox($diasHabiles, "diasHabilesRBT", explode(',', $this->dias)) . '</td>
												 </tr>
												 <tr>
													 <td>Hora Inicio:</td>
													 <td><input type="time" class="form-control" name="franja_HI" value="' . $this->franja_HI . '" required></td>
												 </tr>
												 <tr>
													 <td>Hora Fin:</td>
													 <td><input type="time" class="form-control" name="franja_HF" value="' . $this->franja_HF . '" required></td>
												 </tr>
												 <tr>
													 <th colspan="2"><input type="submit" name="Guardar" value="GUARDAR" class="btn btn-info col-12"></th>
												 </tr>
											 </tbody>
										 </table>
									 </div>
								 </div>
							 </form>
						 </div>';
	return $html;
	}

	/*********************** 3.6 METODO get_list() ***********************/
	// Metodo para obtener todos los registros de la tabla especialidad
	public function get_list($rol){
		$d_new = "newespecialidad/0";
		$d_new_final = base64_encode($d_new);
		$html = '
<div class="container">
    <div class="tab" role="tabpanel">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="thead-dark">
                    <th colspan="8" class="text-center" style="color: white;">DATOS DE LAS ESPECIALIDADES</th>
                </thead>
                <tr>
                    <th colspan="8" class="text-center"><a class="btn btn-info" href="unido.php?d=' . $d_new_final . '">Nuevo</a></th>
                </tr>
                <tr>
                    <th>Nº</th>
                    <th>Descripción</th>
                    <th>Días Hábiles</th>
                    <th colspan="3" class="text-center">Acciones</th>
                </tr>';
		$sql = "SELECT e.IdEsp AS idEsp, e.Descripcion, e.Dias
                FROM especialidades e;";	
		$res = $this->con->query($sql);
		// Sin codificar <td><a href="unido.php?op=del&idEsp=' . $row['idEsp'] . '">Borrar</a></td>
		while($row = $res->fetch_assoc()){
			$d_del = "delespecialidad/" . $row['idEsp'];
			$d_del_final = base64_encode($d_del);
			$d_act = "actespecialidad/" . $row['idEsp'];
			$d_act_final = base64_encode($d_act);
			$d_det = "detespecialidad/" . $row['idEsp'];
			$d_det_final = base64_encode($d_det);					
			$html .= '
				<tr>
					<td>' . $row['idEsp'] . '</td>
					<td>' . $row['Descripcion'] . '</td>
					<td>' . $row['Dias'] . '</td>';
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
	
	/*********************** 3.7 METODO get_detail_especialidad() ***********************/
	/* Metodo para obtener los detalles de un registro de la tabla especialidad
	OJO: Para realizar la especialidad, utilizas el alias asignado a través de "AS" en la sentencia SQL. 
	Por ejemplo: "p.Nombre AS nombrePaciente" asigna el alias "nombrePaciente" a la columna "Nombre" de la tabla "descripcion" en el resultado de la especialidad.

	Después de asignar el alias, puedes utilizar esa variable para acceder a la información en la tabla mediante el alias asignado en lugar del nombre original del campo en la base de datos.

	Ejemplo:
	En la sentencia SQL:
	SELECT p.Nombre AS nombrePaciente FROM descripcion p WHERE p.PacienteID = 1;
	Estás asignando el alias "nombrePaciente" al campo "Nombre" de la tabla "descripcion".
	Luego, al acceder al resultado de la especialidad en PHP, puedes utilizar $row['nombrePaciente'] para obtener el valor del campo "Nombre" del descripcion.

	Por lo tanto, la variable con el alias asignado se convierte en una forma más legible y fácil de entender la información recuperada de la base de datos.
	*/
	public function get_detail_especialidad($id){
		$sql = "SELECT e.Descripcion, e.Dias, e.Franja_HI, e.Franja_HF
                FROM especialidades e
                WHERE IdEsp = $id;";
		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();
		
		$num = $res->num_rows;

		// echo "<br>TUPLA <br>";
		// echo "<pre>";
		// 	print_r($row);
		// echo "</pre>";

        //Si es que no existiese ningun registro debe desplegar un mensaje 
        //$mensaje = "tratar de eliminar el especialidad con idEsp= ".$id;
        //echo $this->_message_error($mensaje);
        //y no debe desplegarse la tablas
        
        if($num==0){
            $mensaje = "tratar de editar el especialidad con idEsp= ".$id;
            echo $this->_message_error($mensaje);
        }else{ 
			$html = '
			<div class="container">
				<div class="table-responsive">
					<table class="table table-striped mb-0">
						<thead class="thead-dark">
							<th colspan="4" class="text-center" style="color:white;">ESPECIALIDADES</th>
						</thead>
						<tbody>
							<tr>
								<td>Descripción: </td>
								<td>'. $row['Descripcion'] .'</td>
							</tr>
							<tr>
								<td>Días Hábiles: </td>
								<td>'. $row['Dias'] .'</td>
							</tr>
							<tr>
								<td>Hora de Inicio: </td>
								<td>'. $row['Franja_HI'] .'</td>
							</tr>
							<tr>
								<td>Hora de Fin: </td>
								<td>'. $row['Franja_HF'] .'</td>
							</tr>   
							<tr>
								<th colspan="2"><a class="btn btn-info" href="unido.php#especialidad">Regresar</a></th>
							</tr>';


						$html .= '
									</tbody>
								</table>
							</div>
						</div>';
				
							return $html;
		}
	}
	
	/*********************** 3.8 METODO delete_especialidad() ***********************/
	// Metodo para Borrar un registro de la DB
	public function delete_especialidad($id){
		$sql = "DELETE FROM especialidades WHERE IdEsp=$id;";
			if($this->con->query($sql)){
			echo $this->_message_ok("ELIMINÓ");
		}else{
			echo $this->_message_error("eliminar");
		}	
	}
	
	/*********************** 3.9 METODO _message_error() ***********************/
	// Metodo para el mensaje de Error
	private function _message_error($tipo){
		$html = '
		<table border="0" align="center">
			<tr>
				<th>Error al ' . $tipo . '. Favor contactar a .................... </th>
			</tr>
			<tr>
				<th><a class="btn btn-success" href="unido.php">Regresar</a></th>
			</tr>
		</table>';
		return $html;
	}
	
	/*********************** 3.10 METODO _message_ok() ***********************/
	// Metodo para el mensaje de OK
	private function _message_ok($tipo){
		$html = '
		<table border="0" align="center">
			<tr>
				<th>El registro se  ' . $tipo . ' correctamente</th>
			</tr>
			<tr>
				<th><a class="btn btn-success" href="unido.php">Regresar</a></th>
			</tr>
		</table>';
		return $html;
	}
	
}
?>


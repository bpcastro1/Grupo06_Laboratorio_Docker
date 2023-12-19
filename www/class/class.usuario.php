<?php

class Usuario {
    private $idUsuario;
    private $nombre;
    private $password;
    private $idRol;
    private $con;

    public function __construct($cn) {
        $this->con = $cn;
       /*  echo "EJECUTANDOSE EL CONSTRUCTOR USUARIO<br><br>"; */
    }

    public function update_usuario() {
        $this->idUsuario = $_POST['IdUsuario'];
        $this->nombre = $_POST['nombre'];
        $this->password = $_POST['password'];
        $this->idRol = $_POST['RolCMB'];

        $sql = "UPDATE usuarios SET Nombre='$this->nombre',
                                   Password='$this->password',
                                   IdRol=$this->idRol
                WHERE IdUsuario=$this->idUsuario;";
        
        echo $sql;

        if ($this->con->query($sql)) {
            echo $this->_message_ok("modificó");
        } else {
            echo $this->_message_error("al modificar");
        }
    }

    public function save_usuario() {
        $this->nombre = $_POST['nombre'];
        $this->password = $_POST['password'];
        $this->idRol = $_POST['RolCMB'];

        $sql = "INSERT INTO usuarios VALUES(NULL,
                                            '$this->nombre',
                                            '$this->password',
                                            $this->idRol);";

        echo $sql;
        if ($this->con->query($sql)) {
            echo $this->_message_ok("guardó");
        } else {
            echo $this->_message_error("guardar");
        }
    }

    private function _get_combo_db($tabla,$valor,$etiqueta,$nombre,$defecto){
		$html = '<select name="' . $nombre . '">';
		$sql = "SELECT $valor,$etiqueta FROM $tabla;";
		$res = $this->con->query($sql);
		while($row = $res->fetch_assoc()){
			//ImpResultQuery($row);
			$html .= ($defecto == $row[$valor])?'<option value="' . $row[$valor] . '" selected>' . $row[$etiqueta] . '</option>' . "\n" : '<option value="' . $row[$valor] . '">' . $row[$etiqueta] . '</option>' . "\n";
		}
		$html .= '</select>';
		return $html;
	}

    public function get_form($id = NULL){
    if ($id == NULL) {
        $this->idUsuario = NULL;
        $this->nombre = NULL;
        $this->password = NULL;
        $this->idRol = NULL;

        $flag = NULL;
        $op = "newusuario";
    } else {
        $sql = "SELECT u.IdUsuario, u.Nombre, u.Password, u.Rol
                FROM usuarios u
                WHERE u.IdUsuario = $id;";
        $res = $this->con->query($sql);
        $row = $res->fetch_assoc();

        $num = $res->num_rows;
        if ($num == 0) {
            $mensaje = "tratar de actualizar el usuario con IdUsuario= " . $id;
            echo $this->_message_error($mensaje);
        } else {
            $this->nombre = $row['Nombre'];
            $this->password = $row['Password'];
            $this->idRol = $row['Rol'];

            $flag = "disabled";
            $op = "updateusuario";
        }
    }

    $html = '<div class="container text-center">
    <form name="usuario" method="POST" action="unido.php" enctype="multipart/form-data">
        
        <input type="hidden" name="IdUsuario" value="' . $id  . '">
        <input type="hidden" name="op" value="' . $op  . '">
        <input type="hidden" name="usuario" value="usuario">
        
        <div class="container">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="thead-dark"> <!-- Utilizamos la clase thead-dark para cambiar el color de fondo de la cabecera -->
                        <th style="color:white" colspan="2">REGISTRO USUARIOS</th>
                    </thead>

                    <tbody>
                        <tr>
                            <td>Usuario</td>
                            <td><input type="text" class="form-control" name="nombre" value="' . $this->nombre . '" required></td>
                        </tr>
                        <tr>
                            <td>Password:</td>
                            <td><input type="text" class="form-control" name="password" value="' . $this->password . '" required></td>
                        </tr>
                        <tr>
                            <td>Rol:</td>
                            <td>' . $this->_get_combo_db("roles","IdRol","Nombre","RolCMB",$this->idRol) . '</td>
                        </tr>
                        <tr>
                            <th colspan="2"><input type="submit" class="btn btn-secondary" name="Guardar" value="GUARDAR"></th>
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
	// Metodo para obtener todos los registros de la tabla usuario
	public function get_list($rol){
		$d_new = "newusuario/0";
		$d_new_final = base64_encode($d_new);
		$html = '<div class="container">
    <div class="tab" role="tabpanel">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="thead-dark">
                    <th colspan="8" class="text-center" style="color: white;">DATOS DE LOS USUARIOS</th>
                </thead>
                <tr>
                    <th colspan="8" class="text-center"><a class="btn btn-info" href="unido.php?d=' . $d_new_final . '">Nuevo</a></th>
                </tr>
                <tr>
                    <th class="text-center">Nº</th>
                    <th class="text-center">Usuario</th>
                    <th colspan="3" class="text-center">Acciones</th>
                </tr>';
		$sql = "SELECT u.IdUsuario AS idUsuario, u.Nombre
                FROM usuarios u;";	
		$res = $this->con->query($sql);
		// Sin codificar <td><a href="unido.php?op=del&idUsuario=' . $row['idUsuario'] . '">Borrar</a></td>
		while($row = $res->fetch_assoc()){
			$d_del = "delusuario/" . $row['idUsuario'];
			$d_del_final = base64_encode($d_del);
			$d_act = "actusuario/" . $row['idUsuario'];
			$d_act_final = base64_encode($d_act);
			$d_det = "detusuario/" . $row['idUsuario'];
			$d_det_final = base64_encode($d_det);					
			$html .= '
				<tr>
					<td>' . $row['idUsuario'] . '</td>
					<td>' . $row['Nombre'] . '</td>';
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

	public function get_detail_usuario($id){
		$sql = "SELECT u.Nombre, u.Password, u.Rol
            FROM usuarios u
            WHERE u.IdUsuario = $id;";
        $res = $this->con->query($sql);
        $row = $res->fetch_assoc();

        $num = $res->num_rows;

        if ($num == 0) {
            $mensaje = "tratar de editar el usuario con IdUsuario= " . $id;
            echo $this->_message_error($mensaje);
        } else {
			$html = '<div class="container">
			<div class="table-responsive">
				<table class="table table-striped mb-0">
					<thead class="thead-dark">
						<th colspan="4" class="text-center" style="color:white;">USUARIOS</th>
					</thead>
					<tbody>
						<tr>
							<td>Usuario: </td>
							<td>'. $row['Nombre'] .'</td>
						</tr>
						<tr>
							<td>Password: </td>
							<td>'. $row['Password'] .'</td>
						</tr>
						<tr>
							<td>Rol: </td>
							<td>'. $row['Rol'] .'</td>
						</tr>   
						<tr>
							<th colspan="2"><a class="btn btn-info" href="unido.php">Regresar</a></th>
						</tr>
					</tbody>
				</table>
			</div>
		</div>';
		
		return $html;
		}
	}
	
	/*********************** 3.8 METODO delete_usuario() ***********************/
	// Metodo para Borrar un registro de la DB
	public function delete_usuario($id){
        $sql = "DELETE FROM usuarios WHERE IdUsuario=$id;";
        if ($this->con->query($sql)) {
            echo $this->_message_ok("ELIMINÓ");
        } else {
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

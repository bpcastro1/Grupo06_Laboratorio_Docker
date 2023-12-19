<?php
class Rol {
    private $idRol;
    private $nombre;
    private $accion;
    private $con;

    function __construct($cn) {
        $this->con = $cn;
    }

    public function update_rol() {
        $this->idRol = $_POST['idRol'];
        $this->nombre = $_POST['nombre'];
        $this->accion = $_POST['accion'];

        $sql = "UPDATE roles SET Nombre='$this->nombre', Accion='$this->accion' WHERE IdRol=$this->idRol";

        if ($this->con->query($sql)) {
            echo $this->_message_ok("modificó");
        } else {
            echo $this->_message_error("al modificar");
        }
    }

    public function save_rol() {
        $this->nombre = $_POST['nombre'];
        $this->accion = $_POST['accion'];

        $sql = "INSERT INTO roles (Nombre, Accion) VALUES ('$this->nombre', '$this->accion')";

        if ($this->con->query($sql)) {
            echo $this->_message_ok("guardó");
        } else {
            echo $this->_message_error("guardar");
        }
    }

    private function _get_combo_db($tabla, $valor, $etiqueta, $nombre, $defecto) {
        $html = '<select name="' . $nombre . '">';
        $sql = "SELECT $valor, $etiqueta FROM $tabla;";
        $res = $this->con->query($sql);
        while ($row = $res->fetch_assoc()) {
            $selected = ($defecto == $row[$valor]) ? 'selected' : '';
            $html .= "<option value='{$row[$valor]}' $selected>{$row[$etiqueta]}</option>\n";
        }
        $html .= '</select>';
        return $html;
    }
    //*************************************** PARTE I ************************************************************

    public function get_form($idRol = NULL) {
        if ($idRol == NULL) {
            $this->nombre = NULL;
            $flag = NULL;
            $op = "newrol";
        } else {
            $sql = "SELECT * FROM roles WHERE IdRol=$idRol;";
            $res = $this->con->query($sql);
            $row = $res->fetch_assoc();

            $num = $res->num_rows;
            if ($num == 0) {
                $mensaje = "tratar de actualizar el rol con idRol= " . $idRol;
                echo $this->_message_error($mensaje);
            } else {
                $this->nombre = $row['Nombre'];
                $this->accion = $row['Accion'];
                $flag = "disabled";
                $op = "updaterol";
            }
        }

        $html = '
        <form name="rol" method="POST" action="unido.php" enctype="multipart/form-data">
        
            <input type="hidden" name="idRol" value="' . $idRol  . '">
            <input type="hidden" name="op" value="' . $op  . '">
        
            <div class="container">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="thead-dark">
                            <th style="color:white" colspan="2">DATOS ROL PERSONA</th>
                        </thead>
        
                        <tbody>
                            <tr>
                                <td>Nombre:</td>
                                <td><input type="text" class="form-control" name="nombre" value="' . $this->nombre . '" required></td>
                            </tr>
                            <tr>
                                <td>Acciones:</td>
                                <td><input type="text" class="form-control" name="accion" value="' . $this->accion . '" required></td>
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

    public function get_list($rol) {
        $d_new = "newrol/0";
        $d_new_final = base64_encode($d_new);
        $html = '
<div class="container">
    <div class="tab" role="tabpanel">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="thead-dark">
                    <th colspan="8" class="text-center" style="color: white;">LISTA DE ROLES</th>
                </thead>
                <tr>
                    <th colspan="8" class="text-center">
                        <a class="btn btn-info" href="unido.php?d=' . $d_new_final . '">Nuevo</a>
                    </th>
                </tr>
                <tr>
                    <th class="text-center">IdRol</th>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Acciones</th>
                    <th colspan="3" class="text-center">Acciones</th>
                </tr>';
        $sql = "SELECT IdRol, Nombre, Accion FROM roles;";
        $res = $this->con->query($sql);
    
        while ($row = $res->fetch_assoc()) {
            $d_del = "delrol/" . $row['IdRol'];
            $d_del_final = base64_encode($d_del);
            $d_act = "actrol/" . $row['IdRol'];
            $d_act_final = base64_encode($d_act);
            $d_det = "detrol/" . $row['IdRol'];
            $d_det_final = base64_encode($d_det);
    
            $html .= '
            <tr>
                <td>' . $row['IdRol'] . '</td>
                <td>' . $row['Nombre'] . '</td>
                <td>' . $row['Accion'] . '</td>';
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
    
        $html .= '</table></div></div></div>';
        return $html;
    }
    

    public function get_detail_rol($idRol){
        $sql = "SELECT * FROM roles where IdRol=$idRol ;";
        
        $res = $this->con->query($sql);
        $row = $res->fetch_assoc();
        
        $num = $res->num_rows;
    
        if($num == 0){
            $mensaje = "tratar de editar el consulta con idRol= ".$idRol;
            echo $this->_message_error($mensaje);
        } else { 
            $html = '
<div class="container">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead class="thead-dark">
                <th colspan="4" class="text-center" style="color:white;">DETALLES DEL ROL</th>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">Rol Nombre: </td>
                    <td class="text-center">'. $row['Nombre'] .'</td>
                </tr>
                <tr>
                    <td class="text-center">Accion: </td>
                    <td class="text-center">'. $row['Accion'] .'</td>
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
    

    public function delete_rol($idRol)
{
    try {
        $sql = "DELETE FROM roles WHERE IdRol=$idRol;";
        $this->con->query($sql);
        echo $this->_message_ok("ELIMINÓ");
    } catch (mysqli_sql_exception $e) {
        $error_message = $e->getMessage();

        // Verificamos si el mensaje de error contiene la cadena específica
        if (strpos($error_message, 'a foreign key constraint fails') !== false) {
            echo $this->_message_error("No se puede eliminar el rol. Por favor, elimine las referencias en otras tablas primero.");
        } else {
            // Manejo de otros tipos de errores si es necesario
            echo $this->_message_error("Error al eliminar el rol: " . $error_message);
        }
    }
}


    //*****************************************************************************************

    private function _message_error($tipo) {
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

    private function _message_ok($tipo) {
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
}
?>
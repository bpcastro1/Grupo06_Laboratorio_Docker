<?php
function coneccion() {
        $c = new mysqli(SERVER, USER, PASS, DB);
        if($c->connect_errno) {
            die("Error de conexión: ".$c->connect_error);
        }
        $c->set_charset("utf8");
        return $c;
}

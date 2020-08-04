<?php

if (file_exists("datos.txt")) {
    $jsonClientes = file_get_contents("datos.txt");
    $aClientes = json_decode($jsonClientes, true);
} else {
    $aClientes = [];
}

$id = isset($_GET["id"])? $_GET["id"] : "";   //Estructura condicional ternaria.

$aMsg = array("mensaje" => "", "codigo" => "");

if (isset($_GET["do"]) && $_GET["do"] == "eliminar"){
    if($aClientes[$id]["imagen"] != ""){
        unlink("files/". $aClientes[$id]["imagen"]);
    }
    unset($aClientes[$id]);
    $aMsg = array("mensaje" => "Cliente eliminado correctamente", "codigo" => "danger");
    $jsonClientes = json_encode($aClientes);
    file_put_contents("datos.txt", $jsonClientes);
    $id="";
}

if ($_POST) {
    $dni = trim($_POST["txtDNI"]);
    $nombre = trim($_POST["txtNombre"]);
    $telefono = trim($_POST["txtTelefono"]);
    $correo = trim($_POST["txtCorreo"]);
    $nombreImagen = "";

    if($_FILES["archivo"]["error"] == UPLOAD_ERR_OK){
        $nombreRandom = date("Ymdhmsi");
        $archivoTmp = $_FILES["archivo"]["tmp_name"];
        $nombreArchivo = $_FILES["archivo"]["name"];
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $nombreImagen = "$nombreRandom.$extension";
        move_uploaded_file($archivoTmp, "files/".$nombreImagen);
    }

    if(isset($_GET["do"]) && $_GET["do"] == "editar"){
        if($_FILES["archivo"]["error"] === UPLOAD_ERR_OK){
            if($aClientes[$id]["imagen"] != ""){
                unlink("files/". $aClientes[$id]["imagen"]);
           }
        }
        //actualización
        
        $aClientes[$id] = array(
            "dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" => $nombreImagen
        );
        $aMsg = array("mensaje" => "Cliente editado correctamente", "codigo" => "success");

    }
    else{
        //Introduccion

        $aClientes[] = array(
            "dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" => $nombreImagen
        );

        $aMsg = array("mensaje" => "Cliente agregado correctamente", "codigo" => "primary");
    }

    $jsonClientes = json_encode($aClientes);

    file_put_contents("datos.txt", $jsonClientes);

}

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABM Clientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body style="background-image: url(fondo/Fondo.png);">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center py-5" style="font-size:50">
                <h1>Registro de Clientes</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6 pl-4">
            <?php if($aMsg["mensaje"] != "") {?>
                <div class="row">
                    <div class="col-12">
                    <div class="alert alert-<?php echo $aMsg["codigo"]?>" role="alert">
                         <?php echo $aMsg["mensaje"]; ?>
                    </div>
                    </div>
                </div>
            <?php } ?>
            <form action="" method="POST" enctype="multipart/form-data" class="form">
                <div class="row">
                    <div class="col-12 form-group">
                        <label for="txtDNI">DNI:</label>
                        <input type="number" name="txtDNI" id="txtDNI" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["dni"] : ""; ?>">
                    </div>
                    <div class="col-12 form-group">
                        <label for="txtNombre">Nombre:</label>
                        <input type="text" name="txtNombre" id="txtNombre" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["nombre"] : ""; ?>">
                    </div>
                    <div class="col-12 form-group">
                        <label for="txtTelefono">Teléfono:</label>
                        <input type="number" name="txtTelefono" id="txtTelefono" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["telefono"] : ""; ?>">
                    </div>
                    <div class="col-12 form-group">
                        <label for="txtCorreo">Correo:</label>
                        <input type="email" name="txtCorreo" id="txtCorreo" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["correo"] : ""; ?>">
                    </div>
                    <div class="col-12 form-group">
                        <label for="txtCorreo">Archivo adjunto</label>
                    </div>
                    <div class="col-12 form-group px-3">
                        <input type="file" name="archivo" id="archivo" class="form-control" style="background-color:rgba(0,0,0,0)">
                    </div>
                    <div class="col-12 form-group">
                        <button value="submit" class="btn btn-primary" name="btnGuardar" id="btnGuardar">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-12 col-sm-6 pr-4">
            <table class="table table-hover border">
                <tr style="text-transform: uppercase;">
                    <th>Imagen</th>
                    <th>DNI</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($aClientes as $id => $cliente) : ?>
                    <tr>
                        <td><img src="files/<?php echo $cliente["imagen"]; ?>" alt="" style="height: 120px;"></td>
                        <td><?php echo $cliente["dni"] ?></td>
                        <td><?php echo $cliente["nombre"] ?></td>
                        <td><?php echo $cliente["correo"] ?></td>
                        <td style="width: 110px;">
                            <a href="index.php?id=<?php echo $id; ?>&do=editar"><i class="fas fa-edit editar"></i></a>
                            <a href="index.php?id=<?php echo $id; ?>&do=eliminar"><i class="fas fa-trash-alt eliminar"></i></a>
                        </td>
                    </tr>
                <?php endforeach ?>

            </table>
            <a href="index.php"><i class= "fas fa-plus limpiar"></i></a>
        </div>
    </div>
</body>

</html>
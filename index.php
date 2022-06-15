<?php

if (file_exists("archivo.txt")) {
    $strJson = file_get_contents("archivo.txt");
    $aClientes = json_decode($strJson, true);
} else {
    $aClientes = array();
}

if (isset($_GET["id"])) {
    $id = $_GET["id"];
} else {
    $id = "";
}

if (isset($_GET["do"]) && $_GET["do"] == "eliminar") {

    unlink("imagenes/" . $aClientes[$_GET["id"]]["nombreImagen"]);

    unset($aClientes[$_GET["id"]]);

    //Convertir aClientes a JSON
    $strJson = json_encode($aClientes);

    //Almacenar el JSON en archivo.txt
    file_put_contents("archivo.txt", $strJson);

    header("Location: index.php");
}

if ($_POST) {

    $dni = $_POST["txtDni"];
    $nombre = $_POST["txtNombre"];
    $telefono = $_POST["txtTelefono"];
    $correo = $_POST["txtCorreo"];
    $nombreImagen = "";

    if ($_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {
        $nombreImagen = date("Ymdhmsi") . rand(1000, 2000);
        $archivo_temp = $_FILES["archivo"]["tmp_name"];
        $extension = pathinfo($_FILES["archivo"]["name"], PATHINFO_EXTENSION);

        if ($extension == "jpg" || $extension == "png" || $extension == "jpeg") {
            move_uploaded_file($archivo_temp, "imagenes/$nombreImagen.$extension");
        }
        $nombreImagen = $nombreImagen . "." . $extension;
    }

    if ($id >= 0) {

        if ($_FILES["archivo"]["error"] !== UPLOAD_ERR_OK) {
            $nombreImagen = $aClientes[$id]["nombreImagen"];
        } else {
            if (file_exists("imagenes/" . $aClientes[$id]["nombreImagen"])) {
                unlink("imagenes/" . $aClientes[$id]["nombreImagen"]);
            }
        }

        //Modificando un cliente
        $aClientes[$id] = array(
            "dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "nombreImagen" => $nombreImagen
        );

        header("Location: index.php");
    } else {

        //Agregando un cliente
        $aClientes[] = array(
            "dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "nombreImagen" => $nombreImagen
        );
    }

    //Array aClientes a JSON
    $strJson = json_encode($aClientes);

    //Almacenar el JSON en archivo.txt
    file_put_contents("archivo.txt", $strJson);
}

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABM Clientes</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/fontawesome/css/fontawesome.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <main class="container">
        <div class="row">
            <div class="col-12 text-center my-4">
                <h1>ABM Clientes</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-5">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div>
                        <label for="txtDni">DNI: *</label>
                        <input type="text" name="txtDni" id="txtDni" class="form-control shadow" value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["dni"] : "" ?>" required>
                    </div>
                    <div class="mt-2">
                        <label for="txtNombre">Nombre: *</label>
                        <input type="text" name="txtNombre" id="txtNombre" class="form-control shadow" value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["nombre"] : "" ?>" required>
                    </div>
                    <div class="mt-2">
                        <label for="txtTelefono">Teléfono: *</label>
                        <input type="text" name="txtTelefono" id="txtTelefono" class="form-control shadow" value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["telefono"] : "" ?>" required>
                    </div>
                    <div class="mt-2">
                        <label for="txtCorreo">Correo: *</label>
                        <input type="email" name="txtCorreo" id="txtCorreo" class="form-control shadow" value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["correo"] : "" ?>" required>
                    </div>
                    <div class="mt-2">
                        <label for="archivo">Archivo Adjunto: </label>
                        <input type="file" name="archivo" id="archivo" accept=".jpg, .jpeg, .png" class="form-control shadow">
                        <p class="mt-1"><b>Archivos admitidos: .jpg, .jpeg, .png</b></p>
                    </div>
                    <div class="mt-1">
                        <button type="submit" name="btnGuardar" class="btn btn-primary">Guardar</button>
                        <a href="index.php" class="btn btn-secondary">Nuevo</a>
                    </div>
                </form>
            </div>
            <div class="col-12 col-sm-7 mt-4 mt-sm-0">
                <div class="table-responsive text-center">
                    <table class="table table-hover table-bordered align-middle" style="height: 100px;">
                        <thead <?php echo count($aClientes) > 0 ? "" : 'class="shadow"' ?>>
                            <tr>
                                <th scope="col">Imagen</th>
                                <th scope="col">DNI</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Teléfono</th>
                                <th scope="col">Correo</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody <?php echo count($aClientes) > 0 ? 'class="shadow"' : "" ?>>
                            <?php if (isset($aClientes)) {

                                //Para mostrar los datos en el modal antes de eliminar el cliente
                                $nombreClienteModal = "";
                                $dniClienteModal = "";
                                $correoClienteModal = "";
                                $telefonoClienteModal = "";

                                foreach ($aClientes as $pos => $cliente) {

                            ?>
                                    <tr>
                                        <td><img src="imagenes/<?php echo $cliente["nombreImagen"]; ?>" style="width: 150px; height: 150px; border-radius: 5px; object-fit: fill;"></td>
                                        <td><?php echo $cliente["dni"]; ?></td>
                                        <td><?php echo $cliente["nombre"]; ?></td>
                                        <td><a href="tel:<?php echo $cliente["telefono"]; ?>"><?php echo $cliente["telefono"]; ?></a></td>
                                        <td><a href="mailto:<?php echo $cliente["correo"]; ?>"><?php echo $cliente["correo"]; ?></a></td>
                                        <td style="font-size: 18px;">
                                            <a href="https://api.whatsapp.com/send?phone=+54<?php echo $cliente["telefono"] ?>&text=Hola <?php echo $cliente["nombre"] ?>"><i class="fa-brands fa-whatsapp"></i></a>
                                            <a href="?id=<?php echo $pos ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#modalBorrarCliente">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                            <?php
                                            //Para mostrar los datos en el modal antes de eliminar el cliente
                                            $nombreClienteModal = $aClientes[$pos]["nombre"];
                                            $dniClienteModal = $aClientes[$pos]["dni"];
                                            $correoClienteModal = $aClientes[$pos]["correo"];
                                            $telefonoClienteModal = $aClientes[$pos]["telefono"];

                                            ?>
                                        </td>
                                    </tr>

                            <?php }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal de alerta antes de borrar a un cliente -->
        <div class="modal fade" id="modalBorrarCliente" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">¿Seguro que desea eliminar el cliente?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <b>DATOS DEL CLIENTE</b><br>
                        <?php

                        echo "<b>Nombre: </b>" . $nombreClienteModal . "<br>";
                        echo "<b>DNI: </b>" . $dniClienteModal . "<br>";
                        echo "<b>Correo: </b>" . $correoClienteModal . "<br>";
                        echo "<b>Teléfono: </b>" . $telefonoClienteModal . "<br>";

                        ?>
                    </div>
                    <div class="modal-footer">
                        <a href="?id=<?php echo $pos ?>&do=eliminar" class="btn btn-danger">Eliminar</a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
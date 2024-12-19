<?php
if ($_SESSION['username'] != "admin@bdweb") {
    header("Location: login.php"); // Redirige a una página de login si no es admin
    exit;
}

if (isset($_GET['pic_id'])) {
    $stmt = $conx->prepare("SELECT pic FROM produktuak WHERE id = ?");
    $stmt->bind_param("i", $_GET['pic_id']);
    $stmt->execute();
    $filequery = $stmt->get_result();
    $delfile = $filequery->fetch_assoc();

    $filePath = "images/" . basename($delfile['pic']);
    $realPath = realpath($filePath);
    if ($realPath && strpos($realPath, realpath('images/')) === 0) {
        unlink($realPath);
    }
    $stmt = $conx->prepare("DELETE FROM produktuak WHERE id = ?");
    $stmt->bind_param("i", $_GET['pic_id']);
    $stmt->execute();
    echo "<div align=center><h5>Produktua ezabatuta</h5></div><br>";
}

if (isset($_GET['upload'])) {
    if ($_FILES['upfile']['error'] == 0) {
        // Validación de tipo MIME y tipo de archivo permitido
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['upfile']['type'], $allowed_types)) {
            $path = "images/" . basename($_FILES['upfile']['name']); // Evita sobrescritura de archivos
            $picName =  basename($_FILES['upfile']['name']);
            if (move_uploaded_file($_FILES['upfile']['tmp_name'], $path)) {
                $uploader = $_SESSION['username'];
                // Sanitización de las entradas
                $izena = htmlspecialchars($_POST['izena']);
                $deskripzioa = htmlspecialchars($_POST['deskripzioa']);
                $salneurria = filter_var($_POST['salneurria'], FILTER_VALIDATE_FLOAT);
                $stock = filter_var($_POST['stock'], FILTER_VALIDATE_INT);
                
                // Inserción del producto en la base de datos
                $stmt = $conx->prepare("INSERT INTO produktuak (izena, deskripzioa, salneurria, pic, stock) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssdss", $izena, $deskripzioa, $salneurria, $picName, $stock);
                if ($stmt->execute()) {
                    echo "<div align=center><h5>Produktu \"".$_POST['izena']."\" txertatuta</h5></div><br>";
                } else {
                    echo "<div align=center><h5>Error al insertar el producto: " . mysqli_error($conx) . "</h5></div><br>";
                }
            } else {
                echo "<div align=center><h5>Error al subir el archivo.</h5></div><br>";
            }
        } else {
            echo "<div align=center><h5>Tipo de archivo no permitido. Solo imágenes JPEG, PNG y GIF.</h5></div><br>";
        }
    }
}
?>

<div align=center>
    <table width=1000 cellpadding=10 cellspacing=10 align=center>
        <tr>
            <td valign=top align=left>
                <fieldset style=width:300;>
                <legend><b>Produktu berria </b></legend>
                <form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']."?action=updel&upload=1"; ?>" method="POST">
                    Izena: <input type="text" name="izena" required><br>
                    Deskripzioa: <input type="text" name="deskripzioa" required><br>
                    Salneurria: <input type="text" name="salneurria" required><br>
                    Stock: <input type="text" name="stock" required><br>
                    Irudia aukeratu:<br>
                    <br>
                    <input name="upfile" type="file" required><br>
                    <br>
                    <input type="submit" value="Igo">
                </form>
                </fieldset>
            </td>
            <td valign=top align=left>
                <fieldset style=width:300;>
                <legend><b>Ezabatu</b></legend>
                <?php
                $delquery = mysqli_query($conx,"SELECT * FROM produktuak");
                $produktuak = array();
                while ($row = mysqli_fetch_array($delquery)) {
                    $produktuak[] = $row;
                }

                foreach ($produktuak as $produktua) {
                ?>
                    <p><?php echo $produktua['izena']; ?></p><br>
                    <a href="<?php echo "images/".$produktua['pic']; ?>"><img src="images/<?php echo $produktua['pic']; ?>" border=1></a><br>
                    <a href="<?php echo $_SERVER['PHP_SELF']."?action=updel&pic_id=".$produktua['id']; ?>"><b>Produktua ezabatu</b></a>
                <br><br>
                <?php
                }
                ?>
                </fieldset>
            </td>
        </tr>
    </table>
</div>

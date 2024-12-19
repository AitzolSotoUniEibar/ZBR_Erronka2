<?php
$search = "";
$testua = "";

if (isset($_GET['keyword']) && $_GET['keyword'] != '') {
    $testua = htmlspecialchars(trim($_GET['keyword']), ENT_QUOTES, 'UTF-8');
}

// Validar conexión a la base de datos
if (!$conx) {
    die("Errorea datu-basearekin konektatzean: " . $conx->connect_error);
}

// Preparar y ejecutar consulta segura
$stmt = $conx->prepare("SELECT * FROM produktuak WHERE izena LIKE ? OR deskripzioa LIKE ?");
$search = "%{$testua}%";
$stmt->bind_param("ss", $search, $search);
$stmt->execute();
$result = $stmt->get_result();

// Almacenar resultados en un array
$produktuak = array();
while ($row = $result->fetch_assoc()) {
    $produktuak[] = $row;
}

if (count($produktuak) == 0) {
    // Si no hay productos, mostrar mensaje de error
    echo "<fieldset class='error-fieldset'>";
    echo "<legend><b>Ez dago produkturik katalogoan " . $testua . " deitzen denik</b></legend>";
    if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
        echo "<div align='center'><h3><b><a href='" . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "?action=updel'>Eguneratu</a> katalogoa.</b></h3></div>";
    } else {
        echo "<div align='center'><h3>Enpresaren hasierako orria</h3></div>";
    }
    echo "</fieldset>";
} else {
?>
    <table width="1000" cellpadding="10" cellspacing="10" align="center">
        <?php foreach ($produktuak as $data): ?>
            <tr>
                <td align="center" valign="top" width="40%">
                    <fieldset>
                        <br>
                        <?php 
                        $pic = isset($data["pic"]) ? htmlspecialchars(basename($data["pic"]), ENT_QUOTES, 'UTF-8') : "placeholder.jpg";
                        $image_path = "images/" . $pic;
                        if (file_exists($image_path) && exif_imagetype($image_path)) {
                            echo "<a href='" . $image_path . "'><img src='" . $image_path . "' alt='Product Image' border='1'></a><br>";
                        } else {
                            echo "<p>Irudia ez dago erabilgarri.</p>";
                        }
                        ?>
                        <br>
                    </fieldset>
                </td>
                <td valign="top" width="60%">
                    <fieldset>
                        <legend><b>Izena</b></legend>
                        <br>
                        <?php 
                        $izena = isset($data['izena']) ? htmlspecialchars($data['izena'], ENT_QUOTES, 'UTF-8') : "Ezezaguna";
                        $salneurria = isset($data['salneurria']) ? htmlspecialchars($data['salneurria'], ENT_QUOTES, 'UTF-8') : "Ezezaguna";
                        echo $izena . " - " . $salneurria . "€"; 
                        ?>
                        <br>
                    </fieldset>
                    <fieldset>
                        <legend><b>Deskripzioa</b></legend>
                        <br>
                        <?php 
                        $deskripzioa = isset($data['deskripzioa']) ? htmlspecialchars($data['deskripzioa'], ENT_QUOTES, 'UTF-8') : "Ez dago deskripziorik.";
                        echo $deskripzioa; 
                        ?>
                        <br>
                    </fieldset>
                    <br>
                    <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
                        <?php if ($_SESSION['username'] == 'admin@bdweb' && isset($data['id'])): ?>
                            <table width="100%" cellpadding="2" cellspacing="2" align="center">
                                <tr>
                                    <td width="50%" align="left">
                                        <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "?action=description&pic_id=" . urlencode($data['id']); ?>">
                                            <b>Deskripzioa/Salneurria aldatu</b>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        <?php else: ?>
                            <p><img src="images/pngegg.png" alt="Icon"></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php
}
?>

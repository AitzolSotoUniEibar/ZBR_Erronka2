<?php
$data = array('email' 		=> '',
			  'firstname' 	=> '',
			  'lastname' 	=> '',
			  'postakodea' 	=> '',
			  'city' 		=> '',
			  'stateProv' 	=> '',
			  'country'		=> '',
			  'telephone' 	=> '',
			  'password' 	=> '',
			  'password2' 	=> '',
			  'imagen'      => ''
);
$error = array('email' 	  => '',
			  'firstname' => '',
			  'lastname'  => '',
			  'city'	  => '',
			  'stateProv' => '',
			  'country'	  => '',
			  'postakodea'  => '',
			  'telephone' => '',
			  'password'  => '',
			  'imagen'	  => '',
);
$hasError = false;
if (isset($_POST['data'])) {
	$data = $_POST['data'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif','image/jpg'];
    if (isset($_FILES['imagen']) && in_array($_FILES['imagen']['type'], $allowed_types)) {
		$path = "irudiak/" . uniqid() . "_" . basename($_FILES['imagen']['name']);
		if (move_uploaded_file($_FILES['imagen']['tmp_name'], $path)) {
			$data['imagen'] = $path;
		}else{
			$data['imagen'] = null;
		}
	}

	if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
		$error['email'] = "Email-aren formatua ez da egokia";
		$hasError = true;
	}
	if (empty($data['firstname'])) {
		$error['firstname'] = "Izena ezin da hutsik egon.";
		$hasError = true;
	}
	if (empty($data['lastname'])) {
		$error['lastname'] = "Abizena ezin da hutsik egon.";
		$hasError = true;
	}
	if (empty($data['city'])) {
		$error['city'] = "Hiria ezin da hutsik egon.";
		$hasError = true;
	}
	if (empty($data['stateProv'])) {
		$error['stateProv'] = "Lurraldea ezin da hutsik egon.";
		$hasError = true;
	}
	if (empty($data['country'])) {
		$error['country'] = "Herrialdea ezin da hutsik egon.";
		$hasError = true;
	}
	if (empty($data['postakodea'])) {
		$error['postakodea'] = "Postakodea ezin da hutsik egon.";
		$hasError = true;
	}
	if (empty($data['telephone'])) {
		$error['telephone'] = "Telefonoa ezin da hutsik egon.";
		$hasError = true;
	}
	if(strlen($data['telephone']) != 9){
		$error['telephone'] = "Telefonoa 9 karaktere izan behar ditu.";
		$hasError = true;
	}
	if (strlen($data['password']) < 6) {
		$error['password'] = "Pasahitzak 6 karaktere izan behar ditu gutxienez";
		$hasError = true;
	}
	if ($data['password'] !== $data['password2']) {
		$error['password'] = "Sartutako pasahitzak ez dira berdinak";
		$hasError = true;
	}

	if(!$hasError){
		$stmt = $conx->prepare("INSERT INTO users (username, password, izena, abizena, hiria, lurraldea, herrialdea, postakodea, telefonoa, irudia) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$password_hashed = password_hash($data['password'], PASSWORD_DEFAULT);
		$stmt->bind_param("ssssssssss", $data['email'], $password_hashed, $data['firstname'], $data['lastname'], $data['city'], 
		$data['stateProv'], $data['country'], $data['postakodea'], $data['telephone'], $data['imagen']);
		if (!$stmt->execute()) {
			die('Error: ' . $stmt->error);
		} else {
			header("Location: index.php");
			exit();
		}
	}
}
?>
	<div class="content">
	<br/>
	<div class="register">

		<h2>Erregistroa egin</h2>
		<br/>

		<b>Sartu informazioa.</b>
		<br/>
		<form action="<?php echo $_SERVER['PHP_SELF']."?action=register"; ?>" method="POST" enctype="multipart/form-data">
			<p>
				<label>Email/username: </label>
				<input type="text" name="data[email]" value="<?php echo htmlspecialchars($data['email']); ?>" />
				<?php if ($error['email']) echo '<p style="color:red;">', $error['email']; ?>
			<p>
			<p>
				<label>Izena: </label>
				<input type="text" name="data[firstname]" value="<?php echo htmlspecialchars($data['firstname']); ?>" />
				<?php if ($error['firstname']) echo '<p style="color:red;">', $error['firstname']; ?>
			<p>
			<p>
				<label>Abizena: </label>
				<input type="text" name="data[lastname]" value="<?php echo htmlspecialchars($data['lastname']); ?>" />
				<?php if ($error['lastname']) echo '<p style="color:red;">', $error['lastname']; ?>
			<p>
			<p>
				<label>Hiria: </label>
				<input type="text" name="data[city]" value="<?php echo htmlspecialchars($data['city']); ?>" />
				<?php if ($error['city']) echo '<p style="color:red;">', $error['city']; ?>
			<p>
			<p>
				<label>Lurraldea: </label>
				<input type="text" name="data[stateProv]" value="<?php echo htmlspecialchars($data['stateProv']); ?>" />
				<?php if ($error['stateProv']) echo '<p style="color:red;">', $error['stateProv']; ?>
			<p>
			<p>
				<label>Herrialdea: </label>
				<input type="text" name="data[country]" value="<?php echo htmlspecialchars($data['country']); ?>" />
				<?php if ($error['country']) echo '<p style="color:red;">', $error['country']; ?>
			<p>
			<p>
				<label>Postakodea: </label>
				<input type="number" name="data[postakodea]" value="<?php echo htmlspecialchars($data['postakodea']); ?>" />
				<?php if ($error['postakodea']) echo '<p style="color:red;">', $error['postakodea']; ?>
			<p>
			<p>
				<label>Telefonoa: </label>
				<input type="number" name="data[telephone]" value="<?php echo htmlspecialchars($data['telephone']); ?>" />
				<?php if ($error['telephone']) echo '<p style="color:red;">', $error['telephone']; ?>
			<p>
			<p>
				<label>Pasahitza: </label>
				<input type="password" name="data[password]" value="<?php echo htmlspecialchars($data['password']); ?>" />
				<?php if ($error['password']) echo '<p style="color:red;">', $error['password']; ?>
			<p>
            <p>
                <label>Pasahitza errepikatu: </label>
                <input type="password" name="data[password2]" value="<?php echo htmlspecialchars($data['password2']); ?>" />
            <p>
            <p>
                <label>Irudia aukeratu:</label>
                <input name="imagen" type="file" />
            <p>
			<p>
				<input type="reset" name="data[clear]" value="Clear" class="button"/>
				<input type="submit" name="data[submit]" value="Submit" class="button marL10"/>
			<p>
		</form>
	</div>
</div>

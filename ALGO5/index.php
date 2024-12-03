<?php require_once 'core/dbConfig.php'; ?>
<?php require_once 'core/models.php'; ?>
<?php  
if (!isset($_SESSION['username'])) {
	header("Location: login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="styles/styles.css">
	<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
</head>
<body>
	<?php include 'navbar.php'; ?>

	<!-- Photo Upload Form -->
	<div class="insertPhotoForm" style="display: flex; justify-content: center;">
		<form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
			<p>
				<label for="#">Description</label>
				<input type="text" name="photoDescription">
			</p>
			<p>
				<label for="#">Photo Upload</label>
				<input type="file" name="image">
				<input type="submit" name="insertPhotoBtn" style="margin-top: 10px;">
			</p>
		</form>
	</div>

	<!-- Create Album Form -->
	<div class="createAlbumForm" style="display: flex; justify-content: center; margin-top: 30px;">
		<form action="core/handleForms.php" method="POST">
			<p>
				<label for="#">Album Name</label>
				<input type="text" name="albumName" required>
			</p>
			<input type="submit" name="createAlbumBtn" value="Create Album" style="margin-top: 10px;">
		</form>
	</div>

	<!-- Display All Albums -->
	<h3>My Albums</h3>
	<?php
		// Fetch all albums created by the logged-in user
		$albums = getAllAlbums($pdo, $_SESSION['username']);
		foreach ($albums as $album) {
			echo "<p><a href='album.php?album_id=" . $album['album_id'] . "'>" . $album['album_name'] . "</a></p>";
		}
	?>

	<!-- Display All Photos -->
	<?php $getAllPhotos = getAllPhotos($pdo); ?>
	<?php foreach ($getAllPhotos as $row) { ?>
	<div class="images" style="display: flex; justify-content: center; margin-top: 25px;">
		<div class="photoContainer" style="background-color: ghostwhite; border-style: solid; border-color: gray;width: 50%;">

			<img src="images/<?php echo $row['photo_name']; ?>" alt="" style="width: 100%;">

			<div class="photoDescription" style="padding:25px;">
				<a href="profile.php?username=<?php echo $row['username']; ?>"><h2><?php echo $row['username']; ?></h2></a>
				<p><i><?php echo $row['date_added']; ?></i></p>
				<h4><?php echo $row['description']; ?></h4>

				<?php if ($_SESSION['username'] == $row['username']) { ?>
					<a href="editphoto.php?photo_id=<?php echo $row['photo_id']; ?>" style="float: right;"> Edit </a>
					<br>
					<br>
					<a href="deletephoto.php?photo_id=<?php echo $row['photo_id']; ?>" style="float: right;"> Delete</a>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php } ?>
</body>
</html>

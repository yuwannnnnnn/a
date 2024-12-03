<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get album details using album_id from URL
if (isset($_GET['album_id'])) {
    $albumId = $_GET['album_id'];
    $album = getAlbumById($pdo, $albumId);  // Fetch album details
} else {
    // Redirect if album_id is not provided
    header("Location: index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Album - <?php echo $album['album_name']; ?></title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <h2>Album: <?php echo $album['album_name']; ?></h2>

    <!-- Upload Photo Section -->
    <div class="uploadSection" style="display: flex; justify-content: center;">
        <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
            <p>
                <label for="photoDescription">Description</label>
                <input type="text" name="photoDescription" id="photoDescription" required>
            </p>
            <p>
                <label for="image">Photo Upload</label>
                <input type="file" name="image" id="image" required>
                <input type="hidden" name="album_id" value="<?php echo $albumId; ?>">  <!-- Pass album_id -->
            </p>
            <button type="submit" name="insertPhotoBtn" style="margin-top: 10px;">Upload Photo</button>
        </form>
    </div>

    <!-- Display all photos in this album (optional) -->
    <div class="albumPhotos" style="display: flex; justify-content: center; flex-wrap: wrap; margin-top: 30px;">
        <?php
        // Fetch and display the images in this album (without relating them to the database)
        $albumPhotos = glob("images/*.{jpg,png,gif}", GLOB_BRACE);  // Get all images in the folder
        foreach ($albumPhotos as $photo) {
            echo "<div style='margin: 5px;'>
                    <img src='$photo' alt='Photo' style='width: 150px; height: 150px;'>
                  </div>";
        }
        ?>
    </div>
</body>
</html>

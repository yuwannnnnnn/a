<?php  
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_POST['insertNewUserBtn'])) {
    $username = trim($_POST['username']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (!empty($username) && !empty($first_name) && !empty($last_name) && !empty($password) && !empty($confirm_password)) {
        if ($password == $confirm_password) {
            $insertQuery = insertNewUser($pdo, $username, $first_name, $last_name, password_hash($password, PASSWORD_DEFAULT));
            $_SESSION['message'] = $insertQuery['message'];

            if ($insertQuery['status'] == '200') {
                $_SESSION['message'] = $insertQuery['message'];
                $_SESSION['status'] = $insertQuery['status'];
                header("Location: ../login.php");
            } else {
                $_SESSION['message'] = $insertQuery['message'];
                $_SESSION['status'] = $insertQuery['status'];
                header("Location: ../register.php");
            }
        } else {
            $_SESSION['message'] = "Please make sure both passwords are equal";
            $_SESSION['status'] = '400';
            header("Location: ../register.php");
        }
    } else {
        $_SESSION['message'] = "Please make sure there are no empty input fields";
        $_SESSION['status'] = '400';
        header("Location: ../register.php");
    }
}

if (isset($_POST['loginUserBtn'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $loginQuery = checkIfUserExists($pdo, $username);
        $userIDFromDB = $loginQuery['userInfoArray']['user_id'];
        $usernameFromDB = $loginQuery['userInfoArray']['username'];
        $passwordFromDB = $loginQuery['userInfoArray']['password'];

        if (password_verify($password, $passwordFromDB)) {
            $_SESSION['user_id'] = $userIDFromDB;
            $_SESSION['username'] = $usernameFromDB;
            header("Location: ../index.php");
        } else {
            $_SESSION['message'] = "Username/password invalid";
            $_SESSION['status'] = "400";
            header("Location: ../login.php");
        }
    } else {
        $_SESSION['message'] = "Please make sure there are no empty input fields";
        $_SESSION['status'] = '400';
        header("Location: ../register.php");
    }
}

if (isset($_GET['logoutUserBtn'])) {
    unset($_SESSION['user_id']);
    unset($_SESSION['username']);
    header("Location: ../login.php");
}

if (isset($_POST['insertPhotoBtn'])) {
    $description = $_POST['photoDescription'];
    $fileName = $_FILES['image']['name'];
    $tempFileName = $_FILES['image']['tmp_name'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $uniqueID = sha1(md5(rand(1, 9999999)));
    $imageName = $uniqueID . "." . $fileExtension;

    // Ensure the target directory exists
    $folder = __DIR__ . "/../images/";
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    // Validate permissions
    if (!is_writable($folder)) {
        $_SESSION['message'] = "Error: Upload directory is not writable.";
        $_SESSION['status'] = "500";
        header("Location: ../index.php");
        exit();
    }

    // Save image record to database
    $saveImgToDb = insertPhoto($pdo, $imageName, $_SESSION['username'], $description);

    if ($saveImgToDb) {
        // Move file to the specified path
        if (is_uploaded_file($tempFileName)) {
            $targetPath = $folder . $imageName;
            if (move_uploaded_file($tempFileName, $targetPath)) {
                header("Location: ../index.php");
            } else {
                $_SESSION['message'] = "Failed to move uploaded file.";
                $_SESSION['status'] = "500";
                header("Location: ../index.php");
            }
        } else {
            $_SESSION['message'] = "No file uploaded or file upload error.";
            $_SESSION['status'] = "400";
            header("Location: ../index.php");
        }
    } else {
        $_SESSION['message'] = "Error saving image to the database.";
        $_SESSION['status'] = "500";
        header("Location: ../index.php");
    }
}

if (isset($_POST['deletePhotoBtn'])) {
    $photo_name = $_POST['photo_name'];
    $photo_id = $_POST['photo_id'];
    $deletePhoto = deletePhoto($pdo, $photo_id);

    if ($deletePhoto) {
        $filePath = "../images/" . $photo_name;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        header("Location: ../index.php");
    } else {
        $_SESSION['message'] = "Failed to delete the photo.";
        $_SESSION['status'] = "500";
        header("Location: ../index.php");
    }
}

if (isset($_POST['createAlbumBtn'])) {
    // Get the album name from the form
    $albumName = $_POST['albumName'];

    // Make sure the album name is not empty
    if (!empty($albumName)) {
        // Insert the album into the database
        $stmt = $pdo->prepare("INSERT INTO albums (album_name, username) VALUES (?, ?)");
        $stmt->execute([$albumName, $_SESSION['username']]);
        
        // Redirect to index page or display success message
        header("Location: index.php");
    } else {
        echo "Please enter a valid album name.";
    }
}
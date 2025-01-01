<?php
if (isset($_GET['edit_profile'])) {
    $user_session_name = $_SESSION['username'];
    $select_query = "SELECT * FROM `user_table` WHERE UserName = '$user_session_name'";
    $result_query = mysqli_query($con, $select_query);

    // Fetch data from DB
    $row_fetch = mysqli_fetch_assoc($result_query);
    $UserID = $row_fetch['UserID'];
    $UserName = $row_fetch['UserName'];
    $UserEmail = $row_fetch['UserEmail'];
    $UserAddress = $row_fetch['UserAddress'];
    $UserMobile = $row_fetch['UserMobile'];
    $UserImage = !empty($row_fetch['UserImage']) ? $row_fetch['UserImage'] : 'default.jpg';

    // Check if edit mode is active
    $is_edit_mode = isset($_GET['edit']) && $_GET['edit'] === 'true';
}

// Update Profile
if (isset($_POST['user_update'])) {
    $update_ID = $UserID;
    $newUserName = $_POST['user_username'];
    $newUserEmail = $_POST['user_email'];
    $UserAddress = $_POST['user_address'];
    $UserMobile = $_POST['user_contact'];
    $userprofileimage = $_FILES['user_image']['name'];
    $userprofileimage_tmp = $_FILES['user_image']['tmp_name'];

    if (!empty($userprofileimage)) {
        move_uploaded_file($userprofileimage_tmp, "./users_images/$userprofileimage");
    } else {
        $userprofileimage = $UserImage; // Keep current image if no new image is uploaded
    }

    // Check if the username or email has been changed
    $need_relogin = false;
    if ($newUserName !== $UserName || $newUserEmail !== $UserEmail) {
        $need_relogin = true;
    }

    // Update the database with new values
    $update_data = "UPDATE `user_table` SET 
                    UserName = '$newUserName', 
                    UserEmail = '$newUserEmail', 
                    UserImage = '$userprofileimage', 
                    UserAddress = '$UserAddress', 
                    UserMobile = '$UserMobile' 
                    WHERE UserID = '$update_ID'";

    $result_query_update = mysqli_query($con, $update_data);

    if ($result_query_update) {
        echo "<script>alert('Profile updated successfully');</script>";
        if ($need_relogin) {
            // If username or email changed, force logout and prompt re-login
            session_unset();
            session_destroy();
            echo "<script>window.open('user_login.php', '_self');</script>";
        } else {
            // Otherwise, just refresh the profile without logging out
            echo "<script>window.location.href = 'profile.php?edit_profile';</script>";
        }
    }
}
?>

<div class="container mt-2">
    <!-- Display User Info -->
    <?php if (!$is_edit_mode): ?>
        <h3 class="mb-4 text-center" style="font-family: 'Poppins', sans-serif; font-weight: bold;">User Profile</h3>
        <div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm" style="border-radius: 10px; background-color: #f9f9f9;">
                <div class="card-body text-center">
                    <!-- Profile Picture -->
                    <div class="mb-4">
                        <img src="./users_images/<?php echo $UserImage; ?>" alt="Profile Picture" 
                             class="rounded-circle shadow-sm" 
                             style="width: 150px; height: 150px; object-fit: cover; border: 5px solid #CCAB8C;">
                    </div>
                    <!-- User Info -->
                    <h4 class="fw-bold" style="color: #644535;"><?php echo $UserName; ?></h4>
                    <p class="text-muted mb-1">Email: <span style="color: #333;"><?php echo $UserEmail; ?></span></p>
                    <p class="text-muted mb-1">Address: <span style="color: #333;"><?php echo $UserAddress; ?></span></p>
                    <p class="text-muted mb-3">Mobile: <span style="color: #333;"><?php echo $UserMobile; ?></span></p>
                    <!-- Edit Button -->
                    <a href="profile.php?edit_profile&edit=true" class="btn px-4 py-2" 
                       style="background-color: #CCAB8C; color: white; border-radius: 20px;">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php else: ?>
        <!-- Edit Form -->
        <div class="container mt-2">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm" style="border-radius: 10px; background-color: #f9f9f9; padding: 20px;">
                        <h3 class="text-center mb-4" style="font-family: 'Poppins', sans-serif; font-weight: bold;">Edit Profile</h3>
                        <form action="profile.php?edit_profile" method="post" enctype="multipart/form-data">
                            <!-- Username -->
                            <div class="form-group mb-4">
                                <label for="user_username" class="form-label fw-bold" style="color: #644535;">Username</label>
                                <input type="text" id="user_username" class="form-control" 
                                    value="<?php echo $UserName; ?>" name="user_username"
                                    style="border-radius: 5px; border: 1px solid #ccc; padding: 10px;">
                            </div>
                            <!-- Email -->
                            <div class="form-group mb-4">
                                <label for="user_email" class="form-label fw-bold" style="color: #644535;">Email</label>
                                <input type="email" id="user_email" class="form-control" 
                                    value="<?php echo $UserEmail; ?>" name="user_email"
                                    style="border-radius: 5px; border: 1px solid #ccc; padding: 10px;">
                            </div>
                            <!-- Profile Image -->
                            <div class="form-group mb-4">
                                <label class="form-label fw-bold" style="color: #644535;">Profile Picture</label>
                                <div class="d-flex align-items-center">
                                    <input type="file" class="form-control me-3" name="user_image" 
                                        style="border-radius: 5px; border: 1px solid #ccc; padding: 10px;">
                                    <img src="./users_images/<?php echo $UserImage; ?>" alt="Current Profile Picture" 
                                        class="rounded-circle shadow-sm" 
                                        style="width: 60px; height: 60px; object-fit: cover; border: 2px solid #CCAB8C;">
                                </div>
                            </div>
                            <!-- Address -->
                            <div class="form-group mb-4">
                                <label for="user_address" class="form-label fw-bold" style="color: #644535;">Address</label>
                                <input type="text" id="user_address" class="form-control" 
                                    value="<?php echo $UserAddress; ?>" name="user_address"
                                    style="border-radius: 5px; border: 1px solid #ccc; padding: 10px;">
                            </div>
                            <!-- Mobile -->
                            <div class="form-group mb-4">
                                <label for="user_contact" class="form-label fw-bold" style="color: #644535;">Mobile</label>
                                <input type="text" id="user_contact" class="form-control" 
                                    value="<?php echo $UserMobile; ?>" name="user_contact"
                                    style="border-radius: 5px; border: 1px solid #ccc; padding: 10px;">
                            </div>
                            <!-- Buttons -->
                            <div class="d-flex justify-content-center mt-4">
                                <input type="submit" value="Update" 
                                    class="btn px-4 me-3" 
                                    style="background-color: #CCAB8C; color: white; border: none; border-radius: 5px;" 
                                    name="user_update">
                                <a href="profile.php?edit_profile" 
                                class="btn btn-secondary px-4" 
                                style="border-radius: 5px;">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Stop direct access
if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])){
  header("Location: ../../");
}

// Get hashed old password
$statement = $dbh->prepare('SELECT * FROM users WHERE id=1');
$statement->execute();
$result = $statement->fetch();

if (isset($_POST['changePassword'])) {
  if (isset($_POST['token']) && $_POST['token'] == $_SESSION['oldToken']) {
    // Check if the old password is correct
    if (sha1($_POST['oldPassword']) == $result['password']) {
      // Password correc, check new passwords match
      if ($_POST['newPassword1'] == $_POST['newPassword2']) {
        // The new passwords match, check the length
        if (strlen($_POST['newPassword1']) >= 6) {
          // The password is long enough, update it
          $passwordHash = sha1($_POST['newPassword1']);
          $statement = $dbh->prepare('UPDATE users SET password=? WHERE id=1');
          $statement->execute([$passwordHash]);
          $passError = LANG_PASSWORD_CHANGED;
              } else {
          $passError = LANG_ERROR_PASSWORD_LEN;
        }
      } else {
        $passError = LANG_ERROR_PASSWORD_MATCH;
      }
    } else {
      $passError = LANG_ERROR_PASSWORD_WRONG;
    }
  }
}

if (isset($_POST['updateDetails'])) {
  if (isset($_POST['token'])) {
    $firstName = htmlspecialchars($_POST['firstName'], ENT_QUOTES);
    $lastName = htmlspecialchars($_POST['lastName'], ENT_QUOTES);
    $email = $_POST['email'];
    if (sha1($_POST['confirmPassword']) == $result['password']) {
      // Password correct, continue
      if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        // Email correct, continue
        $statement = $dbh->prepare('UPDATE users SET first_name=?, last_name=?, email=? WHERE id=1');
        $statement->execute([$_POST['firstName'], $_POST['lastName'], $_POST['email']]);
        $updateError = LANG_DETAILS_UPDATED;
      } else {
        $updateError = LANG_ERROR_EMAIL;
      }
    } else {
      $updateError = LANG_ERROR_PASSWORD_WRONG;
    }
  }
}
?>

<form method="post" autocomplete="off">
  <h2><?php echo LANG_CHANGE_PASSWORD; ?></h2>
  <?php if (isset($passError)) echo '<div class="error">' . $passError . '</div>'; ?>
  <label for="oldPassword"><?php echo LANG_OLD_PASSWORD; ?></label>
  <input type="password" name="oldPassword">
  <label for="newPassword1"><?php echo LANG_NEW_PASSWORD; ?></label>
  <input type="password" name="newPassword1">
  <label for="newPassword2"><?php echo LANG_REPEAT_PASSWORD; ?></label>
  <input type="password" name="newPassword2">
  <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
  <input type="submit" name="changePassword" value="Submit">
</form>

<form method="post" autocomplete="off">
  <h2><?php echo LANG_UPDATE_DETAILS; ?></h2>
  <?php if (isset($updateError)) echo '<div class="error">' . $updateError . '</div>'; ?>
  <label for="firstName"><?php echo LANG_FIRST_NAME; ?></label>
  <input type="text" name="firstName" value="<?php if(isset($firstName)) echo $firstName; else echo $result['first_name']; ?>">
  <label for="lastName"><?php echo LANG_LAST_NAME; ?></label>
  <input type="text" name="lastName" value="<?php if(isset($lastName)) echo $lastName; else echo $result['last_name']; ?>">
  <label for="email"><?php echo LANG_EMAIL; ?></label>
  <input type="email" name="email" value="<?php if(isset($email)) echo $email; else echo $result['email']; ?>">
  <label for="confirmPassword"><?php echo LANG_CONFIRM_PASSWORD; ?></label>
  <input type="password" name="confirmPassword">
  <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
  <input type="submit" name="updateDetails" value="<?php echo LANG_SUBMIT; ?>">
</form>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Study With Me - Login</title>
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" 
    crossorigin="anonymous">
</head>
<body>
<div class="container my-5">

  <h1 class="text-center">StudyBuddy</h1>
  <p class="text-center">Sign in or create a new account</p>

  <?php
    if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
        echo '<div class="alert alert-danger">';
        foreach ($_SESSION['errors'] as $err) {
            echo "<p>$err</p>";
        }
        echo '</div>';
        unset($_SESSION['errors']);
    }

    if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
      echo '<div class="alert alert-danger">';
      $message= $_SESSION['success'];
      echo "<p>$message</p>";
      echo '</div>';
      unset($_SESSION['success']);
  }

  ?>

  <form 
    action="index.php?command=login" 
    method="POST" 
    class="mx-auto my-4" 
    style="max-width: 400px;"
  >
    <!-- sign-in logic -->

    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input 
        type="text" 
        class="form-control" 
        id="username" 
        name="username" 
        placeholder="janedoe"
        required
      >
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input 
        type="password" 
        class="form-control" 
        id="password" 
        name="password" 
        required
      >
    </div>

    <button type="submit" class="btn btn-primary w-100">
      Login
    </button>
  </form>
  <a href="index.php?command=create_profile" class="btn">
    New to StudyBuddy? Sign up here
</a>
</div>

<script 
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
  integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" 
  crossorigin="anonymous">
</script>
</body>
</html>

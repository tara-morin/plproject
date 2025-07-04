<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up for StudyBuddy</title>
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" 
    crossorigin="anonymous">
</head>
<body>
<div class="container my-5">

  <h1 class="text-center">StudyBuddy</h1>
  <p class="text-center">Sign Up for StudyBuddy</p>
  
  <?php
    // Php code block to display any errors from a previous profile creation
    if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
        echo '<div class="alert alert-danger">';
        foreach ($_SESSION['errors'] as $err) {
            echo "<p>$err</p>";
        }
        echo '</div>';
        // Clear them out so they don't persist
        unset($_SESSION['errors']);
    }
  ?>

  <form 
    action="index.php?command=create_profile" 
    method="POST" 
    class="mx-auto my-4" 
    style="max-width: 400px;"
  >
    <!-- Form for creating a profile, with fields for name, username, 2 for password, and user status in the app. -->

    <div class="mb-3">
      <label for="name" class="form-label">Full Name</label>
      <input 
        type="text" 
        class="form-control" 
        id="name" 
        name="name" 
        placeholder="Jane Doe"
        required
      >
    </div>

    <div class="mb-3">
      <label for="username" class="form-label">Username (min 6 chars)</label>
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

    <div class="mb-3">
      <label for="conf_password" class="form-label">Confirm your Password</label>
      <input 
        type="password"
        class="form-control" 
        id="conf_password" 
        name="conf_password" 
        required
      >
    </div>

    <div class="mb-3">
      <label for="status" class="form-label">Using StudyBuddy for</label>
      <select class="form-select" id="status" name="status" required>
        <option value="">-- Select One --</option>
        <option value="school">School</option>
        <option value="work">Work</option>
      </select>
    </div>
    <!--submits form and will redirect to login screen-->
    <button type="submit" class="btn btn-primary w-100">
      Sign Up
    </button>
  </form>
</div>

<script 
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
  integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" 
  crossorigin="anonymous">
</script>
</body>
</html>

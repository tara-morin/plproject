<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Study With Me - Welcome</title>
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" 
    crossorigin="anonymous">
</head>
<body>
<div class="container mt-5">
  <h1 class="text-center">Welcome to Study With Me</h1>

  <?php
  // Display errors if any
  if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
      echo '<div class="alert alert-danger">';
      foreach ($_SESSION['errors'] as $err) {
          echo "<p>$err</p>";
      }
      echo '</div>';
      unset($_SESSION['errors']);
  }
  ?>

  <!-- Sign-up / Login Form -->
  <form action="index.php?command=create_profile" method="POST" class="mt-4">
    <div class="mb-3">
      <label for="name" class="form-label">Full Name</label>
      <input type="text" class="form-control" id="name" name="name" required>
    </div>

    <div class="mb-3">
      <label for="username" class="form-label">Username (min 6 characters)</label>
      <input type="text" class="form-control" id="username" name="username" required>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>

    <div class="mb-3">
      <label for="status" class="form-label">I am using StudyBuddy for:</label>
      <select class="form-select" id="status" name="status" required>
        <option value="">-- Select One --</option>
        <option value="school">School</option>
        <option value="work">Work</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Sign up / Login</button>
  </form>
</div>
</body>
</html>

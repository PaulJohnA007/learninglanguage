  <?php
  function handleLogin($username, $password, $conn) {
      $stmt = $conn->prepare("SELECT id, username, password, grade_level, user_type FROM users WHERE username = ?");
      $stmt->bind_param("s", $username);
      $stmt->execute();
      $result = $stmt->get_result();
    
      if($result->num_rows > 0) {
          $user = $result->fetch_assoc();
          if($password === $user['password']) {
              $_SESSION['user_id'] = $user['id'];
              $_SESSION['username'] = $user['username'];
              $_SESSION['grade_level'] = $user['grade_level'];
              $_SESSION['user_type'] = $user['user_type'];
              $_SESSION['login_start_time'] = time();
            
              return $user;
          }
      }
      return false;
  }


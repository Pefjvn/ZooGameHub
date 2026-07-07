<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Info Form</title>
</head>
<body>

  <form id="userForm" action="submit.php" method="POST">
    <label for="name">Name:</label><br>
    <input type="text" id="name" name="name"><br><br>

    <label for="age">Age:</label><br>
    <input type="number" id="age" name="age"><br><br>

    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email"><br><br>

    <label for="phone">Phone:</label><br>
    <input type="tel" id="phone" name="phone"><br><br>

    <button type="submit">Send</button>
  </form>

</body>
</html>
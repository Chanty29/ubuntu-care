   <?php
   $conn = new mysqli("127.0.0.1", "root", "", "ubuntu_care", 3307);
   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
   echo "Ubuntu Care is connected to the database.";
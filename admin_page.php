<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="css/admin.css"> <!-- Link to the external CSS file -->

    <?php
    session_start();
    if (isset($_SESSION['SESSION_EMAIL'])) {
        // Set a timestamp for the session start
        if (!isset($_SESSION['SESSION_START_TIME'])) {
            $_SESSION['SESSION_START_TIME'] = time();
        } else {
            // Check if the session has exceeded the timeout
            $sessionTimeout = 9999; // 15 seconds
            if (time() - $_SESSION['SESSION_START_TIME'] > $sessionTimeout) {
                // Session has expired, destroy it
                session_unset();
                session_destroy();
                header("Location: session-timeout-alert.php");
                die();
            }
        }
    } else {
        $msg = "<div class='alert alert-danger'>You are not logged in. Please log in.</div>";
        header("Location: index.php");
        die();
    }

    include 'config.php';
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='{$_SESSION['SESSION_EMAIL']}'");

    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);

        echo "<div class='nav-bar'>";
        echo "<img src='images/game-no-bg.png' width='100px' style='margin-right: 1200px;'>"; // Adjust margin as needed
        echo "<div class='user-info'>";
        echo "<span>" . $row['name'] . "</span> <a href='logout.php'>Logout</a>";
        echo "</div>";
        echo "</div>";
    }
    ?>
</head>

<body>
    <div>
        <div class="tab" onclick="openTab('usersTab')">Users</div>
        <div class="tab" onclick="openTab('gamesTab')">Games</div>
    </div>

    <div id="usersTab" class="tab-content active">
        <h2>Registered Users</h2>
        <?php
        // Fetch registered users from the database
        $result = mysqli_query($conn, "SELECT * FROM users");

        if (mysqli_num_rows($result) > 0) {
            echo '<table>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>';

            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>
                <td>' . $row['id'] . '</td>
                <td>' . $row['name'] . '</td>
                <td>' . $row['email'] . '</td>
                <td>' . $row['role'] . '</td>
                <td><button class="remove-btn" onclick="removeUser(' . $row['id'] . ')">Remove</button></td>
            </tr>';
            }

            echo '
        </table>';
        } else {
            echo '<p>No registered users found.</p>';
        }
        ?>
    </div>

    <div id="gamesTab" class="tab-content">
        <h2>Registered Games</h2>
        <button onclick="openAddGameModal()" style="margin-left: 20px;" class="add-button">Add Game</button>

        <?php
        $gamesQuery = mysqli_query($conn, "SELECT * FROM games");

        if (mysqli_num_rows($gamesQuery) > 0) {
            echo '<table>
            <tr>
                <th>Game Name</th>
                <th>Game Price</th>
                <th>Game Description</th>
                <th>Game Image</th>
                <th>Action</th> <!-- New column for the Remove button -->
            </tr>';

            while ($gameRow = mysqli_fetch_assoc($gamesQuery)) {
                echo '<tr>
                <td>' . $gameRow['game_name'] . '</td>
                <td>₱ ' . $gameRow['game_price'] . '</td>
                <td>' . $gameRow['game_description'] . '</td>
                <td><img src="' . $gameRow['game_image'] . '" alt="' . $gameRow['game_name'] . '" style="max-width: 100px;"></td>
                <td><button class="remove-btn" onclick="removeGame(' . $gameRow['game_id'] . ')">Remove</button></td> <!-- New Remove button -->
            </tr>';
            }

            echo '
        </table>';
        } else {
            echo '<p>No registered games found.</p>';
        }
        ?>
    </div>

    <?php
    // Include your configuration file and establish a database connection

    if (isset($_POST['add_game'])) {
        // Process the form submission to add a new game
        $gameName = mysqli_real_escape_string($conn, $_POST['game_name']);
        $gamePrice = mysqli_real_escape_string($conn, $_POST['game_price']);
        $gameDescription = mysqli_real_escape_string($conn, $_POST['game_description']);

        // Upload and save the game image
        $targetDirectory = "game-images/"; // Specify your target directory
        $targetFile = $targetDirectory . basename($_FILES["game_image"]["name"]);
        move_uploaded_file($_FILES["game_image"]["tmp_name"], $targetFile);

        // Insert the new game into the database
        $insertQuery = "INSERT INTO games (game_name, game_price, game_description, game_image) VALUES ('$gameName', '$gamePrice', '$gameDescription', '$targetFile')";
        mysqli_query($conn, $insertQuery);

        // Optionally, you can redirect to the same page after adding the game
        header("Location: admin_page.php");
        exit();
    }

    // Close the database connection
    mysqli_close($conn);
    ?>

    <div id="addGameModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddGameModal()">&times;</span>
            <form action="" method="post" enctype="multipart/form-data">
                <h2 style="text-align: center; margin-bottom: 10px; margin-right: 20px;">Add New Game</h2>
                <label for="game_name">Game Name:</label>
                <input type="text" id="game_name" name="game_name" required>
                <br>
                <label for="game_price">Game Price:</label>
                <input type="text" id="game_price" name="game_price" required>
                <br>
                <label for="game_description">Game Description:</label>
                <textarea id="game_description" name="game_description" rows="4" required></textarea>
                <br>
                <label for="game_image">Game Image:</label>
                <input type="file" id="game_image" name="game_image" accept="image/*" required>

                <button type="submit" name="add_game" class="add-button">Add Game</button>
            </form>
        </div>
    </div>

    <script>
        function removeUser(id) {
            if (confirm('Are you sure you want to remove this user?')) {
                // Make an AJAX request to delete the user
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'delete_user.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert('User removed successfully.');
                        // Refresh the page or update the UI as needed
                        window.location.reload(true);
                    }
                };
                xhr.send('id=' + id);
            }
        }

        function removeGame(id) {
            if (confirm('Are you sure you want to remove this game?')) {
                // Make an AJAX request to delete the game
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'delete_game.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert('Game removed successfully.');
                        // Refresh the page or update the UI as needed
                        window.location.reload(true);
                    }
                };
                xhr.send('game_id=' + id);
            }
        }

        var addGameModal = document.getElementById('addGameModal');

        function openAddGameModal() {
            addGameModal.style.display = 'block';
        }

        function closeAddGameModal() {
            addGameModal.style.display = 'none';
        }

        // Close the modal if the user clicks outside of it
        window.onclick = function(event) {
            if (event.target === addGameModal) {
                addGameModal.style.display = 'none';
            }
        };

        function openTab(tabName) {
            var i, tabContent, tabLinks;
            tabContent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabContent.length; i++) {
                tabContent[i].classList.remove("active");
            }
            document.getElementById(tabName).classList.add("active");
        }
    </script>
</body>

</html>
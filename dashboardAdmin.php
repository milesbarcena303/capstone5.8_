<!DOCTYPE html>
<html lang="en">
<?php include("db_conn.php"); ?>
<?php
session_start();
if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);

    // Set a session timeout (e.g., 30 minutes)
    $sessionTimeout = 30 * 60; // 30 minutes in seconds
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $sessionTimeout) {
        session_unset();
        session_destroy();
        header("Location: loginAdmin.php"); // Redirect to login page
        exit;
    }
    $_SESSION['last_activity'] = time();
    

} else {
    header("Location: loginAdmin.php");
    exit;
}
?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://cvsu.edu.ph/wp-content/uploads/2018/01/CvSU-logo-trans.png" sizes="192x192">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <title>CVSU Admin Dashboard</title>
   
</head>
<body>
    <!-- User Settings Modal -->
    <div class="modal fade user-settings-modal" id="userSettingsModal" tabindex="-1" aria-labelledby="userSettingsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userSettingsModalLabel">User Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <!-- Add your user settings form or content here -->
                    <!-- For example, change password, update profile, etc. -->
                    <button id="darkModeToggle" class="btn btn-primary">Toggle Dark Mode</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Training console within a modal -->
    <div class="modal fade" id="trainingConsoleModal" tabindex="-1" aria-labelledby="trainingConsoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trainingConsoleModalLabel">Training Console</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <button id="trainButton" class="btn btn-success btn-md mb-4" type="submit" name="train_chatbot">
                        Train Chatbot
                    </button>
                </form>
                <div id="training-console" class="text-left">
                    <?php
                    if (isset($_POST['train_chatbot'])) {
                        try {
                            // Execute the Python training script and capture output and errors
                            $output = shell_exec("python train.py 2>&1");

                            // Check if there was an error
                            if ($output === null) {
                                throw new Exception("Command execution failed");
                            }

                            // Display the training progress or logs (including errors)
                            echo '<div class="container mt-4">';
                            echo '<div class="row justify-content-center">';
                            echo '<div class="col-md-10">';
                            echo '<pre>' . $output . '</pre>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        } catch (Exception $e) {
                            // Handle the exception (e.g., display an error message)
                            echo '<div class="container mt-4">';
                            echo '<div class="row justify-content-center">';
                            echo '<div class="col-md-10">';
                            echo '<div class="alert alert-danger" role="alert">';
                            echo 'An error occurred: ' . $e->getMessage();
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Add Intent within a modal -->
<div class="modal fade" id="addIntentModal" tabindex="-1" aria-labelledby="addIntentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addIntentModalLabel">Add New Intent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="process_add_intent.php" method="POST">
                    <div class="mb-3">
                        <label for="tag" class="form-label">Tag:</label>
                        <input type="text" class="form-control" id="tag" name="tag" required>
                    </div>

                    <div class="mb-3">
                        <label for="patterns" class="form-label">Patterns:</label>
                        <div id="patternsContainer">
                        <textarea class="form-control" name="patterns[]" rows="2" required></textarea>
                        </div>
                        <button type="button" class="btn btn-primary mt-2" onclick="addPattern()">Add Pattern</button>
                    </div>

                    <div class="mb-3">
                        <label for="responses" class="form-label">Responses:</label>
                        <div id="responsesContainer">
                            <textarea class="form-control" name="responses[]" rows="3" required></textarea>
                        </div>
                        <button type="button" class="btn btn-primary mt-2" onclick="addResponse()">Add Response</button>
                    </div>

                    <div class="text-center">
                        <button class="btn btn-success" type="submit">Add Intent</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Feedback within a modal -->
<div class="modal fade" id="viewTableModal" tabindex="-1" aria-labelledby="viewTableModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTableModalLabel">Feedback Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $result = mysqli_query($conn, "SELECT * FROM test_db.feedback");

                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td>" . $row['date'] . "</td>";
                                echo "<td>" . $row['message'] . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>




    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-transparent text-dark" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase border-bottom">
                <img src="static/images/CvSU-logo-trans.png" alt="image" width="50" height="40"> CVSU
            </div>
            <div class="list-group list-group-flush my-3">
                <a href="dashboardAdmin.php" class="list-group-item list-group-item-action bg-transparent second-text second-text active">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <button class="list-group-item list-group-item-action bg-transparent second-text active"
                    data-bs-toggle="modal" data-bs-target="#addIntentModal">
                    <i class="fas fa-project-diagram me-2"></i> Add Response
                </button>
                <button class="list-group-item list-group-item-action bg-transparent second-text active"
                    data-bs-toggle="modal" data-bs-target="#trainingConsoleModal">
                    <i class="fas fa-brain me-2"></i> Train
                </button>
                <a href="#" class="list-group-item list-group-item-action bg-transparent second-text active"
                    data-bs-toggle="modal" data-bs-target="#viewTableModal">
                    <i class="fas fa-paperclip me-2"></i> Feedback
                </a>
                <a href="logout.php" class="list-group-item list-group-item-action bg-transparent text-danger fw-bold"
                    onclick="return confirm('Are you sure you want to logout?');">
                    <i class="fas fa-power-off me-2"></i> Logout
                </a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->
        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-transparent py-4 px-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0">Admin Dashboard</h2>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle second-text fw-bold" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-2"></i> <?php echo $_SESSION['user_name']; ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal"
                                        data-bs-target="#userSettingsModal">Settings</a></li>
                                <li><a class="dropdown-item" href="logout.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- Page Content (below the navigation bar) -->
            
            <div class="row my-5">
    <h3 class="fs-4 mb-3 col-md-8" style= " padding-left: 30px;">Automatic Response System Dataset</h3>

    <!-- Smaller Search Bar with Search Icon (aligned to the right) -->
    <div class="col-sm-4 d-inline-flex justify-content-end" style= " padding-right: 30px">
        <div class="input-group">
            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search...">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
        </div>
    </div>
</div>
           <!-- table -->
<div class="col">
    <table class="table bg-white rounded shadow-sm table-hover">
        <thead>
            <tr>
                <th scope="col" width="50">#</th>
                <th scope="col">Tags</th>
                <th scope="col">Patterns</th>
                <th scope="col">Responses</th>
                <th scope="col" class="actions-column">Actions</th> 
            </tr>
        </thead>
        <tbody>
        <?php
        $json_data = file_get_contents("intents.json");
        $intents = json_decode($json_data, true);
        if (isset($intents['intents']) && is_array($intents['intents'])):
            $rowNumber = 1; // Initialize a counter variable
            foreach ($intents['intents'] as $intent):
        ?>
            <tr>
                <th scope="row"><?= $rowNumber ?></th>
                <td><?= $intent['tag'] ?></td>

                <!-- Display patterns as a table-like layout -->
                <td>
                    <table class="table table-bordered">
                        <?php foreach ($intent['patterns'] as $pattern): ?>
                            <tr><td><?= $pattern ?></td></tr>
                        <?php endforeach; ?>
                    </table>
                </td>

                <!-- Display responses as a table-like layout -->
                <td>
                    <table class="table table-bordered">
                        <?php foreach ($intent['responses'] as $response): ?>
                            <tr><td><?= $response ?></td></tr>
                        <?php endforeach; ?>
                    </table>
                </td>

                <td>
                    <button class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editModal<?= $rowNumber ?>">Edit</button>
                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $rowNumber ?>">Delete</button>
                </td>
            </tr>

            <!-- Edit Modal -->
<div class="modal fade" id="editModal<?= $rowNumber ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $rowNumber ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel<?= $rowNumber ?>">Edit Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="edit.php" method="post">
                    <!-- Use id instead of tag for editing -->
                    <input type="hidden" name="idToEdit" value="<?= $intent['id'] ?>">
                    New Tag Value: <input type="text" name="newTagValue" class="form-control" value="<?= $intent['tag'] ?>"><br>

                    <div class="form-group mb-3">
                        <label for="patternsToEdit">Patterns:</label>
                        <div id="patternsContainer">
                            <?php foreach ($intent['patterns'] as $pattern): ?>
                                <input type="text" name="patternsToEdit[]" class="form-control mb-2" value="<?= $pattern ?>">
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="responsesToEdit">Responses:</label>
                        <div id="responsesContainer">
                            <?php foreach ($intent['responses'] as $response): ?>
                                <input type="text" name="responsesToEdit[]" class="form-control mb-2" value="<?= $response ?>">
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Delete modal -->
<div class="modal fade" id="deleteModal<?= $rowNumber ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?= $rowNumber ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel<?= $rowNumber ?>">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this entry?
            </div>
            <div class="modal-footer">
                <form action="delete.php" method="post">
                    <!-- Make sure the value of idToDelete matches the correct id -->
                    <input type="hidden" name="idToDelete" value="<?= $intent['id'] ?>">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>



            <?php
            $rowNumber++; // Increment the counter variable
            endforeach;
        endif;
        ?>
        </tbody>
    </table>
</div>


        <!-- /#page-content-wrapper -->
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <script>
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");
        toggleButton.onclick = function () {
            el.classList.toggle("toggled");
        };

        const searchInput = document.getElementById("searchInput");
        searchInput.addEventListener("input", function () {
            const searchText = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll("table tbody tr");

            rows.forEach(function (row) {
                const columns = row.getElementsByTagName("td");
                let found = false;

                // Loop through columns and check if any contains the search text
                for (let i = 0; i < columns.length; i++) {
                    if (columns[i].textContent.toLowerCase().includes(searchText)) {
                        found = true;
                        break;
                    }
                }

                // Show or hide the row based on search results
                if (found) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });

        // Function to update the training console with new text
        function updateTrainingConsole(text) {
            const trainingConsole = document.getElementById("training-console");
            trainingConsole.innerHTML += text;
        }

        document.getElementById('trainButton').addEventListener('click', function () {
            // Clear the existing console output
            document.getElementById("training-console").innerHTML = "";

            // Function to fetch updates and update the console
            function fetchUpdates() {
                fetch('http://127.0.0.1:5000/train', {
                    method: 'POST'
                })
                    .then(function (response) {
                        if (response.ok) {
                            return response.text(); // Convert the response to text
                        } else {
                            console.error('Error starting training:', response.statusText);
                        }
                    })
                    .then(function (data) {
                        // Update the console with new data
                        updateTrainingConsole(data);

                        // Continue fetching updates every 1 second (adjust as needed)
                        setTimeout(fetchUpdates, 1000);
                    });
            }

            // Start fetching updates
            fetchUpdates();
        });

         // Dark mode toggle functionality
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;

        darkModeToggle.addEventListener('click', function () {
            body.classList.toggle('dark-mode');
            const isDarkMode = body.classList.contains('dark-mode');
            // Store user's preference in localStorage
            localStorage.setItem('darkMode', isDarkMode);
        });

        // Check the user's dark mode preference from localStorage
        const isDarkMode = localStorage.getItem('darkMode') === 'true';
        if (isDarkMode) {
            body.classList.add('dark-mode');
            
        }

        
    function addPattern() {
    var patternsContainer = document.getElementById('patternsContainer');
    var textarea = document.createElement('textarea');
    textarea.className = 'form-control mt-2';
    textarea.name = 'patterns[]';
    textarea.rows = 2; // Set the number of rows
    textarea.required = true;
    patternsContainer.appendChild(textarea);
    }


    function addResponse() {
    var responsesContainer = document.getElementById('responsesContainer');
    var textarea = document.createElement('textarea');
    textarea.className = 'form-control mt-2';
    textarea.name = 'responses[]';
    textarea.rows = 3; // Set the number of rows
    textarea.required = true;
    responsesContainer.appendChild(textarea);
    }


    </script>


<?php
    // Handle Deleting an Intent
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tagToDelete'])) {
        $tag_to_delete = $_POST['tagToDelete'];

        $json_data = file_get_contents("intents.json");
        $intents = json_decode($json_data, true);

        foreach ($intents['intents'] as $key => $intent) {
            if ($intent['tag'] == $tag_to_delete) {
                unset($intents['intents'][$key]);
            }
        }

        $intents['intents'] = array_values($intents['intents']);
        file_put_contents("intents.json", json_encode($intents, JSON_PRETTY_PRINT));
        header("Location: dashboardAdmin.php");
        exit();
    }

    // Handle Editing an Intent
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tagToEdit']) && isset($_POST['newTagValue'])) {
        $tag_to_edit = $_POST['tagToEdit'];
        $new_tag_value = $_POST['newTagValue'];

        $json_data = file_get_contents("intents.json");
        $intents = json_decode($json_data, true);

        foreach ($intents['intents'] as &$intent) {
            if ($intent['tag'] == $tag_to_edit) {
                $intent['tag'] = $new_tag_value;
            }
        }

        file_put_contents("intents.json", json_encode($intents, JSON_PRETTY_PRINT));
        header("Location: dashboardAdmin.php");
        exit();
    }

    // Handle Adding an Intent
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tag']) && isset($_POST['patterns']) && isset($_POST['responses'])) {
        $tag = $_POST["tag"];
        $patterns = $_POST["patterns"];
        $responses = $_POST["responses"];

        $json_data = file_get_contents("intents.json");
        $intents = json_decode($json_data, true);

        $new_intent = array(
            "tag" => $tag,
            "patterns" => $patterns,
            "responses" => $responses
        );

        $intents['intents'][] = $new_intent;

        file_put_contents("intents.json", json_encode($intents, JSON_PRETTY_PRINT));

        echo '<script>alert("Intent added successfully, click the train button to refresh the data.");</script>';
        echo '<script>window.location.href = "dashboardAdmin.php";</script>';
        exit();
    }
    ?>
</body>
</html>

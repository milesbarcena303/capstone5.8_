<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['idToDelete'])) {
        $id_to_delete = $_POST['idToDelete'];
        echo "Received id to delete: $id_to_delete"; 

        $json_data = file_get_contents("intents.json");
        $intents = json_decode($json_data, true);

        foreach ($intents['intents'] as $key => $intent) {
            if ($intent['id'] == $id_to_delete) {
                unset($intents['intents'][$key]);
                break; // Stop after deleting the first matching intent
            }
        }

        $intents['intents'] = array_values($intents['intents']); // Reindex the array

        file_put_contents("intents.json", json_encode($intents, JSON_PRETTY_PRINT));
        header("Location: dashboardAdmin.php"); // Redirect back to the dashboard
        exit();
    }
}
?>

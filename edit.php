<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['idToEdit']) && isset($_POST['newTagValue']) && isset($_POST['patternsToEdit']) && isset($_POST['responsesToEdit'])) {
        $id_to_edit = $_POST['idToEdit'];
        $new_tag_value = $_POST['newTagValue'];
        $patterns_to_edit = is_array($_POST['patternsToEdit']) ? $_POST['patternsToEdit'] : [$_POST['patternsToEdit']];
        $responses_to_edit = is_array($_POST['responsesToEdit']) ? $_POST['responsesToEdit'] : [$_POST['responsesToEdit']];

        $json_data = file_get_contents("intents.json");
        $intents = json_decode($json_data, true);

        foreach ($intents['intents'] as &$intent) {
            if ($intent['id'] == $id_to_edit) {
                $intent['tag'] = $new_tag_value;
                $intent['patterns'] = $patterns_to_edit;
                $intent['responses'] = $responses_to_edit;
            }
        }

        file_put_contents("intents.json", json_encode($intents, JSON_PRETTY_PRINT));
        header("Location: dashboardAdmin.php"); // Redirect back to the dashboard
        exit();
    }
}
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // get form data
    $tag = $_POST["tag"];
    $patterns = $_POST["patterns"]; // Access as an array
    $responses = $_POST["responses"]; // Access as an array

    // loading data and get the last used id
    $json_data = file_get_contents("intents.json");
    $intents = json_decode($json_data, true);

    // get the last entry
    $last_entry = end($intents['intents']);

    // get the last used id
    $last_id = isset($last_entry['id']) ? $last_entry['id'] : 0;

    // increment the id for the new intent
    $new_id = ++$last_id;

    // adding the intent
    $new_intent = array(
        "id" => $new_id,
        "tag" => $tag,
        "patterns" => $patterns,
        "responses" => $responses
    );
    $intents['intents'][] = $new_intent;

    // update the JSON file
    file_put_contents("intents.json", json_encode($intents, JSON_PRETTY_PRINT));

    // alert
    echo '<script>alert("Intent added successfully, click the train button to refresh the data.");</script>';
    echo '<script>window.location.href = "dashboardAdmin.php";</script>';
    exit();
}
?>

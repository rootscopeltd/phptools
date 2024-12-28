<?php
$title = 'PHP Unserialize Viewer';
$description = 'A simple tool to unserialize PHP data';
$og_url = 'https://phpunserialize.com';
require_once 'header.php';
// Handle form submission
$output = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['serialized_data'])) {
    try {
        // Attempt to unserialize the data
        $data = unserialize($_POST['serialized_data']);

        if ($data === false) {
            throw new Exception('Invalid serialized data');
        }
        // Convert the result to a readable format
        ob_start();
        var_dump($data);
        $output = ob_get_clean();

        // Make the output HTML-safe
        $output = htmlspecialchars($output);
    } catch (Exception $e) {
        $error = "Error: Invalid serialized data";
    }
}
?>
    <h1 class="topnav">PHP Unserialize Viewer</h1>
    <div class="container">
        <form method="POST">
            <label for="serialized_data">Enter serialized PHP data:</label>
            <textarea name="serialized_data" id="serialized_data"><?php echo isset($_POST['serialized_data']) ? htmlspecialchars($_POST['serialized_data']) : ''; ?></textarea>
            <button type="submit" class="submit-btn">Unserialize</button>
        </form>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($output): ?>
            <h3>Result:</h3>
            <div class="output"><?php echo $output; ?></div>
        <?php endif; ?>

        <div style="margin-top: 20px;">
            <h3>Example inputs:</h3>
            Serialized array
            <pre>a:3:{i:0;s:5:"apple";i:1;s:6:"banana";i:2;s:6:"orange";}</pre>

            Serialized object
            <pre>O:8:"stdClass":2:{s:4:"name";s:4:"John";s:3:"age";i:30;}</pre>
        </div>
    </div>

<?php require_once 'footer.php'; ?>
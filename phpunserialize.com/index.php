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
    <h1 class="topnav">PHP Unserialize</h1>
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
        <div>
            <h2>What are <code>serialize()</code> and <code>unserialize()</code>?</h2>
            <p>
                <code>serialize()</code> converts a PHP data structure (like an array or object) into a string that can be saved or transferred easily.
            </p>
            <p>
                <code>unserialize()</code> takes that storable/transferrable string and converts it back into the original PHP data structure.
            </p>
            <p>
                Commonly, you might store serialized data in a file or database, or send it across a network (although JSON is often used these days for broader compatibility).
            </p>
            <p>
                <strong>Note:</strong> Using <code>unserialize()</code> on untrusted data can be dangerous because it may allow malicious code injection. Consider safer alternatives like JSON, or at least use strict options such as <code>allowed_classes</code> to mitigate risks.
            </p>
        </div>
    </div>

<?php require_once 'footer.php'; ?>

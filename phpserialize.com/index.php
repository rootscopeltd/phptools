<?php
$title       = 'PHP Serialize Online';
$description = 'A simple tool to serialize PHP data';
$og_url      = 'https://phpserialize.com';
require_once 'header.php';

function parseArrayInput($input) {
    // Remove whitespace and trailing semicolon
    $input = rtrim(trim($input), ';');

    // Check for both array syntaxes
    $isArrayFunc = preg_match('/^array\((.*)\)$/', $input, $matches);
    $isBrackets = preg_match('/^\[(.*)\]$/', $input, $bracketMatches);

    if (!$isArrayFunc && !$isBrackets) {
        throw new Exception('Input must be a valid PHP array (array() or [] syntax)');
    }

    // Extract content between delimiters
    $content = $isArrayFunc ? $matches[1] : $bracketMatches[1];

    // Split by commas, but not within quotes
    $elements = preg_split('/,(?=(?:[^\']*\'[^\']*\')*[^\']*$)/', $content);

    $result = array();
    foreach ($elements as $element) {
        $element = trim($element);

        // Handle quoted strings (both single and double quotes)
        if (preg_match('/^[\'"](.*)[\'"]$/', $element, $matches)) {
            $result[] = $matches[1];
        } else if (is_numeric($element)) {
            $result[] = $element + 0; // Convert to number
        } else if ($element === 'true') {
            $result[] = true;
        } else if ($element === 'false') {
            $result[] = false;
        } else if ($element === 'null') {
            $result[] = null;
        } else if (!empty($element)) {
            $result[] = $element;
        }
    }

    return $result;
}

// Handle form submission
$output = '';
$error  = '';

// Security settings
define( 'RATE_LIMIT_SECONDS', 60 );
define( 'RATE_LIMIT_ATTEMPTS', 10 );

// Rate limiting
$rate_key = 'rate_' . $_SERVER['REMOTE_ADDR'];
if ( isset( $_SESSION[ $rate_key ] ) && is_array( $_SESSION[ $rate_key ] ) ) {
	// Clean old attempts
	$_SESSION[ $rate_key ] = array_filter(
		$_SESSION[ $rate_key ],
		function ( $time ) {
			return $time > ( time() - RATE_LIMIT_SECONDS );
		}
	);

	if ( count( $_SESSION[ $rate_key ] ) >= RATE_LIMIT_ATTEMPTS ) {
		die( 'Too many attempts. Please try again later.' );
	}
}
$_SESSION[ $rate_key ][] = time();

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && ! empty( $_POST['php_data'] ) ) {
	try {
        $input = trim($_POST['php_data']);
        $input = strip_tags($input);

        // Remove any trailing semicolons before parsing
        $input = rtrim($input, ';');
		if ( preg_match( '/O:\d+:"[^"]+":\d+:{/', $input ) ) {
			throw new Exception( 'Potential object injection detected.' );
		}
				// Log suspicious patterns
		if ( preg_match( '/(eval|exec|system|shell|passthru)/i', $input ) ) {
			error_log( "Suspicious input detected from {$_SERVER['REMOTE_ADDR']}: " . substr( $input, 0, 100 ) );
			throw new Exception( 'Invalid input format' );
		}

        $data = parseArrayInput($input);
        $output = serialize($data);
        $output = htmlspecialchars($output);
	} catch ( Exception $e ) {
		error_log( 'Error: ' . $e->getMessage() );
		$error = 'An error occurred.';
	}
}

// Generate CSRF token for form
$_SESSION['csrf_token'] = bin2hex( random_bytes( 32 ) );
?>
	<h1 class="topnav">PHP Serialize Online Viewer</h1>
	<div class="container">
		<form method="POST">
			<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

			<label for="php_data">Enter PHP array notation:</label>
			<textarea name="php_data" id="php_data"><?php echo isset( $_POST['php_data'] ) ? htmlspecialchars( $_POST['php_data'] ) : ''; ?></textarea>
			<button type="submit" class="submit-btn">Serialize</button>
		</form>

		<?php if ( $error ) : ?>
			<div class="error"><?php echo $error; ?></div>
		<?php endif; ?>

		<?php if ( $output ) : ?>
			<h3>Result:</h3>
			<div class="output"><?php echo $output; ?></div>
		<?php endif; ?>

		<div style="margin-top: 20px;">
			<h3>Example inputs:</h3>
			Simple array (strings and numbers)
			<pre>["apple", "banana", "orange", 42]</pre>
            <pre>array("apple", "banana", "orange", 42)</pre>
			<pre>["name", 123, true, false, null]</pre>
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
		</div>
	</div>

<?php require_once 'footer.php'; ?>

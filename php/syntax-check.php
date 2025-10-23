<?php
// Syntax check for health questionnaire files
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing file includes...\n";

try {
    echo "1. Testing connection.php...\n";
    require_once 'connection.php';
    echo "✓ Connection OK\n";
    
    echo "2. Testing health-questionnaire-handler-fixed.php...\n";
    require_once 'health-questionnaire-handler-fixed.php';
    echo "✓ Handler OK\n";
    
    echo "3. Creating handler instance...\n";
    $handler = new HealthQuestionnaireHandler();
    echo "✓ Handler instance created\n";
    
    echo "All syntax checks passed!\n";
    
} catch (ParseError $e) {
    echo "❌ Parse Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n";
} catch (Error $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n";
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n";
}
?>

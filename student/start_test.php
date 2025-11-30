<?php
require_once __DIR__ . '/../includes/auth_student.php';
require_once __DIR__ . '/../config/db.php';

// Fetch settings
$settings = null;
$sql_settings = "SELECT test_duration_minutes, questions_per_test, shuffle_questions, shuffle_options FROM settings WHERE id = 1";
if ($result = $conn->query($sql_settings)) {
    $settings = $result->fetch_assoc();
    $result->close();
}

if (!$settings) {
    // Critical error: cannot proceed without settings
    $_SESSION['test_error'] = "Critical Error: Test settings could not be loaded.";
    header("location: dashboard.php");
    exit;
}

$required_questions = $settings['questions_per_test'];

// Fetch all question IDs and correct options to determine if enough questions exist
$question_map = [];
$sql_questions_fetch = "SELECT id, correct_option FROM questions";
if ($result = $conn->query($sql_questions_fetch)) {
    while ($row = $result->fetch_assoc()) {
        $question_map[] = $row;
    }
    $result->close();
}

if (count($question_map) < $required_questions) {
    $_SESSION['test_error'] = "Cannot start test: Only " . count($question_map) . " questions available. Need " . $required_questions . ".";
    header("location: dashboard.php");
    exit;
}

// --- Logic to select and store questions for the current session ---

// Shuffle all available questions
if ($settings['shuffle_questions']) {
    shuffle($question_map);
}

// Select the required number of questions
$selected_questions_ids = array_map(function($q) { return $q['id']; }, array_slice($question_map, 0, $required_questions));

// Create the question_ids array for the session
$session_questions = [];
$correct_answers_map = [];

// Fetch the full question details for the selected IDs
$placeholders = implode(',', array_fill(0, count($selected_questions_ids), '?'));
$sql_fetch_full_q = "SELECT id, question_text, option_a, option_b, option_c, option_d, correct_option FROM questions WHERE id IN ($placeholders)";

$param_types = str_repeat('i', count($selected_questions_ids));

if ($stmt = $conn->prepare($sql_fetch_full_q)) {
    $stmt->bind_param($param_types, ...$selected_questions_ids);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            // Store correct answer separately for security
            $correct_answers_map[$row['id']] = $row['correct_option'];

            // Prepare question data for the session (without the correct answer)
            $question_data = [
                'id' => $row['id'],
                'text' => $row['question_text'],
                'options' => [
                    'A' => $row['option_a'],
                    'B' => $row['option_b'],
                    'C' => $row['option_c'],
                    'D' => $row['option_d']
                ]
            ];
            
            // Shuffle options if setting is enabled
            if ($settings['shuffle_options']) {
                $options_keys = array_keys($question_data['options']);
                shuffle($options_keys);
                $shuffled_options = [];
                foreach ($options_keys as $key) {
                    $shuffled_options[$key] = $question_data['options'][$key];
                }
                $question_data['options'] = $shuffled_options;
            }

            $session_questions[] = $question_data;
        }
    }
    $stmt->close();
}


// Store test data in session
$_SESSION['test_active'] = true;
$_SESSION['test_questions'] = $session_questions;
$_SESSION['test_correct_answers'] = $correct_answers_map;
$_SESSION['test_duration_minutes'] = $settings['test_duration_minutes'];
$_SESSION['test_start_time'] = time();

// Redirect to the test page
header("location: test.php");
exit;
?>
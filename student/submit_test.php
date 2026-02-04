<?php
require_once '../config.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: ".$base_url."student_login.php");
    exit;
}
if (!isset($_SESSION['test_questions'])) {
    header("Location: dashboard.php");
    exit;
}

$studentId = $_SESSION['student_id'];
$questions = $_SESSION['test_questions'];
$answers   = $_POST['answers'] ?? [];

$total = count($questions);
$correct = 0;

foreach ($questions as $id => $correctOption) {
    $given = $answers[$id] ?? null;
    if ($given === $correctOption) $correct++;
}

$score = $total > 0 ? round(($correct / $total) * 100) : 0;

$stmt = $conn->prepare("INSERT INTO results (student_id, score, total_questions, correct_answers)
                        VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiii", $studentId, $score, $total, $correct);
$stmt->execute();
$resultId = $stmt->insert_id;
$stmt->close();
unset($_SESSION['test_questions']);

header("Location: result.php?id=".$resultId);
exit;

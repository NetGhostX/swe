<?php
require_once("classes/SubjectData.php");
require_once("classes/TopicData.php");

if (!isset($_GET['query'])) {
    die(json_encode([]));
}

$query = strtolower(trim($_GET['query']));
$subjects = SubjectData::getAll();
$results = [];

foreach ($subjects as $subject) {
    if (strpos(strtolower($subject->displayName), $query) !== false) {
        $results[] = [
            'type' => 'subject',
            'id' => $subject->id,
            'displayName' => $subject->displayName
        ];
    }
    foreach ($subject->topics as $topic) {
        if (
            strpos(strtolower($subject->displayName), $query) !== false ||
            strpos(strtolower($topic->displayName), $query) !== false ||
            strpos(strtolower($topic->description), $query) !== false ||
            strpos(strtolower($topic->getFinishedArticle()), $query) !== false
        ) {
            $results[] = [
                'type' => 'topic',
                'subjectId' => $topic->subjectId,
                'id' => $topic->id,
                'displayName' => $subject->displayName . ' - ' . $topic->displayName
            ];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($results);
?>
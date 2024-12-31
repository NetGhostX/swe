<?php

require_once '../webseite/classes/SubjectData.php';
require_once '../webseite/classes/TopicData.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$path = explode('/', trim($_SERVER['PATH_INFO'], '/'));

if (count($path) < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid API request']);
    exit;
}

$resource = $path[0];

switch ($resource) {
    case 'subjects':
        handleSubjects($method);
        break;
    case 'topics':
        handleTopics($method);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Resource not found']);
        break;
}

function handleSubjects($method)
{
    switch ($method) {
        case 'POST':
            createSubject();
            break;
        case 'PUT':
            updateSubject();
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
}

function handleTopics($method)
{
    switch ($method) {
        case 'POST':
            createTopic();
            break;
        case 'PUT':
            updateTopic();
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
}

function createSubject()
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'], $data['displayName'], $data['description'], $data['color'], $data['icon'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }

    $subject = SubjectData::createNew(
        $data['id'],
        $data['displayName'],
        $data['description'],
        $data['color'],
        $data['icon'],
        []
    );

    if ($subject && $subject->save()) {
        http_response_code(201);
        echo json_encode(['message' => 'Subject created successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create subject']);
    }
}

function updateSubject()
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'], $data['displayName'], $data['description'], $data['color'], $data['icon'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }

    $subject = SubjectData::fromName($data['id']);
    if (!$subject) {
        http_response_code(404);
        echo json_encode(['error' => 'Subject not found']);
        return;
    }

    $subject->setDisplayName($data['displayName']);
    $subject->setDescription($data['description']);
    $subject->setColor($data['color']);
    $subject->setIcon($data['icon']);

    if ($subject->save()) {
        http_response_code(200);
        echo json_encode(['message' => 'Subject updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update subject']);
    }
}

function createTopic()
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'], $data['subjectId'], $data['displayName'], $data['icon'], $data['description'], $data['article'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }

    $topic = TopicData::createNew(
        $data['id'],
        $data['subjectId'],
        $data['displayName'],
        $data['icon'],
        $data['description'],
        [],
        $data['article']
    );

    if ($topic && $topic->save()) {
        http_response_code(201);
        echo json_encode(['message' => 'Topic created successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create topic']);
    }
}

function updateTopic()
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'], $data['subjectId'], $data['displayName'], $data['icon'], $data['description'], $data['article'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }

    $topic = TopicData::fromName($data['subjectId'], $data['id']);
    if (!$topic) {
        http_response_code(404);
        echo json_encode(['error' => 'Topic not found']);
        return;
    }

    $topic->setDisplayName($data['displayName']);
    $topic->setIcon($data['icon']);
    $topic->setDescription($data['description']);
    $topic->setArticle($data['article']);

    if ($topic->save()) {
        http_response_code(200);
        echo json_encode(['message' => 'Topic updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update topic']);
    }
}

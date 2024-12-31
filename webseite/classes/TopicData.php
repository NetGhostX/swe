<?php

use exception\SubjectDoesNotExistException;
use exception\TopicAlreadyExistsException;

require_once("Task.php");
require_once("Config.php");
require_once("Util.php");

class TopicData
{
    /**
     * @var string Innerhalb des zugehörigen Faches eindeutige ID, darf nur A-Z, a-z, 0-9 sowie - und _ enthalten
     */
    public string $id;

    /**
     * @var string Die eindeutige ID des zugehörigen Faches
     */
    public string $subjectId;

    /**
     * @var string Der für User angezeigt Name des Themas, darf nur reinen Text enthalten
     */
    public string $displayName;

    /**
     * @var string Das Icon des Themas als Font-Awesome CSS-Klasse
     */
    public string $icon;

    /**
     * @var string Eine kurze Beschreibung des Themas, z.B. für die Fachübersichtsseite, darf HTML enthalten
     */
    public string $description;

    /**
     * @var array Die IDs aller verwandten Themen als String
     */
    public array $relatedTopics;

    /**
     * @var array Die Dateinamen (Datei.pdf) aller downloadbarer Dateien zu diesem Thema als String
     */
    public array $files;

    /**
     * @var string Der gesamte Erklärungstext zum Thema, enthält fertiges HTML und LATEX Formelsyntax für MathJax https://docs.mathjax.org/en/latest/basic/mathematics.html
     */
    private string $article;

    /**
     * @var array Alle zugehörigen Formelaufgaben als Task
     * @see Task
     */
    private array $tasks;

    /**
     * Erstellt ein neues Thema. Es wird noch nichts gespeichert!
     * @param string $id Innerhalb des zugehörigen Faches eindeutige ID, darf nur A-Z, a-z, 0-9 sowie - und _ enthalten
     * @param string $subjectId Die eindeutige ID des zugehörigen Faches, das Fach muss schon existieren
     * @param string $displayName Der für User angezeigt Name des Themas, darf nur reinen Text enthalten
     * @param string $icon Das Icon des Themas als Font-Awesome CSS-Klasse
     * @param string $description Eine kurze Beschreibung des Themas, z.B. für die Fachübersichtsseite, darf HTML enthalten
     * @param array $relatedTopics Die IDs aller verwandten Themen als String
     * @param string $article Der gesamte Erklärungstext zum Thema, enthält fertiges HTML und LATEX Formelsyntax für MathJax https://docs.mathjax.org/en/latest/basic/mathematics.html
     * @return TopicData|false Neues Thema oder false, wenn ein Fehler auftritt
     */
    public static function createNew(string $id, string $subjectId, string $displayName, string $icon, string $description, array $relatedTopics, string $article): TopicData|false
    {
        $result = new TopicData();

        if (Util::containsIllegalCharacters($subjectId)) {
            return false;
        }
        if (!SubjectData::exists($subjectId)) {
            return false;
        }
        $result->subjectId = $subjectId;

        if (Util::containsIllegalCharacters($id)) {
            return false;
        }
        if (self::exists($subjectId, $id)) {
            return false;
        }
        $result->id = $id;

        $result->displayName = $displayName;

        $result->icon = $icon;

        $result->description = $description;

        $result->relatedTopics = $relatedTopics;

        $result->files = array();

        $result->article = $article;

        $result->tasks = array();

        return $result;
    }

    /**
     * Erstellt ein neues Thema aus API-Anfragedaten. Es wird noch nichts gespeichert!
     * @param array $data Die API-Anfragedaten
     * @return TopicData|false Neues Thema oder false, wenn ein Fehler auftritt
     */
    public static function createFromApiData(array $data): TopicData|false
    {
        if (!isset($data['id'], $data['subjectId'], $data['displayName'], $data['icon'], $data['description'], $data['article'])) {
            return false;
        }

        return self::createNew(
            $data['id'],
            $data['subjectId'],
            $data['displayName'],
            $data['icon'],
            $data['description'],
            [],
            $data['article']
        );
    }

    /**
     * Aktualisiert ein bestehendes Thema aus API-Anfragedaten
     * @param array $data Die API-Anfragedaten
     * @return bool true, wenn erfolgreich, sonst false
     */
    public function updateFromApiData(array $data): bool
    {
        if (!isset($data['displayName'], $data['icon'], $data['description'], $data['article'])) {
            return false;
        }

        $this->setDisplayName($data['displayName']);
        $this->setIcon($data['icon']);
        $this->setDescription($data['description']);
        $this->setArticle($data['article']);

        return true;
    }

    /**
     * Prüft, ob das Thema zu den angegebenen IDs existiert
     * @param string $subjectId ID des Faches
     * @param string $topicId ID des Themas
     * @return bool true, wenn es existiert, sonst false
     */
    public static function exists(string $subjectId, string $topicId): bool
    {
        if (!is_dir(Config::getTopicDirectory($subjectId, $topicId))) {
            return false;
        }

        return true;
    }

    /**
     * Gibt alle Themen zu einem gegebenen Fach zurück
     * @param $subjectId string Die ID des Faches
     * @return array Alle zugehörigen Themen als TopicData Objekte
     */
    public static function getAll(string $subjectId): array
    {
        $result = array();

        $topicNames = scandir(Config::getTopicsDirectory($subjectId));

        usort($topicNames, function ($a, $b) {
            return strcmp($a, $b);
        });

        foreach ($topicNames as $topicName) {
            if ($topicName == "." || $topicName == "..") {
                continue;
            }
            $topicData = TopicData::fromName($subjectId, $topicName);
            if (!isset($topicData)) {
                continue;
            }

            $result[$topicData->id] = $topicData;
        }

        return $result;
    }

    /**
     * Gibt Themendaten zu einem bestimmten Thema eines Faches zurück
     * @param $subjectId string Die ID des Faches
     * @param $topicId string Die ID des Themas
     * @return TopicData|null Die Themendaten oder null, wenn das Thema nicht existiert
     */
    public static function fromName(string $subjectId, string $topicId): TopicData|null
    {
        $result = new TopicData();

        if (Util::containsIllegalCharacters($subjectId)) {
            return null;
        }
        if (Util::containsIllegalCharacters($topicId)) {
            return null;
        }
        $result->id = $topicId;
        $result->subjectId = $subjectId;

        $data = Util::parseJsonFromFile(Config::getTopicDirectory($subjectId, $topicId) . "properties.json");
        if (!isset($data)) {
            return null;
        }

        if (!isset($data->displayName)) {
            return null;
        }
        $result->displayName = $data->displayName;

        if (!isset($data->icon)) {
            return null;
        }
        $result->icon = $data->icon;

        if (!isset($data->description)) {
            return null;
        }
        $result->description = $data->description;

        $relatedTopics = array();
        if (isset($data->relatedTopics)) {
            $relatedTopics = $data->relatedTopics;
        }
        $result->relatedTopics = $relatedTopics;

        $files = Util::getFilesFromDirectory(Config::getTopicDirectory($subjectId, $topicId) . "downloads/");
        $result->files = $files;

        $article = Util::readFileContent(Config::getTopicDirectory($subjectId, $topicId) . "article.html");
        if (!isset($article)) {
            $article = "Kein Erklärtext vorhanden";
        }
        $result->article = $article;

        $taskJson = Util::readFileContent(Config::getTopicDirectory($subjectId, $topicId) . "tasks.json");
        $result->tasks = array();
        if(isset($taskJson)) {
            $arr = json_decode($taskJson, true);
            foreach ($arr as $rawTask) {
                $text = $rawTask["text"];
                if(!isset($text)) {
                    continue;
                }

                $vars = $rawTask["vars"];
                if (!isset($vars)) {
                    continue;
                }

                $result->tasks[] = new Task($text, $vars);
            }
        }

        $result->cleanupRelatedTopics();
        $result->cleanupFiles();

        return $result;
    }

    /**
     * Schreibt alle Daten in Dateien
     * @return bool true, wenn erfolgreich, sonst false
     */
    public function save(): bool
    {
        $this->cleanupRelatedTopics();
        $this->cleanupFiles();

        $data = array();
        $data["displayName"] = $this->displayName;
        $data["icon"] = $this->icon;
        $data["description"] = $this->description;
        $data["relatedTopics"] = $this->relatedTopics;

        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if (!$json) {
            return false;
        }

        if (!is_dir(Config::getSubjectDirectory($this->getSubjectId()))) {
            return false;
        }

        $topicDirectory = Config::getTopicDirectory($this->getSubjectId(), $this->getId());
        if (!is_dir($topicDirectory)) {
            mkdir($topicDirectory, 0777, true);
        }

        $taskArray = array();
        foreach ($this->tasks as $task) {
            $element = array();
            $element["text"] = $task->getText();
            $element["vars"] = $task->getVariables();
            $taskArray[] = $element;
        }

        $taskJson = json_encode($taskArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if (!$taskJson) {
            return false;
        }

        if (!(Util::writeFileContent($topicDirectory . "properties.json", $json)
            && Util::writeFileContent($topicDirectory . "article.html", $this->article)
            && Util::writeFileContent($topicDirectory . "tasks.json", $taskJson)
        )
        ) {
            return false;
        }

        return true;
    }

    /**
     * Lädt eine Datei als Download zum Thema hoch
     * @param string $name Dateiname von User, z.B. $_FILES['html-input-name']['name'][0]
     * @param string $tmp_name Temporärer Pfad zur hochgeladenen Datei, z.B. $_FILES['html-input-name']['tmp_name'][0]
     * @return bool true, wenn erfolgreich, sonst false
     */
    public function addDownload(string $name, string $tmp_name): bool
    {
        $downloadDirectory = Config::getTopicDirectory($this->getSubjectId(), $this->getId()) . "downloads/";

        if (!is_dir($downloadDirectory)) {
            if (!mkdir($downloadDirectory)) {
                return false;
            }
        }

        if (!move_uploaded_file($tmp_name, $downloadDirectory . $name)) {
            return false;
        }

        $this->files[] = $name;

        return true;
    }

    /**
     * Löscht eine downloadbare Datei des Themas
     * @param string $name Dateiname
     * @return bool true, wenn erfolgreich, sonst false
     */
    public function deleteDownload(string $name): bool
    {
        if (!isset($this->files[$name])) {
            return false;
        }

        if (!unlink(Config::getTopicDirectory($this->getSubjectId(), $this->getId()) . "downloads/$name")) {
            return false;
        }

        $this->files = array_diff($this->files, [$name]);

        return true;
    }

    /**
     * Lädt eine Datei als Bild zum Thema hoch
     * @param string $name Dateiname von User, z.B. $_FILES['html-input-name']['name'][0]
     * @param string $tmp_name Temporärer Pfad zum hochgeladenen Bild, z.B. $_FILES['html-input-name']['tmp_name'][0]
     * @return bool true, wenn erfolgreich, sonst false
     */
    public function addImage(string $name, string $tmp_name): bool
    {
        $imageDirectory = Config::getTopicDirectory($this->getSubjectId(), $this->getId()) . "images/";

        if (!is_dir($imageDirectory)) {
            if (!mkdir($imageDirectory)) {
                return false;
            }
        }

        if (!move_uploaded_file($tmp_name, $imageDirectory . $name)) {
            return false;
        }

        return true;
    }

    /**
     * Prüft für alle verwandten Themen, ob diese auch existieren. Wenn nicht, wird es aus der Liste entfernt
     * @return bool true, wenn Elemente entfernt wurden, sonst false
     */
    private function cleanupRelatedTopics(): bool
    {
        $changed = false;
        $nonexistentEntries = array();

        foreach ($this->relatedTopics as $topic) {
            if (!self::exists($this->subjectId, $topic)) {
                $nonexistentEntries[] = $topic;
                $changed = true;
            }
        }

        $this->relatedTopics = array_diff($this->relatedTopics, $nonexistentEntries);

        return $changed;
    }

    /**
     * Prüft für alle Downloads, ob die zugehörige Datei existiert und ob zu jeder Datei ein Eintrag existiert.
     * Wenn eine Datei nicht existiert, wird auch der zugehörige Eintrag entfernt.
     * Wenn ein Eintrag nicht existiert, wird auch die Datei gelöscht.
     * @return bool true, wenn etwas verändert wurde
     */
    private function cleanupFiles(): bool
    {
        $changed = false;

        $nonexistentEntries = array();
        foreach ($this->files as $file) {
            if(!file_exists(Config::getTopicDirectory($this->subjectId, $this->id) . "downloads/$file")) {
                $nonexistentEntries[] = $file;
                $changed = true;
            }
        }
        $this->files = array_diff($this->files, $nonexistentEntries);

        foreach (Util::getFilesFromDirectory(Config::getTopicDirectory($this->subjectId, $this->id) . "downloads/") as $file) {
            if(!array_search($file, $this->files)) {
                $this->deleteDownload($file);
                $changed = true;
            }
        }

        return $changed;
    }

    /**
     * Löscht ein Bild des Themas
     * @param string $name Dateiname
     * @return bool true, wenn erfolgreich, sonst false
     */
    public function deleteImage(string $name): bool
    {
        if (!unlink(Config::getTopicDirectory($this->getSubjectId(), $this->getId()) . "images/$name")) {
            return false;
        }

        return true;
    }

    /**
     * Löscht das Thema inklusive aller zugehörigen Dateien
     * @return bool true, wenn erfolgreich gelöscht, sonst false
     */
    public function delete(): bool
    {
        if (!Util::delete(Config::getTopicDirectory($this->getSubjectId(), $this->getId()))) {
            return false;
        }

        return true;
    }

    public function addTask(Task $task): bool
    {
        $this->tasks[] = $task;
        return true;
    }

    public function removeTask(Task $task): bool
    {
        $this->tasks = array_diff($this->tasks, [$task]);
        return true;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getSubjectId(): string
    {
        return $this->subjectId;
    }

    public function setSubjectId(string $subjectId): void
    {
        $this->subjectId = $subjectId;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getRelatedTopics(): array
    {
        return $this->relatedTopics;
    }

    public function setRelatedTopics(array $relatedTopics): void
    {
        $this->relatedTopics = $relatedTopics;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function setFiles(array $files): void
    {
        $this->files = $files;
    }

    /**
     * Gibt anders als getArticle() Bildpfade richtig aus
     * @return string HTML Quelltext für den Erklärtext
     */
    public function getFinishedArticle(): string
    {
        return str_replace('$TOPICPATH', Config::getTopicDirectory($this->subjectId, $this->id) . "images", $this->article);
    }

    public function getArticle(): string
    {
        return $this->article;
    }

    public function setArticle(string $article): void
    {
        $this->article = $article;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function setTasks(array $tasks): void
    {
        $this->tasks = $tasks;
    }
}

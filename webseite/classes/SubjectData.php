<?php
require_once("Config.php");
require_once("Util.php");
require_once("TopicData.php");

/**
 * Stellt alle relevanten Daten für ein einzelnes Fach bereit
 *
 */
class SubjectData
{
    /**
     * @var string Ein eindeutiger Bezeichner für das Fach, darf nur A-Z, a-Z, 0-9 sowie _ und - enthalten
     */
    public string $id;

    /**
     * @var string Der für User angezeigte Name des Faches, nur reiner Text
     */
    public string $displayName;

    /**
     * @var string Eine kurze Beschreibung des Faches, z.B. für den Text auf der Startseite, kann HTML enthalten
     */
    public string $description;

    /**
     * @var string Themenfarbe des Faches als hexcode
     */
    public string $color;

    /**
     * @var string Icon des Faches als Font-Awesome CSS-Klasse
     */
    public string $icon;

    /**
     * @var array Alle Themen des Faches als TopicData Objekt
     * @see TopicData
     */
    public array $topics;

    /**
     * Erstellt ein neues Fach. Es wird noch nichts gespeichert!
     * @param string $id Ein eindeutiger Bezeichner für das Fach, darf nur A-Z, a-Z, 0-9 sowie _ und - enthalten
     * @param string $displayName Der für User angezeigte Name des Faches, nur reiner Text
     * @param string $description Eine kurze Beschreibung des Faches, z.B. für den Text auf der Startseite, kann HTML enthalten
     * @param string $color Themenfarbe des Faches als hexcode
     * @param string $icon Icon des Faches als Font-Awesome CSS-Klasse
     * @param array $topics Alle Themen des Faches als TopicData Object
     * @return SubjectData|false Neues Fach oder false, wenn ein Fehler auftritt
     */
    public static function createNew(string $id, string $displayName, string $description, string $color, string $icon, array $topics): SubjectData|false
    {
        $result = new SubjectData();

        if(Util::containsIllegalCharacters($id)) {
            return false;
        }
        if(self::exists($id)) {
            return false;
        }
        $result->id = $id;

        $result->displayName = $displayName;

        $result->description = $description;

        $result->color = $color;

        $result->icon = $icon;

        $result->topics = $topics;

        return $result;
    }

    /**
     * Prüft, ob das Thema zu den angegebenen IDs existiert
     * @param string $subjectId ID des Faches
     * @return bool true, wenn es existiert, sonst false
     */
    public static function exists(string $subjectId): bool
    {
        if(!is_dir(Config::getSubjectDirectory($subjectId))) {
            return false;
        }

        return true;
    }

    /**
     * Gibt alle Fächer als SubjectData Objekt zurück
     * @return array Alle Fächer als SubjectData
     */
    public static function getAll(): array
    {
        $result = array();

        $subjectNames = scandir(Config::getSubjectsDirectory());

        usort($subjectNames, function ($a, $b) {
            return strcmp($a, $b);
        });

        foreach ($subjectNames as $subjectName) {
            if ($subjectName == "." || $subjectName == "..") {
                continue;
            }

            $subjectData = SubjectData::fromName($subjectName);
            if (!isset($subjectData)) {
                continue;
            }

            $result[$subjectData->id] = $subjectData;
        }

        return $result;
    }

    /**
     * Lädt ein Fach über eine gegebene ID
     * @param $subjectId string Die eindeutige ID des Faches
     * @return SubjectData|null Das Fach zu der ID oder null, wenn kein entsprechendes Fach gefunden wurde
     */
    public static function fromName(string $subjectId): SubjectData|null
    {
        $result = new SubjectData();

        if (Util::containsIllegalCharacters($subjectId)) {
            return null;
        }
        $result->id = $subjectId;

        $filename = Config::getSubjectDirectory($subjectId) . "properties.json";
        $data = Util::parseJsonFromFile($filename);
        if (!isset($data)) {
            return null;
        }

        if (!isset($data->displayName)) {
            return null;
        }
        $result->displayName = $data->displayName;

        if (!isset($data->description)) {
            return null;
        }
        $result->description = $data->description;

        if (!isset($data->color)) {
            return null;
        }
        $result->color = $data->color;

        if (!isset($data->icon)) {
            return null;
        }
        $result->icon = $data->icon;

        $result->topics = TopicData::getAll($subjectId);

        return $result;
    }

    /**
     * Schreibt alle Daten in Dateien
     * @return bool true, wenn erfolgreich, sonst false
     */
    public function save(): bool
    {
        $data = array();
        $data["displayName"] = $this->displayName;
        $data["description"] = $this->description;
        $data["color"] = $this->color;
        $data["icon"] = $this->icon;

        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if (!$json) {
            return false;
        }

        $subjectDirectory = Config::getSubjectDirectory($this->getId());
        if (!is_dir($subjectDirectory)) {
            mkdir($subjectDirectory, 0777, true);
        }

        if (!Util::writeFileContent($subjectDirectory . "properties.json", $json)) {
            return false;
        }

        return true;
    }

    /**
     * Löscht das Fach inklusive aller zugehörigen Themen
     * @return bool true, wenn erfolgreich, sonst false
     */
    public function delete(): bool
    {
        if (!Util::delete(Config::getSubjectDirectory($this->getId()))) {
            return false;
        }

        return true;
    }

    /**
     * Fügt ein neues Thema zum Fach hinzu
     * @param TopicData $topic Das neue Thema
     * @return bool true, wenn erfolgreich, sonst false
     */
    public function addTopic(TopicData $topic): bool
    {
        if(isset($this->topics[$topic->getId()])) {
            return false;
        }

        $this->topics[] = $topic;
        return true;
    }

    /**
     * Entfernt ein Thema vom Fach
     * @param TopicData $topic Das zu entfernende Thema
     * @return bool true, wenn erfolgreich, sonst false
     */
    public function removeTopic(TopicData $topic): bool
    {
        if(!isset($this->topics[$topic->getId()])) {
            return false;
        }

        $this->topics = array_diff($this->topics, [$topic]);
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

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function getTopics(): array
    {
        return $this->topics;
    }

    public function setTopics(array $topics): void
    {
        $this->topics = $topics;
    }
}
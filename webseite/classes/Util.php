<?php

/**
 * Hilfsmethoden für verschiedene Aufgaben
 */
class Util
{
    /**
     * Entfernt alle Zeichen außer A-Z, a-z, 0-9 sowie - und _
     * @param string $string Eingabe mit möglicherweise ungültigen Zeichen
     * @return string Eingabestring mit allen ungültigen Zeichen entfernt
     */
    static function removeIllegalCharacters(string $string): string
    {
        return preg_replace("/[^a-zA-Z0-9_-]/", "", $string);
    }

    /**
     * Prüft, ob Zeichen außer A-Z, a-z, 0-9 sowie - und _ enthalten sind
     * @param string $string Zu prüfender Text
     * @return bool true, wenn ungültige Zeichen enthalten sind, sonst false
     */
    static function containsIllegalCharacters(string $string): bool
    {
        if (preg_match("/[^a-zA-Z0-9_-]/", $string)) {
            return true;
        }

        return false;
    }

    static function getFilesFromDirectory(string $directory): array
    {
        $files = array();

        if (is_dir($directory)) {
            $fileNames = scandir($directory);
            foreach ($fileNames as $fileName) {
                if ($fileName == "." || $fileName == "..") {
                    continue;
                }

                $files[] = $fileName;
            }
        }

        return $files;
    }

    /**
     * Liest den gesamten Text aus einer Datei aus
     * @param string $filename Dateipfad
     * @return string|null Der enthaltene Text oder null bei einem Fehler
     */
    static function readFileContent(string $filename): string|null
    {
        if (!file_exists($filename)) {
            return null;
        }
        $file = fopen($filename, "r");
        if (!$file) {
            return null;
        }
        $fileContent = fread($file, filesize($filename));
        if (!$fileContent) {
            return null;
        }
        fclose($file);

        return $fileContent;
    }

    /**
     * Schreibt Inhalt in eine Datei. Überschreibt vorhandene Inhalte. Erstellt eine neue Datei, falls nötig.
     * @param string $filename Dateipfad
     * @param string $content Zu schreibender Text
     * @return bool true, wenn erfolgreich, false, wenn ein Fehler aufgetreten ist
     */
    static function writeFileContent(string $filename, string $content): bool
    {
        $file = fopen($filename, "w");
        if (!$file) {
            return false;
        }

        if (!fwrite($file, $content)) {
            return false;
        }

        fclose($file);

        return true;
    }

    /**
     * Löscht eine Datei oder einen Ordner inklusive aller Inhalte
     * @param string $path Pfad zur Datei oder Verzeichnis
     * @return bool true, wenn gelöscht, sonst false
     */
    static function delete(string $path): bool
    {
        if(is_file($path)) {
            if(!unlink($path)) {
                return false;
            }
        } else if(is_dir($path)) {
            $entries = scandir($path);
            foreach ($entries as $entry) {
                if($entry == "." || $entry == "..") {
                    continue;
                }

                self::delete($path . "/" . $entry);
            }

        }

        return true;
    }

    /**
     * Öffnet eine Datei und gibt JSON-Inhalte als Array zurück
     * @param string $filename Dateipfad
     * @return mixed Array mit Key-Value Paaren
     */
    static function parseJsonFromFile(string $filename): mixed
    {
        $content = self::readFileContent($filename);
        if (!isset($content)) {
            return null;
        }
        return json_decode($content);
    }
}
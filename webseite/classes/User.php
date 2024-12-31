<?php

require_once("Util.php");

/**
 * Repräsentiert einen Nutzer inklusive aller Nutzerdaten
 */
class User
{
    /**
     * @var string Pfad zum Verzeichnis der Nutzerdaten
     */
    private static string $userdataDirectory = "./users/";

    /**
     * @var string Dateiname der Benutzerdaten
     */
    private static string $userdataFile = "users.csv";

    /**
     * @var string Benutzername, darf A-Z, a-z, 0-9 sowie - und _ enthalten, max. 100 Zeichen
     */
    private string $username;

    /**
     * @var string Passworthash
     */
    private string $passwordHash;


    /**
     * Erstellt einen neuen Benutzer
     * @param string $username Benutzername, darf A-Z, a-z, 0-9 sowie - und _ enthalten, max. 100 Zeichen
     * @param string $password Passwort, max. 100 Zeichen
     * @return User|false Den neu erstellten Benutzer oder false, wenn der Benutzer nicht erstellt werden konnte
     */
    public static function createUser(string $username, string $password): User|false
    {
        if(self::getFromUsername($username) !== false) {
            return false;
        }

        if(strlen($username) > 100) {
            return false;
        }
        if(strlen($password) > 100) {
            return false;
        }

        if (Util::containsIllegalCharacters($username)) {
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_ARGON2I);

        if(!is_dir(self::$userdataDirectory)) {
            mkdir(self::$userdataDirectory);
        }

        $file = fopen(self::$userdataDirectory . self::$userdataFile, "a");
        if (!$file) {
            return false;
        }

        fputcsv($file, array($username, $passwordHash),",", '"', "\\");
        fclose($file);

        return self::getFromUsername($username);
    }

    /**
     * Gibt einen Benutzer anhand eines Benutzernamen zurück
     * @param string $username Benutzername nach dem gesucht wird
     * @return User|false Der gefundene Besucher oder false, wenn der Benutzer nicht gefunden wurde
     */
    public static function getFromUsername(string $username): User|false
    {
        if (!file_exists(self::$userdataDirectory . self::$userdataFile)) {
            return false;
        }

        $file = fopen(self::$userdataDirectory . self::$userdataFile, "r");
        if (!$file) {
            return false;
        }

        while (($data = fgetcsv($file, 300, ',', '"', '\\')) !== false) {
            if (count($data) != 2) {
                continue;
            }
            if ($data[0] !== $username) {
                continue;
            }

            $user = new User();
            $user->username = $data[0];
            $user->passwordHash = $data[1];
            return $user;
        }

        fclose($file);

        return false;
    }

    /**
     * Löscht einen Benutzer
     * @param string $password Richtiges Passwort zu diesem Nutzer
     * @return bool true, wenn erfolgreich gelöscht, sonst false
     */
    public function delete(string $password): bool
    {
        if(!$this->isPasswordCorrect($password)) {
            return false;
        }

        if(!$this->logout()) {
            return false;
        }

        $file = fopen(self::$userdataDirectory . self::$userdataFile, "r");
        if(!$file) {
            return false;
        }

        $newCsv = array();

        while (($data = fgetcsv($file, 300, ',', '"', '\\')) !== false) {
            if (count($data) != 2) {
                continue;
            }
            if ($data[0] !== $this->username) {
                $newCsv[] = $data;
            }
        }
        fclose($file);

        $file = fopen(self::$userdataDirectory . self::$userdataFile, "w");
        if(!$file) {
            return false;
        }

        foreach ($newCsv as $newCsvData) {
            fputcsv($file, $newCsvData, ',', '"', '\\');
        }
        fclose($file);

        unset($this->username);
        unset($this->passwordHash);

        return true;
    }

    /**
     * Ändert das Passwort des Accounts
     * @param string $oldPassword altes Passwort
     * @param string $newPassword Neues Passwort
     * @return bool true, wenn erfolgreich geändert, sonst false
     */
    public function changePassword(string $oldPassword, string $newPassword): bool
    {
        if(!$this->isPasswordCorrect($oldPassword)) {
            return false;
        }

        if(!$this->logout()) {
            return false;
        }

        $file = fopen(self::$userdataDirectory . self::$userdataFile, "c+");
        if(!$file) {
            return false;
        }

        $this->passwordHash = password_hash($newPassword, PASSWORD_ARGON2I);

        $lastLine = ftell($file);
        while (($data = fgetcsv($file, 300, ',', '"', '\\')) !== false) {
            if (count($data) != 2) {

            } else if ($data[0] !== $this->username) {

            } else {
                $data[1] = $this->passwordHash;

                fseek($file, $lastLine);

                fputcsv($file, $data, ',', '"', '\\');
                break;
            }

            $lastLine = ftell($file);
        }
        fclose($file);

        return true;
    }

    /**
     * Prüft, ob ein Passwort für diesen Benutzer korrekt ist
     * @param string $password Zu prüfendes Passwort
     * @return bool true, wenn korrekt, sonst false
     */
    public function isPasswordCorrect(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    /**
     * Meldet den Benutzer an, wenn das Passwort richtig ist
     * @param string $password Passwort
     * @return bool true, wenn erfolgreich, sonst false
     */
    public function login(string $password): bool
    {
        if(!$this->isPasswordCorrect($password)) {
            return false;
        }

        if($this->isLoggedIn()) {
            return false;
        }

        $_SESSION["user"] = $this;
        $_SESSION["login_time"] = time();

        return true;
    }

    /**
     * Prüft, ob ein Benutzer angemeldet ist
     * @return bool true, wenn angemeldet, sonst false
     */
    public function isLoggedIn(): bool
    {
        if(!isset($_SESSION["user"])) {
            return false;
        }

        if($_SESSION["user"] != $this) {
            return false;
        }

        if(time() - $_SESSION["login_time"] > 86400 * 5) {
            session_unset();
            return false;
        }

        $_SESSION["login_time"] = time();

        return true;
    }

    /**
     * Meldet den Benutzer ab
     * @return bool true, wenn erfolgreich abgemeldet, false, wenn vorher schon abgemeldet
     */
    public function logout(): bool
    {
        if(!$this->isLoggedIn()) {
            return false;
        }

        session_unset();

        return true;
    }

    /**
     * Gibt den Benutzernamen zurück
     * @return string Benutzername
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}
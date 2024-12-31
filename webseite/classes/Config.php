<?php

/**
 * Konfigurationsdaten
 */
class Config
{
    /**
     * @return string Verzeichnis für generelle Konfigurationsdaten
     */
    public static function getConfigDirectory(): string
    {
        return "config/";
    }

    /**
     * @return string Verzeichnis für Fächer
     */
    public static function getSubjectsDirectory(): string
    {
        return self::getConfigDirectory() . "subjects/";
    }

    /**
     * @param string $subjectId ID des Faches
     * @return string Verzeichnis des Faches
     */
    public static function getSubjectDirectory(string $subjectId): string
    {
        return self::getSubjectsDirectory() . $subjectId . "/";
    }

    /**
     * @param string $subjectId ID des Faches
     * @return string Verzeichnis der Themen des Faches
     */
    public static function getTopicsDirectory(string $subjectId): string
    {
        return self::getSubjectDirectory($subjectId) . "topics/";
    }

    /**
     * @param string $subjectId ID des Faches
     * @param string $topicId ID des Themas
     * @return string Verzeichnis des Themas
     */
    public static function getTopicDirectory(string $subjectId, string $topicId): string
    {
        return self::getTopicsDirectory($subjectId) . $topicId . "/";
    }
}
<!DOCTYPE html>

<?php
require_once("classes/SubjectData.php");
require_once("classes/TopicData.php");

if (!isset($_GET["subject"])) {
    die("Ungültige Seite");
}
$subjectData = SubjectData::fromName($_GET["subject"]);
if (!isset($subjectData)) {
    die("Ungültige Seite");
}
$topics = $subjectData->topics;
?>

<html lang="de">
<!--topics-->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo($subjectData->displayName); ?> 5. Klasse</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/subject.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script src="assets/js/sidebar.js"></script>
    <script src="assets/js/search.js"></script>
</head>


<body class="min-h-screen">
<?php include 'header.php'; ?>

<nav class="sidebar bg-[<?php echo($subjectData->color); ?>] pt-24">
    <div class="sidebar-header">
        <i class="fas fa-graduation-cap"></i>

        <h2><?php echo($subjectData->displayName); ?></h2>
    </div>

    <a href="index.php" class="nav-link">
        <i class="fas fa-home"></i> Startseite
    </a>
    <a href="#" class="nav-link active">
        <i class="fas fa-book"></i> <?php echo($subjectData->displayName); ?> Übersicht
    </a>
</nav>

<div class="main-content">
    <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-8 mb-8">

        <?php

        foreach ($topics as $topicData) {
            ?>

            <div class="topic-card"
                 onclick="window.location.href='topic.php?subject=<?php echo($topicData->subjectId); ?>&topic=<?php echo($topicData->id); ?>'"
                 style="cursor: pointer;">
                <div class="topic-header">
                    <i class="fas <?php echo($topicData->icon); ?> topic-icon text-[<?php echo($subjectData->color); ?>]"></i>
                    <h3 class="topic-title"><?php echo($topicData->displayName); ?></h3>
                </div>
                <div class="topic-content">
                    <p class="topic-description">
                        <?php echo($topicData->description); ?>
                    </p>
                    <!--
                    <div class="related-topics bg-gray-100">
                        <h4>Verwandte Themen:</h4>
                        <ul>
                            <?php
                    foreach ($topicData->relatedTopics as $relatedTopicName) {
                        $relatedTopic = $subjectData->topics[$relatedTopicName];
                        if (!isset($relatedTopicName)) {
                            continue;
                        }

                        ?>

                                <li onclick="event.stopPropagation();" class="border-[<?php echo($subjectData->color); ?>] border-2">
                                    <a href='<?php echo("topic.php?subject=$subjectData->id&topic=$relatedTopic->id") ?>'>
                                        <?php echo($relatedTopic->displayName); ?>
                                    </a>
                                </li>

                                <?php
                    }
                    ?>
                        </ul>
                    </div>-->
                </div>
                <div class="download-section bg-gray-100 p-[20px] rounded-[20px]">
                    <h4>Übungen herunterladen:</h4>
                    <div class="download-links">
                        <?php
                        foreach ($topicData->files as $fileName) {
                            ?>

                            <a onclick="event.stopPropagation();"
                               href="<?php echo("config/subjects/$subjectData->id/topics/$topicData->id/downloads/$fileName") ?>"
                               target="_blank" download
                               class="download-btn border-2 border-[<?php echo($subjectData->color); ?>]">
                                <i class="fas fa-file-pdf"></i>
                                <?php echo($fileName); ?>
                            </a>

                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>

            <?php
        }
        ?>

    </div>
</div>

<footer class="sticky top-[100vh] lg:ms-[280px] w-full lg:w-auto bg-white/80 backdrop-blur-lg shadow-sm p-5">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between">
            <p class="text-gray-600">&copy; Horst-Schlämmer-Gedächtnis-Gymnasium</p>
            <p><a href="impressum.php"
                  class="text-[var(--primary-color)] hover:text-[var(--accent-color)]">Impressum</a></p>
        </div>
    </div>
</footer>

</body>
</html>

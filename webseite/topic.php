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
if (!isset($_GET["topic"])) {
    die("Ungültige Seite");
}
$topicData = TopicData::fromName($_GET["subject"], $_GET["topic"]);
if (!isset($topicData)) {
    die("Ungültige Seite");
}

?>

<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo($topicData->displayName); ?> - <?php echo($subjectData->displayName); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/topic.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script src="assets/js/sidebar.js"></script>
</head>


<body class="min-h-screen">
<?php include 'header.php'; ?>

<!-- Left Sidebar -->
<nav class="sidebar bg-[<?php echo($subjectData->color); ?>] pt-24">
    <div class="sidebar-header">
        <i class="fas fa-graduation-cap"></i>
        <h2><?php echo($subjectData->displayName); ?></h2>
    </div>
    <a href="index.php" class="nav-link">
        <i class="fas fa-home"></i> Startseite
    </a>
    <a href="subject.php?subject=<?php echo($subjectData->id); ?>" class="nav-link">
        <i class="fas fa-book"></i> <?php echo($subjectData->displayName); ?> Übersicht
    </a>
    <a href="#" class="nav-link active">
        <i class="fas fa-calculator"></i> <?php echo($topicData->displayName); ?>
    </a>
</nav>

<!-- Main Content -->
<main class="main-content">
    <div class="max-w-7xl mx-auto mt-5">
        <div class="mt-16"></div>

        <div class="related-topics bg-gray-100 p-4 rounded-lg">
            <h4>Verwandte Themen:</h4>
            <ul class="flex flex-wrap gap-2">
                <?php
                foreach ($topicData->relatedTopics as $relatedTopicName) {
                    $relatedTopic = $subjectData->topics[$relatedTopicName];
                    if (!isset($relatedTopic)) {
                        continue;
                    }
                    ?>
                    <li onclick="event.stopPropagation();"
                        class="border-[<?php echo($subjectData->color); ?>] border-2">
                        <a href='<?php echo("topic.php?subject=$subjectData->id&topic=$relatedTopic->id") ?>'
                           class="block">
                            <?php echo($relatedTopic->displayName); ?>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>

        <div class="content-card mt-[8px]">
            <h1 class="content-title"><?php echo($topicData->displayName); ?></h1>
            <p class="content-text">
                <?php echo($topicData->description); ?>
            </p>
            <p class="content-text article-section">
                <?php echo($topicData->getFinishedArticle()); ?>
            </p>

            <div class="exercise-section bg-gray-100">
                <h3 style="margin-bottom: 1rem;" class="text-[var(--primary-color)]">Übungen herunterladen:</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    <?php
                    foreach ($topicData->files as $fileName) {
                        ?>

                        <a href='<?php echo("config/subjects/$subjectData->id/topics/$topicData->id/downloads/$fileName") ?>'
                           target="_blank" download
                           class="download-btn border-[<?php echo($subjectData->color); ?>] border-2">
                            <i class="fas fa-file-pdf"></i>
                            <?php echo($fileName); ?>
                        </a>

                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="related-topics bg-gray-100 p-4 rounded-lg">
            <h4>Verwandte Themen:</h4>
            <ul class="flex flex-wrap gap-2">
                <?php
                foreach ($topicData->relatedTopics as $relatedTopicName) {
                    $relatedTopic = $subjectData->topics[$relatedTopicName];
                    if (!isset($relatedTopic)) {
                        continue;
                    }
                    ?>
                    <li onclick="event.stopPropagation();"
                        class="border-[<?php echo($subjectData->color); ?>] border-2">
                        <a href='<?php echo("topic.php?subject=$subjectData->id&topic=$relatedTopic->id") ?>'
                           class="block">
                            <?php echo($relatedTopic->displayName); ?>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
</main>

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

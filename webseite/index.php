<!DOCTYPE html>
<!--Homepage-->
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HSGG Lernportal</title>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>


<body class="bg-gray-50 min-h-screen">
<!-- Animated background blobs -->
<div class="fixed inset-0 -z-10 overflow-hidden">
    <div class="blob absolute w-96 h-96 bg-sky-300/30 -top-48 -left-16"></div>
    <div class="blob absolute w-96 h-96 bg-sky-300/30 bottom-0 right-0"></div>
</div>

<?php include 'header.php'; ?>

<!-- Hero Section -->
<div class="hidden md:block pt-24 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                Willkommen beim <br><span
                        class="text-[var(--primary-color)]">Horst-Schlämmer-Gedächtnis-Gymnasium</span>
            </h1>
        </div>
    </div>
</div>

<!-- Subject Grid -->
<div class="max-w-7xl mx-auto px-4 pt-20 grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
    <!-- Mathematik -->
    <?php
    require_once("classes/SubjectData.php");
    $subjects = SubjectData::getAll();

    foreach ($subjects as $subject) {
        // receive number of exercises for all topics of a subject
        $numOfExcercises = 0;
        foreach ($subject->topics as $topic) {
            $numOfExcercises += count($topic->files);
        }

        ?>
        <a href="subject.php?subject=<?php echo($subject->id); ?>" class="block">
            <div class="rounded-2xl border-4 border-[<?php echo($subject->color); ?>] p-6 card-hover bg-white">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                        <i class="fas <?php echo($subject->icon); ?> text-2xl text-[<?php echo($subject->color); ?>]"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900 mb-2"><?php echo($subject->displayName); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo($subject->description); ?></p>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center p-2 rounded-lg bg-gray-100">
                                <div class="font-bold text-[<?php echo($subject->color); ?>]"><?php echo(count($subject->topics)); ?></div>
                                <div class="text-sm text-gray-600">Themen</div>
                            </div>
                            <div class="text-center p-2 rounded-lg bg-gray-100">
                                <div class="font-bold text-[<?php echo($subject->color); ?>]"><?php echo($numOfExcercises); ?></div>
                                <div class="text-sm text-gray-600">Übungen</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <?php
    }
    ?>

</div>

<footer class="sticky top-[100vh] w-full bg-white/80 backdrop-blur-lg shadow-sm p-5">
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

<!-- header.php -->
<nav class="fixed top-0 w-full right-0 bg-white/80 backdrop-blur-lg shadow-sm z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Menu Toggle Button -->
            <div class="flex items-center space-x-4">

                <button class="menu-toggle hidden text-[var(--primary-color)] border-2 border-[var(--primary-color)] w-10 h-10 flex items-center justify-center rounded-lg hover:bg-[var(--primary-color)] hover:text-white transition duration-300">
                    <i class="fas fa-bars"></i>
                </button>

                <a href="index.php" class="flex items-center">
                    <img src="assets/images/hsgg-logo.png" alt="HSGG Logo" class="h-10 mr-3">
                    <span class="text-2xl font-bold text-[var(--primary-color)]">HSGG</span>
                </a>
            </div>

            <!-- Login/Logout Button -->
            <div class="flex items-center space-x-4">
                <!-- Search Button -->
                <button id="openSearchDialog"
                        class="bg-white text-[var(--primary-color)] border-2 border-[var(--primary-color)] w-10 h-10 flex items-center justify-center rounded-lg hover:bg-[var(--primary-color)] hover:text-white transition duration-300">
                    <i class="fas fa-search"></i>
                </button>

                <?php
                require_once("classes/User.php");
                session_start();
                if (isset($_SESSION['user']) && $_SESSION['user']->isLoggedIn()) {
                    ?>
                    <div class="flex items-center space-x-4">
                        <!-- Dropdown Button Elemente -->
                        <div class="relative">
                            <!-- Dropdown Trigger -->
                            <button id="userDropdownToggle"
                                class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--accent-color)] transition duration-300 flex items-center">
                                <span><?php echo htmlspecialchars($_SESSION['user']->getUsername()); ?></span>
                                <i class="fas fa-chevron-down ml-2"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="userDropdownMenu"
                             class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border">
                                <!-- TODO: Accountseite entsprechend verlinken -->
                                <a href="index.php"
                               class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg flex items-center">
                                    <i class="fas fa-user mr-2"></i> Accountseite
                                </a>
                                <button id="dropdownChangePasswordButton"
                                    class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-key mr-2"></i> Passwort ändern
                                </button>
                                <form id="dropdownLogoutForm" action="logout.php" method="POST" class="block">
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-b-lg flex items-center">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Bearbeiten Button -->     <!-- TODO: Korrekte/dynamische Verlinkung implementieren -->
                        <a href="index.php" class="bg-white text-[var(--primary-color)] border-2 border-[var(--primary-color)] w-10 h-10 flex items-center justify-center rounded-lg hover:bg-[var(--primary-color)] hover:text-white transition duration-300">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </div>
                    <?php
                } else {
                    // Login Button
                    echo '<button id="loginButton" class="bg-white text-[var(--primary-color)] border-2 border-[var(--primary-color)] w-10 h-10 flex items-center justify-center rounded-lg hover:bg-[var(--primary-color)] hover:text-white transition duration-300">
                              <i class="fas fa-sign-in-alt"></i>
                          </button>';
                }
                ?>
            </div>
        </div>
    </div>
</nav>

<!-- Login Popup | erscheint nur, wenn kein Nutzer eingeloggt ist -->
<?php
if (!isset($_SESSION['user']) || !$_SESSION['user']->isLoggedIn()) {
    ?>
    <div id="loginPopup" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50" role="dialog"
         aria-labelledby="loginTitle" aria-hidden="true">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm relative">
            <button id="closePopupButton" class="absolute top-2 right-2 text-gray-500 text-xl"
                    aria-label="Close Login Popup">&times;
            </button>
            <h2 id="loginTitle" class="text-2xl font-bold mb-6 text-center">Login</h2>
            <form id="loginForm" action="login.php" method="POST">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 mb-2">Benutzername:</label>
                    <input type="text" id="username" name="username" class="w-full p-2 border rounded-lg" required
                           autofocus>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 mb-2">Passwort:</label>
                    <input type="password" id="password" name="password" class="w-full p-2 border rounded-lg" required>
                </div>
                <button type="submit"
                        class="w-full bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--accent-color)] transition duration-300">
                    Login
                </button>
            </form>
            <div id="errorMessage" class="hidden text-red-500 text-center mt-4">Falscher Benutzername oder Passwort
            </div>
        </div>
    </div>
    <?php
}
?>

<!-- Passwort-ändern-Popup -->
<?php
if (isset($_SESSION['user']) && $_SESSION['user']->isLoggedIn()) {
    ?>
    <div id="changePasswordPopup" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50"
         role="dialog"
         aria-labelledby="changePasswordTitle" aria-hidden="true">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm relative">
            <button id="closeChangePasswordPopupButton" class="absolute top-2 right-2 text-gray-500 text-xl"
                    aria-label="Close Change Password Popup">&times;
            </button>
            <h2 id="changePasswordTitle" class="text-2xl font-bold mb-6 text-center">Passwort ändern</h2>
            <form id="changePasswordForm" action="password.php" method="POST">
                <div class="mb-4">
                    <label for="currentPassword" class="block text-gray-700 mb-2">Aktuelles Passwort:</label>
                    <input type="password" id="currentPassword" name="currentPassword"
                           class="w-full p-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="newPassword" class="block text-gray-700 mb-2">Neues Passwort:</label>
                    <input type="password" id="newPassword" name="newPassword" class="w-full p-2 border rounded-lg"
                           required>
                </div>
                <div class="mb-4">
                    <label for="confirmNewPassword" class="block text-gray-700 mb-2">Neues Passwort bestätigen:</label>
                    <input type="password" id="confirmNewPassword" name="confirmNewPassword"
                           class="w-full p-2 border rounded-lg" required>
                </div>
                <button type="submit"
                        class="w-full bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--accent-color)] transition duration-300">
                    Passwort ändern
                </button>
            </form>
            <div id="changePasswordErrorMessage" class="hidden text-red-500 text-center mt-4"></div>
        </div>
    </div>
    <?php
}
?>

<!-- Success Message Popup -->
<div id="passwordSuccessPopup" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm text-center">
        <h2 class="text-xl font-bold text-green-600 mb-4">Erfolg!</h2>
        <p>Passwort wurde erfolgreich geändert.</p>
        <button id="closeSuccessPopup" class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Schließen
        </button>
    </div>
</div>

<!-- Search Dialog -->
<div id="searchDialog" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-lg relative">
        <button id="closeSearchDialog" class="absolute top-2 right-2 text-gray-500 text-xl">&times;</button>
        <h2 class="text-2xl font-bold mb-6 text-center">Suche</h2>
        <input type="text" id="searchInput" placeholder="Search..." class="w-full p-2 border rounded-lg mb-4">
        <div id="searchResults" class="max-h-64 overflow-y-auto"></div>
    </div>
</div>

<script>
    // JavaScript to handle opening and closing of the login popup
    const loginButton = document.getElementById('loginButton');
    const loginPopup = document.getElementById('loginPopup');
    const closePopupButton = document.getElementById('closePopupButton');
    const usernameInput = document.getElementById('username');
    const errorMessage = document.getElementById('errorMessage');

    if (loginButton) { // Überprüfen, ob das Element vorhanden ist
        loginButton.addEventListener('click', function () {
            loginPopup.classList.remove('hidden');
            usernameInput.focus(); // Set focus to username field
        });
    }

    if (closePopupButton) { // Überprüfen, ob das Element vorhanden ist
        closePopupButton.addEventListener('click', function () {
            loginPopup.classList.add('hidden');
        });
    }

    window.addEventListener('click', function (event) {
        if (event.target === loginPopup) {
            loginPopup.classList.add('hidden');
        }
    });

    // Schließe Popup mit ESC
    document.addEventListener('keydown', function (event) {
        if (event.key === "Escape" && !loginPopup.classList.contains('hidden')) {
            loginPopup.classList.add('hidden');
        }
    });

    // Zeige Fehlermeldung beim Login an
    window.addEventListener('DOMContentLoaded', (event) => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('error') && urlParams.get('error') === '1') {
            loginPopup.classList.remove('hidden');
            errorMessage.classList.remove('hidden');
        }
    });



    // JavaScript to handle opening and closing of the change password popup
    const changePasswordButton = document.getElementById('changePasswordButton');
    const changePasswordPopup = document.getElementById('changePasswordPopup');
    const closeChangePasswordPopupButton = document.getElementById('closeChangePasswordPopupButton');

    if (changePasswordButton) { // Überprüfen, ob das Element vorhanden ist
        changePasswordButton.addEventListener('click', function () {
            if (changePasswordPopup) {
                changePasswordPopup.classList.remove('hidden');
            }
        });
    }
    if (closeChangePasswordPopupButton) {
        closeChangePasswordPopupButton.addEventListener('click', function () {
            if (changePasswordPopup) {
                changePasswordPopup.classList.add('hidden');
            }
        });
    }

    window.addEventListener('click', function (event) {
        if (event.target === changePasswordPopup) {
            changePasswordPopup.classList.add('hidden');
        }
    });

    // Close popup with ESC key
    document.addEventListener('keydown', function (event) {
        if (event.key === "Escape" && !changePasswordPopup.classList.contains('hidden')) {
            changePasswordPopup.classList.add('hidden');
        }
    });

    // Zeige Fehlermeldung beim Passwort ändern an
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.has('password_error')) {
            const changePasswordPopup = document.getElementById('changePasswordPopup');
            const changePasswordErrorMessage = document.getElementById('changePasswordErrorMessage');
            const errorType = urlParams.get('password_error');

            changePasswordPopup.classList.remove('hidden');
            changePasswordErrorMessage.classList.remove('hidden');

            switch (errorType) {
                case 'wrong_current_password':
                    changePasswordErrorMessage.textContent = 'Das aktuelle Passwort ist falsch.';
                    break;
                case 'password_mismatch':
                    changePasswordErrorMessage.textContent = 'Die neuen Passwörter stimmen nicht überein.';
                    break;
                default:
                    changePasswordErrorMessage.textContent = 'Fehler beim Ändern des Passworts.';
            }
        }
    });

    // Zeige Erfolgspopup beim Passwortwechsel an
    window.addEventListener('DOMContentLoaded', (event) => {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.has('password_success')) {
            const passwordSuccessPopup = document.getElementById('passwordSuccessPopup');
            if (passwordSuccessPopup) {
                passwordSuccessPopup.classList.remove('hidden');
            }
            const closeSuccessPopup = document.getElementById('closeSuccessPopup');
            if (closeSuccessPopup) {
                closeSuccessPopup.addEventListener('click', () => {
                    passwordSuccessPopup.classList.add('hidden');
                    // Optional: Entferne den URL-Parameter ohne Neuladen
                    const newUrl = window.location.href.split('?')[0];
                    window.history.replaceState({}, document.title, newUrl);
                });
            }
        }
    });

    // Dropdown öffnen/schließen
    const userDropdownToggle = document.getElementById('userDropdownToggle');
    const userDropdownMenu = document.getElementById('userDropdownMenu');

    if (userDropdownToggle && userDropdownMenu) {
        userDropdownToggle.addEventListener('click', (event) => {
            event.stopPropagation(); // Verhindert das Schließen des Menüs bei Klick auf den Button
            userDropdownMenu.classList.toggle('hidden');
        });

        // Schließe Dropdown, wenn außerhalb geklickt wird
        window.addEventListener('click', () => {
            if (!userDropdownMenu.classList.contains('hidden')) {
                userDropdownMenu.classList.add('hidden');
            }
        });

        // Schließe Dropdown mit ESC
        document.addEventListener('keydown', (event) => {
            if (event.key === "Escape" && !userDropdownMenu.classList.contains('hidden')) {
                userDropdownMenu.classList.add('hidden');
            }
        });
    }

    // Passwort ändern über Dropdown öffnen
    const dropdownChangePasswordButton = document.getElementById('dropdownChangePasswordButton');
    if (dropdownChangePasswordButton) {
        dropdownChangePasswordButton.addEventListener('click', () => {
            const changePasswordPopup = document.getElementById('changePasswordPopup');
            if (changePasswordPopup) {
                changePasswordPopup.classList.remove('hidden');
            }
        });
    }
    document.getElementById('openSearchDialog').addEventListener('click', function () {
        document.getElementById('searchDialog').classList.remove('hidden');
    });

    document.getElementById('closeSearchDialog').addEventListener('click', function () {
        document.getElementById('searchDialog').classList.add('hidden');
    });

    function debounce(func, delay) {
        let debounceTimer;
        return function () {
            const context = this;
            const args = arguments;
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(context, args), delay);
        };
    }

    const searchInput = document.getElementById('searchInput');
    const resultsContainer = document.getElementById('searchResults');

    searchInput.addEventListener('input', debounce(function () {
        const query = this.value.toLowerCase();
        resultsContainer.innerHTML = '';

        if (query.length > 0) {
            fetch('search.php?query=' + query)
                .then(response => response.json())
                .then(data => {
                    data.forEach(item => {
                        const resultItem = document.createElement('div');
                        resultItem.classList.add('p-4', 'mb-2', 'rounded-lg', 'bg-white', 'hover:bg-gray-100', 'transition', 'duration-100', 'flex', 'items-center', 'space-x-2', 'cursor-pointer');

                        const subjectSpan = document.createElement('span');
                        subjectSpan.classList.add('font-bold');
                        subjectSpan.textContent = item.displayName.split(' - ')[0];

                        const breadcrumbSpan = document.createElement('span');
                        breadcrumbSpan.classList.add('text-gray-500');
                        breadcrumbSpan.textContent = item.displayName.split(' - ').slice(1).join(' > ');

                        resultItem.appendChild(subjectSpan);
                        resultItem.appendChild(breadcrumbSpan);

                        resultItem.addEventListener('click', function () {
                            if (item.type === 'subject') {
                                window.location.href = 'subject.php?subject=' + item.id;
                            } else {
                                window.location.href = 'topic.php?subject=' + item.subjectId + '&topic=' + item.id;
                            }
                        });

                        resultsContainer.appendChild(resultItem);
                    });
                });
        }
    }, 300));

</script>

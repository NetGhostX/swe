import { SubjectManager } from './subjects/subjectManager.js';
import { TopicManager } from './topics/topicManager.js';

// Make modal functions globally available
window.openModal = (modalId) => {
    const modal = document.getElementById(modalId);
    modal.classList.remove('hidden');
    
    if (modalId === 'topicModal') {
        document.dispatchEvent(new Event('openTopicModal'));
    }
    
    gsap.fromTo(modal.children[0], 
        { y: -50, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.3 }
    );
};

window.closeModal = (modalId) => {
    const modal = document.getElementById(modalId);
    gsap.to(modal.children[0], {
        y: -50,
        opacity: 0,
        duration: 0.3,
        onComplete: () => {
            modal.classList.add('hidden');
        }
    });
};

// Make this function globally available
window.openSubjectModal = () => {
    openModal('subjectModal');
};

async function loadModals() {
    const modalContainer = document.getElementById('modalContainer');
    const modals = ['subjectModal', 'topicModal', 'resourceModal'];
    
    for (const modal of modals) {
        try {
            const response = await fetch(`components/${modal}.html`);
            const html = await response.text();
            modalContainer.innerHTML += html;
        } catch (error) {
            console.error(`Error loading ${modal}:`, error);
        }
    }
    
    // Initialize color picker functionality for subject modal
    initializeColorPicker();
}

function initializeColorPicker() {
    const colorOptions = document.querySelectorAll('.color-option');
    colorOptions.forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.color-option').forEach(opt => 
                opt.classList.remove('ring-2', 'ring-blue-500'));
            this.classList.add('ring-2', 'ring-blue-500');
            document.getElementById('selectedColor').value = this.dataset.color;
        });
    });
}

// Initialize everything after DOM content is loaded
document.addEventListener('DOMContentLoaded', async () => {
    await loadModals();
    const subjectManager = new SubjectManager();
    // Make topicManager globally available
    window.topicManager = new TopicManager();
    
    // Initialize event listeners for the subject form
    const form = document.getElementById('subjectForm');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Speichern...';

            try {
                await subjectManager.handleSubjectSubmit(e);
                showNotification('Fach erfolgreich erstellt!', 'success');
            } catch (error) {
                showNotification(error.message || 'Fehler beim Erstellen des Fachs', 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Speichern';
            }
        });
    }
});

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed bottom-4 right-4 p-4 rounded-lg text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    gsap.to(notification, {
        opacity: 0,
        y: 20,
        delay: 3,
        duration: 0.5,
        onComplete: () => notification.remove()
    });
}

// Form submissions and other JavaScript functions
// ... rest of your JavaScript code ...

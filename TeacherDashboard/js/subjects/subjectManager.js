import { generateColorPalette } from './colors.js';
import { SubjectModel } from './SubjectModel.js';
import { SubjectStorage } from './SubjectStorage.js';

export class SubjectManager {
    constructor() {
        this.storage = new SubjectStorage();
        this.subjects = [];
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        const form = document.getElementById('subjectForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubjectSubmit(e));
        }
        
        this.initializeColorPicker();
        this.initializeIconPicker();
    }

    initializeColorPicker() {
        const palette = generateColorPalette();
        const container = document.getElementById('colorPalette');
        
        if (container) {
            container.innerHTML = palette.map(color => `
                <div class="color-option" 
                     style="background-color: ${color.hex}" 
                     data-color="${color.hex}"
                     title="${color.name}">
                </div>
            `).join('');

            this.addColorPickerListeners();
        }
    }

    initializeIconPicker() {
        const iconOptions = document.querySelectorAll('.icon-option');
        iconOptions.forEach(option => {
            option.addEventListener('click', () => {
                iconOptions.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                document.getElementById('selectedIcon').value = option.dataset.icon;
            });
        });
    }

    addColorPickerListeners() {
        const colorOptions = document.querySelectorAll('.color-option');
        colorOptions.forEach(option => {
            option.addEventListener('click', () => {
                colorOptions.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                document.getElementById('selectedColor').value = option.dataset.color;
            });
        });
    }

    async handleSubjectSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        try {
            const subjectData = {
                name: formData.get('displayName'),
                description: formData.get('description'),
                color: formData.get('color'),
                icon: formData.get('icon'),
                materials: await this.processFiles(formData.getAll('materials[]'))
            };

            const subject = new SubjectModel(subjectData);
            subject.validate();

            await this.storage.saveSubject(subject.toJSON());
            this.updateSubjectsList(subject.toJSON());
            closeModal('subjectModal');
            e.target.reset();
            
            return subject;
        } catch (error) {
            throw new Error(`Failed to create subject: ${error.message}`);
        }
    }

    async processFiles(files) {
        return Array.from(files).map(file => ({
            name: file.name,
            size: file.size,
            type: file.type,
            lastModified: file.lastModified
        }));
    }

    async loadSubjects() {
        const subjects = await this.storage.getAllSubjects();
        subjects.forEach(subject => this.updateSubjectsList(subject));
    }

    validateSubjectForm(formData) {
        const requiredFields = ['displayName', 'color', 'icon'];
        return requiredFields.every(field => formData.get(field)?.trim());
    }

    updateSubjectsList(subject) {
        const container = document.getElementById('subjectsList');
        if (!container) {
            const mainContent = document.querySelector('main');
            const subjectsSection = document.createElement('div');
            subjectsSection.className = 'mt-8';
            subjectsSection.innerHTML = `
                <h2 class="text-xl font-semibold mb-4">Meine Fächer</h2>
                <div id="subjectsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                </div>
            `;
            mainContent.appendChild(subjectsSection);
        }

        const subjectElement = this.createSubjectElement(subject);
        document.getElementById('subjectsList').appendChild(subjectElement);
    }

    createSubjectElement(subject) {
        const div = document.createElement('div');
        div.className = 'bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4';
        div.innerHTML = `
            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: ${subject.color}">
                <i class="fas ${subject.icon} text-white"></i>
            </div>
            <div>
                <h3 class="font-medium">${subject.name}</h3>
                <p class="text-sm text-gray-500">${subject.description || ''}</p>
            </div>
        `;
        return div;
    }
}

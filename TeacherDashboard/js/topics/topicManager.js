import { SubjectStorage } from '../subjects/SubjectStorage.js';

export class TopicManager {
    constructor() {
        this.subjectStorage = new SubjectStorage();
        this.quill = null;
        this.initializeEventListeners();
    }

    initializeEditor() {
        const toolbarOptions = [
            ['undo', 'redo'],
            [{ 'header': [1, 2, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'script': 'sub'}, { 'script': 'super' }],
            [{ 'indent': '-1'}, { 'indent': '+1' }],
            ['link', 'image', 'formula'],
            ['clean']
        ];

        const editorOptions = {
            modules: {
                toolbar: toolbarOptions,
                keyboard: {
                    bindings: {
                        custom1: {
                            key: 'Z',
                            shortKey: true,
                            handler: () => this.undo()
                        },
                        custom2: {
                            key: 'Z',
                            shortKey: true,
                            shiftKey: true,
                            handler: () => this.redo()
                        },
                        custom3: {
                            key: 'Delete',
                            handler: () => this.deleteSelected()
                        }
                    }
                },
                history: {
                    delay: 1000,
                    maxStack: 500,
                    userOnly: true
                }
            },
            theme: 'snow',
            formats: [
                'header',
                'bold', 'italic', 'underline', 'strike',
                'list', 'bullet',
                'indent',
                'link', 'image',
                'code-block',
                'blockquote',
                'clean'
            ]
        };

        this.quill = new Quill('#quillEditor', editorOptions);
        this.quill.on('text-change', () => {
            this.updatePreview();
        });
    }

    undo() {
        if (this.quill) {
            this.quill.history.undo();
        }
    }

    redo() {
        if (this.quill) {
            this.quill.history.redo();
        }
    }

    deleteSelected() {
        if (this.quill) {
            const range = this.quill.getSelection();
            if (range) {
                if (range.length > 0) {
                    // Delete selected text/content
                    this.quill.deleteText(range.index, range.length);
                } else {
                    // Delete current line if no selection
                    const [line] = this.quill.getLine(range.index);
                    const lineLength = line.length();
                    this.quill.deleteText(range.index - lineLength, lineLength);
                }
            }
        }
    }

    async loadSubjectsIntoSelect() {
        const select = document.getElementById('topicSubjectSelect');
        if (!select) return;

        // Clear existing options except the first placeholder
        while (select.options.length > 1) {
            select.remove(1);
        }

        try {
            const subjects = await this.subjectStorage.getAllSubjects();
            subjects.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.id;
                option.textContent = subject.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading subjects:', error);
        }
    }

    initializeEventListeners() {
        const form = document.getElementById('topicForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleTopicSubmit(e));
        }
        document.addEventListener('openTopicModal', () => {
            this.loadSubjectsIntoSelect();
            if (!this.quill) {
                this.initializeEditor();
            }
        });
    }

    updatePreview() {
        const content = this.quill.root.innerHTML;
        const preview = document.getElementById('contentPreview');
        const quillContent = document.getElementById('quillContent');
        
        if (preview) {
            // Preserve classes for styling while updating content
            preview.innerHTML = content;
            // Update hidden input with content
            if (quillContent) {
                quillContent.value = content;
            }
            // Re-render math if present
            if (window.MathJax) {
                MathJax.typeset([preview]);
            }
            // Highlight code blocks if any
            if (window.Prism) {
                Prism.highlightAllUnder(preview);
            }
        }
    }

    handleTopicSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const content = this.quill.root.innerHTML;
        const subjectId = formData.get('subject');
        
        if (!subjectId) {
            throw new Error('Bitte wählen Sie ein Fach aus');
        }

        // Add your topic saving logic here
        
        closeModal('topicModal');
    }

    clearEditor() {
        if (this.quill) {
            this.quill.setContents([]);
            this.updatePreview();
        }
    }
}

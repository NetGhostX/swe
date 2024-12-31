export class SubjectStorage {
    constructor() {
        this.storageKey = 'teacherDash_subjects';
    }

    async getAllSubjects() {
        try {
            const data = localStorage.getItem(this.storageKey);
            return data ? JSON.parse(data) : [];
        } catch (error) {
            console.error('Error loading subjects:', error);
            return [];
        }
    }

    async saveSubject(subject) {
        try {
            const subjects = await this.getAllSubjects();
            const existingIndex = subjects.findIndex(s => s.id === subject.id);

            if (existingIndex >= 0) {
                subjects[existingIndex] = subject;
            } else {
                subjects.push(subject);
            }

            localStorage.setItem(this.storageKey, JSON.stringify(subjects));
            return subject;
        } catch (error) {
            throw new Error('Failed to save subject: ' + error.message);
        }
    }

    async deleteSubject(subjectId) {
        const subjects = await this.getAllSubjects();
        const filtered = subjects.filter(s => s.id !== subjectId);
        localStorage.setItem(this.storageKey, JSON.stringify(filtered));
    }
}
